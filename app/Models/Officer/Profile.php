<?php

namespace App\Models\Officer;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
      /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'profile_staffs';

    protected $guarded= [];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
