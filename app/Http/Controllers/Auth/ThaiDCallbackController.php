<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Log;
use HP;

class ThaiDCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // 1) Resolve sessionThaID from cookie (format: "<session>/<state>", domain .tisi.go.th)
        $sessionThaID = $this->resolveSessionThaID($request);

        // 2) Call CheckThaiD2.asp and get raw XML
        $xml = $this->fetchThaIdXml($sessionThaID);

        // 3) Parse XML
        $parsed = $this->parseThaIdXml($xml);

        Log::debug('[thaid][DEBUG-TEMP] handle() real request', [
            'cookie_raw'    => $request->cookie('sessionThaID'),
            'sessionThaID'  => $sessionThaID,
            'xml_raw'       => $xml,
            'parsed'        => $parsed,
        ]);

        if (!$parsed['return'] || !$parsed['pid']) {
            return $this->debugHeaders(
                redirect('/login')->with('flash_message', 'ไม่สามารถยืนยันตัวตนผ่าน ThaiD ได้ กรุณาลองใหม่อีกครั้ง'),
                $request,
                [
                    'cookie-present' => $sessionThaID !== '' ? 'yes' : 'no',
                    'xml-return'     => $parsed['return'] ? 'true' : 'false',
                    'xml-pid'        => $parsed['pid'] ?? 'null',
                    'result'         => 'invalid-thaid-session',
                ]
            );
        }

        $taxNumber = $parsed['pid'];

        // เลิกใช้ progid/moi_prog แล้ว (ดู docs/bugsso.md ข้อ 17) - ทีม ASP ส่งคุกกี้ "appname"
        // มาตรงๆ แทน (Domain=.tisi.go.th, ยืนยันแล้วว่าแชร์ทุก subdomain) เอาไปหาปลายทางใน
        // ตาราง setting_systems (WHERE app_name = ?) แทน moi_prog (WHERE progid = ?) เดิม
        $appName = $this->resolveAppName($request);

        // identity รอบนี้ไม่ตรงกับ app_name ที่ login ค้างอยู่เดิม (เช่น auto-login เป็นระบบหนึ่ง
        // ไปแล้วก่อนหน้า แต่ ThaiD ส่ง app_name ใหม่มา) -> เคลียร์ session/cache เก่าทิ้งก่อน
        // เหมือนตอน logout กันไม่ให้ auto-login รอบใหม่ไปปนกับ session ของ app_name เดิม
        // (pattern เดียวกับ IIndustryCallbackController::handle())
        if (\Illuminate\Support\Facades\Auth::check() && $appName !== null && session('iindustry_app_name') !== null && session('iindustry_app_name') !== $appName) {
            HP::clearStaleAuthSession($request);
        }

        // 4) เช็คว่ามีบัญชีอยู่แล้วหรือยัง - ถ้ามี auto-login ตรงเข้าระบบปลายทางเลย
        // ต้องใช้ closure ครอบ ไม่ใช่ ->where('branch_type', '!=', 2) ตรงๆ เพราะ SQL 3-valued
        // logic: ถ้า branch_type IS NULL การเทียบ != 2 จะได้ NULL (ไม่ใช่ TRUE) แถวนั้นจะถูก
        // กรองทิ้งไปเงียบๆ ทั้งที่ tax_number ตรง (พบจริงกับ user สมัครถูกต้องแล้วด้วย
        // ไม่ใช่แค่ record broken - ดู docs/bugsso.md ข้อ 19)
        $existing = User::where('tax_number', $taxNumber)
            ->where(function ($q) {
                $q->where('branch_type', '!=', 2)->orWhereNull('branch_type');
            })
            ->first();

        Log::debug('[thaid][DEBUG-TEMP] handle() existing lookup result', [
            'taxNumber'        => $taxNumber,
            'appName'          => $appName,
            'found'            => $existing ? true : false,
            'user_id'          => $existing->id ?? null,
            'tax_number_in_db' => $existing->tax_number ?? null,
            'branch_type'      => $existing->branch_type ?? null,
            'db_connection'    => config('database.default'),
            'db_database'      => config('database.connections.' . config('database.default') . '.database'),
            'db_host'          => config('database.connections.' . config('database.default') . '.host'), // เฉพาะ host เท่านั้น ไม่มี user/password
        ]);

        $debugData = [
            'tax-number' => $taxNumber,
            'app-name'   => (string) $appName,
            'found'      => $existing ? 'true' : 'false',
            'user-id'    => $existing->id ?? 'null',
            'branch-type'=> $existing->branch_type ?? 'null',
            'db-host'    => config('database.connections.' . config('database.default') . '.host'),
            'db-name'    => config('database.connections.' . config('database.default') . '.database'),
        ];

        if ($existing) {
            // จำ app_name ของ session ที่กำลัง login เข้าไว้ เทียบกับรอบถัดไปว่าเปลี่ยนไปไหม
            session()->put('iindustry_app_name', $appName);
            session()->save();

            // login + ตั้ง cookie session_id เสมอ (ฟังก์ชันนี้ไม่คืนค่า null อีกแล้ว
            // แม้ app_name จะไม่ตรง mapping ใดๆ ใน setting_systems ก็ยัง login ให้ แค่พาไปหน้าแรกแทน)
            // checkApiData=true: เช็คสถานะกับ API กลาง (DBD/DOPA/RD) เหมือน manual login ทุกประการ
            $response = HP::loginAndRedirectByAppName($existing, $appName, false, true);
            return $this->debugHeaders($response, $request, $debugData + ['result' => 'existing-user-login']);
        }

        // 5) ยังไม่มีบัญชีตรงกับ identity ที่ ThaiD ส่งมารอบนี้ -> เคลียร์ session เก่าทิ้งก่อน
        //    (เหมือน i-industry) กันผู้ใช้ค้าง login เป็นคนละคนกับ identity ที่เพิ่งยืนยันมาจริง
        HP::clearStaleAuthSession($request);

        // 6) snapshot ข้อมูลจาก ThaiD ลง session แล้วไปหน้า register
        $response = $this->redirectToRegisterSnapshot($parsed, $appName);
        return $this->debugHeaders($response, $request, $debugData + ['result' => 'no-account-to-register']);
    }

    /**
     * DEBUG-TEMP: แปะข้อมูล diagnostic ลง HTTP response header แทนการต้อง SSH เข้า server
     * ไปอ่าน storage/logs/laravel.log - เปิด DevTools -> Network -> คลิก request
     * /internal/thaid-callback -> ดู Response Headers ได้เลยทุกเซิร์ฟเวอร์ไม่ว่า ThaiD จะ
     * redirect เข้าเครื่องไหนจริง ไม่ต้องมีสิทธิ์ SSH เข้าเซิร์ฟเวอร์นั้นเลย
     * ลบทิ้งพร้อมกับ Log::debug([thaid][DEBUG-TEMP]) หลังเลิกใช้งาน
     */
    private function debugHeaders($response, Request $request, array $data)
    {
        $response->headers->set('X-Debug-Handled-By-Host', $request->getHost());
        $response->headers->set('X-Debug-App-URL', (string) config('app.url'));
        foreach ($data as $key => $value) {
            $response->headers->set('X-Debug-' . $key, (string) $value);
        }
        return $response;
    }

    /**
     * อ่าน cookie "sessionThaID" (domain .tisi.go.th, รูปแบบ "<session>/<state>")
     * pattern เดียวกับ IIndustryCallbackController::resolveUidAid()
     */
    private function resolveSessionThaID(Request $request): string
    {
        $cookieHeader = (string) $request->headers->get('Cookie', '');

        $raw = $request->cookie('sessionThaID');
        if (!$raw && $cookieHeader) {
            if (preg_match('/(?:^|;\s*)(sessionThaID)\s*=\s*([^;]+)/i', $cookieHeader, $m)) {
                $raw = $m[2];
            }
        }

        if ($raw === null || $raw === '') {
            return '';
        }

        $decoded = urldecode($raw);
        if (strpos($decoded, '%') !== false) $decoded = urldecode($decoded);

        return $decoded;
    }

    /**
     * อ่านคุกกี้ "appname" (Domain=.tisi.go.th) ที่ทีม ASP ส่งมาตรงๆ แทน progid/state เดิม
     * ค่าอาจเป็น URL-encoded ต้อง urldecode ก่อนเทียบกับ setting_systems.app_name เสมอ -
     * pattern เดียวกับ IIndustryCallbackController::resolveAppName()
     */
    private function resolveAppName(Request $request): ?string
    {
        $cookieHeader = (string) $request->headers->get('Cookie', '');

        $raw = $request->cookie('appname');
        if (!$raw && $cookieHeader) {
            if (preg_match('/(?:^|;\s*)(appname)\s*=\s*([^;]+)/i', $cookieHeader, $m)) {
                $raw = $m[2];
            }
        }

        if ($raw === null || $raw === '') {
            return null;
        }

        $decoded = urldecode($raw);
        if (strpos($decoded, '%') !== false) $decoded = urldecode($decoded);

        $decoded = trim($decoded);
        return $decoded !== '' ? $decoded : null;
    }

    /**
     * GET CheckThaiD2.asp?sessionThaID=<session>/<state> และคืน raw XML (ว่างถ้าล้มเหลว)
     * ยืนยันจาก curl ตรงๆ แล้วว่า "sessionThaID" คือ parameter เดียวที่ต้องส่ง
     * (ทดสอบแล้ว: ไม่ส่ง หรือส่งเป็น session/state แยก -> VBScript error "Subscript out of range")
     */
    private function fetchThaIdXml(string $sessionThaID): string
    {
        if ($sessionThaID === '') {
            return '';
        }

        $url = 'https://www3.tisi.go.th/session/CheckThaiD2.asp?sessionThaID=' . urlencode($sessionThaID);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/xml,text/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: th-TH,th;q=0.9,en-US;q=0.8,en;q=0.7',
            'User-Agent: Mozilla/5.0',
        ));
        $body = curl_exec($ch);
        if ($body === false) {
            curl_close($ch);
            return '';
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code >= 200 && $code < 300) return (string) $body;
        return '';
    }

    /**
     * แกะ XML <ThaID> จาก CheckThaiD2.asp
     * Returns: ['return','pid','name','titleTh','given_name','family_name','birthdate','email','phone','state']
     */
    private function parseThaIdXml(string $xml): array
    {
        $out = array(
            'return'      => false,
            'pid'         => null,
            'name'        => null,
            'titleTh'     => null,
            'given_name'  => null,
            'family_name' => null,
            'birthdate'   => null,
            'email'       => null,
            'phone'       => null,
            'state'       => null,
        );

        if ($xml === '') {
            return $out;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        if (!@$dom->loadXML($xml) || !($dom->documentElement instanceof \DOMElement)) {
            return $out;
        }
        $root = $dom->documentElement;

        $get = function (string $tag) use ($root) {
            $node = $root->getElementsByTagName($tag)->item(0);
            return $node ? trim($node->nodeValue) : '';
        };

        $out['return']      = strtolower($get('Return')) === 'true';
        $out['pid']         = preg_replace('/\D/', '', $get('pid')) ?: null;
        $out['name']        = $get('name') ?: null;
        $out['titleTh']     = $get('titleTh') ?: null;
        $out['given_name']  = $get('given_name') ?: null;
        $out['family_name'] = $get('family_name') ?: null;
        $out['birthdate']   = $get('birthdate') ?: null;
        $out['email']       = $get('email') ?: null;
        $out['phone']       = $get('phone') ?: null;
        $out['state']       = preg_replace('/\D/', '', $get('state')) ?: null;

        return $out;
    }

    /**
     * เก็บข้อมูลจาก ThaiD ลง session แล้ว redirect ไปหน้า register/fill
     * pattern เดียวกับ IIndustryCallbackController::redirectToRegisterSnapshot()
     */
    private function redirectToRegisterSnapshot(array $parsed, $appName)
    {
        $payload = array(
            'source'      => 'thaid',
            'jt'          => '1', // ThaiD ยืนยันด้วยเลขบัตร ปชช. เสมอ = บุคคลธรรมดา
            'uid'         => $parsed['pid'],
            'bid'         => null,
            'app_name'    => $appName !== null ? (string) $appName : null,
            'name'        => $parsed['name'],
            'given_name'  => $parsed['given_name'],
            'family_name' => $parsed['family_name'],
            'birthdate'   => $parsed['birthdate'],
            'email'       => $parsed['email'],
            'phone'       => $parsed['phone'],
        );

        session()->put('prereg', $payload);
        session()->put('reg_source', 'thaid');
        session()->put('reg_app_name', $payload['app_name']);
        session()->save();

        $to = route('register.fill', ['source' => 'thaid'], false);

        return redirect()->to($to);
    }
}
