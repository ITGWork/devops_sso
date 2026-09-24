<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
class UserGroupMap extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ros_user_usergroup_map';
    public $timestamps = false;
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
    protected $fillable = ['user_id', 'group_id'];
 


}
