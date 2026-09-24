<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

class IbcbsGazette extends Model
{
    protected $table = 'section5_ibcbs_gazettes';

    protected $primaryKey = 'id';

    protected $fillable = [ 
        'ibcb_id',
        'ibcb_code',
        'issue',
        'year',
        'announcement_date',
        'government_gazette_date',
        'government_gazette_description',
        'sign_id',
        'sign_name',
        'sign_position',
        'created_by',
        'updated_by'
    ];
}
