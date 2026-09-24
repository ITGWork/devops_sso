<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Models\WS\Log;
use App\Models\Setting\SettingSystem;
use App\Sessions;
use App\RoleUser;
use App\Role;
use App\Models\Officer\Sessions as OfficerSession;
use App\Models\Officer\User as OfficerUser;
use App\User;
use HP_API;
use HP;

class UserController extends Controller
{

    //API 001 Auth
    public function Auth(Request $request){

        //เช็คข้อมูล app_name, app_secret และสิทธิ์การใช้ API
        $header = $request->header();

        $check_header = HP_API::check_client($header, __FUNCTION__);

        $app_name = array_key_exists('app-name', $header) ? $header['app-name'][0] : null ;

        if($check_header['status']===false){//ข้อมูลไม่ถูกต้องหรือไม่มีสิทธิ์
            Log::Add($app_name, __FUNCTION__, $check_header['code'], $check_header['msg']);
            return response()->json(['status' => $check_header['code'], 'message' => $check_header['msg']]);
        }

        $input = $request->only(
                                'session_id',
                                'user_agent'
                                );
        $rule = [
                  'session_id' => 'required|string',
                  'user_agent' => 'required|string'
                ];

        $validator = Validator::make($input, $rule);

        if ($validator->fails()) {

            $error = $validator->messages();
            Log::Add($app_name, __FUNCTION__, '200', $error->toJson());
            return response()->json(['status'=> '200', 'message'=> $error]);

        }

        //ลบค่า session ที่หมดอายุแล้ว
        $minute = config('session.lifetime');
        $date_expire = date("Y-m-d H:i:s", strtotime("-$minute minute", strtotime(date("Y-m-d H:i:s"))));
        Sessions::where('last_visit_at', '<', $date_expire)->delete();

        //หาค่า session
        $session = Sessions::where('id', $input['session_id'])->where('user_agent', $input['user_agent'])->first();

        if(is_null($session)){
            Log::Add($app_name, __FUNCTION__, '500', 'Session Id or User Agent incorrect.');
            return response()->json(['status'=> '500', 'message'=> 'Session Id or User Agent incorrect.']);
        }

        //อัพเดทเวลาเข้าใช้งาน
        Sessions::Modify($session->id, $app_name);

        //ดึงข้อมูลผู้ใช้งาน
        $user_result = User::where('id', $session->user_id)->first();

        if(!is_null($user_result)){ //ถ้าพบข้อมูล

            if(!is_null($session->act_instead)){ //ใช้ระบบในฐานะผู้รับมอบอำนาจ จะใช้ระบบได้ตามที่ได้รับมอบอำนาจ

                $act_instead = User::find($session->act_instead);
                if(!is_null($act_instead)){
                    $user = $this->format_user_data($act_instead);//ข้อมูลผู้มอบอำนาจให้เอามาใส่ส่วนหลัก
                }else{
                    $user    = null;
                    $status  = '501';
                    $message = 'User login not found.';
                    goto end;
                }

                $user->session_id = $input['session_id']; //session id
                $user->act_instead = $this->format_user_data($user_result);//ผู้ใช้ที่ lgoin อยู่ที่ดำเนินการแทน

                $setting_systems = HP::getAgentSystems($user_result->id, $session->act_instead);
                $setting_systems = $setting_systems->filter(function ($setting_system, $key) {
                                                            return $setting_system->state == 1 && !is_null($setting_system->app_name);
                                                        }); //ไม่เอารายการที่ถูกปิด และไม่กรอก app_name
                $user->app_allow = $setting_systems->pluck('app_name');

            }else{ //ใช้ระบบในฐานะตัวเอง ใช้ระบบได้ทั้งหมด
                $user = $this->format_user_data($user_result); //จัดรูปแบบข้อมูลผู้ใช้งาน
                $user->session_id = $input['session_id']; //session id
                $user->act_instead = (object)[]; //ข้อมูลการได้รับมอบสิทธิ์
                $user->app_allow = SettingSystem::whereNotNull('app_name')
                                                ->where('state', 1)
                                                ->when($user->branch_type==2, function($query){//สาขาให้ใช้ได้ที่ไม่ปิด
                                                    $query->where('branch_block', '!=', 1);
                                                })
                                                ->pluck('app_name');
            }

            $status  = '000';
            $message = 'Found a user login.';

        }else{//ไม่พบข้อมูล

            $user    = null;
            $status  = '501';
            $message = 'User login not found.';

        }

        end:
        Log::Add($app_name, __FUNCTION__, $status, $message);
        return response()->json([
                        'status' => $status,
                        'message'=> $message,
                        'result' => $user
        ]);

    }

