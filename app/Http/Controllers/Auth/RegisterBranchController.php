<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\RoleUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use App\Models\Basic\Prefix;
use App\Models\Basic\ConfigRoles as config_roles;
use HP;
use Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;

class RegisterBranchController extends Controller
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
        $this->middleware('guest');
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
    protected function index()
    {
        return view('auth/register-branch');
    }

    public function register(Request $request)
    {

        $request->validate([
            'password' => 'required|string|confirmed'
        ]);

        $prefix                             = Prefix::where('state', 1)->pluck('title', 'id');

        $requests                           = $request->all();
        $requestData                        = $requests['jform'];
        $requestData['contact_tax_id']      = isset($requestData['contact_tax_id']) ? self::PregReplace($requestData['contact_tax_id']) : null;
        $requestData['contact_tel']         = isset($requestData['contact_tel']) ? $requestData['contact_tel'] : null;
        $requestData['contact_phone_number']= isset($requestData['contact_phone_number']) ? self::PregReplace($requestData['contact_phone_number']) : null;
        $requestData['tax_number']          = isset($requestData['tax_number']) ? self::PregReplace($requestData['tax_number']) : null;
        $requestData['password']            = Hash::make($request->password);
        $requestData['tel']                 = isset($requestData['contact_tel']) ? $requestData['contact_tel'] : null;
        $requestData['fax']                 = isset($requestData['contact_fax']) ? $requestData['contact_fax'] : null;

        //ตรวจสอบข้อมูลที่จำเป็นอีกครั้ง
        $user_table = (new User)->getTable();
        $rule = [
                    'email' => 'required|email|unique:'.$user_table.',email',
                    'tax_number' => 'required|string'
                ];
        $validator = Validator::make($requestData, $rule);
        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ '.$errorString]);
        }

        //เช็คว่าครบ 13 หลัก
        if(in_array($requestData['applicanttype_id'], [1, 3, 4])){
            $requestData['tax_number'] = $this->CutNumberOnly($requestData['tax_number']);//ตัดออกให้เหลือแต่ตัวเลข

            if(strlen($requestData['tax_number'])!=13){
                return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ เลขประจำตัวผู้เสียภาษีไม่เท่ากับ 13 หลัก']);
            }
        }

        $branch_data                = $this->genBranchData($requestData['tax_number']);//ชื่อผู้ใช้งานและรหัสสาขา
        $requestData['username']    = $branch_data->username;//ชื่อผู้ใช้งาน
        $requestData['branch_code'] = $branch_data->branch_code;//รหัสสาขา
        $requestData['branch_type'] = 2;//ประเภท 2 = สาขา

        if(in_array($requestData['applicanttype_id'],[5])){ //ชื่อผู้ประกอบการ   อื่นๆ
            $requestData['date_niti']           =  HP::convertDate($requestData['date_birthday']);  // วันที่จดทะเบียนอื่นๆ
            $requestData['name']                =  $requestData['another_name'] ;
        }else if(in_array($requestData['applicanttype_id'],[4])){ //ชื่อผู้ประกอบการ   ส่วนราชการ
            $requestData['date_niti']           =  HP::convertDate($requestData['date_birthday']);  // วันที่จดทะเบียนส่วนราชการ
            $requestData['name']                =  $requestData['service_name'] ;
        }else if(in_array($requestData['applicanttype_id'],[3])){ //ชื่อผู้ประกอบการ  คณะบุคคล
            $requestData['date_niti']           =  HP::convertDate($requestData['date_birthday']);  // วันที่จดทะเบียนนิติบุคคล
            $requestData['name']                =  $requestData['faculty_name'] ;
        }else{//ชื่อผู้ประกอบการ นิติบุคคล
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

        $requestData['registerDate']        =  date('Y-m-d H:i:s');
        $requestData['lastvisitDate']       =  date('Y-m-d H:i:s');
        $requestData['state']               = 1;
        $requestData['block']               = 1;
        $requestData['params']              = '{}';
        $requestData['department_id']       = '0';
        $requestData['agency_tel']          = '';
        $requestData['authorize_data']      = '';

        $user = User::create($requestData);

        if($user){

            $config_roles  =  config_roles::select('role_id')->whereIn('group_type', [1, 2])->get()->pluck('role_id');
            if(count($config_roles) > 0){
                RoleUser::where('user_id', $user->id)->delete();
                foreach($config_roles as $role){
                    $RoleUser            = [];
                    $RoleUser['user_id'] = $user->id;
                    $RoleUser['role_id'] = $role;
                    RoleUser::create($RoleUser);
                }

            }

            if($user->applicanttype_id == 2){ //บุคคลธรรมดา
                $name = 'คุณ'.@$user->contact_first_name.' '.@$user->contact_last_name;
            }else{
                $name = !empty($user->name) ? $user->name : '-';
            }

            $mail = new RegisterMail(['email' => 'e-Accreditation@tisi.mail.go.th' ?? '-',
                                      'name'  => $name,
                                      'username' => $requestData['username'],
                                      'check_api' => !empty($user->check_api) ? 1 : 0,
                                      'link' => !empty($user->id) ? url('/activated-mail/'.base64_encode($user->id)) : url('')
                                    ]);

            if($user->email){
                Mail::to($user->email)->send($mail);
            }

            return redirect('/login')->with('flash_message', 'บันทึกสำเร็จ กรุณายืนยันตัวตน ที่อีเมลที่ท่านลงเบียนไว้');
        } else {
            return back()->withInput()->withErrors(['ลงทะเบียนไม่สำเร็จ']);
        }

    }

    public function genBranchData($tax_number){

        $username    = null;
        $branch_code = null;

        $users = User::where('tax_number', $tax_number)->orderby('username', 'desc')->get();

        if($users->count()===0){//เป็นสาขาแรก
            $branch_code = '0001';
            $username = $tax_number.$branch_code;
        }else{
            $last_branch = $users->first();//สาขาล่าสุดที่มีในระบบ
            $next_number = (int)$last_branch->branch_code;

            next:
            $next_branch = str_pad(++$next_number, 4, '0', STR_PAD_LEFT);
            $branch_code = $next_branch;
            $username = $tax_number.$next_branch;

            if(count($users->where('username', $username))>0){//ยังมีซ้ำ
                goto next;
            }
        }

        return (object)compact('username', 'branch_code');
    }

    // สำหรับเพิ่มรูปไปที่ store
    public function storeFile($files, $tax_number)
    {

        if ($files) {
            $attach_path     = $this->attach_path.$tax_number;
            $filename        = HP::ConvertCertifyFileName(@$files->getClientOriginalName());
            $fullFileName    = str_random(10).'-date_time'.date('Ymd_hms') . '.' . $files->getClientOriginalExtension();

            $storagePath     = Storage::putFileAs($attach_path, $files,  str_replace(" ","",$fullFileName) );
            $file_name       = basename($storagePath); // Extract the filename
            $corporatefile[] = array('realfile' => $file_name, 'filename' => $filename);
            return json_encode($corporatefile);
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

}
