<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotUserMail;
use App\User;

class CheckEmailController extends Controller
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
    public function index(Request $request){

        $tax_number = $request->get('tax');
        $user_list  = collect([]);

        if(!is_null($tax_number)){
            $user_list = User::where('tax_number', $tax_number)->get();
        }

        return view('auth/emails/check', [
                                          'tax' => $tax_number,
                                          'user_list' => $user_list
                                         ]);
    }

}
