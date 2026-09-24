<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counsel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'certify_counsel';

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
    protected $fillable = ['entrepreneur_name', 'contact_name', 'contact_tel', 'contact_email', 'feedback', 'created_by', 'updated_by'];

    
}
