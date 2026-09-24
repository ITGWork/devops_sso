<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

class ApplicationIbcbStatus extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_application_ibcb_status';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['title'];
}
