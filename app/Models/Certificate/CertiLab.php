<?php

namespace App\Models\Certificate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertiLab extends Model
{

    protected $table = "app_certi_labs";
    protected $primaryKey = 'id';
    protected $fillable = ['app_no','name','status','trader_id','token','email','attach','attach_pdf','attach_pdf_client_name',  'province','amphur','district','subgroup','get_date','purpose_type','name','lab_type','created_by','tax_id'
                         ];
    public $sortable = ['id','app_no','name','trader_id','lab_type'];

}
