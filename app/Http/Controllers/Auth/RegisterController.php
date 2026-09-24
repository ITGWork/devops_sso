<?php

namespace App\Http\Controllers\Auth;

use App\Profile;
use App\User;
use App\RoleUser;
use App\UserGroupMap;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use App\Models\Basic\Prefix;
use App\Models\Basic\Province;
use App\Models\Basic\ConfigRoles as config_roles;
use HP;
use DB;
use Storage;
use Session;
use Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;
use App\Mail\Authorities;
use App\Models\WS\MOILog;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        // AJAX/utility endpoint ที่หน้า register เรียกใช้ต้องเปิดสาธารณะเสมอ ไม่ว่า
        // browser จะมี session cookie ค้างอยู่ (แม้หมดอายุ/ใช้ไม่ได้แล้ว) หรือไม่ก็ตาม
        // เดิมไม่มี except() เลย ทำให้ guest middleware (RedirectIfAuthenticated) เตะ
        // request เหล่านี้ไป "/" ก่อนถึง controller เสมอเมื่อมี session ค้าง ทำให้หน้า
        // register ค้างที่ loading overlay (AJAX ไม่เคย resolve เป็น success)
        $this->middleware('guest')->except([
            'datatype',
            'check_tax_number',
            'get_tax_number',
            'get_legal_entity',
            'get_legal_faculty',
            'get_taxid',
            'check_email',
            'get_house_address',
            'ActivatedMail',
        ]);
        $this->attach_path = 'media/com_user/';
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function registered(Request $request, $user)
    {
        if($user->profile == null){
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();
        }
        activity($user->name)
            ->performedOn($user)
            ->causedBy($user)
            ->log('Registered');
        $user->assignRole('user');
    }

    public function register(Request $request)
    {

        $request->validate([
            'password' => 'required|string|confirmed'
        ]);

        $prefix                             = Prefix::where('state',1)->pluck('title', 'id');

        $requests                           = $request->all();
        $requestData                        = $requests['jform'];
        $requestData['contact_tax_id']      = isset($requestData['contact_tax_id']) ? self::PregReplace($requestData['contact_tax_id']) : null;
        $requestData['contact_tel']         = isset($requestData['contact_tel']) ? $requestData['contact_tel'] : null;
        $requestData['contact_phone_number']= isset($requestData['contact_phone_number']) ? self::PregReplace($requestData['contact_phone_number']) : null;
        $requestData['tax_number']          = isset($requestData['tax_number']) ? self::PregReplace($requestData['tax_number']) : null;
        $requestData['username']            = $requestData['tax_number'];
        // $requestData['username']            = isset($requestData['username']) ? $requestData['username'] : null;
        $requestData['password']            = Hash::make($request->password);
        $requestData['fax']                 = isset($requestData['contact_fax']) ? $requestData['contact_fax'] : null;

        // Validate address against master data before creating the user.
        $addressSource = $requestData['address_source'] ?? null;
        $isApiPerson = (string)($requestData['applicanttype_id'] ?? '') === '2'
            && (string)($requestData['check_api'] ?? '0') === '1';
        if ($addressSource === 'manual_master' || $isApiPerson) {
            $resolvedAddress = HP::GetIDAddress(
                $requestData['subdistrict'] ?? null,
                $requestData['district'] ?? null,
                $requestData['province'] ?? null
            );

            $addressIsValid = !empty($resolvedAddress->province_id)
                && !empty($resolvedAddress->district_id)
                && !empty($resolvedAddress->subdistrict_id)
                && !empty($resolvedAddress->zipcode);
            $submittedIdsMatch = empty($requestData['province_id'])
                || ((string)$requestData['province_id'] === (string)$resolvedAddress->province_id
                    && (string)($requestData['district_id'] ?? '') === (string)$resolvedAddress->district_id
                    && (string)($requestData['subdistrict_id'] ?? '') === (string)$resolvedAddress->subdistrict_id);

            if (!$addressIsValid || !$submittedIdsMatch) {
                return back()->withInput()->withErrors([
                    'ลงทะเบียนไม่สำเร็จ กรุณาเลือกแขวง/ตำบล เขต/อำเภอ และจังหวัดจากรายการค้นหาที่อยู่'
                ]);
            }

            $requestData['zipcode'] = $resolvedAddress->zipcode;
        }

        // These fields are only used for validation and are not users table columns.
        unset($requestData['address_source'], $requestData['province_id'], $requestData['district_id'], $requestData['subdistrict_id']);

        //ตรวจสอบข้อมูลที่จำเป็นอีกครั้ง
        $user_table = (new User)->getTable();
        $rule = [
                    'email' => 'required|email|unique:'.$user_table.',email',
                    'tax_number' => 'required|string',
                    'tel' => 'required|string',
                    'applicanttype_id' => ['required', Rule::in(['1', '2', '3', '4', '5'])]
                ];
        $validator = Validator::make($requestData, $rule);
        if ($validator->fails()) {
            $errorString = implode(",",$validator->messages()->all());
            return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ '.$errorString]);
        }

        //เช็คว่าครบ 13 หลัก
        if(in_array($requestData['applicanttype_id'], [1, 2, 3, 4])){
            $requestData['tax_number'] = $this->CutNumberOnly($requestData['tax_number']);//ตัดออกให้เหลือแต่ตัวเลข
            $requestData['username']   = $requestData['tax_number'];
            if(strlen($requestData['tax_number'])!=13){
                return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ เลขประจำตัวผู้เสียภาษีไม่เท่ากับ 13 หลัก']);
            }
        }

        //เช็คซ้ำในฐานข้อมูลที่ไม่ใช่สาขา
        $count_tax = User::where('tax_number', $requestData['tax_number'])->where('branch_type', '!=', 2)->count();
        if($count_tax > 0){
            return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ เลขประจำตัวผู้เสียภาษีนี้ได้ลงทะเบียนแล้ว']);
        }

        if ($requestData['applicanttype_id']==2 && $requestData['check_api']==1) { //บุคคลธรรมดาและเช็คเลขจาก API เช็ควันเกิดว่าถูกต้องหรือไม่
            if(Crypt::decrypt($requestData['date_of_birth_encrypt'])!=HP::convertDate($requestData['date_birthday'])){
                return back()->withInput()->withErrors(['วันเกิดไม่ตรงกรุณาตรวจสอบ']);
            }
        }

     if(in_array($requestData['applicanttype_id'],[5])){ //ชื่อผู้ประกอบการ   อื่นๆ
        $requestData['date_niti']           =  HP::convertDate($requestData['date_birthday']);  // วันที่จดทะเบียนอื่นๆ
        $requestData['name']                =  $requestData['another_name'] ;
    }else if(in_array($requestData['applicanttype_id'],[4])){ //ชื่อผู้ประกอบการ   ส่วนราชการ
        $requestData['date_niti']           =  HP::convertDate($requestData['date_birthday']);  // วันที่จดทะเบียนส่วนราชการ
        $requestData['name']                =  $requestData['service_name'] ;
    }else  if(in_array($requestData['applicanttype_id'],[3])){ //ชื่อผู้ประกอบการ  คณะบุคคล
        $requestData['date_niti']           =  HP::convertDate($requestData['date_birthday']);  // วันที่จดทะเบียนนิติบุคคล
        $requestData['name']                =  $requestData['faculty_name'] ;
    }else if(in_array($requestData['applicanttype_id'],[2])){ //ชื่อผู้ประกอบการ  บุคคลธรรมดา
        $requestData['date_of_birth']       = HP::convertDate($requestData['date_birthday']);  // วันเกิดบุคคลธรรมดา
        $requestData['prefix_name']         = $requestData['person_prefix_name'];
        $requestData['prefix_text']         = $prefix[$requestData['person_prefix_name']];
        $requestData['name']                =  (isset($requestData['person_first_name']) && isset($requestData['person_last_name']))   ? $requestData['prefix_text'].''.$requestData['person_first_name'].' '. $requestData['person_last_name']  : null;
    }else{  // ชื่อผู้ประกอบการ นิติบุคคล
        $prefix_name                        = ['1'=>'บริษัทจำกัด','2'=>'บริษัทมหาชนจำกัด','3'=>'ห้างหุ้นส่วนจำกัด','4'=>'ห้างหุ้นส่วนสามัญนิติบุคคล'];
        $requestData['date_niti']           =  HP::convertDate($requestData['date_birthday']);  // วันที่จดทะเบียนนิติบุคคล
        $requestData['prefix_name']         = $requestData['prefix_name'];
        $requestData['prefix_text']         = array_key_exists($requestData['prefix_name'],$prefix_name) ? $prefix_name[$requestData['prefix_name']] : null;

    }
        $requestData['contact_name']        =  (isset($requestData['contact_first_name']) && isset($requestData['contact_last_name']))   ? $requestData['contact_first_name'].' '. $requestData['contact_last_name']  : null;
        $requestData['contact_prefix_name'] = $requestData['contact_prefix_name'];
        $requestData['contact_prefix_text'] = $prefix[$requestData['contact_prefix_name']] ?? null;

        if(is_file($request->personfile)){
            $requestData['personfile']     =   self::storeFile($request->personfile, $requestData['username']);
        }



        // reg_source is set server-side only by IIndustryCallbackController after a real
        // i-industry cookie/API round-trip. Peek (not pull) here so a failed attempt
        // (e.g. duplicate-key error) doesn't burn the flag before a successful retry -
        // it's only consumed once User::create() actually succeeds, below.
        // 'i-industry' และ 'thaid' ต่างก็ยืนยันตัวตนผ่าน API ภายนอกมาแล้วเหมือนกัน
        // เลยข้ามการยืนยันอีเมลได้ทั้งคู่ (ใช้ตัวแปรชื่อเดิม $fromIIndustry ไว้ก่อน
        // เพื่อลด diff แต่ความหมายจริงคือ "มาจากช่องทางที่ยืนยันตัวตนแล้ว")
        $fromIIndustry = in_array(session('reg_source'), ['i-industry', 'thaid'], true);
        $regAppName    = session('reg_app_name');

        $requestData['registerDate']        =  date('Y-m-d H:i:s');
        if($fromIIndustry){
            $requestData['state']               = 2; // ยืนยันตัวตนแล้ว - ข้ามการยืนยันอีเมล เพราะยืนยันตัวตนผ่าน i-industry/thaid มาแล้ว
            $requestData['block']               = 0;
            $requestData['i_industry_lastlogin'] = date('Y-m-d H:i:s');
        }else{
            $requestData['state']               = 1;
            $requestData['block']               = 1;
        }
        $requestData['params']              = '{}';
        $requestData['department_id']       = '0';
        $requestData['agency_tel']          = '';
        $requestData['authorize_data']      = '';

        try {
            $user = User::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ ข้อมูลนี้มีการลงทะเบียนในระบบแล้ว']);
            }
            throw $e;
        }

        // create() succeeded - now safe to burn the one-time i-industry flags
        session()->forget(['reg_source', 'reg_app_name']);

        // sync ข้อมูลไป ros_users (e-license) ทันทีตอนลงทะเบียนสำเร็จ ไม่ต้องรอให้ผู้ใช้
        // ไปกดบันทึกที่หน้า /profile/show อีกต่อไป (เดิมใช้ forceProfileCheck=true บังคับ
        // พาไปหน้านั้นเพื่อ trigger sync เพราะตอนนั้น syncToRosUsers() ยังเป็น private
        // method อยู่ใน ProfileController เท่านั้น - ตอนนี้ย้ายมาเป็น HP:: แล้วเรียกตรงได้เลย)
        HP::syncToRosUsers($user);

        if($user){

            $config_roles  =  config_roles::select('role_id')->whereIn('group_type',[1,2])->get()->pluck('role_id');
            if(count($config_roles) > 0){
                      RoleUser::where('user_id', $user->id)->delete();
                foreach($config_roles as $role){
                        $requestData            = [];
                        $requestData['user_id'] = $user->id;
                        $requestData['role_id'] =  $role;
                        RoleUser::create($requestData);
                }

            }

            $redirect_uri     = $request->get('redirect_uri'); //URL ที่จะให้ไปไซต์อื่นหลัง login
            $loged_url        = !empty($redirect_uri) && filter_var($redirect_uri, FILTER_VALIDATE_URL) ? '?redirect_uri=' . $redirect_uri : '';
            $loged_url_base64 = !empty($redirect_uri) && filter_var($redirect_uri, FILTER_VALIDATE_URL) ? '/'.base64_encode($redirect_uri) : '' ;

            //  end insert สิทธิ์ กต และ สก site center
            //    'link'   =>   !empty($user->id)  ?      url('/activated-mail/'.base64_encode($user->id))    : url('')
            // 'name'   =>  !empty($user->contact_prefix_text) &&  !empty($user->contact_first_name) &&   !empty($user->contact_last_name)  ? $user->contact_prefix_text.$user->contact_first_name.' '.$user->contact_last_name : '-',
                if($user->applicanttype_id == 2){ //บุคคลธรรมดา
                     $name =   'คุณ'.@$user->contact_first_name.' '.@$user->contact_last_name;
                }else{
                     $name =   !empty($user->name)  ?  $user->name  : '-';
                }

            if($fromIIndustry){
                // มาจาก i-industry ซึ่งยืนยันตัวตนผ่าน API มาแล้ว ข้ามการส่งอีเมลยืนยันทั้งหมด
                // syncToRosUsers() ทำไปแล้วข้างบนทันทีหลัง create() สำเร็จ ไม่ต้องบังคับ
                // ผ่านหน้า /profile/show อีกแล้ว (forceProfileCheck=false ค่า default) -
                // popup กรอกข้อมูลผู้ติดต่อจะโผล่เฉพาะกรณีข้อมูลไม่ครบจริงๆ เท่านั้น
                // login + ตั้ง cookie session_id เสมอ (ฟังก์ชันนี้ไม่คืนค่า null อีกแล้ว)
                return HP::loginAndRedirectByAppName($user, $regAppName);
            }else{
                $mail = new RegisterMail(['email'      => 'e-Accreditation@tisi.mail.go.th' ?? '-',
                                           'name'      => $name,
                                           'check_api' => !empty($user->check_api) ? 1 : 0,
                                           'link'      => !empty($user->id) ? url('/activated-mail/'.base64_encode($user->id).$loged_url_base64) : url('')
                                        ]);

                $mail_sent = true;
                if($user->email){
                    try {
                        Mail::to($user->email)->send($mail);
                    } catch (\Exception $e) {
                        $mail_sent = false;
                        Log::error('[register] send RegisterMail failed: '.$e->getMessage(), ['user_id' => $user->id, 'email' => $user->email]);
                    }
                }

                $flash_message = $mail_sent
                    ? 'บันทึกสำเร็จ กรุณายืนยันตัวตน ที่อีเมลที่ท่านลงเบียนไว้'
                    : 'บันทึกสำเร็จ แต่ระบบไม่สามารถส่งอีเมลยืนยันตัวตนได้ในขณะนี้ กรุณาติดต่อเจ้าหน้าที่';
            }

            return redirect('/login'.$loged_url)->with('flash_message', $flash_message);
        } else {
            return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ']);
        }

    }

        // สำหรับเพิ่มรูปไปที่ store
        public function storeFile($files, $tax_number)
        {

            if ($files) {
                $attach_path        =  $this->attach_path.$tax_number;
                $filename           =  HP::ConvertCertifyFileName(@$files->getClientOriginalName());
                $fullFileName       =  str_random(10).'-date_time'.date('Ymd_hms') . '.' . $files->getClientOriginalExtension();

                $storagePath        = Storage::putFileAs($attach_path, $files,  str_replace(" ","",$fullFileName) );
                $file_name          = basename($storagePath); // Extract the filename
                $corporatefile[]    = array('realfile' => $file_name, 'filename' => $filename);
                return   json_encode($corporatefile, JSON_UNESCAPED_UNICODE);
            }else{
                return null;
            }
        }


    public function PregReplace($request)
    {
        return preg_replace("/[^a-z\d]/i", '', $request);
    }

    //ตัดเอาเฉพาะตัวเลขเท่านั้น
    private function CutNumberOnly($input){
        return preg_replace("/[^0-9]/", '', $input);
    }

    public function check_tax_number(Request $req)
    {
         $response = [];
        //  ->where('applicanttype_id', $req->applicanttype_id)
         $user = User::where('tax_number', $req->tax_id)->where('branch_type', '!=', 2)->first();
         if(!is_null($user) &&   !in_array($req->applicanttype_id,[1]) ){
                    $response['check'] = true;
                    $response['branch_code'] = false;
                    $response['applicant_type'] = $user->ApplicantTypeTitle ?? 'คณะบุคคล';
         }else  if(!is_null($user) &&  in_array($req->applicanttype_id,[1]) ){
             if($req->branch_type == 2 ){
                   $branch_type = User::where('tax_number', $req->tax_id)->where('branch_type',2)->where('branch_code',$req->branch_code)->first();
                 if(!is_null($branch_type)){
                    $response['check'] = true;
                    $response['branch_code'] = true;
                    $response['name'] = $user->name ?? '';
                 }else{
                    $response['check'] = false;
                    $response['branch_code'] = false;
                 }

             }else{
                $response['check'] = true;
                $response['branch_code'] = false;
             }

         }
         else{
            $response['check'] = false;
            $response['branch_code'] = false;
         }

         $email = User::where('email', $req->email)->first();
         if(!is_null($email)){
            $response['email'] = true;
         }else{
            $response['email'] = false;
         }
        
        if($req->applicanttype_id=='2' && $req->check_api=='1'){ //บุคคลธรรมดาและเช็คเลขมาจาก API

            $person = HP::getPersonal($req->tax_id, $req->ip());

            if($person->status=='success'){ //ได้ข้อมูล

                $date_of_birth = HP::convertDate($req->date_of_birth);  //วันเกิดบุคคลธรรมดา

                $births               = str_split($person->dateOfBirth, 2);
                $births[2]            = $births[2] == '00' ? '01' : $births[2]; //เดือน 00
                $births[3]            = $births[3] == '00' ? '01' : $births[3]; //วันที่ 00
                $person_date_of_birth = (($births[0] . $births[1]) - 543) . '-' . $births[2] . '-' . $births[3];

                if($date_of_birth == $person_date_of_birth){ //วันเกิดตรง
                    $response['date_of_birth_check']   = true;
                    $response['date_of_birth_encrypt'] = Crypt::encrypt($person_date_of_birth);
                }else{ //วันเกิดไม่ตรง
                    $response['date_of_birth_check'] = false;
                }
            }else{
                $response['date_of_birth_check'] = 'no-connect';
            }
        }

        //$response = ['check' => false, 'email' => false, 'branch_code' => false];
        return response()->json($response);
    }

    public function get_tax_number(Request $req)
    {
         $response = [];
         $user = User::where('tax_number', $req->tax_id)->first();
         if(!is_null($user)){
            $response['check'] = true;
         }else{
            $response['check'] = false;
         }

        $person = $this->getPerson($req->tax_id, $req->ip);
        if(is_null($person)){//ไม่พบข้อมูลในทะเบียนราษฎร์
            // $response['person'] =  'ขออภัยเลขประจำตัวประชาชน '. $req->tax_id . ' ไม่พบในทะเบียนราษฎร์กรุณาติดต่อเจ้าหน้าที่';
            $response['person'] =  'not-found';
        }elseif($person=='no-connect'){
            $response['person'] = $person;
        }elseif($person->statusOfPersonCode == '1'){//เสียชีวิต
            $response['person'] =  'เลขประจำตัวประชาชน '. $req->tax_id . ' ไม่สามารถลงทะเบียนได้ เนื่องจากมีสถานะเป็น:&nbsp;<u>เสียชีวิต</u>';
        }else{
            $response['person'] =  true;
        }

        return response()->json($response);
    }

    private function getPerson($tax_id, $ip){

        $person = null;

        if(HP::check_number_counter($tax_id)===false){//รูปแบบข้อมูลไม่ใช่ตัวเลข 13 หลัก
            return $person;
        }

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
        if($i <= 3){//ลองส่งใหม่ 3 ครั้ง
            try {

                $request_start = date('Y-m-d H:i:s');
                $api = null;

                $json_data = file_get_contents($url, false, $context);
                $api = json_decode($json_data);

                if(!empty($api->firstName)){ //พบข้อมูล
                    $person = $api;
                }elseif(is_object($api) && property_exists($api, 'Message') && trim($api->Message)=='CitizenID is not specify'){ //รูปแบบเลขประชาชนไม่ถูกต้อง (ไม่พบข้อมูล)
                    $person = null;
                }elseif(is_object($api) && property_exists($api, 'Code') && trim($api->Code)=='00404'){ //ไม่พบข้อมูล
                    $person = null;
                }else{ //อื่นๆ เชื่อมไปเอาข้อมูลมาไม่ได้
                    $person = 'no-connect';
                }

            } catch (\Exception $e) {
                $i++;

                if ($i <= 3) {
                    //บันทึก Log
                    MOILog::Add($tax_id, $url, 'person', $request_start, @$http_response_header, $api);
                }

                goto start;
            }
        }else{//ถ้าเชื่อมต่อไม่ได้
            $person = 'no-connect';
        }

        //บันทึก Log
        MOILog::Add($tax_id, $url, 'person', $request_start, @$http_response_header, (!is_object($person) ? $api : null));

        return $person;
    }

    // เช็คอีเมล
    public function check_email(Request $req)
    {
         $response = [];
        $user = User::where('email', $req->email)->first();
        if(!is_null($user)){
           $response['check'] = true;
           $response['status'] = 'กรุณากรอกใหม่ เนื่องจาก e-Mail นี้ได้ลงทะเบียนในระบบบริการอิเล็กทรอนิกส์ สมอ.';
        }else{
           $response['check'] = false;
        }
        if(filter_var($req->email, FILTER_VALIDATE_EMAIL) ){
            $response['check_email'] = true;
         }else{
            $response['status_email'] = 'กรุณากรอกใหม่ เนื่องจากรูปแบบ e-Mail ไม่ถูกต้อง :&nbsp;<u>'. $req->email . '</u>';
            $response['check_email'] = false;
         }



        return $response;
    }

    // เช็คเลข 13 หลัก
    public function get_taxid(Request $req)
    {
        $config = HP::getConfig();
        $faculty_title_allows = explode(',', $config->faculty_title_allow);
        $requestId = $this->normalizeRegisterRequestId($req->input('request_id'));

         $response = [];
         $user = User::where('tax_number', $req->tax_id)->where('branch_type', '!=', 2)->first();
         if(!is_null($user)){
            $response['check'] = true;
            $response['applicant_type'] = $user->ApplicantTypeTitle ?? 'นิติบุคคล';
         }else{
            $response['check'] = false;
         }

if(!empty($req->tax_id) && strlen($req->tax_id) == 13){
    $entity = $this->CheckLegalEntity($req->tax_id, $requestId); // นิติบุคคล
    if(!in_array($entity,[1, 2, 3, 'no-connect', 'not-found'])){
        $response['status']            = 'หมายเลข  '. $req->tax_id . ' เป็นนิติบุคคล ไม่สามารถลงทะเบียนได้ เนื่องจากมีสถานะเป็น:&nbsp;<u>'.$entity.'</u>';
        $response['check_api']         = true;
        $response['type']              = 1;
    }else if(in_array($entity,[1,2,3])){
        $response['status']            = 'หมายเลข ' . $req->tax_id .' เป็นนิติบุคคล ท่านต้องการลงทะเบียนประเภทนิติบุคคลหรือไม่';
        $response['check_api']         = true;
        $response['type']              = 1;
    }else{
        $person = $this->getPerson($req->tax_id, $req->ip);  // บุคคลธรรมดา
       if(is_null($person) || $person=='no-connect'){//ไม่พบข้อมูลในทะเบียนราษฎร์ หรือเชื่อมไม่ได้
                   $faculty = self::getFaculty($req->tax_id);
               // if($faculty == 'คณะบุคคล' || $faculty == 'สหกรณ์'){
                if(in_array($faculty, $faculty_title_allows)){//เป็นคณะบุคคล
                   $response['status']         =  'หมายเลข ' . $req->tax_id .' เป็นคณะบุคคล  ท่านต้องการลงทะเบียนประเภทคณะบุคคลหรือไม่';
                   $response['check_api']      = true;
                   $response['type']           = 3;
               }else{
                   $response['status']         = false;
                   $response['check_api']      = false;
                   $response['type']           = 3;
               }
        }elseif($person->statusOfPersonCode == '1'){//เสียชีวิต
               $response['status']         =  'หมายเลข  '. $req->tax_id . ' เป็นเลขประจำตัวประชาชน ไม่สามารถลงทะเบียนได้ เนื่องจากมีสถานะเป็น:&nbsp;<u>เสียชีวิต</u>';
               $response['check_api']      = true;
               $response['type']           = 2;
               $response['person']         = 1;
        }else{
               $response['status']         =  'หมายเลข  '. $req->tax_id . ' เป็นบุคคลธรรมดา ท่านต้องการลงทะเบียนประเภทบุคคลธรรมดาหรือไม่';
               $response['check_api']      = true;
               $response['type']           = 2;
               $response['person']         = 0;
        }
    }
}else{
    $response['check_api']      = false;
}

        $response['request_id'] = $requestId;
        return response()->json($response);
    }




    public function get_legal_entity(Request $req)
    {
        $response = [];
        $user = User::where('tax_number', $req->tax_id)->where('branch_type', '!=', 2)->first();
        if(!is_null($user)){
            $response['check'] = true;
            $response['status'] = 'เลขนิติบุคคล ' . $req->tax_id .' มีการลงทะเบียนในระบบแล้ว:&nbsp;<u>'.($user->name ?? '').'</u>';
        }else{
            $response['check'] = false;
        }

        $requestId = $this->normalizeRegisterRequestId($req->input('request_id'));
        $entity = $this->CheckLegalEntity($req->tax_id, $requestId);
        $response['juristic_status'] = $entity;//true=สถานะปกติ, false=เลิกกิจการ, 'not-found'=ไม่พบใน DBD, 'no-connect'=ไม่สามารถเชื่อมต่อได้
        $response['request_id'] = $requestId;

        if ($entity === 'no-connect') {
            return response()->json([
                'check' => $response['check'] ?? false,
                'connection' => false,
                'error_code' => 'EXTERNAL_SERVICE_UNAVAILABLE',
                'message' => 'ไม่สามารถเชื่อมต่อข้อมูล DBD ได้ในขณะนี้',
                'request_id' => $requestId,
            ], 503);
        }

        return response()->json($response);
    }


    private function normalizeRegisterRequestId($requestId = null)
    {
        $requestId = is_string($requestId) ? trim($requestId) : '';

        return preg_match('/^[A-Za-z0-9_-]{16,80}$/', $requestId)
            ? $requestId
            : bin2hex(random_bytes(16));
    }

    private function cacheDbdRegistrationResponse($taxNumber, $requestId, $api)
    {
        if (empty($requestId) || !is_object($api)) {
            return;
        }

        $cache = session()->get('register_dbd_cache', []);
        $now = time();
        foreach ($cache as $key => $entry) {
            if (!is_array($entry) || ($now - (int)($entry['created_at'] ?? 0)) > 600) {
                unset($cache[$key]);
            }
        }

        $cache[$requestId] = [
            'tax_number' => (string)$taxNumber,
            'created_at' => $now,
            'api' => $api,
        ];
        session()->put('register_dbd_cache', $cache);
    }

    private function getCachedDbdRegistrationResponse($taxNumber, $requestId)
    {
        if (empty($requestId)) {
            return null;
        }

        $cache = session()->get('register_dbd_cache', []);
        $entry = $cache[$requestId] ?? null;
        if (!is_array($entry)
            || (string)($entry['tax_number'] ?? '') !== (string)$taxNumber
            || (time() - (int)($entry['created_at'] ?? 0)) > 600) {
            return null;
        }

        $api = $entry['api'] ?? null;
        if (is_array($api)) {
            $api = (object)$api;
        }
        if (!is_object($api)) {
            return null;
        }

        $api->status = 'success';
        return $api;
    }

    public function CheckLegalEntity($tax_number, $requestId = null)
    {

        $response = $result = 'not-found';

        if(HP::check_number_counter($tax_number)===false){//รูปแบบข้อมูลไม่ใช่ตัวเลข 13 หลัก
            return $response;
        }

        $config = HP::getConfig();
        $url = $config->tisi_api_corporation_url; //'https://www3.tisi.go.th/moiapi/srv.asp?pid=1';
        $data = array(
                'val' => $tax_number,
                'IP' => $_SERVER['REMOTE_ADDR'],    // IP Address,
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
                $api = null ;

                $json_data = file_get_contents($url, false, $context);
                $api = json_decode($json_data);
                if(!empty($api->JuristicName_TH)){
                    $juristic_status = ['ยังดำเนินกิจการอยู่' => '1', 'ฟื้นฟู' => '2', 'คืนสู่ทะเบียน' => '3'];
                    $status   = array_key_exists($api->JuristicStatus,$juristic_status) ? $juristic_status[$api->JuristicStatus] : $api->JuristicStatus ;  //สถานะนิติบุคคล
                    $response = $status;
                    $result   = 'success';
                    $this->cacheDbdRegistrationResponse($tax_number, $requestId, $api);
                }elseif(is_object($api) && property_exists($api, 'result') && trim($api->result)=='Bad Request'){//ไม่พบข้อมูล

                }else{//บริการปลายทางมีปัญหา
                    $response = $result = 'no-connect';
                }

            } catch (\Throwable $e) {
                $i++;

                if($i <= 3){
                    //บันทึก Log
                    MOILog::Add($tax_number, $url, 'corporation', $request_start, @$http_response_header, ($result!='success' ? $api : null), $requestId);
                }

                goto start;
            }
        }else{
            $response = $result = 'no-connect';
        }

        //บันทึก Log
        MOILog::Add($tax_number, $url, 'corporation', $request_start, @$http_response_header, ($result!='success' ? $api : null), $requestId);

        return $response;//[เป็นตัวเลข]=สถานะปกติ, [สถานะอื่น]=เลิกกิจการ, 'not-found'=ไม่พบข้อมูลในกรมพัฒนาธุรกิจการค้า, 'no-connect'=ไม่สามารถเชื่อมต่อได้

    }


    public function get_legal_faculty(Request $req)
    {
         $response = [];
         $user = User::where('tax_number', $req->tax_id)->where('branch_type', '!=', 2)->first();
         if(!is_null($user)){
            $response['check'] = true;
            $response['applicant_type'] = $user->ApplicantTypeTitle ?? 'คณะบุคคล';
         }else{
            $response['check'] = false;
            $response['applicant_type'] = false;
         }

        $faculty = self::getFaculty($req->tax_id);
        $response['branch_title'] = $faculty;

        return response()->json($response);
    }

    public function getFaculty($tax_number)
    {

        $response = 'not-found';
        $result   = null ;

        if(HP::check_number_counter($tax_number)===false){//รูปแบบข้อมูลไม่ใช่ตัวเลข 13 หลัก
            return $response;
        }

        $config = HP::getConfig();
        $url = $config->tisi_api_faculty_url; //'https://www3.tisi.go.th/moiapi/srv.asp?pid=5';
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
        $context = stream_context_create($options);

        $i = 1;
        start:
        if($i <= 3){//ลองส่งใหม่ 3 ครั้ง
            try {
                $request_start = date('Y-m-d H:i:s');
                $api = null;

                $json_data = file_get_contents($url, false, $context);
                $api = json_decode($json_data);
                if(!empty($api->vBranchTitleName)){
                    $response = $api->vBranchTitleName;
                    $result   = 'success';
                }elseif(!empty($api->Message) && $api->Message=='Response Failed'){
                    $response = 'no-connect';
                }
            } catch (\Exception $e) {
                $i ++;
                goto start;
            }
        }else{//ถ้าเชื่อมต่อไม่ได้
            $response = 'no-connect';

            if ($i <= 3) {
                //บันทึก Log
                MOILog::Add($tax_number, $url, 'rd', $request_start, @$http_response_header, ($result != 'success' ? $api : null));
            }

        }

        //บันทึก Log
        MOILog::Add($tax_number, $url, 'rd', $request_start, @$http_response_header, ($result != 'success' ? $api : null));

        return $response;

    }

    public function ActivatedMail($code, $redirect_uri=''){
        

        $redirect_uri = !empty($redirect_uri) ? base64_decode($redirect_uri) : '' ; //URL ที่จะให้ไปไซต์อื่นหลัง login
        $loged_url    = !empty($redirect_uri) && filter_var($redirect_uri, FILTER_VALIDATE_URL) ? '?redirect_uri=' . $redirect_uri : '';
        
        $user = User::where('id', base64_decode($code))->first();
        if(!is_null($user) && $user->state  == 1 ){

            //if(in_array($user->applicanttype_id,[4,5]) && $user->check_api != 1){
            if($user->check_api != 1){//ถ้าเป็นการสมัครโดยข้อมูลกรอกเอง ให้เจ้าหน้าที่อนุมัติก่อน
                $user->block = 1;
                $user->state = 3; // เจ้าหน้าที่มายื่นยัน
                $user->save();
                // $config = HP::getConfig();
                // if(!empty($config->url_center) && !empty($config->mail_center)){
                //         $mail = new Authorities([
                //             'name'   =>   !empty($user->name)  ?   $user->name  : '',
                //             'link'   =>   !empty($user->tax_number)  ?  $config->url_center.'sso/user-sso?perPage=10&search='.$user->tax_number   : url('')
                //         ]);
                //         if($user->email){
                //             Mail::to($user->email)->send($mail);
                //         }
                // }

                return redirect('/login'.$loged_url)->with('flash_message', 'กรุณารอเจ้าหน้าที่มายืนยันการลงทะเบียน!'  );
            }else{
                $user->block = 0;
                $user->state = 2; // ยืนยันตัวตนแล้ว
                $user->save();
                return redirect('/login'.$loged_url)->with('flash_message', 'ท่านได้ยืนยันตัวตนในอีเมลแล้ว');
            }
        }else{
            return redirect('/login'.$loged_url);
        }
    }


    /**
     * ดึงที่อยู่ตามทะเบียนบ้านซ้ำโดยไม่ต้องดึงข้อมูลบุคคลใหม่
     */
    public function get_house_address(Request $req)
    {
        return response()->json($this->houseAddressResponse(
            $req->tax_id,
            $req->ip(),
            $this->shouldSimulateHouseAddressFailure($req)
        ));
    }

    protected function shouldSimulateHouseAddressFailure(Request $req)
    {
        $requested = in_array(strtolower((string)$req->input('simulate_house_address_failure')), ['1', 'true', 'yes'], true);
        $productionHosts = ['sso.tisi.go.th', 'www.sso.tisi.go.th'];

        // เปิดได้เฉพาะ host ที่ไม่ใช่ production จริง แม้ dev server จะใช้ APP_ENV=production
        return $requested && !in_array(strtolower($req->getHost()), $productionHosts, true);
    }

    protected function houseAddressResponse($taxId, $ip, $simulateFailure = false)
    {
        $houseAddress = $simulateFailure ? [
            'address'  => '', 'building' => '', 'moo'      => '', 'soi'      => '',
            'road'     => '', 'tumbol'   => '', 'ampur'    => '', 'province' => '',
            'zipcode'  => '',
        ] : HP::getHouseAddress($taxId, $ip);
        $addressIds = HP::GetIDAddress(
            $houseAddress['tumbol'] ?? null,
            $houseAddress['ampur'] ?? null,
            $houseAddress['province'] ?? null
        );

        $complete = !empty($houseAddress['address'])
            && !empty($houseAddress['tumbol'])
            && !empty($houseAddress['ampur'])
            && !empty($houseAddress['province'])
            && !empty($addressIds->province_id)
            && !empty($addressIds->district_id)
            && !empty($addressIds->subdistrict_id)
            && !empty($addressIds->zipcode);

        if ($complete) {
            $houseAddress['zipcode'] = $addressIds->zipcode;
        }

        return array_merge($houseAddress, [
            'house_address_status' => $complete ? 'success' : 'unavailable',
            'house_address_error' => $complete ? null : ($simulateFailure ? 'HOUSE_ADDRESS_SIMULATED' : 'HOUSE_ADDRESS_UNAVAILABLE'),
            'house_address_simulated' => $simulateFailure,
            'province_id' => $complete ? $addressIds->province_id : null,
            'district_id' => $complete ? $addressIds->district_id : null,
            'subdistrict_id' => $complete ? $addressIds->subdistrict_id : null,
        ]);
    }

    public function datatype(Request $req)
    {

        $response = [];

        if(HP::check_number_counter($req->tax_id, 13)===false && HP::check_number_counter($req->tax_id, 14)===false){//รูปแบบข้อมูลไม่ใช่ตัวเลข 13 หลัก และไม่ใช่ 14 หลัก
            return $response;
        }

        $config = HP::getConfig();

        if($req->applicanttype_id == 1){ // การดึงข้อมูลนิติบุคคลจาก DBD ด้วยเลขนิติบุคคล 13 หลัก 0105553080958

            $api = $this->getCachedDbdRegistrationResponse(
                $req->tax_id,
                $req->input('request_id')
            );
            if (is_null($api)) {
                $api = HP::getJuristic($req->tax_id, $req->ip, $req->input('request_id'));
            }

            if (!is_object($api) || (($api->status ?? null) === 'no-connect')) {
                return response()->json([
                    'connection' => false,
                    'error_code' => 'EXTERNAL_SERVICE_UNAVAILABLE',
                    'message' => 'ไม่สามารถเชื่อมต่อข้อมูล DBD ได้ในขณะนี้',
                    'request_id' => $req->input('request_id'),
                ], 503);
            }

            $data_prefix     = ['บริษัทจำกัด' => '1', 'บริษัทมหาชนจำกัด' => '2', 'ห้างหุ้นส่วนจำกัด' => '3', 'ห้างหุ้นส่วนสามัญนิติบุคคล' => '4'];
            $juristic_status = ['ยังดำเนินกิจการอยู่' => '1', 'ฟื้นฟู' => '2', 'คืนสู่ทะเบียน' => '3'];
            if(!empty($api->JuristicName_TH)){ // Start การดึงข้อมูลนิติบุคคลจาก DBD ด้วยเลขนิติบุคคล 13 หลัก
                $response['applicanttype_id']  = 1;       // ประเภทผู้ประกอบการ
                $response['JuristicType']      =  $api->JuristicType ;
                $response['prefix_id']         =  array_key_exists($api->JuristicType,$data_prefix) ? $data_prefix[$api->JuristicType] : ''  ;        // คำนำหน้า
                $response['juristic_status']   =  array_key_exists($api->JuristicStatus,$juristic_status) ? $juristic_status[$api->JuristicStatus] : $api->JuristicStatus ;  //สถานะนิติบุคคล
                $response['tax_id']            = $api->JuristicID ?? '';        // Username สำหรับเข้าใช้งาน
                $response['name'] = $api->JuristicName_TH ?? '';

                // ตรวจสอบว่ามีคำนำหน้าอยู่ในชื่อจาก DBD แล้วหรือยัง ก่อนเติม ป้องกันชื่อซ้ำ เอา Function พี่แบงค์มาใช้ (ทิวแก้ไข 10/7/2569)
                if(in_array($api->JuristicType,['บริษัทจำกัด','บริษัทมหาชนจำกัด'])){
                    if(mb_strpos($response['name'], 'บริษัท') === false){
                        $response['name'] = 'บริษัท '.$response['name'];
                    }
                }else if(in_array($api->JuristicType,['ห้างหุ้นส่วนจำกัด'])){
                    if(mb_strpos($response['name'], 'ห้างหุ้นส่วน') === false){
                        $response['name'] = 'ห้างหุ้นส่วนจำกัด '.$response['name'];
                    }
                }

                $response['name'] = HP::replace_multi_space($response['name']);

                $response['name_last']         = '';
                $response['RegisterDate']      = !empty($api->RegisterDate) ? substr($api->RegisterDate,6) .'/'.substr($api->RegisterDate,4,-2).'/'.substr($api->RegisterDate,0,4) : '';

                if(!empty($api->CommitteeInformations)){  // ข้อมูลคณะกรรมการ

                    $prefixs                            = Prefix::pluck('id', 'initial');
                    $prefixs = (array) $prefixs;        //Kantapon 1/10/2568
                    $informations                       =  min($api->CommitteeInformations);
                    $response['first_name']             =  $informations->FirstName ?? ''; // ชื่อ
                    $response['last_name']              =  $informations->LastName ?? ''; // สกุล
                    if($informations->Title == 'น.ส.'){
                        $response['contact_prefix_name']    =   '3'; // คำนำหน้า
                    }else{
                        $response['contact_prefix_name']    =  array_key_exists($informations->Title,$prefixs) ? $prefixs[$informations->Title] : ''; // คำนำหน้า
                    }

                }else{
                    $response['first_name']             =  ''; // ชื่อ
                    $response['last_name']              =  ''; // สกุล
                    $response['contact_prefix_name']    =  ''; // คำนำหน้า
                }

                if( count($api->AddressInformations) > 0){  // in_array($api->JuristicType,['บริษัทจำกัด']) &&
                    // $address = max($api->AddressInformations);
                    $address = $api->AddressInformations[0];
                    $ampur_temp = $address->Ampur;
                    $address = HP::format_address_company_api($address);

                    //$response['address']            =  HP::replace_address($address->FullAddress, $address->Moo, $address->Soi, $address->Road)   ; // ที่อยู่
                    $response['address']            =  $address->AddressNo; // ที่อยู่
                    $response['building']           =  $address->Building ?? ''; //  อาคาร
                    // $response['building']           =  ''; //  อาคาร
                    $response['moo']                =  $address->Moo ?? ''; //  หมู่
                    $response['soi']                =  $address->Soi ?? ''; // ซอย
                    $response['road']               =  $address->Road ?? ''; //  ถนน
                    $response['ampur']              =  $address->Ampur ?? ''; // แขวง/อำเภอ
                    $response['tumbol']             =  $address->Tumbol ?? ''; //  ตำบล/แขวง
                    $response['province']           =  $address->Province ?? ''; // จังหวัด

                    $zipcode  = HP::getZipcode($address->Tumbol, $ampur_temp, $address->Province);
                    if(!empty($zipcode)){
                        $response['zipcode']            = $zipcode ?? ''; // รหัสไปรษณีย์
                    }else{
                        $response['zipcode']            =  ''; // รหัสไปรษณีย์
                    }

                    $response['phone']              =  $address->Phone ?? ''; // โทรศัพท์
                    $response['email']              =  $address->Email ?? ''; // อีเมล
                    $response['country_code']       =  '';  // รหัสประเทศ

                }else{
                    $response['address']            =  ''; // ที่อยู่
                    $response['building']           =  ''; // อาคาร
                    $response['moo']                =  ''; // หมู่
                    $response['soi']                =  ''; // ซอย
                    $response['road']               =  ''; // ถนน
                    $response['tumbol']             =  ''; // ตำบล/แขวง
                    $response['ampur']              =  ''; // แขวง/อำเภอ
                    $response['province']           =  ''; // จังหวัด
                    $response['zipcode']            =  ''; // รหัสไปรษณีย์
                    $response['phone']              =  ''; // โทรศัพท์
                    $response['email']              =  ''; // อีเมล
                    $response['country_code']       =  ''; // รหัสประเทศ
                }
            }elseif(is_object($api) && property_exists($api, 'result') && trim($api->result)=='Bad Request'){//ไม่พบข้อมูล

            }else{//บริการปลายทางมีปัญหา
                $response['connection'] = false ;
            }
            no_connect_corporation:

        }else if(in_array($req->applicanttype_id, [2, 4, 5])){

            //$response['connection'] = false; ปิดไปเมื่อ 24/10/2568 โดย  Kantapon
            //goto end;                        ปิดไปเมื่อ 24/10/2568 โดย  Kantapon
            
            $api = HP::getPersonal($req->tax_id, $req->ip);

            if(!empty($api->firstName)){
                $prefixs                       = Prefix::pluck('id', 'initial')->toArray();
                $response['applicanttype_id']  = 2; // ประเภทผู้ประกอบการ
                $response['JuristicType']      = $api->titleName ;
                $response['nationality']       = $api->nationalityDesc  ?? '';
                $response['prefix_id']         = array_key_exists($api->titleDesc,$prefixs) ? $prefixs[$api->titleDesc] : ''; // คำนำหน้า
                $response['juristic_status']   = '';
                $response['tax_id']            = $api->JuristicID ?? '';        // Username สำหรับเข้าใช้งาน
                $response['name']              = $api->firstName ?? '';
                $response['name_last']         = $api->lastName ?? '';

                //วันเกิด
                $births                        = str_split($api->dateOfBirth, 2);
                $births[2]                     = $births[2]=='00' ? '01' : $births[2];//เดือน 00
                $births[3]                     = $births[3]=='00' ? '01' : $births[3];//วันที่ 00
                $response['RegisterDate']      = $births[3].'/'.$births[2].'/'.($births[0].$births[1]);
            }elseif(is_object($api) && ((property_exists($api, 'Message') && trim($api->Message)=='CitizenID is not specify') || (property_exists($api, 'Code') && trim($api->Code)=='00404'))){ //รูปแบบเลขประชาชนไม่ถูกต้อง และ ไม่พบข้อมูล
                $response['applicanttype_id'] = 2;       // ประเภทผู้ประกอบการ
                $response['JuristicType']     = '';
                $response['nationality']      = '';
                $response['prefix_id']        = '';        // คำนำหน้า
                $response['juristic_status']  = '';
                $response['tax_id']           = '';        // Username สำหรับเข้าใช้งาน
                $response['name']             = '';
                $response['name_last']        = '';
                $response['RegisterDate']     = '';
            }else{//อื่นๆ เชื่อมไปเอาข้อมูลมาไม่ได้
                no_connect_person:
                $response['connection'] = false;
            }

            // ดึงที่อยู่ตามทะเบียนบ้าน - ย้าย logic ไปเป็น HP::getHouseAddress() แล้ว (เรียกซ้ำได้
            // จากที่อื่น เช่น ProfileController::show() สำหรับบัญชีเก่าที่ข้อมูลไม่ครบ)
            $houseAddress = $this->houseAddressResponse(
                $req->tax_id,
                $req->ip(),
                $this->shouldSimulateHouseAddressFailure($req)
            );
            $response = array_merge($response, $houseAddress, [
                'phone'        => '', // โทรศัพท์
                'email'        => '', // อีเมล
                'country_code' => '', // รหัสประเทศ
            ]);

        }else if($req->applicanttype_id == 3){

            $api = HP::getRdVat($req->tax_id, $req->ip);

            if(!empty($api->vName)){
                $response['juristic_status']   = '';
                $response['applicanttype_id']  = 3;       // ประเภทผู้ประกอบการ
                $response['prefix_id']         = $api->vBranchTitleName; // คำนำหน้า
                $response['tax_id']            = $api->vNID ?? '';        // Username สำหรับเข้าใช้งาน
                if($api->vBranchTitleName == "สหกรณ์"){
                    $response['name']              = 'สหกรณ์'.$api->vBranchName ?? '';
                }else{
                    $response['name']              = $api->vBranchName ?? '';
                }
                $response['name_last']         =  '';
                if(!empty($api->vBusinessFirstDate)){

                    $api->vBusinessFirstDate = str_replace('/', '-', $api->vBusinessFirstDate);
                    $date = explode('-', $api->vBusinessFirstDate);

                    if(count($date)==3){

                        if (strlen($date['0']) === 4) { //แบบ ปี-เดือน-วัน
                            $response['RegisterDate'] = $api->vBusinessFirstDate;
                        } elseif (strlen($date['2']) === 4) { //แบบ วัน-เดือน-ปี หรือ เดือน-วัน-ปี
                            if (in_array($api->vBranchTitleName, ['ห้างหุ้นส่วนสามัญ', 'สหกรณ์', 'มหาวิทยาลัย', 'โรงเรียน', 'กิจการร่วมค้า'])) { //เดือน-วัน-ปี
                                $response['RegisterDate'] = $date[1].'/'.$date[0].'/'.($date[2]+543);
                            } else { //วัน-เดือน-ปี
                                $response['RegisterDate'] = $date[0].'/'.$date[1].'/'.($date[2]+543);
                            }
                        }
                    }else{
                        $response['RegisterDate'] = '';
                    }
                }else{
                    $response['RegisterDate'] = '';
                }

                $response['address']      = $api->vHouseNumber ?? '';  // ที่อยู่
                $response['building']     = ''; // อาคาร
                $response['moo']          = $api->vMooNumber ?? ''; // หมู่
                $response['soi']          = $api->vSoiName ?? ''; // ซอย
                $response['road']         = $api->vStreetName; // ถนน
                $response['tumbol']       = $api->vThambol ?? ''; // ตำบล/แขวง
                $response['ampur']        = $api->vAmphur ?? ''; // แขวง/อำเภอ
                $response['province']     = $api->vProvince ?? ''; // จังหวัด
                $response['zipcode']      = $api->vPostCode ?? ''; // รหัสไปรษณีย์
                $response['phone']        = ''; // โทรศัพท์
                $response['email']        = ''; // อีเมล
                $response['country_code'] = ''; // รหัสประเทศ

                //ตัดคำออก
                list($response['moo'], $response['soi'], $response['road'], $response['tumbol'], $response['ampur']) = $this->replace_prefix($response['moo'], $response['soi'], $response['road'], $response['tumbol'], $response['ampur']);

                //แปลง 0 หรือ - เป็น null
                $response['address'] = HP::FormatToNull($response['address']);
                $response['moo']     = HP::FormatToNull($response['moo']);
                $response['soi']     = HP::FormatToNull($response['soi']);
                $response['road']    = HP::FormatToNull($response['road']);

            }elseif(!empty($api->Message) && $api->Message=='Response Failed'){
                no_connect_faculty:
                $response['connection'] = false;
            }
        }

        end:
        return response()->json($response);
    }

    //ตัดคำออก
    private function replace_prefix($moo, $soi, $road, $tumbol, $ampur){

        $address_moo = trim($moo);
        $moo         = !empty($address_moo) && mb_strpos($address_moo, 'หมู่')===0 ? trim(mb_substr($address_moo, 4)) : $address_moo ; //ตัดคำว่าซอย คำแรกออก

        $address_soi = trim($soi);
        $soi         = !empty($address_soi) && mb_strpos($address_soi, 'ซอย')===0 ? trim(mb_substr($address_soi, 3)) : $address_soi ; //ตัดคำว่าซอย คำแรกออก

        $address_road = trim($road);
        $road         = !empty($address_road) && mb_strpos($address_road, 'ถนน')===0 ? trim(mb_substr($address_road, 3)) : $address_road ; //ตัดคำว่าถนน คำแรกออก

        $address_tumbol = trim($tumbol);
        $tumbol         = !empty($address_tumbol) && (mb_strpos($address_tumbol, 'แขวง')===0 || mb_strpos($address_tumbol, 'ตำบล')===0) ? trim(mb_substr($address_tumbol, 4)) : $address_tumbol ; //ตัดคำว่าตำบล/แขวง คำแรกออก

        $address_ampur = trim($ampur);
        $address_ampur = !empty($address_ampur) && mb_strpos($address_ampur, 'อำเภอ')===0 ? trim(mb_substr($address_ampur, 5)) : $address_ampur ; //ตัดคำว่าอำเภอ คำแรกออก
        $ampur         = !empty($address_ampur) && mb_strpos($address_ampur, 'เขต')===0 ? trim(mb_substr($address_ampur, 3)) : $address_ampur ; //ตัดคำว่าเขต คำแรกออก

        return [$moo, $soi, $road, $tumbol, $ampur];
    }
/*
public function showFill(Request $request)
{
    //
    return view('auth.register_fill', [
        'uid' => $request->uid,
        'jt'  => $request->jt,
        'bid' => $request->bid,
        'progid' => $request->progid
    ]);
}
 */


 public function showFill(\Illuminate\Http\Request $request)
 {
     // single-use: read and remove from session
     $payload = session()->pull('prereg', []);

     return view('auth.register_fill', [
         'prereg'   => $payload,
         'uid'      => $payload['uid']      ?? null,
         'bid'      => $payload['bid']      ?? null,
         'jt'       => $payload['jt']       ?? null,
         'app_name' => $payload['app_name'] ?? null,
     ]);
 }
 
 
}
