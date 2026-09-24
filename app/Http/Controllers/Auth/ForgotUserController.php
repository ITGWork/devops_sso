<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotUserMail;
use App\User;

class ForgotUserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    //ฟอร์ม
    public function index(){
        return view('auth/users/email');
    }

    //ส่งเมล
    public function send_mail(Request $request){

        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->get('email');
        $users = User::where('email', $email)->get();

        if($users->count() > 0){

            $user = $users->first();
            $mail = new ForgotUserMail([
                                            'name'  => $user->name,
                                            'users' => $users
                                       ]);

            Mail::to($email)->send($mail);

            return redirect('forgot-user')->with('message', 'ส่งอีเมลเรียบร้อยแล้ว');
        }else{
            //ไม่พบอีเมล
            return back()->withInput()->withErrors(['email' => 'ไม่พบอีเมลของคุณในระบบ']);
        }

    }

}
