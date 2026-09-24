<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use App\User;
class SettingUrl extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'setting_url';
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
    protected $fillable = ['column_name', 'data'];



}
