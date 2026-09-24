<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

class ApplicationLabStatus extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_application_labs_status';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
                            'title'
                        ];

}