    //API 002 Login
    public function Login(Request $request){

        //เช็คข้อมูล app_name, app_secret และสิทธิ์การใช้ API
        $header = $request->header();

        $check_header = HP_API::check_client($header, __FUNCTION__);

        $app_name = array_key_exists('app-name', $header) ? $header['app-name'][0] : null ;

        if($check_header['status']===false){//ข้อมูลไม่ถูกต้องหรือไม่มีสิทธิ์
            Log::Add($app_name, __FUNCTION__, $check_header['code'], $check_header['msg']);
            return response()->json(['status' => $check_header['code'], 'message' => $check_header['msg']]);
        }

        $input = $request->only(
                                'username',
                                'password',
                                'user_agent',
                                'ip_address'
                                );
        $rule = [
                  'username' => 'required|string',
                  'password' => 'required|string',
                  'user_agent' => 'required|string',
                  'ip_address' => 'string|ipv4'
                ];

        $validator = Validator::make($input, $rule);

        if ($validator->fails()) {

            $error = $validator->messages();
            Log::Add($app_name, __FUNCTION__, '200', $error->toJson());
            return response()->json(['status'=> '200', 'message'=> $error]);

        }

        //ดึงข้อมูลผู้ใช้งาน
        $user_result = User::where('username', $input['username'])->first();

        if(is_null($user_result)){//ไม่พบข้อมูลผู้ใช้งาน
            Log::Add($app_name, __FUNCTION__, '503', 'Username not found.');
            return response()->json(['status'=> '503', 'message'=> 'Username not found.']);
        }

        if(!Hash::check($input['password'], $user_result->password)){//รหัสผ่านไม่ถูกต้อง
            Log::Add($app_name, __FUNCTION__, '504', 'Password incorrect.');
            return response()->json(['status'=> '504', 'message'=> 'Password incorrect.']);
        }

        if($user_result->state == 1){ // รอยืนยันตัวตน
            Log::Add($app_name, __FUNCTION__, '505', 'Username has not been verified in email.');
            return response()->json(['status'=> '505', 'message'=> 'Username has not been verified in email.']);
        }else if($user_result->block == 1){ // บล็อกการใช้งาน
            Log::Add($app_name, __FUNCTION__, '506', 'Username suspended.');
            return response()->json(['status'=> '506', 'message'=> 'Username suspended.']);
        }

        //บันทึกลงตาราง session
        $session_id = session()->getId();
        $ip_address = array_key_exists('ip_address', $input) ? $input['ip_address'] : $request->ip() ;
        Sessions::Add($session_id, $user_result->getKey(), $ip_address, $input['user_agent'], 'api', null, $app_name);

        //บันทึกวัน/เวลา Login
        $user_result->lastvisitDate = date('Y-m-d H:i:s');
        $user_result->save();

        //ข้อมูลที่จะตอบกลับ
        $user = $this->format_user_data($user_result);

        //session id
        $user->session_id = $session_id;

        //ข้อมูลการได้รับมอบสิทธิ์
        //$user->act_instead = (object)[];

        //ข้อมูลสิทธิ์การใช้งาน
        $user->app_allow = SettingSystem::whereNotNull('app_name')
                                        ->where('state', 1)
                                        ->when($user->branch_type==2, function($query){//สาขาให้ใช้ได้ที่ไม่ปิด
                                            $query->where('branch_block', '!=', 1);
                                        })
                                        ->pluck('app_name');

        $status  = '000';
        $message = 'Login success.';

        Log::Add($app_name, __FUNCTION__, $status, $message);
        return response()->json([
                        'status'  => $status,
                        'message' => $message,
                        'result'  => $user
        ]);

    }

