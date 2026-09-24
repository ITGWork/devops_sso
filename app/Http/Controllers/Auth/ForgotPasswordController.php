<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function preResetLinkEmail(Request $request){

        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->get('email');
        $users = User::where('email', $email)->get();

        if($users->count() > 1){ //มีมากกว่า 1 บัญชีไม่ให้รีเซ็ต
            return back()->withInput()
                         ->withErrors(['message' => '<h3>อีเมลของคุณมีบัญชีผู้ใช้งานมากกว่า 1 บัญชี</h3> <h5 class="text-dark">กรุณาติดต่อ สมอ. ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร </h5><h5 class="text-dark">โทร. 0 2430 6834 ต่อ 2450, 2451 </h5><h5 class="text-dark">เพื่อดำเนินการแก้ไขอีเมลให้เรียบร้อยก่อน</h5>']);
        }else{
            return $this->sendResetLinkEmail($request);
        }

    }

}
