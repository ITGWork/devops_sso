<?php

namespace App\Models\Certify\ApplicantCB;

use Illuminate\Database\Eloquent\Model;
use App\Models\Certify\ApplicantCB\CertiCBFileAll;

class CertiCbExportMapreq extends Model
{
    protected $table = 'certificate_cb_export_mapreq';

    protected $fillable = [
            'app_certi_cb_id',
            'certificate_exports_id'
    ];
   
    
    public function certicb_export_mapreq_group_many()
    {
        return $this->hasMany(CertiCbExportMapreq::class, 'certificate_exports_id','certificate_exports_id');
    }

    public function app_certi_cb_to()
    {
        return $this->belongsTo(CertiCb::class, 'app_certi_cb_id');
    }
    public function app_certi_cb_export_to() {
        return $this->belongsTo(CertiCBExport::class,'certificate_exports_id', 'id');
    }

    public function app_certi_cb_file_all()
    {
        return $this->hasMany(CertiCBFileAll::class, 'app_certi_cb_id', 'app_certi_cb_id')->orderBy('created_at','desc');
    }

    public function getCertiCBFilePrimaryAttribute() {
        $certicb_export_mapreqs = $this->certicb_export_mapreq_group_many()->select('app_certi_cb_id')->groupBy('app_certi_cb_id')->get()->pluck('app_certi_cb_file_all')->flatten();
        $state1 = $certicb_export_mapreqs->where('state', 1);
        $check1 = $state1->where('end_date', '!=', null)->sortByDesc('end_date')->first();
        if(!empty($check1)){
            return $check1;
        }
        $check2 = $state1->sortByDesc('id')->first();
        if(!empty($check2)){
            return $check2;
        }
        $check3 = $certicb_export_mapreqs->where('end_date', '!=', null)->sortByDesc('end_date')->first();
        if(!empty($check3)){
            return $check3;
        }
        $check4 = $certicb_export_mapreqs->sortByDesc('id')->first();
        if(!empty($check4)){
            return $check4;
        }
    }

}
