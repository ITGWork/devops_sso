<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Certificate\CertificateExport;
use App\AttachFile;
class ApplicationLabCertificate extends Model
{
    protected $table = 'section5_application_labs_cer';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'application_lab_id',
        'application_no',
        'certificate_no',
        'certificate_id',
        'certificate_start_date',
        'certificate_end_date',
        'issued_by',
        'accereditatio_no'
    ];

    public function certificate_export(){
        return $this->belongsTo(CertificateExport::class,  'certificate_id');
    }  

    public function certificate_file(){
        return $this->belongsTo(AttachFile::class,  'id', 'ref_id')->where('ref_table', $this->getTable() )->where('section','audit_certificate_file');
    }  
}
