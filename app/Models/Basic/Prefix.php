<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Prefix extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'basic_prefix';
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
    protected $fillable = ['title', 'title_en', 'state', 'created_by', 'updated_by'];
    /* User Relation */
    public function user_created(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_by');
    }


}
