<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class LoginLog extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sso_login_logs';

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
    protected $fillable = ['session_id',
                           'user_id',
                           'ip_address',
                           'user_agent',
                           'login_at',
                           'logout_at',
                           'last_visit_at',
                           'act_instead',
                           'channel',
                           'app_name'
                          ];

    public function get_act_instead(){
        return $this->belongsTo(User::class, 'act_instead', 'id');
    }

    public $timestamps = false;

}
