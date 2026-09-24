<?php

namespace App\Models\Officer;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Officer\SubDepartment;
use App\Models\Officer\Profile;

use HP;

class User extends Authenticatable
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_register';

    protected $primaryKey = 'runrecno';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reg_13ID', 'reg_fname', 'reg_lname', 'reg_email', 'reg_phone', 'reg_wphone', 'reg_pword', 'reg_unmd5', 'reg_subdepart'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'reg_pword', 'remember_token',
    ];

    public function profile(){
        return $this->hasOne(Profile::class);
    }

    public function subdepart(){
      return $this->belongsTo(SubDepartment::class, 'reg_subdepart');
    }

    public function getFullNameAttribute() {
        return "{$this->reg_fname} {$this->reg_lname}";
    }

}
