<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Section5\Section5ApplicationInspectorsScope;
use App\Models\Basic\Subdistrict;
use App\Models\Basic\District;
use App\Models\Basic\Province;
use App\User;

class Section5ApplicationInspector extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_application_inspectors';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
                            'application_no',
                            'application_date',
                            'application_status',
                            'applicant_prefix',
                            'applicant_first_name',
                            'applicant_last_name',
                            'applicant_full_name',
                            'applicant_taxid',
                            'applicant_date_of_birth',
                            'applicant_address',
                            'applicant_moo',
                            'applicant_soi',
                            'applicant_road',
                            'applicant_subdistrict',
                            'applicant_district',
                            'applicant_province',
                            'applicant_zipcode',
                            'applicant_position',
                            'applicant_phone',
                            'applicant_fax',
                            'applicant_mobile',
                            'applicant_email',
                            'agency_id',
                            'agency_name',
                            'agency_taxid',
                            'agency_address',
                            'agency_moo',
                            'agency_soi',
                            'agency_road',
                            'agency_subdistrict',
                            'agency_district',
                            'agency_province',
                            'agency_zipcode',
                            'configs_evidence',
                            'created_by',
                            'updated_by',
                            'remarks_delete',
                            'delete_by',
                            'delete_at'
                        ];


    public function agency(){
        return $this->belongsTo(User::class, 'agency_name');
    }  

    public function sso_application_inspector_register_subs(){
        return $this->hasMany(Section5ApplicationInspectorsScope::class, 'application_id');
    }  

    public function section5_application_inspectors_status(){
        return $this->belongsTo(ApplicationInspectorStatus::class, 'application_status')->withDefault();
    }  

    public function agency_subdistricts(){
        return $this->belongsTo(Subdistrict::class, 'agency_subdistrict');
    }  
    
    public function agency_districts(){
        return $this->belongsTo(District::class,  'agency_district');
    }  

    public function agency_provinces(){
        return $this->belongsTo(Province::class, 'agency_province');
    }   

    public function getAgencyTitleAttribute() {
        return !empty($this->agency)?$this->agency->name.' | '.$this->agency->tax_number:null;
    }

    public function getAgencySubdistrictNameAttribute() {
        return !empty($this->agency_subdistricts)?$this->agency_subdistricts->DISTRICT_NAME:null;
    }

    public function getAgencyDistrictNameAttribute() {
        return !empty($this->agency_districts)?str_replace('เขต','',$this->agency_districts->AMPHUR_NAME):null;
    }

    public function getAgencyProvinceNameAttribute() {
        return !empty($this->agency_provinces)?$this->agency_provinces->PROVINCE_NAME:null;
    }

    public function getAgencyPostcodeNameAttribute() {
        return !empty($this->agency_postcode)?$this->agency_postcode:null;
    }

    public function getBranchGroupNameAttribute() {
        return implode(', ', @$this->sso_application_inspector_register_subs->pluck('BranchGroupName')->toArray());
    }

    public function getBranchNameAttribute() {
        return implode(', ', @$this->sso_application_inspector_register_subs->pluck('BranchName')->toArray());
    }

    // public function getBranchGroupBranchNameAttribute() {
    //     $expenses  = $this->sso_application_inspector_register_subs->groupBy('BranchGroupName');
    //     $html = '';
           
    //         foreach($expenses as $k1=> $datas){
    //             $html .= $k1."<br>";
    //             $arr = [];
    //             foreach($datas as $val){
    //                 $arr[] = $val->BranchName;
    //             }
    //             $html .= "<small><i>(".implode(', ', $arr).")</i></small><br>";
    //         }

    //     return $html;
    // }

    public function getBranchGroupBranchNameAttribute() {
        $expenses  = $this->sso_application_inspector_register_subs->groupBy('BranchGroupName');
        $html = '';
           
            foreach($expenses as $k1=> $datas){
                $html .= $k1."<br>";
                $arr = $datas->pluck('BranchName')->implode(', ');
                $html .= "<small><i>(".$arr.")</i></small><br>";
            }

        return $html;
    }


    public function getAppStatusAttribute(){
        $status = @$this->section5_application_inspectors_status->title;
        if($this->application_status == 11 && !empty($this->remarks_delete)){
            $status .= "<br>({$this->remarks_delete})";
        }
        return $status;
    }

    public function getFullNameTaxIdAttribute(){
      $html = '';
      $html .= !empty($this->applicant_full_name)?$this->applicant_full_name:'-';
      $html .= '<br>';
      $html .= !empty($this->applicant_taxid)?'('.$this->applicant_taxid.')':'-';

        return $html;
    }


}