    private function format_user_data($user_result){

        //ภาพประจำตัว
        $picture = !empty($user_result->picture) ? HP::getFileStorage('sso_users/'.$user_result->picture) : null ;

        //เอกสารบริษัท
        $corporatefiles = json_decode($user_result->corporatefile);
        $corporatefile  = is_array($corporatefiles) && count($corporatefiles) > 0 ? HP::getFileStorage("media/com_user/$user_result->tax_number/".$corporatefiles[0]->realfile) : null ;

        //เอกสารบุคคล
        $personfiles = json_decode($user_result->personfile);
        $personfile  = is_array($personfiles) && count($personfiles) > 0 ? HP::getFileStorage("media/com_user/$user_result->tax_number/".$personfiles[0]->realfile) : null ;

        //จัดใส่อีกตัวแปรเพื่อจัดรูปแบบข้อมูล
        $user = (object)[];
        $user->username            = $user_result->username;//ชื่อผู้ใช้งาน
        $user->email               = $user_result->email;//อีเมล
        $user->block               = $user_result->block;//ระงับการใช้งาน
        $user->registerDate        = $user_result->registerDate;//วันที่ลงทะเบียน
        $user->lastvisitDate       = $user_result->lastvisitDate;//วันที่ log in ล่าสุด
        $user->sendEmail           = $user_result->sendEmail;//รับอีเมลจากระบบ
        $user->picture             = filter_var($picture, FILTER_VALIDATE_URL) ? $picture : null ;//URL ภาพประจำตัว
        $user->state               = $user_result->state;//สถานะการยืนยัน

        //ข้อมูลผู้ประกอบการ
        $user->prefix_text         = $user_result->prefix_text;//คำนำหน้าชื่อ
        $user->name                = $user_result->name;//ชื่อ
        $user->applicanttype_id    = $user_result->applicanttype_id;
        $user->person_type         = $user_result->person_type;//ประเภทข้อมูลที่ใช้ลงทะเบียน
        $user->tax_number          = $user_result->tax_number;//เลขผู้เสียภาษี
        $user->date_niti           = $user_result->date_niti;//วันที่จดทะเบียนนิติบุคคล
        $user->branch_type         = $user_result->branch_type;//ประเภทสาขา
        $user->branch_code         = $user_result->branch_code;//รหัสสาขา
        $user->corporatefile       = filter_var($corporatefile, FILTER_VALIDATE_URL) ? $corporatefile : null ;//เอกสารบริษัท
        $user->prefix_id           = $user_result->prefix_name;//ไอดีคำนำหน้าชื่อ
        $user->person_first_name   = $user_result->person_first_name;//ชื่อ
        $user->person_last_name    = $user_result->person_last_name;//สกุล
        $user->date_of_birth       = $user_result->date_of_birth;//วันเกิด
        $user->personfile          = filter_var($personfile, FILTER_VALIDATE_URL) ? $personfile : null ;//เอกสารบุคคล

        //สำนักงานใหญ่
        $user->address_no          = $user_result->address_no;
        $user->building            = $user_result->building;
        $user->moo                 = $user_result->moo;
        $user->soi                 = $user_result->soi;
        $user->street              = $user_result->street;
        $user->subdistrict         = $user_result->subdistrict;
        $user->district            = $user_result->district;
        $user->province            = $user_result->province;
        $user->zipcode             = $user_result->zipcode;
        $user->tel                 = $user_result->tel;
        $user->fax                 = $user_result->fax;
        $user->latitude            = $user_result->latitude;
        $user->longitude           = $user_result->longitude;

        //ที่อยู่ที่สามารถติดต่อได้
        $user->contact_name        = $user_result->contact_name;
        $user->contact_tax_id      = $user_result->contact_tax_id;
        $user->contact_prefix_id   = $user_result->contact_prefix_name;
        $user->contact_prefix_text = $user_result->contact_prefix_text;
        $user->contact_first_name  = $user_result->contact_first_name;
        $user->contact_last_name   = $user_result->contact_last_name;
        $user->contact_tel         = $user_result->contact_tel;
        $user->contact_fax         = $user_result->contact_fax;
        $user->contact_phone_number= $user_result->contact_phone_number;
        $user->contact_position    = $user_result->contact_position;
        $user->contact_address_no  = $user_result->contact_address_no;
        $user->contact_building    = $user_result->contact_building;
        $user->contact_moo         = $user_result->contact_moo;
        $user->contact_soi         = $user_result->contact_soi;
        $user->contact_street      = $user_result->contact_street;
        $user->contact_subdistrict = $user_result->contact_subdistrict;
        $user->contact_district    = $user_result->contact_district;
        $user->contact_province    = $user_result->contact_province;
        $user->contact_zipcode     = $user_result->contact_zipcode;

        return $user;
    }

