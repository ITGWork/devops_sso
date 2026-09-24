<?php

namespace App\Models\Certify\ApplicantCB;

use Illuminate\Database\Eloquent\Model;

class CertiCBFileAll extends Model
{
    protected $table = 'app_certi_cb_file_all';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'app_certi_cb_id', 
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
                            'issue_date'
                            ];
         public function certi_cb_to()
    {
        return $this->belongsTo(CertiCb::class,'app_certi_cb_id');
    }                       

                            
}
