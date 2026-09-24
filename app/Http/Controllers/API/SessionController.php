<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Sessions;
use App\User;
use HP;


class SessionController extends Controller
{

 public function  Sessions(Request $request){
    $session_id     =  HP::getSession();

    if(is_null($session_id)){
      return response()->json(['status'=> false, 'error'=> 'Session Id or User Agent incorrect']);
    }

    $web_service    = Sessions::first();
    if(is_null($web_service)){
      return response()->json(['status'=> false, 'error'=> 'Session Id or User Agent incorrect']);
    }

    $result               = [];
    $result['user_agent'] = $web_service->user_agent;
    $result['session']    = $web_service->id;

    return response()->json([
                            'status'  =>  !is_null($web_service) ?  '000' : 'Session Id or User Agent incorrect',
                            'message'=> 'Found member login',
                            'result'  => $result
                           ]);
  }



}