    //API 051 officer_auth
    public function officer_auth(Request $request){

        //เช็คข้อมูล app_name, app_secret และสิทธิ์การใช้ API
        $header = $request->header();

        $check_header = HP_API::check_client($header, __FUNCTION__);

        $app_name = array_key_exists('app-name', $header) ? $header['app-name'][0] : null ;

        if($check_header['status']===false){//ข้อมูลไม่ถูกต้องหรือไม่มีสิทธิ์
            Log::Add($app_name, __FUNCTION__, $check_header['code'], $check_header['msg']);
            return response()->json(['status' => $check_header['code'], 'message' => $check_header['msg']]);
        }

        $input = $request->only(
                                'session_officer',
                                'user_agent'
                                );
        $rule = [
                  'session_officer' => 'required|string',
                  'user_agent' => 'required|string'
                ];

        $validator = Validator::make($input, $rule);

        if ($validator->fails()) {

            $error = $validator->messages();
            Log::Add($app_name, __FUNCTION__, '200', $error->toJson());
            return response()->json(['status'=> '200', 'message'=> $error]);

        }

        //ลบค่า session ที่หมดอายุแล้ว
        $minute = config('session.lifetime');
        $date_expire = date("Y-m-d H:i:s", strtotime("-$minute minute", strtotime(date("Y-m-d H:i:s"))));
        OfficerSession::where('last_visit_at', '<', $date_expire)->delete();

        $session = OfficerSession::where('id', $input['session_officer'])->where('user_agent', $input['user_agent'])->first();

        if(is_null($session)){
            Log::Add($app_name, __FUNCTION__, '502', 'Session Officer or User Agent incorrect.');
            return response()->json(['status'=> '502', 'message'=> 'Session Officer or User Agent incorrect.']);
        }

        //อัพเดทเวลาเข้าใช้งาน
        OfficerSession::Modify($session->id, $app_name);

        //ดึงข้อมูลผู้ใช้งาน
        $user_result = OfficerUser::where('runrecno', $session->user_id)->first();

        if(!is_null($user_result)){//ถ้าพบข้อมูล

            //ข้อมูลที่จะตอบกลับ
            $user = $this->format_user_data_officer($user_result);

            //session id
            $user->session_officer = $input['session_officer'];

            $status  = '000';
            $message = 'Found a user login.';
        }else{//ไม่พบข้อมูล
            $user    = null;
            $status  = '501';
            $message = 'User login not found.';
        }

        Log::Add($app_name, __FUNCTION__, $status, $message);
        return response()->json([
                        'status' => $status,
                        'message'=> $message,
                        'result' => $user
        ]);

    }

