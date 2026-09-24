<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use HP;

class LoginFail extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sso_login_fails';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'username',
                            'ip_address',
                            'login_at'
                          ];

    public $timestamps = false;

    //บันทึก Session
    static function Add($ip_address, $username){

        $config = HP::getConfig();
        $sso_login_fail_lock_time = property_exists($config, 'sso_login_fail_lock_time') ? $config->sso_login_fail_lock_time : 15 ;

        //ลบรายการที่เกิน 20 นาที
        LoginFail::where('login_at', '<', date("Y-m-d H:i:s", strtotime("-{$sso_login_fail_lock_time} minutes")))->delete();

        //เวลาปัจจุบัน
        $date_time = date('Y-m-d H:i:s');

        //บันทึกลง LoginFail
        $session = new LoginFail;
        $session->username   = $username;
        $session->ip_address = $ip_address;
        $session->login_at   = $date_time;
        $session->save();

    }

    //นับจำนวนที่ Login ผิดเกิน 5 ครั้งใน 15 นาที
    static function CheckLock($username){

        $config = HP::getConfig();
        $sso_login_fail_lock_time = property_exists($config, 'sso_login_fail_lock_time') ? $config->sso_login_fail_lock_time : 15 ;
        $sso_login_fail_amount    = property_exists($config, 'sso_login_fail_amount') ? (int)$config->sso_login_fail_amount : 5 ;

        $count_fail = LoginFail::where('username', $username)
                          ->where('login_at', '>', date("Y-m-d H:i:s", strtotime("-{$sso_login_fail_lock_time} minutes")))
                          ->count();

        return $count_fail>=$sso_login_fail_amount ? true : false ;
    }

}
