<?php

use App\Models\WS\Client;

class HP_API
{

    //เช็คข้อมูล app_name, app_secret และสิทธิ์การใช้ API
    public static function check_client($header, $API)
    {
        $status = true;
        $code   = '000';
        $msg    = '';

        $field_name   = 'app-name';
        $field_secret = 'app-secret';

        if(array_key_exists($field_name, $header) && count($header[$field_name]) > 0){
            $app_name = $header[$field_name][0];
        }else{
            $status = false;
            $code   = '101';
            $msg    = "$field_name is required.";
            goto end;
        }

        if(array_key_exists($field_secret, $header) && count($header[$field_secret]) > 0){
            $app_secret = $header[$field_secret][0];
        }else{
            $status = false;
            $code   = '102';
            $msg    = "$field_secret is required.";
            goto end;
        }

        $ws = Client::where('app_name', $app_name)->first();

        if(is_null($ws)){
            $status = false;
            $code   = '103';
            $msg    = "$field_name not found in the system.";
            goto end;
        }

        if($ws->app_secret!=$app_secret){
            $status = false;
            $code   = '104';
            $msg    = "$field_secret invalid.";
            goto end;
        }

        if($ws->state!=1){
            $status = false;
            $code   = '105';
            $msg    = "$field_name suspended.";
            goto end;
        }

        $api_list = json_decode($ws->ListAPI, true);
        if(!is_array($api_list) || !in_array(strtolower($API), $api_list)){
            $status = false;
            $code   = '106';
            $msg    = "$field_name is not licensed to use this service.";
            goto end;
        }

        end:
        return compact('status', 'code', 'msg');

    }

}