    //API 052 officer_login
    public function officer_login(Request $request){

        //เช็คข้อมูล app_name, app_secret และสิทธิ์การใช้ API
        $header = $request->header();

        $check_header = HP_API::check_client($header, __FUNCTION__);

        $app_name = array_key_exists('app-name', $header) ? $header['app-name'][0] : null ;

        if($check_header['status']===false){//ข้อมูลไม่ถูกต้องหรือไม่มีสิทธิ์
            Log::Add($app_name, __FUNCTION__, $check_header['code'], $check_header['msg']);
            return response()->json(['status' => $check_header['code'], 'message' => $check_header['msg']]);
        }

        $input = $request->only(
                                'username',
                                'password',
                                'user_agent',
                                'ip_address'
                                );
        $rule = [
                  'username' => 'required|string',
                  'password' => 'required|string',
                  'user_agent' => 'required|string',
                  'ip_address' => 'string|ipv4'
                ];

        $validator = Validator::make($input, $rule);

        if ($validator->fails()) {

            $error = $validator->messages();
            Log::Add($app_name, __FUNCTION__, '200', $error->toJson());
            return response()->json(['status'=> '200', 'message'=> $error]);

        }

        //ดึงข้อมูลผู้ใช้งาน จากอีเมล
        $user_result = OfficerUser::where('reg_email', $input['username'])->first();

        if(is_null($user_result)){
            //ดึงข้อมูลผู้ใช้งาน จากชื่อผู้ใช้งาน
            $user_result = OfficerUser::where('reg_uname', $input['username'])->first();
        }

        if(is_null($user_result)){
            //ดึงข้อมูลผู้ใช้งาน จากเลขประจำตัวประชาชน
            $user_result = OfficerUser::where('reg_13ID', $input['username'])->first();
        }

        if(is_null($user_result)){//ไม่พบข้อมูลผู้ใช้งาน
            Log::Add($app_name, __FUNCTION__, '503', 'Username not found.');
            return response()->json(['status'=> '503', 'message'=> 'Username not found.']);
        }

        if(md5($input['password']) != $user_result->reg_pword){//รหัสผ่านไม่ถูกต้อง
            Log::Add($app_name, __FUNCTION__, '504', 'Password incorrect.');
            return response()->json(['status'=> '504', 'message'=> 'Password incorrect.']);
        }

        if($user_result->status == 0){ // บล็อกการใช้งาน
            Log::Add($app_name, __FUNCTION__, '506', 'Username suspended.');
            return response()->json(['status'=> '506', 'message'=> 'Username suspended.']);
        }

        //บันทึกลงตาราง session
        $session_id = session()->getId();
        $ip_address = array_key_exists('ip_address', $input) ? $input['ip_address'] : $request->ip() ;
        OfficerSession::Add($session_id, $user_result->getKey(), $ip_address, $input['user_agent'], 'api', $app_name);

        //ข้อมูลที่จะตอบกลับ
        $user = $this->format_user_data_officer($user_result);

        //session id
        $user->session_officer = $session_id;

        $status  = '000';
        $message = 'Login success.';

        Log::Add($app_name, __FUNCTION__, $status, $message);
        return response()->json([
                        'status' => $status,
                        'message'=> $message,
                        'result' => $user
        ]);

    }

