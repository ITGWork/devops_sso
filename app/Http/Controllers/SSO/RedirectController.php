<?php

namespace App\Http\Controllers\SSO;

use App\Http\Controllers\Controller;

use HP;
use App\Models\Setting\SettingSystem;

class RedirectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

   public function index($id)
    {
        $setting = SettingSystem::findOrFail($id);

        if($setting->transfer_method=='redirect'){
            return redirect($setting->urls);
        }elseif($setting->transfer_method=='form_post'){
            return view('sso/redirect', compact('setting'));
        }

    }

}
