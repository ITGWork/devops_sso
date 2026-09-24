<?php

namespace App\Models\Certify\ApplicantIB;

use Illuminate\Database\Eloquent\Model;
use App\Models\Certify\ApplicantIB\CertiIBFileAll;

class CertiIbExportMapreq extends Model
{
    protected $table = 'certificate_ib_export_mapreq';

    protected $fillable = [
            'app_certi_ib_id',
            'certificate_exports_id'
    ];

    public function certiib_export_mapreq_group_many()
    {
        return $this->hasMany(CertiIbExportMapreq::class, 'certificate_exports_id','certificate_exports_id');
    }

    public function app_certi_ib_to()
    {
        return $this->belongsTo(CertiIb::class, 'app_certi_ib_id');
    }

    public function app_certi_ib_file_all()
    {
        return $this->hasMany(CertiIBFileAll::class, 'app_certi_ib_id', 'app_certi_ib_id');
    }

    public function app_certi_ib_file()
    {
        return $this->belongsTo(CertiIBFileAll::class,  'app_certi_ib_id', 'app_certi_ib_id')->where('state','1');
    }

    public function app_certi_ib_export_to() {
        return $this->belongsTo(CertiIBExport::class,'certificate_exports_id', 'id');
    }
    
    public function getCertiIBFilePrimaryAttribute() {
        $certiib_export_mapreqs = $this->certiib_export_mapreq_group_many()->select('app_certi_ib_id')->groupBy('app_certi_ib_id')->get()->pluck('app_certi_ib_file_all')->flatten();
        $state1 = $certiib_export_mapreqs->where('state', 1);
        $check1 = $state1->where('end_date', '!=', null)->sortByDesc('end_date')->first();
        if(!empty($check1)){
            return $check1;
        }
        $check2 = $state1->sortByDesc('id')->first();
        if(!empty($check2)){
            return $check2;
        }
        $check3 = $certiib_export_mapreqs->where('end_date', '!=', null)->sortByDesc('end_date')->first();
        if(!empty($check3)){
            return $check3;
        }
        $check4 = $certiib_export_mapreqs->sortByDesc('id')->first();
        if(!empty($check4)){
            return $check4;
        }
    }

}
 