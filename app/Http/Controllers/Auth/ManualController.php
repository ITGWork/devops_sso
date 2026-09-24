<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\Config\ConfigsManual;
use HP;

class ManualController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $manuals = ConfigsManual::where('site', 'tisi-sso')->get();
        return view('auth/manual/index', compact('manuals'));

    }

}
