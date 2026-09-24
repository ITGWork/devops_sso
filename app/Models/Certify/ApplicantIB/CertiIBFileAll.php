<?php

namespace App\Models\Certify\ApplicantIB;

use Illuminate\Database\Eloquent\Model;

class CertiIBFileAll extends Model
{
    protected $table = 'app_certi_ib_file_all';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'app_certi_ib_id', //TB: app_certi_cb
                            'attach',
                            'attach_client_name',
                            'attach_pdf',
                            'attach_pdf_client_name',
                            'start_date',
                            'end_date',
                            'state' ,
                            'status_cancel' ,
                            'created_cancel' ,
                            'date_cancel' ,
                            'app_no' ,
                            'ref_table' ,
                            'ref_id' ,
                            ];
       
    public function certi_ib_to()
    {
        return $this->belongsTo(CertiIb::class,'app_certi_ib_id');
    }  
    
    
}