    private function format_user_data_officer($user_result){

        //กลุ่มงานย่อย
        $sub_department = $user_result->subdepart;
        $department     = !is_null($sub_department) ? $sub_department->department : null ;

        //โปรไฟล์
        $profile = $user_result->profile;

        //ภาพประจำตัว
        $picture = !is_null($profile) && !empty($profile->pic) ? HP::getFileStorage('users/'.$profile->pic) : null ;

        //จัดใส่อีกตัวแปรเพื่อจัดรูปแบบข้อมูล
        $user = (object)[];
        $user->runrecno            = $user_result->runrecno ; //เลขรัน
        $user->username            = !empty($user_result->reg_uname) ? $user_result->reg_uname : null;//ชื่อผู้ใช้งาน
        $user->tax_number          = preg_replace('/[^0-9]/', '', $user_result->reg_13ID);//เลขผู้เสียภาษี
        $user->prefix_code         = !empty($user_result->reg_intital) ? $user_result->reg_intital : null ;//รหัสคำนำหน้าชื่อ
        $user->first_name          = $user_result->reg_fname;//ชื่อ
        $user->last_name           = $user_result->reg_lname;//สกุล
        $user->email               = $user_result->reg_email;//อีเมล
        $user->phone               = $user_result->reg_phone;//เบอร์โทร
        $user->work_phone          = $user_result->reg_wphone;//เบอร์โทรที่ทำงาน
        $user->status              = $user_result->status;//1 = ใช้งาน, 0 = ปิด
        $user->picture             = filter_var($picture, FILTER_VALIDATE_URL) ? $picture : null ;//URL ภาพประจำตัว
        $user->role                = $user_result->role; //ระดับตำแหน่ง
        $user->position            = $user_result->position; //ตำแหน่ง
        $user->department_id       = !is_null($department) ? $department->did : null ; //รหัสกลุ่มงานหลัก
        $user->department_name     = !is_null($department) ? $department->depart_name : null ; //ชื่อกลุ่มงานหลัก
        $user->sub_department_id   = !is_null($sub_department) ? $sub_department->sub_id : null ; //รหัสกลุ่มงานย่อย
        $user->sub_department_name = !is_null($sub_department) ? $sub_department->sub_departname : null ; //ชื่อกลุ่มงานย่อย

        return $user;
    }

    //API 053 officer_login
    public function officer_role(Request $request){

        //เช็คข้อมูล app_name, app_secret และสิทธิ์การใช้ API
        $header = $request->header();

        $check_header = HP_API::check_client($header, __FUNCTION__);

        $app_name = array_key_exists('app-name', $header) ? $header['app-name'][0] : null ;

        if($check_header['status']===false){//ข้อมูลไม่ถูกต้องหรือไม่มีสิทธิ์
            Log::Add($app_name, __FUNCTION__, $check_header['code'], $check_header['msg']);
            return response()->json(['status' => $check_header['code'], 'message' => $check_header['msg']]);
        }

        $input = $request->only(
                                'username'
                                );
        $rule = [
                  'username' => 'required|string'
                ];

        $validator = Validator::make($input, $rule);

        if ($validator->fails()) {

            $error = $validator->messages();
            Log::Add($app_name, __FUNCTION__, '200', $error->toJson());
            return response()->json(['status'=> '200', 'message'=> $error]);

        }

        //ดึงข้อมูลผู้ใช้งาน จากอีเมล
        $user_result = OfficerUser::where('reg_email', $input['username'])->first();

        if(is_null($user_result)){//ไม่พบข้อมูลผู้ใช้งาน
            Log::Add($app_name, __FUNCTION__, '503', 'Username not found.');
            return response()->json(['status'=> '503', 'message'=> 'Username not found.']);
        }

        //ข้อมูลที่จะตอบกลับ
        $role_ids = RoleUser::where('user_runrecno', $user_result->getKey())->pluck('role_id');
        $roles    = Role::whereIn('id', $role_ids)->orderby('id')->pluck('name', 'id');
        // dd($roles);
        $status  = '000';
        $message = 'Found a user.';

        Log::Add($app_name, __FUNCTION__, $status, $message);
        return response()->json([
                        'status' => $status,
                        'message'=> $message,
                        'result' => $roles
        ]);

    }

}
