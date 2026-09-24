<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'province';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'PROVINCE_ID';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['PROVINCE_NAME', 'created_by', 'updated_by'];



}
