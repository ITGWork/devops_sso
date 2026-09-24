<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;

class Zipcode extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'basic_zipcode';
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
    protected $fillable = ['district_code','zipcode'];



}
