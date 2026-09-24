<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\User;

class RoleUser extends Model
{
    use Sortable;
    // use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'role_user';

    public $incrementing = false;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['role_id', 'user_trader_autonumber', 'user_runrecno', 'user_id', 'tax_id'];

    /*
      Sorting
    */
    public $sortable =  ['role_id', 'user_trader_autonumber', 'user_runrecno', 'user_id', 'tax_id'];

    public $timestamps = false;

    public function user_to(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role_to(){
        return $this->belongsTo(Role::class, 'role_id');
    }

}
