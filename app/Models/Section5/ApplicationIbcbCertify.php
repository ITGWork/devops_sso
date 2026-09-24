<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Bsection5\Standard;

class ApplicationIbcbCertify extends Model
{
    protected $table = 'section5_application_ibcb_cer';

    protected $primaryKey = 'id';

    protected $fillable = [ 
        'application_id',
        'application_no',
        'certificate_std_id',
        'certificate_id',
        'certificate_table',
        'certificate_no',
        'certificate_start_date',
        'certificate_end_date',
        'issued_by'
        
    ];

    public function tis_standard(){
        return $this->belongsTo(Standard::class, 'certificate_std_id');
    }  
    
}
