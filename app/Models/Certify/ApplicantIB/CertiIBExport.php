<?php

namespace App\Models\Certify\ApplicantIB;

use Illuminate\Database\Eloquent\Model;
use App\Models\Certify\ApplicantIB\CertiIb;

class CertiIBExport extends Model
{
    protected $table = 'app_certi_ib_export';
    protected $primaryKey = 'id';
    protected $fillable = [
							              'org_name',
                            'app_certi_ib_id', //TB: app_certi_cb
                            'type_unit',
                            'app_no',
                            'certificate',
                            'radio_address',
                            'name_unit',
                            'address',
                            'allay',
                            'village_no',
                            'road',
                            'province_name',
                            'amphur_name',
                            'district_name',
                            'postcode',
                            'formula',
                            'attachs',
                            'status',
                            'accereditatio_no',
                            'accereditatio_no_en',
                            'date_start',
                            'date_end',
                            'created_by',
                            'updated_by' ,
                            'name_en','name_unit_en','address_en','allay_en','village_no_en','road_en','province_name_en','amphur_name_en','district_name_en','formula_en',
                            'attach_client_name',
                            'cer_type','certificate_path','certificate_file','certificate_newfile','documentId','signtureid','status_revoke','date_revoke','reason_revoke','user_revoke'
                            ];

    public function CertiIBCostTo()
    {
        return $this->belongsTo(CertiIb::class,'app_certi_ib_id');
    }

    public function getStatusTitleAttribute() {
        $list = '';
        if($this->status == 19){
            $list =  'ลงนามเรียบร้อย';
        }else{
            $list =  'ออกใบรับรอง และ ลงนาม';
        }
        
        return  $list ?? '-';
    }  
    public function certificate_ib_export()
    {
        return $this->hasMany(CertiIBFileAll::class, 'certificate_exports_id');
    }
    public function certificate_ib_export_mapreq()
    {
        return $this->hasMany(CertiIbExportMapreq::class, 'certificate_exports_id');
    }

    public function getCertiIBFileAllAttribute() {

        $files = $this->certificate_ib_export_mapreq()->select('app_certi_ib_id')->groupBy('app_certi_ib_id')->get();
        if(!empty($files) && count($files) > 0){
              $certi =    $files->pluck('app_certi_ib_file_all')->flatten();
             $state1 = $certi->where('state', 1)->first();
            if(!empty($state1)){
                return $state1;
            }
        }
        return '';
    }

}
