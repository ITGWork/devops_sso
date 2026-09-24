<?php

namespace App\Models\Officer;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_access_logs';

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
                           'login_log_id',
                           'last_visit_at',
                           'app_name'
                          ];

    public $timestamps = false;

}
