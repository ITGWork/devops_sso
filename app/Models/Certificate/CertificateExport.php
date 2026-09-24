<?php

namespace App\Models\Certificate;

use App\Models\Certificate\CertiLab;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Certificate\CertLabsFileAll;
class CertificateExport extends Model
{
    use SoftDeletes;
    protected $table = "certificate_exports";
    protected $primaryKey = 'id';
    protected $fillable = ['request_number','lang','certificate_no','status','certificate_order','certificate_for',
                            'org_name','lab_name','address_no',     'address_moo','address_soi', 'address_road','address_province', 'address_district', 
                            'address_subdistrict', 'address_postcode','formula','accereditatio_no_en',
                            'accereditatio_no', 'certificate_date_start','certificate_date_end', 'certificate_date_first', 'issue_no', 'scope_permanent',
                            'scope_site', 'scope_temporary', 'scope_mobile', 'attachs', 'lab_type', 'radio_address','attachs_client_name',
                            'title_en','lab_name_en','address_no_en','address_moo_en','address_soi_en','address_road_en','address_province_en','address_district_en','address_subdistrict_en','formula_en'
                         ];

    public function cert_labs_file_all(){
        return $this->hasMany(CertLabsFileAll::class, 'ref_id','id')->where('ref_table', $this->getTable());
    }

    public function CertiLabTo()
    {
        return $this->belongsTo(CertiLab::class,'certificate_for');
    }

    public function getLabTypeNameAttribute()
    {
        if ($this->lab_type == '2'){
            $text   = "IB";
        }elseif ($this->lab_type == '1'){
            $text   = "CB";
        }elseif ($this->lab_type == '3'){
            $text   = "Lab ทดสอบ";
        }elseif ($this->lab_type == '4'){
            $text   = "Lab สอบเทียบ";
        }else{
            $text   = "N/A";
        }

        return $text;
    }

    public function getStatusTextAttribute()
    {
        if ($this->status == 0){
            return "ออกใบรับรอง และ ลงนาม"; //จัดทำใบรับรอง
        }elseif ($this->status == 1){
            return "ตรวจสอบความถูกต้อง";
        }elseif ($this->status == 2){
            return "ออกใบรับรองและลงนาม";
        }elseif ($this->status == 3){
            return "ลงนามเรียบร้อย";
        }else{
            return "N/A";
        }
    }

    public function certificate_lab_export_mapreq()
    {
        return $this->hasMany(CertiLabExportMapreq::class, 'certificate_exports_id');
    }

    public function getCertiLabFileAllAttribute() {

        $files = $this->certificate_lab_export_mapreq()->select('app_certi_lab_id')->groupBy('app_certi_lab_id')->get();
        if(!empty($files) && count($files) > 0){
              $certi =    $files->pluck('app_certi_lab_file_all')->flatten();
             $state1 = $certi->where('state', 1)->last();
            if(!empty($state1)){
                return $state1;
            }
        }
        return '';
    }
}
