<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;
use HP;
 
class ResetEmailController extends Controller
{

    public function inform(Request $request)
    {

        $user = User::where('email', $request->email)->first(); // เช็ค username

        if (!is_null($user)) { 
            if($user->state == 1){ // ส่งอีเมลยืนยันตัวตน

                if($user->applicanttype_id == 2){ //บุคคลธรรมดา 
                    $name =   'คุณ'.@$user->contact_first_name.' '.@$user->contact_last_name;
                }else{
                    $name =   !empty($user->name)  ?  $user->name  : '-';
                }

                $mail = new RegisterMail([
                                            'email'     =>  'e-Accreditation@tisi.mail.go.th' ?? '-',
                                            'name'      =>  $name,
                                            'check_api' =>  !empty($user->check_api)  ?    1     : 0,
                                            'link'      =>   !empty($user->id)  ?      url('/activated-mail/'.base64_encode($user->id))    : url('')
                                        ]);

                if( !empty($user->email) ){
                    Mail::to($user->email)->send($mail);
                }
                return redirect('/login')->with('flash_message', 'ส่งอีเมลเรียบร้อยแล้ว');
            }else if($user->state == 2){ //ยืนยันตัวตนแล้ว
                return back()->withInput()->with('flash_message', 'อีเมล <u>'.$request->email.' </u> นี้ยืนยันตัวตนแล้วไม่สามารถยืนยันตัวตนอีกครั้งได้');
            }else if($user->state == 3){  //รอเจ้าหน้าที่ยืนยันตัวตน
                return back()->withInput()->with('flash_message', 'อีเมล <u>'.$request->email.' </u> นี้ต้องรอเจ้าหน้าที่มายืนยัน');
            }
        }else{
            return back()->withInput()->with('flash_message', 'อีเมล '.  $request->email  . ' นี้ไม่มีข้อมูลการลงทะเบียน');
        }

    }


}
