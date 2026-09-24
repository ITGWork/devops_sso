<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Basic\Province;
use App\Models\Basic\District;
use App\Models\Basic\Subdistrict;

use App\Models\Section5\ApplicationIbcbScope;
use App\Models\Section5\ApplicationIbcbStatus;
use App\Models\Section5\ApplicationIbcbAccept;

use App\User;
class ApplicationIbcb extends Model
{
    protected $table = 'section5_application_ibcb';

    protected $primaryKey = 'id';

    protected $fillable = [
        'application_no',
        'application_date',
        'application_status',
        'application_type',
        'applicant_taxid',
        'applicant_name',
        'applicant_date_niti',
        'hq_address',
        'hq_building',
        'hq_moo',
        'hq_soi',
        'hq_road',
        'hq_subdistrict_id',
        'hq_district_id',
        'hq_province_id',
        'hq_zipcode',
        'ibcb_name',
        'ibcb_address',
        'ibcb_building',
        'ibcb_moo',
        'ibcb_soi',
        'ibcb_road',
        'ibcb_subdistrict_id',
        'ibcb_district_id',
        'ibcb_province_id',
        'ibcb_zipcode',
        'ibcb_phone',
        'ibcb_fax',
        'co_name',
        'co_position',
        'co_mobile',
        'co_phone',
        'co_fax',
        'co_email',
        'audit_type',
        'created_by',
        'agent_id',
        'updated_by',
        'config_evidencce',
        'remarks_delete',
        'delete_by',
        'delete_at',
        'delete_state',
        'ibcb_id', 
        'ibcb_code',
        'applicant_request_type'
    ];

    public function user_created(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_agent(){
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function getCreaterNameAttribute(){
        if(!is_null($this->agent_id)){
            $user_agent = $this->user_agent;
            return !is_null($user_agent) ? $user_agent->name : '';
        }else{
            $user = $this->user_created;
            return !is_null($user) ? $user->name : '';
        }
    }

    public function hq_subdistrict(){
        return $this->belongsTo(Subdistrict::class, 'hq_subdistrict_id');
    }

    public function hq_district(){
        return $this->belongsTo(District::class,  'hq_district_id');
    }

    public function hq_province(){
        return $this->belongsTo(Province::class, 'hq_province_id');
    }

    public function ibcb_subdistrict(){
        return $this->belongsTo(Subdistrict::class, 'ibcb_subdistrict_id');
    }

    public function ibcb_district(){
        return $this->belongsTo(District::class,  'ibcb_district_id');
    }

    public function ibcb_province(){
        return $this->belongsTo(Province::class, 'ibcb_province_id');
    }

    public function getHqSubdistrictNameAttribute() {
        return !empty($this->hq_subdistrict)?$this->hq_subdistrict->DISTRICT_NAME:null;
    }

    public function getHqDistrictNameAttribute() {
        return !empty($this->hq_district)?str_replace('เขต','',$this->hq_district->AMPHUR_NAME):null;
    }

    public function getHqProvinceNameAttribute() {
        return !empty($this->hq_province)?$this->hq_province->PROVINCE_NAME:null;
    }

    public function getHQPostcodeNameAttribute() {
        return !empty($this->hq_zipcode)?$this->hq_zipcode:null;
    }

    public function getIbcbSubdistrictNameAttribute() {
        return !empty($this->ibcb_subdistrict)?$this->ibcb_subdistrict->DISTRICT_NAME:null;
    }

    public function getIbcbDistrictNameAttribute() {
        return !empty($this->ibcb_district)?str_replace('เขต','',$this->ibcb_district->AMPHUR_NAME):null;
    }

    public function getIbcbProvinceNameAttribute() {
        return !empty($this->ibcb_province)?$this->ibcb_province->PROVINCE_NAME:null;
    }

    public function getIbcbPostcodeNameAttribute() {
        return !empty($this->ibcb_zipcode)?$this->ibcb_zipcode:null;
    }

    public function scopes_group(){
        return $this->hasMany(ApplicationIbcbScope::class, 'application_id');
    }

    public function getScopeGroupAttribute(){

        $app_scope = $this->scopes_group()->select('branch_group_id')->groupBy('branch_group_id')->get();
        $list = [];
        foreach( $app_scope AS $item ){
            $bs_branch_group = $item->bs_branch_group;

            if( !is_null($bs_branch_group) ){
                $list[] = $bs_branch_group->title;
            }

        }

        $txt = implode( ' ,',  $list );

        return $txt;
    }


    public function application_status_list(){
        return $this->belongsTo(ApplicationIbcbStatus::class, 'application_status');
    }

    public function getStatusTitleAttribute() {
        $status = @$this->application_status_list->title;
        if($this->application_status == 99 && !empty($this->remarks_delete)){
            $status .= "<br>({$this->remarks_delete})";
        }
        return $status;
    }

    public function application_ibcb_accepts(){
        return $this->hasMany(ApplicationIbcbAccept::class, 'application_id');
    }  

    public function getFullNameTaxIdAttribute(){
        $html = '';
        $html .= !empty($this->applicant_name)?$this->applicant_name:'-';
        $html .= '<br>';
        $html .= !empty($this->applicant_taxid)?'('.$this->applicant_taxid.')':'-';
  
          return $html;
    }
    
}
