<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Models\Basic\Province;
use App\Models\Basic\District;
use App\Models\Basic\Subdistrict;
use App\Models\Section5\ApplicationLabScope;
use App\Models\Section5\ApplicationLabStatus;
use App\Models\Section5\ApplicationLabAccept;
use App\Models\Section5\ApplicationLabBoardApprove;
use App\Models\Section5\Labs;

class ApplicationLab extends Model
{
    protected $table = 'section5_application_labs';

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
        'applicant_taxid',
        'applicant_name',
        'applicant_date_niti',
        'hq_address',
        'hq_moo',
        'hq_soi',
        'hq_road',
        'hq_building',
        'hq_subdistrict_id',
        'hq_district_id',
        'hq_province_id',
        'hq_zipcode',
        'lab_name',
        'lab_address',
        'lab_moo',
        'lab_soi',
        'lab_road',
        'lab_building',
        'lab_subdistrict_id',
        'lab_district_id',
        'lab_province_id',
        'lab_zipcode',
        'lab_phone',
        'lab_fax',
        'co_name',
        'co_position',
        'co_mobile',
        'co_phone',
        'co_fax',
        'co_email',
        'audit_type',
        'audit_date',
        'created_by',
        'agent_id',
        'updated_by',
        'config_evidencce',
        'remarks_delete',
        'delete_by',
        'delete_at',
        'delete_state',
        'applicant_type', 
        'lab_id', 
        'lab_code'
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

    public function getHQSubdistrictNameAttribute() {
        return !empty($this->hq_subdistrict)?$this->hq_subdistrict->DISTRICT_NAME:null;
    }

    public function getHQDistrictNameAttribute() {
        return !empty($this->hq_district)?str_replace('เขต','',$this->hq_district->AMPHUR_NAME):null;
    }

    public function getHQProvinceNameAttribute() {
        return !empty($this->hq_province)?$this->hq_province->PROVINCE_NAME:null;
    }

    public function getHQPostcodeNameAttribute() {
        return !empty($this->hq_zipcode)?$this->hq_zipcode:null;
    }

    public function lab_subdistrict(){
        return $this->belongsTo(Subdistrict::class, 'lab_subdistrict_id');
    }

    public function lab_district(){
        return $this->belongsTo(District::class,  'lab_district_id');
    }

    public function lab_province(){
        return $this->belongsTo(Province::class, 'lab_province_id');
    }

    public function getLabSubdistrictNameAttribute() {
        return !empty($this->lab_subdistrict)?$this->lab_subdistrict->DISTRICT_NAME:null;
    }

    public function getLabDistrictNameAttribute() {
        return !empty($this->lab_district)?str_replace('เขต','',$this->lab_district->AMPHUR_NAME):null;
    }

    public function getLabProvinceNameAttribute() {
        return !empty($this->lab_province)?$this->lab_province->PROVINCE_NAME:null;
    }

    public function getLabPostcodeNameAttribute() {
        return !empty($this->lab_zipcode)?$this->lab_zipcode:null;
    }

    public function app_scope_standard(){
        return $this->hasMany(ApplicationLabScope::class, 'application_lab_id');
    }

    public function getScopeStandardAttribute(){

        $app_scope_standard = $this->app_scope_standard()->select('tis_id')->groupBy('tis_id')->get();
        $list = [];
        foreach( $app_scope_standard AS $item ){
            $tis_standards = $item->standards;

            if( !is_null($tis_standards) ){
                $list[] = $tis_standards->tb3_Tisno;
            }

        }

        $txt = implode( ' ,',  $list );

        return $txt;
    }

    public function application_status_list(){
        return $this->belongsTo(ApplicationLabStatus::class, 'application_status');
    }

    public function getStatusTitleAttribute() {
        $status = @$this->application_status_list->title;
        if($this->application_status == 100 && !empty($this->remarks_delete)){
            $status .= "<br>({$this->remarks_delete})";
        }
        return $status;
    }

    public function app_accept(){
        return $this->hasMany(ApplicationLabAccept::class, 'application_lab_id');
    }

    public function board_approve(){
        return $this->hasOne(ApplicationLabBoardApprove::class, 'application_no', 'application_no');
    }

    public function lab(){
        return $this->hasOne(Labs::class, 'ref_lab_application_no', 'application_no');
    }
}
