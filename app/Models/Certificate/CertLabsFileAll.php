<?php

namespace App\Models\Certificate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 
use Kyslik\ColumnSortable\Sortable;

class CertLabsFileAll extends Model
{
    use Sortable;

    protected $table = "app_cert_lab_file_all";
    protected $primaryKey = 'id';
    protected $fillable = [
                            'app_certi_lab_id',
                            'app_no',
                            'attach',
                            'attach_pdf',
                            'attach_client_name',
                            'start_date',
                            'end_date',
                            'attach_pdf_client_name',
                            'state',
                            'status_cancel',
                            'created_cancel',
                            'date_cancel',
                            'ref_table',
                            'ref_id'
                        ];

}
