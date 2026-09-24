<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Session;
use DB;
use App\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = 'password/reset_success';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function reset(Request $request){

        $input = $request->all();

        if(filter_var($input['email'], FILTER_VALIDATE_EMAIL)){

            $reset = DB::table('password_resets')->where('email', $input['email'])->first();

            if(!is_null($reset)){

                if(Hash::check($input['token'], $reset->token)){

                    $password = Hash::make($input['password']);
                    $lastResetTime = date('Y-m-d H:i:s');
                    $result = User::where('email', $input['email'])->update(['password' => $password, 'lastResetTime' => $lastResetTime]);
                    DB::table('password_resets')->where('email', $input['email'])->delete();

                    if($result){
                        Session::flash('flash_message', 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว');
                        return redirect('login');
                    }else{
                        Session::flash('flash_message', 'ไม่พบอีเมลของคุณกรุณาลงทะเบียนเพื่อเข้าใช้งาน');
                        return redirect('login');
                    }
                }else{
                    //ไม่พบ token
                    return back()->withInput()
                                 ->withErrors([__('passwords.token')]);
                }
            }else{

                //ไม่พบ อีเมลในตารางขอเปลี่ยนรหัสผ่าน
                return back()->withInput()
                             ->withErrors([__('passwords.no_email')]);
            }

        }else{
            //ไม่พบ อีเมลในตารางขอเปลี่ยนรหัสผ่าน
            return back()->withInput()
                         ->withErrors(['รูปแบบอีเมลไม่ถูกต้อง']);
        }

    }

}
