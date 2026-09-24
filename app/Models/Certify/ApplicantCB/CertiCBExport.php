<?php

namespace App\Models\Certify\ApplicantCB;

use Illuminate\Database\Eloquent\Model;
use App\Models\Certify\ApplicantCB\CertiCb;
class CertiCBExport extends Model
{
    protected $table = 'app_certi_cb_export';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'app_certi_cb_id', //TB: app_certi_cb
                            'type_standard',
                            'app_no',
                            'certificate',
                            'cb_name',
                            'name_standard',
                            'radio_address',
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
                            'check_badge',
                            'accereditatio_no',
                            'accereditatio_no_en',
                            'date_start',
                            'date_end',
                            'check_badge',
                            'created_by',
                            'updated_by',
                            'name_en','name_standard_en','address_en','allay_en','village_no_en','road_en','province_name_en','amphur_name_en','district_name_en','formula_en',
                            'attach_client_name',
                            'cer_type','certificate_path','certificate_file','certificate_newfile','documentId','signtureid','status_revoke','date_revoke','reason_revoke','user_revoke'
                            ];
    public function CertiCbTo()
    {
        return $this->belongsTo(CertiCb::class,'app_certi_cb_id');
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
    
    public function certificate_cb_export_mapreq()
    {
        return $this->hasMany(CertiCbExportMapreq::class, 'certificate_exports_id');
    }

    public function getCertiCBFileAllAttribute() {

        $files = $this->certificate_cb_export_mapreq()->select('app_certi_cb_id')->groupBy('app_certi_cb_id')->get();
        if(!empty($files) && count($files) > 0){
              $certi =    $files->pluck('app_certi_cb_file_all')->flatten();
             $state1 = $certi->where('state', 1)->first();
            if(!empty($state1)){
                return $state1;
            }
        }
        return '';
    }
}
