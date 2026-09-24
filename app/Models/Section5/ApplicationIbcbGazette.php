<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

class ApplicationIbcbGazette extends Model
{
    protected $table = 'section5_application_ibcb_gazettes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'application_id',
        'application_no',
        'issue',
        'year',
        'announcement_date',
        'sign_id',
        'sign_name',
        'sign_position',
        'created_by',
        'updated_by'

    ];
}
