<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use App\User;
class SettingSystem extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'setting_systems';
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
    protected $fillable = ['title', 'details', 'urls', 'icons', 'colors', 'state', 'branch_block', 'created_by', 'updated_by'];



}
