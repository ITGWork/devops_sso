<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bsection5\Standard;

use App\Models\Certify\ApplicantIB\CertiIBExport;
use App\Models\Certify\ApplicantIB\CertiIb;
use App\Models\Certify\ApplicantCB\CertiCBExport;
use App\Models\Certify\ApplicantCB\CertiCb;

class IbcbsCertificate extends Model
{
    protected $table = 'section5_ibcbs_certificates';

    protected $primaryKey = 'id';

    protected $fillable = [ 
        'ibcb_id',
        'ibcb_code',
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

    
    public function certify_cb_export(){
        return $this->belongsTo(CertiCBExport::class, 'certificate_id');
    }  

    public function certify_ib_export(){
        return $this->belongsTo(CertiIBExport::class, 'certificate_id');
    }  
}
