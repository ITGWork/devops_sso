<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use App\UserHistory;
use App\Sessions;
use App\LoginLog;
use Session;
use HP;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

/* Google Authen */
use Crypt;

use Google2FA;
use App\Models\Basic\Prefix;
use App\LoginFail;
use App\Models\WS\MOILog;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'reset_success', 'showApiDataConfirm', 'proceedApiDataConfirm']);
    }

    public function login(Request $request)
    {

        //เช็คว่าบัญชีถูกระงับไว้ชั่วคราวหรือไม่ Login ผิดเกินจำนวนครั้งที่กำหนด
        $check_lock = LoginFail::CheckLock($request->username);
        if($check_lock){
            return back()->withInput()
                         ->withErrors(['บัญชีผู้ใช้งานของคุณถูกระงับการใช้งานชั่วคราว'])
                         ->with('flash_lock_login', '1');
        }

        $user = User::where('username', $request->username)->first(); // เช็ค username

        if (!is_null($user)) {
            if(Hash::check($request->password, $user->password)){
                if($user->state == 1){ // รอยืนยันตัวตน
                    return back()->withInput()->with('flash_message', 'ชื่อผู้ใช้งานยังไม่ได้ยืนยันตัวตนในอีเมล ถ้าคุณไม่ได้รับอีเมลสามารถกรอกแบบฟอร์มได้จากลิงค์ข้างล่างนี้ <br><br><a href="'.url('reset-email').'" target="_blank"><i class="mdi mdi-hand-pointing-right"></i> ลิงค์</a> ');
                }else if($user->state == 3){
                    return back()->withInput()->with('flash_message', 'กรุณารอเจ้าหน้าที่สมอ.ยืนยันบัญชีผู้ใช้งานของคุณ');
                }else if($user->block == 1){ // บล็อกการใช้งาน
                    return back()->withInput()->with('flash_message', 'การเข้าสู่ระบบถูกปฏิเสธ! บัญชีของคุณถูกระงับการใช้งาน  <br><br><a href="'.url('contact').'" target="_blank">กรุณาติดต่อเจ้าหน้าที่</a>');
                }else if($user->block == 0){ // ใช้งาน

                    if($user->applicanttype_id == 1 || $user->applicanttype_id == 2 || $user->applicanttype_id == 3){

                        if(HP::check_number_counter($user->tax_number, 13)){
                            $apiCheckConfig = HP::getConfig(); // flag เปิด/ปิดเช็ค API รายตัว (basic/config แท็บ "เชื่อมโยง API กลางสมอ.")
                            if($user->applicanttype_id == 1 && !empty($apiCheckConfig->check_api_tisi_corporation)){ // เช็ค นิติบุคคล ใน DBD (ถ้าเปิดใช้งาน API นี้)
                                $entity = self::CheckLegalEntity($user->tax_number);
                                if($entity['status'] === false){
                                    return back()->withInput()->with('flash_message', 'ขออภัยเลขนิติบุคคล '.$user->tax_number .' ของคุณไม่พบการขึ้นทะเบียนกับกรมพัฒนาธุรกิจการค้า <br><br><a href="'.url('contact').'" target="_blank">กรุณาติดต่อเจ้าหน้าที่</a>');
                                }else if($entity['status'] === true){ // เชื่อม api ไม่ได้ ให้ login ได้

                                }else if($entity['status'] === 'other'){ // เชื่อม api ได้ ได้รับข้อความจาก API เป็นอื่นๆ
                                    return back()->withInput()->with('flash_message', 'เกิดเหตุขัดข้องการเชื่อมโยงข้อมูลของระบบ <a href="'.url('contact').'" target="_blank">กรุณาติดต่อเจ้าหน้าที่</a>');
                                }else if(!in_array($entity['status'], [1,2,3])){ //! 1.ยังดำเนินกิจการอยู่, 2.ฟื้นฟู, 3.คืนสู่ทะเบียน // ไม่ให้ Login

                                    if($user->juristic_status!=4){//เก็บประวัติ
                                        UserHistory::Add($user->id, 'juristic_status', $user->juristic_status, 4, null, 'system:sso');
                                    }

                                    if($user->juristic_cause_quit!=$entity['status']){//เก็บประวัติ
                                        UserHistory::Add($user->id, 'juristic_cause_quit', $user->juristic_cause_quit, $entity['status'], null, 'system:sso');
                                    }

                                    $user->juristic_status     = 4; //เลิกกิจการ
                                    $user->juristic_cause_quit = $entity['status']; //สาเหตุเลิกกิจการ
                                    $user->save();
                                    return back()->withInput()->with('flash_message', 'เลขนิติบุคคล '. $user->tax_number . ' ไม่สามารถ Login ได้ เนื่องจากมีสถานะกิจการเป็น:&nbsp;<u>'.$entity['status'].'</u>');
                                }else if(in_array($entity['status'], [1,2,3])){ //1.ยังดำเนินกิจการอยู่, 2.ฟื้นฟู, 3.คืนสู่ทะเบียน // Login ปกติ

                                    if($user->juristic_status!=$entity['status']){//เก็บประวัติ
                                        UserHistory::Add($user->id, 'juristic_status', $user->juristic_status, $entity['status'], null, 'system:sso');
                                    }

                                    $user->juristic_status = $entity['status'];
                                    $user->save();

                                    $compare_msg = $this->compareCompanyAndUpdate($user, $entity['data']);//เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล
                                    if(!empty($compare_msg)){//มีข้อมูลที่ไม่เหมือนและได้อัพเดทแล้ว
                                        Session::flash('flash_message', $compare_msg);
                                    }

                                }
                            }elseif($user->applicanttype_id == 2 && !empty($apiCheckConfig->check_api_tisi_person)){ // เช็ค บุคคลธรรมดา ใน DOPA (ถ้าเปิดใช้งาน API นี้)
                                $person = $this->getPerson($user->tax_number, $request->ip);
                                if(is_null($person)){//ไม่พบข้อมูลในทะเบียนราษฎร์
                                    return back()->withInput()->with('flash_message', 'ขออภัยเลขประจำตัวประชาชน '. $user->tax_number . ' ไม่พบในทะเบียนราษฎร์กรุณาติดต่อเจ้าหน้าที่');
                                }elseif($person === true){ // request api ไม่ได้ ให้ login ได้

                                }elseif($person === false){ // request api ได้รับข้อความตอบกลับอื่นๆ
                                    return back()->withInput()->with('flash_message', 'เกิดเหตุขัดข้องการเชื่อมโยงข้อมูลของระบบ <a href="'.url('contact').'" target="_blank">กรุณาติดต่อเจ้าหน้าที่</a>');
                                }elseif($person->statusOfPersonCode == '1'){//เสียชีวิต
                                    return back()->withInput()->with('flash_message', 'เลขประจำตัวประชาชน '. $user->tax_number . ' ไม่สามารถ Login ได้ เนื่องจากมีสถานะเป็น:&nbsp;<u>เสียชีวิต</u>');
                                }else{// Login ปกติ
                                    $compare_msg = $this->comparePersonAndUpdate($user, $person);//เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล
                                    if(!empty($compare_msg)){//มีข้อมูลที่ไม่เหมือนและได้อัพเดทแล้ว
                                        Session::flash('flash_message', $compare_msg);
                                    }
                                }
                            }elseif($user->applicanttype_id == 3 && !empty($apiCheckConfig->check_api_tisi_faculty)){// เช็ค คณะบุคคล ใน RD กรมสรรพากร (ถ้าเปิดใช้งาน API นี้)

                                $rd = HP::getRdVat($user->tax_number, $request->ip());

                                if(!empty($rd->vMessageErr)){//ไม่พบข้อมูลในสรรพากร
                                    return back()->withInput()->with('flash_message', 'ขออภัยเลขประจำตัวผู้เสียภาษี '. $user->tax_number . ' ไม่พบในกรมสรรพากรกรุณาติดต่อเจ้าหน้าที่');
                                }elseif($rd->status === 'no-connect'){ // request api ไม่ได้ ให้ login ได้

                                }elseif(!empty($rd->Message) && $rd->Message=='Response Failed'){
                                    return back()->withInput()->with('flash_message', 'เกิดเหตุขัดข้องการเชื่อมโยงข้อมูลของระบบ <a href="' . url('contact') . '" target="_blank">กรุณาติดต่อเจ้าหน้าที่</a>');
                                }else{// Login ปกติ

                                    $compare_msg = $this->compareRdAndUpdate($user, $rd);//เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล
                                    if(!empty($compare_msg)){//มีข้อมูลที่ไม่เหมือนและได้อัพเดทแล้ว
                                        Session::flash('flash_message', $compare_msg);
                                    }

                                }
                            }
                            // หมายเหตุ: ถ้า applicanttype_id ตรง 1/2/3 แต่ปิด flag check_api_tisi_* ไว้
                            // จะไม่เข้า if/elseif ข้างบนเลยสักอัน (เงื่อนไข applicanttype_id ตรงแต่ flag เป็นเท็จ)
                            // ปล่อยผ่านไป login สำเร็จเลยเหมือนกรณี "เชื่อมต่อ API ไม่ได้"
                        }else{
                            return back()->withInput()->with('flash_message', 'เลขประจำตัวประชาชน/เลขนิติบุคคล '. $user->tax_number .' ไม่เท่ากับ 13 หลักกรุณาติดต่อเจ้าหน้าที่');
                        }

                    }

                    //เข้าสู่ระบบได้สำเร็จ
                    $redirect_uri = $request->get('redirect_uri'); //URL ที่จะให้ไปไซต์อื่น
                    $loged_url = !empty($redirect_uri) && filter_var($redirect_uri, FILTER_VALIDATE_URL) ? $redirect_uri : 'dashboard';
                    $config = HP::getConfig();
                    if(property_exists($config, 'sso_google2fa_status') && $config->sso_google2fa_status!=0){//เปิดใช้ google2fa
                        //(เปิดใช้งานแบบไม่บังคับ+เชื่อมต่อ 2fa แล้ว) หรือ เปิดใช้แบบบังคับ
                        if(($config->sso_google2fa_status==1 && $user->google2fa_status==1) || $config->sso_google2fa_status==2){
                            Session::put('2fa:user:id', $user->getKey());
                            return redirect('login');
                        }else{//เปิดใช้งานแบบไม่บังคับทุกคน แต่ยังไม่เชื่อม 2fa
                            $this->set_login_session($request, $user);
                            return redirect($loged_url);
                        }
                    }else{//ไม่เปิดใช้ google2fa ให้เข้าสู่ระบบได้เลย
                        $this->set_login_session($request, $user);
                        return redirect($loged_url);
                    }

                }else{
                    return back()->withInput()->withErrors(['ชื่อผู้ใช้งานยังไม่ได้ยืนยันตัวตนใน E-mail']);
                }
            }else{//รหัสผ่านไม่ถูกต้อง

                //เก็บประวัติ Login ผิดพลาด
                LoginFail::Add($request->ip(), $request->username);

                return back()->withInput()->withErrors(['ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง']);
            }

        }else{//ไม่พบ username

            //เก็บประวัติ Login ผิดพลาด
            LoginFail::Add($request->ip(), $request->username);

            if(HP::check_number_counter($request->username, 13)){//เป็นเลข 13 หลัก

                $entity = self::CheckLegalEntity($request->username);

                if(!is_null($entity['data'])){
                    return back()->withInput()->with('message', 'เลขนิติบุคคล '.  $request->username  . ' ไม่มีข้อมูลการลงทะเบียน ท่านต้องการลงทะเบียนหรือไม่ ?');
                }else{
                    return back()->withInput()->with('message', 'หมายเลข '.  $request->username  . ' ไม่มีข้อมูลการลงทะเบียน ท่านต้องการลงทะเบียนหรือไม่ ?');
                }
            }else{
                return back()->withInput()->withErrors(['ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง']);
            }

        }

    }

    //Login และบันทึก session ในฐานข้อมูล
    private function set_login_session($request, $user){

        //Login
        Auth::login($user);

        //บันทึกวัน/เวลา Login
        $user->lastvisitDate = date('Y-m-d H:i:s');
        $user->save();

        $session_id = session()->getId();

        //บันทึกลงตาราง session
        Sessions::Add(
                    $session_id,
                    $user->getKey(),
                    $request->ip(),
                    $request->userAgent(),
                    'web'
                );

        //บันทึกลง response cookie
        $config = HP::getConfig();
        $minutes = config('session.lifetime');
        Cookie::queue($config->sso_name_cookie_login,
                      $session_id,
                      $minutes,
                      null,
                      $config->sso_domain_cookie_login,
                      null,
                      false
                  );

    }

    /**
     * แสดงหน้ายืนยันรับทราบข้อมูลที่ระบบอัปเดตให้อัตโนมัติจาก API กลาง (DBD/DOPA/RD)
     * ใช้ตอน auto-login (i-industry/ThaiD) ที่พบข้อมูลไม่ตรงแล้วแก้ให้ - ต่างจาก manual
     * login ที่ปลายทางเป็นหน้าในระบบเราเอง (dashboard) จึงเห็น flash_message เป็น toast
     * ได้เลย แต่ auto-login ปลายทางมักเป็นระบบภายนอก (e-License ฯลฯ) ถ้า redirect ตรงไป
     * เลยผู้ใช้จะไม่มีทางเห็นข้อความนี้ ต้องมีหน้ากลางในระบบเราให้กดยืนยันก่อน
     */
    public function showApiDataConfirm()
    {
        $message = session('api_update_confirm_message');
        if (empty($message)) {
            return redirect('/dashboard');
        }
        return view('auth.api_data_confirm', compact('message'));
    }

    //กดยืนยันรับทราบแล้ว - พาไปปลายทางเดิมที่เก็บไว้ตอน loginAndRedirectByAppName()
    // เช็ค setting_id ก่อนเพื่อรองรับ transfer_method='form_post' ด้วย (bare
    // redirect($destination) ใช้ได้แค่กรณี 'redirect' เท่านั้น)
    public function proceedApiDataConfirm()
    {
        $settingId   = session()->pull('api_update_confirm_setting_id');
        $destination = session()->pull('api_update_confirm_destination', '/');
        session()->forget('api_update_confirm_message');

        if (!empty($settingId)) {
            $setting = \App\Models\Setting\SettingSystem::find($settingId);
            if ($setting) {
                return HP::buildSystemRedirect($setting);
            }
        }

        return redirect($destination);
    }

    public function logout(Request $request)
    {
        //ล้างค่าต่างๆ
        $this->action_logout($request);

        return redirect('/');
    }

    private function action_logout($request){

        //Config
        $config = HP::getConfig();

        $user = Auth::user();

        if(!is_null($user)){// Login อยู่

            Sessions::Remove(session()->getId());//ลบจากตาราง sso_session

            activity($user->name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('LoggedOut');
            $this->guard()->logout();
            $request->session()->invalidate();
        }else{// ไม่ได้ Login
            $cookie = Cookie::get($config->sso_name_cookie_login);
            if(!is_null($cookie)){//มีค่า cookie
                Sessions::Remove($cookie);//ลบจากตาราง sso_session
            }
        }

        //ลบ Cookie Login
        Cookie::queue(Cookie::forget($config->sso_name_cookie_login, null, $config->sso_domain_cookie_login));

        return true;
    }

    //ส่งไปหน้า logout พร้อมข้อความที่จะแสดงในหน้า login
    public function reset_success(Request $request){

        //ล้างค่าต่างๆ
        $this->action_logout($request);

        Session::flash('flash_message', 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว');

        return redirect('login');

    }

    public function CheckLegalEntity($tax_number)
    {

        $response = ['status' => false, 'data' => null];

        $config = HP::getConfig();

        $url = $config->tisi_api_corporation_url; //'https://www3.tisi.go.th/moiapi/srv.asp?pid=1';
        $data = array(
                'val' => $tax_number,
                'IP' =>  $_SERVER['REMOTE_ADDR'],    // IP Address,
                'Refer' => 'sso.tisi.go.th'
                );
        $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                    'timeout' => 10
                )
        );
        if(strpos($url, 'https')===0){//ถ้าเป็น https
            $options["ssl"] = array(
                                    "verify_peer" => false,
                                    "verify_peer_name" => false,
                                );
        }
        $context  = stream_context_create($options);
        $i = 1;
        start:
        if($i <= 3){
            try {
                $request_start = date('Y-m-d H:i:s');
                $api = null;

                $json_data = @file_get_contents($url, false, $context);
                if ($json_data === false || trim($json_data) === '') {
                    throw new \RuntimeException('DBD API returned an empty response.');
                }
                $api = json_decode($json_data);
                if (json_last_error() !== JSON_ERROR_NONE || !is_object($api)) {
                    throw new \RuntimeException('DBD API returned invalid JSON.');
                }
                if(!empty($api->JuristicName_TH)){
                    $juristic_status = ['ยังดำเนินกิจการอยู่' => '1', 'ฟื้นฟู' => '2', 'คืนสู่ทะเบียน' => '3'];
                    $status = array_key_exists($api->JuristicStatus,$juristic_status) ? $juristic_status[$api->JuristicStatus] : $api->JuristicStatus ;  //สถานะนิติบุคคล
                    $response['status'] = $status;
                    $response['data'] = $api;
                }elseif(property_exists($api, 'result') && trim($api->result)=='Bad Request'){//ไม่พบข้อมูล

                }else{//บริการปลายทางมีปัญหา
                    $response['status'] = 'other';
                }
            } catch (\Throwable $e) {
                $api = [
                    'error' => get_class($e),
                    'message' => $e->getMessage(),
                ];
                $i++;

                if ($i <= 3) {
                    //บันทึก Log
                    MOILog::Add($tax_number, $url, 'corporation', $request_start, @$http_response_header, (is_null($response['data']) ? $api : null));
                }

                goto start;
            }
        }else{
            $response['status'] = true;
        }

        //บันทึก Log
        MOILog::Add($tax_number, $url, 'corporation', $request_start, @$http_response_header, (is_null($response['data']) ? $api : null));

        return $response;

    }

    public function getPerson($tax_id, $ip){

        $person = null;

        $config = HP::getConfig();

        $url = $config->tisi_api_person_url; //'https://www3.tisi.go.th/moiapi/srv.asp?pid=2';
        $data = array(
                'val'   => $tax_id,
                'IP'    => $ip,
                'Refer' => 'sso.tisi.go.th'
                );
        $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                    'timeout' => 10
                )
        );
        if(strpos($url, 'https')===0){//ถ้าเป็น https
            $options["ssl"] = array(
                                    "verify_peer" => false,
                                    "verify_peer_name" => false,
                              );
        }
        $context  = stream_context_create($options);
        $i = 1;
        start:
        if($i <= 3){
            try {

                $request_start = date('Y-m-d H:i:s');
                $api = null;

                $json_data = @file_get_contents($url, false, $context);
                if ($json_data === false || trim($json_data) === '') {
                    throw new \RuntimeException('DOPA API returned an empty response.');
                }
                $api = json_decode($json_data);
                if (json_last_error() !== JSON_ERROR_NONE || !is_object($api)) {
                    throw new \RuntimeException('DOPA API returned invalid JSON.');
                }

                // Normalize DOPA response fields so both Code/Message and code/message are supported.
                $apiCode = trim((string)($api->code ?? $api->Code ?? ''));
                $apiMessage = trim((string)($api->message ?? $api->Message ?? ''));
                if ($apiCode !== '') {
                    $api->Code = $apiCode;
                }
                if ($apiMessage !== '') {
                    $api->Message = $apiMessage;
                }
                $quotaExhausted = $apiCode === '503'
                    && mb_strpos($apiMessage, 'สิทธิ์การเรียกใช้ข้อมูลประจำวันครบถ้วนแล้ว') !== false;

                if(!empty($api->firstName)){//พบข้อมูล
                    $person = $api;
                }elseif(property_exists($api, 'Message') && trim($api->Message)=='CitizenID is not specify'){//รูปแบบเลขประชาชนไม่ถูกต้อง (ไม่พบข้อมูล)
                    $person = null;
                }elseif(property_exists($api, 'Code') && trim($api->Code)=='00404'){//ไม่พบข้อมูล
                    $person = null;
                }elseif((property_exists($api, 'Code') && trim($api->Code)=='90050') || $quotaExhausted){//โควตาประจำวันเต็ม ให้ bypass การตรวจ API และเข้าสู่ระบบได้
                    $person = true;
                }else{ //อื่นๆ เชื่อมไปเอาข้อมูลมาไม่ได้
                    $person = false;
                }

                /*elseif(property_exists($api, 'Code') && $api->Code=='90001'){//ไม่ได้ login ที่ API สปอ. ให้ผ่านเข้าระบบได้
                    $person = true;
                }*/
            } catch (\Throwable $e) {
                $api = [
                    'error' => get_class($e),
                    'message' => $e->getMessage(),
                ];
                $i++;

                if ($i <= 3) {
                    //บันทึก Log
                    MOILog::Add($tax_id, $url, 'person', $request_start, @$http_response_header, $api);
                }

                goto start;
            }
        }else{ //เชื่อมไม่ได้ครบ 3 ครั้ง
            $person = true;
        }

        //บันทึก Log
        MOILog::Add($tax_id, $url, 'person', $request_start, @$http_response_header, (!is_object($person) ? $api : null));

        return $person;
    }

    //เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล นิติบุคคล
    public function compareCompanyAndUpdate($user, $company, $update=true){

        $user_before = $user->toArray();//ข้อมูลก่อนการเปลี่ยนแปลงเพื่อบันทึกลง log
        $data_changes = [];//เก็บข้อมูลที่ไม่ตรงเพื่อแสดงผล
        $msg_html     = '';

        if($user->prefix_text != $company->JuristicType){//ประเภทบริษัทไม่ตรง

            $data_changes[] = ['label' => 'ประเภทบริษัท', 'old' => $user->prefix_text, 'new' => $company->JuristicType];

            $juristic_types = HP::juristic_types();
            $key = array_search($company->JuristicType, $juristic_types);

            $user->prefix_name = $key!==false ? $key : null ;
            $user->prefix_text = $company->JuristicType;
        }
        // แบงก์ แก้ไข เรื่องวันที่ จาก i indeustry 4/2/2569
        // $register_dates    = str_split($company->RegisterDate, 2);
        // $company_date_niti = (($register_dates[0].$register_dates[1])-543).'-'.$register_dates[2].'-'.$register_dates[3];
        $rawDate = $company->RegisterDate; // สมมติค่าคือ '25551115' หรือ '20121115'

        if (strlen($rawDate) >= 8) {
            // 1. ตัดส่วนประกอบจากท้ายสตริง
            $day   = substr($rawDate, -2);    // สองหลักสุดท้าย
            $month = substr($rawDate, -4, 2); // สองหลักถัดมา
            $year  = substr($rawDate, 0, strlen($rawDate) - 4); // ส่วนที่เหลือข้างหน้าทั้งหมดคือปี

            $year = intval($year);

            // 2. ตรวจสอบว่าเป็นปี พ.ศ. หรือไม่ (ใช้เกณฑ์ 2400)
            // ถ้าเกิน 2400 แสดงว่าเป็น พ.ศ. ให้ลบออก 543 เพื่อเป็น ค.ศ.
            if ($year > 2400) {
                $year = $year - 543;
            }

            // 3. รูปแบบ Y-m-d 
            $company_date_niti = $year . '-' . $month . '-' . $day;
        } else {
            $company_date_niti = null; // กรณีข้อมูลมาไม่ครบ
        }
        // แบงก์ แก้ไข เรื่องวันที่ จาก i indeustry 4/2/2569
        
        
        if($user->date_niti != $company_date_niti){//วันที่จดทะเบียนไม่ตรง
            $data_changes[] = ['label' => 'วันที่จดทะเบียนนิติบุคคล', 'old' => HP::DateThai($user->date_niti), 'new' => HP::DateThai($company_date_niti)];
            $user->date_niti = $company_date_niti;
        }

        if(in_array($company->JuristicType, ['บริษัทจำกัด', 'บริษัทมหาชนจำกัด','ห้างหุ้นส่วนจำกัด', 'ห้างหุ้นส่วนสามัญนิติบุคคล'])){

        //แบงก์แก้ไข 29/1/2569
        //     $company_name = 'บริษัท '.$company->JuristicName_TH;
        // }else if(in_array($company->JuristicType, ['ห้างหุ้นส่วนจำกัด', 'ห้างหุ้นส่วนสามัญนิติบุคคล'])){
        //     $company_name = $company->JuristicType.' '.$company->JuristicName_TH;
        // }else{
        //     $company_name = $company->JuristicName_TH;
        
        

        $company_name = $company->JuristicName_TH;
            // if(in_array($company->JuristicType, ['บริษัทจำกัด', 'บริษัทมหาชนจำกัด'])){
            //     if(mb_substr($company->JuristicName_TH,0,7) != 'บริษัท ')
            //     $company_name = 'บริษัท '.$company->JuristicName_TH;
            // } else if(in_array($company->JuristicType, ['ห้างหุ้นส่วนจำกัด', 'ห้างหุ้นส่วนสามัญนิติบุคคล'])){
            //     if(mb_substr($company->JuristicName_TH,0,12) != 'ห้างหุ้นส่วน')
            //         $company_name = $company->JuristicType.' '.$company->JuristicName_TH;
            // } else {
            //     $company_name = $company->JuristicName_TH;
            // }

            $type = $company->JuristicType; // เช่น 'ห้างหุ้นส่วนจำกัด'

            if (in_array($type, ['บริษัทจำกัด', 'บริษัทมหาชนจำกัด'])) {
            // ตรวจสอบว่ามีคำว่า "บริษัท" อยู่ในชื่อหรือยัง
                if (mb_strpos($company_name, 'บริษัท') === false) {
                    $company_name = 'บริษัท ' . $company_name;
                    }
            } 
            else if (in_array($type, ['ห้างหุ้นส่วนจำกัด', 'ห้างหุ้นส่วนสามัญนิติบุคคล'])) {
                // ตรวจสอบว่ามีคำว่า "ห้างหุ้นส่วน" อยู่ในชื่อหรือยัง
                if (mb_strpos($company_name, 'ห้างหุ้นส่วน') === false) {
                    $company_name = $type . ' ' . $company_name;
                }
            }


        // แบงก์แก้ไข 29/1/2569
        }

        $company_name = HP::replace_multi_space($company_name);

        if($user->name!=$company_name){//ชื่อบริษัทไม่ตรง
            $data_changes[] = ['label' => 'ชื่อบริษัท', 'old' => $user->name, 'new' => $company_name];
            $user->name = $company_name;
        }

        //ที่ตั้งสำนักงาน
        $address = [];
        if(property_exists($company, 'AddressInformations') && count($company->AddressInformations) > 0){
            foreach ($company->AddressInformations as $info) {
                if($info->AddressName=='สำนักงานใหญ่'){
                    $address = $info;
                    break;
                }
            }
            if(count((array)$address)==0){//ไม่มี สำนักงานใหญ่ ให้เอาข้อมูล Array ชุดแรกเป็นสำนักงานใหญ่
                $address = $company->AddressInformations[0];
            }

            //อัพเดทฟิลด์ที่ให้แก้ไขได้/ไม่ได้
            // $params = (object)json_decode($user->params);
            // $editable_address_no = empty($address->AddressNo) ? true : false; //ข้อมูลเลขที่ตั้งเป็นค่าว่าง ให้แก้ไขได้
            // if(!property_exists($params, 'editable')){
            //     $params->editable = (object)['address_no' => $editable_address_no];
            // }else{
            //     $params->editable->address_no = $editable_address_no;
            // }
            // $user->params = json_encode($params);

            $address = HP::format_address_company_api($address);
            //var_dump($address); // แสดงประเภทข้อมูลและค่า
            //die(); // หยุดการทำงานเพื่อให้เห็นค่าชัดเจน

            //เช็คข้อมูล
            if($user->address_no!=$address->AddressNo){//เลขที่ไม่ตรง
                $data_changes[] = ['label' => 'เลขที่ (สำนักงานใหญ่)', 'old' => $user->address_no, 'new' => $address->AddressNo];
                $user->address_no = $address->AddressNo;
            }
            // $fulladdress =  HP::replace_address($address->FullAddress, $address->Moo, $address->Soi, $address->Road);
            // if($user->address_no!=$fulladdress && !empty($fulladdress)){//เลขที่ไม่ตรง
            //     $data_changes[] = ['label' => 'เลขที่ (สำนักงานใหญ่)', 'old' => $user->address_no, 'new' => $fulladdress];
            //     $user->address_no = $fulladdress;
            // }

            if($user->building!=$address->Building){//อาคารไม่ตรง
                // $data_changes[] = ['label' => 'อาคาร (สำนักงานใหญ่)', 'old' => $user->building, 'new' => $address->Building];
                $user->building = $address->Building;
            }

            if($user->moo!=$address->Moo){//หมู่ไม่ตรง
                $data_changes[] = ['label' => 'หมู่ (สำนักงานใหญ่)', 'old' => $user->moo, 'new' => $address->Moo];
                $user->moo = $address->Moo;
            }

            if($user->soi!=$address->Soi){//ซอยไม่ตรง
                $data_changes[] = ['label' => 'ตรอก/ซอย (สำนักงานใหญ่)', 'old' => $user->soi, 'new' => $address->Soi];
                $user->soi = $address->Soi;
            }

            if($user->street!=$address->Road){//ถนนไม่ตรง
                $data_changes[] = ['label' => 'ถนน (สำนักงานใหญ่)', 'old' => $user->street, 'new' => $address->Road];
                $user->street = $address->Road;
            }

            if($user->subdistrict!=$address->Tumbol){//ตำบลไม่ตรง
                $data_changes[] = ['label' => 'ตำบล/แขวง (สำนักงานใหญ่)', 'old' => $user->subdistrict, 'new' => $address->Tumbol];
                $user->subdistrict = $address->Tumbol;
            }

            if($user->district!=$address->Ampur){//อำเภอไม่ตรง
                $data_changes[] = ['label' => 'อำเภอ/เขต (สำนักงานใหญ่)', 'old' => $user->district, 'new' => $address->Ampur];
                $user->district = $address->Ampur;
            }

            if($user->province!=$address->Province){//จังหวัดไม่ตรง
                $data_changes[] = ['label' => 'จังหวัด (สำนักงานใหญ่)', 'old' => $user->province, 'new' => $address->Province];
                $user->province = $address->Province;
            }

        }

        if($update===true){//ให้อัพเดทข้อมูลในฐานข้อมูลด้วย
            $user->check_api = 1;

            //เก็บ Log
            $user_after = $user->toArray();//ข้อมูลหลังการเปลี่ยนแปลงเพื่อบันทึกลง log
            foreach ($user_after as $key => $value) {
                if(array_key_exists($key, $user_before) ){
                    if($user_before[$key]!=$value){
                        UserHistory::Add($user->id,
                                        $key,
                                        $user_before[$key],
                                        $value,
                                        null,
                                        'system:sso'
                                    );
                    }
                }
            }

            //อัพเดทข้อมูล
            $user->save();
        }

        //HTML แสดงข้อมูลที่เปลี่ยนแปลง
        if(count($data_changes) > 0){
            $msg_html .= '<div class="row">';
            $msg_html .= '    <div class="col-md-12">';
            $msg_html .= '        <h4>พบข้อมูลของคุณไม่ตรงกับกรมพัฒนาธุรกิจการค้า ระบบได้ปรับปรุงข้อมูลแล้วดังนี้</h4>';
            $msg_html .= '    </div>';
            $msg_html .= '    <div class="col-md-12">';
            $msg_html .= '        <div class="table-responsive">';
            $msg_html .= '            <table class="table color-bordered-table info-bordered-table">';
            $msg_html .= '                <thead>';
            $msg_html .= '                    <tr><th class="text-center">ชื่อข้อมูล</th><th class="text-center">ข้อมูลเดิม</th><th class="text-center">ข้อมูลใหม่</th></tr>';
            $msg_html .= '                </thead>';
            $msg_html .= '                <tbody>';
                            foreach ($data_changes as $data_change) {
                                $msg_html .= '<tr><td><b>'.$data_change['label'].'</b></td><td class="danger">'.$data_change['old'].'</td><td class="success">'.$data_change['new'].'</td></tr>';
                            }
            $msg_html .= '                </tbody>';
            $msg_html .= '            </table>';
            $msg_html .= '        </div>';
            $msg_html .= '    </div>';
            $msg_html .= '</div>';
        }

        return $msg_html;

    }

    //เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล บุคคลธรรมดา
    public function comparePersonAndUpdate($user, $person, $update=true){

        $user_before = $user->toArray();//ข้อมูลก่อนการเปลี่ยนแปลงเพื่อบันทึกลง log
        $data_changes = [];//เก็บข้อมูลที่ไม่ตรงเพื่อแสดงผล
        $msg_html     = '';

        $person_name = $person->titleName.$person->firstName.' '.$person->lastName;//ชื่อเต็ม
        $person_name = HP::replace_multi_space($person_name);

        if($user->prefix_text != $person->titleName){//คำนำหน้าชื่อไม่ตรง

            $prefix = Prefix::where('title', $person->titleName)->orWhere('initial', $person->titleName)->first();
            if(is_null($prefix)){//ไม่พบในข้อมูลพื้นฐาน
                $prefix = Prefix::where('title', 'อื่นๆ')->first();
            }

            $data_changes[] = ['label' => 'คำนำหน้าชื่อ', 'old' => $user->prefix_text, 'new' => $person->titleName];

            $user->prefix_name = $prefix->id;
            $user->prefix_text = $person->titleName;

            $user->name = $person_name;//ชื่อเต็ม
        }

        if($user->person_first_name != $person->firstName){//ชื่อไม่ตรง

            $data_changes[] = ['label' => 'ชื่อ', 'old' => $user->person_first_name, 'new' => $person->firstName];

            $user->person_first_name = $person->firstName;
            $user->name = $person_name;//ชื่อเต็ม
        }

        if($user->person_last_name != $person->lastName){//สกุลไม่ตรง

            $data_changes[] = ['label' => 'สกุล', 'old' => $user->person_last_name, 'new' => $person->lastName];

            $user->person_last_name = $person->lastName;
            $user->name = $person_name;//ชื่อเต็ม
        }

        if($user->name!=$person_name){//ชื่อเต็มไม่ตรง
            $data_changes[] = ['label' => 'คำนำหน้าชื่อ-สกุล', 'old' => $user->name, 'new' => $person_name];

            $user->name = $person_name;//ชื่อเต็ม
        }

        $births               = str_split($person->dateOfBirth, 2);
        $births[2]            = $births[2]=='00' ? '01' : $births[2];//เดือน 00
        $births[3]            = $births[3]=='00' ? '01' : $births[3];//วันที่ 00
        $person_date_of_birth = (($births[0].$births[1])-543).'-'.$births[2].'-'.$births[3];
        if($user->date_of_birth != $person_date_of_birth){//วันเกิดไม่ตรง

            $data_changes[] = ['label' => 'วันเกิด', 'old' => HP::DateThai($user->date_of_birth), 'new' => HP::DateThai($person_date_of_birth)];
            $user->date_of_birth = $person_date_of_birth;
        }

        // if($user->nationality != $person->nationalityDesc){//สัญชาติไม่ตรง

        //     $data_changes[] = ['label' => 'สัญชาติ', 'old' => $user->nationality, 'new' => $person->nationalityDesc];
        //     $user->nationality = $person->nationalityDesc;
        // }

        if($update===true){//ให้อัพเดทข้อมูล

            $user->check_api = 1;

            //เก็บ Log
            $user_after = $user->toArray();//ข้อมูลหลังการเปลี่ยนแปลงเพื่อบันทึกลง log
            foreach ($user_after as $key => $value) {
                if(array_key_exists($key, $user_before) ){
                    if($user_before[$key]!=$value){
                        UserHistory::Add($user->id,
                                        $key,
                                        $user_before[$key],
                                        $value,
                                        null,
                                        'system:sso'
                                    );
                    }
                }
            }

            //อัพเดทข้อมูล
            $user->save();
        }

        //HTML แสดงข้อมูลที่เปลี่ยนแปลง
        if(count($data_changes) > 0){
            $msg_html .= '<div class="row">';
            $msg_html .= '    <div class="col-md-12">';
            $msg_html .= '        <h4>พบข้อมูลของคุณไม่ตรงกับทะเบียนราษฎร์ ระบบได้ปรับปรุงข้อมูลแล้วดังนี้</h4>';
            $msg_html .= '    </div>';
            $msg_html .= '    <div class="col-md-12">';
            $msg_html .= '        <div class="table-responsive">';
            $msg_html .= '            <table class="table color-bordered-table info-bordered-table">';
            $msg_html .= '                <thead>';
            $msg_html .= '                    <tr><th class="text-center">ชื่อข้อมูล</th><th class="text-center">ข้อมูลเดิม</th><th class="text-center">ข้อมูลใหม่</th></tr>';
            $msg_html .= '                </thead>';
            $msg_html .= '                <tbody>';
                            foreach ($data_changes as $data_change) {
                                $msg_html .= '<tr><td><b>'.$data_change['label'].'</b></td><td class="danger">'.$data_change['old'].'</td><td class="success">'.$data_change['new'].'</td></tr>';
                            }
            $msg_html .= '                </tbody>';
            $msg_html .= '            </table>';
            $msg_html .= '        </div>';
            $msg_html .= '    </div>';
            $msg_html .= '</div>';
        }

        return $msg_html;

    }

    //เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล บุคคลธรรมดา
    public function compareRdAndUpdate($user, $rd, $update=true){

        $user_before  = $user->toArray();//ข้อมูลก่อนการเปลี่ยนแปลงเพื่อบันทึกลง log
        $data_changes = [];//เก็บข้อมูลที่ไม่ตรงเพื่อแสดงผล
        $msg_html     = '';

        $rd = HP::format_address_rd_api($rd);//จัดรูปแบบข้อมูลที่อยู่ใหม่

        if($user->name != $rd->vBranchName){//ชื่อไม่ตรง
            $data_changes[] = ['label' => 'ชื่อ', 'old' => $user->name, 'new' => $rd->vBranchName];
            $user->name = $rd->vBranchName;
        }

        if($user->address_no != $rd->vHouseNumber){//เลขที่ไม่ตรง
            $data_changes[] = ['label' => 'เลขที่', 'old' => $user->address_no, 'new' => $rd->vHouseNumber];
            $user->address_no = $rd->vHouseNumber;
        }

        if($user->moo != $rd->vMooNumber){//หมู่ไม่ตรง
            $data_changes[] = ['label' => 'หมู่', 'old' => $user->moo, 'new' => $rd->vMooNumber];
            $user->moo = $rd->vMooNumber;
        }

        if($user->soi != $rd->vSoiName){//ซอยไม่ตรง
            $data_changes[] = ['label' => 'ซอย', 'old' => $user->soi, 'new' => $rd->vSoiName];
            $user->soi = $rd->vSoiName;
        }

        if($user->street != $rd->vStreetName){//ถนนไม่ตรง
            $data_changes[] = ['label' => 'ถนน', 'old' => $user->street, 'new' => $rd->vStreetName];
            $user->street = $rd->vStreetName;
        }

        if($user->subdistrict != $rd->vThambol){//ตำบล/แขวง
            $data_changes[] = ['label' => 'ตำบล', 'old' => $user->subdistrict, 'new' => $rd->vThambol];
            $user->subdistrict = $rd->vThambol;
        }

        if($user->district != $rd->vAmphur){//อำเภอ/เขตไม่ตรง
            $data_changes[] = ['label' => 'อำเภอ', 'old' => $user->district, 'new' => $rd->vAmphur];
            $user->district = $rd->vAmphur;
        }

        if($user->province != $rd->vProvince){//จังหวัด
            $data_changes[] = ['label' => 'จังหวัด', 'old' => $user->province, 'new' => $rd->vProvince];
            $user->province = $rd->vProvince;
        }

        if(!is_null($rd->vBusinessFirstDate) && $user->date_niti != $rd->vBusinessFirstDate){//วันที่จดทะเบียน
            $data_changes[] = ['label' => 'วันที่จดทะเบียน', 'old' => $user->date_niti, 'new' => $rd->vBusinessFirstDate];
            $user->date_niti = $rd->vBusinessFirstDate;
        }

        if($update===true){//อัพเดทในฐานข้อมูลด้วย
            $user->check_api = 1;//เป็นข้อมูลจาก API

            //เก็บ Log
            $user_after = $user->toArray();//ข้อมูลหลังการเปลี่ยนแปลงเพื่อบันทึกลง log
            foreach ($user_after as $key => $value) {
                if(array_key_exists($key, $user_before) ){
                    if($user_before[$key]!=$value){
                        UserHistory::Add($user->id,
                                        $key,
                                        $user_before[$key],
                                        $value,
                                        null,
                                        'system:sso'
                                    );
                    }
                }
            }

            //อัพเดทข้อมูล
            $user->save();
        }

        //HTML แสดงข้อมูลที่เปลี่ยนแปลง
        if(count($data_changes) > 0){
            $msg_html .= '<div class="row">';
            $msg_html .= '    <div class="col-md-12">';
            $msg_html .= '        <h4>พบข้อมูลของคุณไม่ตรงกับกรมสรรพากร ระบบได้ปรับปรุงข้อมูลแล้วดังนี้</h4>';
            $msg_html .= '    </div>';
            $msg_html .= '    <div class="col-md-12">';
            $msg_html .= '        <div class="table-responsive">';
            $msg_html .= '            <table class="table color-bordered-table info-bordered-table">';
            $msg_html .= '                <thead>';
            $msg_html .= '                    <tr><th class="text-center">ชื่อข้อมูล</th><th class="text-center">ข้อมูลเดิม</th><th class="text-center">ข้อมูลใหม่</th></tr>';
            $msg_html .= '                </thead>';
            $msg_html .= '                <tbody>';
                            foreach ($data_changes as $data_change) {
                                $msg_html .= '<tr><td><b>'.$data_change['label'].'</b></td><td class="danger">'.$data_change['old'].'</td><td class="success">'.$data_change['new'].'</td></tr>';
                            }
            $msg_html .= '                </tbody>';
            $msg_html .= '            </table>';
            $msg_html .= '        </div>';
            $msg_html .= '    </div>';
            $msg_html .= '</div>';
        }

        return $msg_html;

    }

        public function google2fa_validate(Request $request){

            $one_time_password = implode('', $request->get('one_time_password'));

            $user_id = Session::get('2fa:user:id', null);
            $user = User::find($user_id);

            $secret = Crypt::decrypt($user->google2fa_secret);

            $result = Google2FA::verifyKey($secret, $one_time_password);

            if($result){//รหัสถูกต้อง
                Session::forget('2fa:user:id');
                $this->set_login_session($request, $user);
                return redirect('dashboard');
            }else{
                Session::flash('one_time_password_error', 'รหัส Google Authenticator ไม่ถูกต้อง');
                return redirect('login');
            }
        }

        public function google2fa_setup(Request $request){

            $one_time_password = implode('', $request->get('one_time_password'));

            $user_id = Session::get('2fa:user:id', null);
            $user = User::find($user_id);

            $secret = Crypt::decrypt($user->google2fa_secret);

            $result = Google2FA::verifyKey($secret, $one_time_password);

            if($result){//รหัสถูกต้อง

                Session::forget('2fa:user:id');

                //อัพเดทผูก google 2fa สำเร็จ
                $user->google2fa_status = 1;
                $user->save();

                $this->set_login_session($request, $user);
                return redirect('dashboard');
            }else{
                Session::flash('one_time_password_error', 'รหัส Google Authenticator ไม่ถูกต้อง');
                return redirect('login');
            }
        }

        //clear session 2fa:user:id
        public function google2fa_clear_session(){
            Session::forget('2fa:user:id');
        }

}
