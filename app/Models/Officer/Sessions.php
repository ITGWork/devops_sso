<?php

namespace App\Models\Officer;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_sessions';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['id',
                           'user_id',
                           'ip_address',
                           'user_agent',
                           'login_at',
                           'last_visit_at'
                          ];

    public $timestamps = false;

    //บันทึก Session
    static function Add($session_id, $user_id, $ip_address, $user_agent, $channel = 'web', $app_name = null){

        //เวลาที่ ปัจจุบัน
        $date_time = date('Y-m-d H:i:s');

        //บันทึกลง Session
        $session = new Sessions;
        $session->id            = $session_id;
        $session->user_id       = $user_id;
        $session->ip_address    = $ip_address;
        $session->user_agent    = $user_agent;
        $session->login_at      = $date_time;
        $session->last_visit_at = $date_time;
        $session->save();

        //บันทึกลง Log Login
        $login_log = new LoginLog;
        $login_log->session_id   = $session_id;
        $login_log->user_id      = $user_id;
        $login_log->ip_address   = $ip_address;
        $login_log->user_agent   = $user_agent;
        $login_log->login_at     = $date_time;
        $login_log->last_visit_at= $date_time;
        $login_log->channel      = $channel;
        $login_log->app_name     = $app_name;
        $login_log->save();

        //อัพเดทตาราง sso_access_logs
        if(!is_null($app_name) && !is_null($login_log)){
            $access_log = new AccessLog;
            $access_log->login_log_id  = $login_log->id;
            $access_log->last_visit_at = $date_time;
            $access_log->app_name      = $app_name;
            $access_log->save();
        }

    }

    //อัพเดทเวลา
    static function Modify($session_id, $app_name = null){

        //เวลาที่ ปัจจุบัน
        $date_time = date('Y-m-d H:i:s');

        //อัพเดทตาราง sso_session
        $session = Sessions::where('id', $session_id)->first();
        $session->last_visit_at = $date_time;
        $session->save();

        //อัพเดทตาราง sso_login_logs
        $login_log = LoginLog::where('session_id', $session_id)->first();
        if(!is_null($login_log)){
            $login_log->last_visit_at = $date_time;
            $login_log->save();
        }

        //อัพเดทตาราง sso_access_logs
        if(!is_null($app_name) && !is_null($login_log)){
            $access_log = new AccessLog;
            $access_log->login_log_id  = $login_log->id;
            $access_log->last_visit_at = $date_time;
            $access_log->app_name      = $app_name;
            $access_log->save();
        }

    }

    //ลบ Session
    static function Remove($session_id){

        //เวลาที่ logout
        $date_time = date('Y-m-d H:i:s');

        //ลบจากตาราง sso_session
        Sessions::where('id', $session_id)->delete();

        //อัพเดทตาราง sso_login_logs
        $login_log = LoginLog::where('session_id', $session_id)->first();
        if(!is_null($login_log)){
            $login_log->last_visit_at = $date_time;
            $login_log->logout_at     = $date_time;
            $login_log->save();
        }

    }

}
