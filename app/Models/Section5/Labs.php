<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Section5\LabsScope;
use App\Models\Section5\LabsHistory;
use App\Models\Section5\ApplicationLabBoardApprove;

use App\Models\Basic\Province;
use App\Models\Basic\District;
use App\Models\Basic\Subdistrict;

use App\Models\Sso\User AS SSO_USER;
use App\Models\Tis\Standard;

use Carbon\Carbon;

class Labs extends Model
{
    protected $table = 'section5_labs';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'name',
                            'taxid',
                            'lab_code',
                            'lab_name',
                            'lab_user_id',
                            'lab_address',
                            'lab_moo',
                            'lab_soi',
                            'lab_building',
                            'lab_road',
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
                            'lab_start_date',
                            'lab_end_date',
                            'state',
                            'ref_lab_application_no',
                            'created_by',
                            'updated_by'
                        ];

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
        return !empty($this->lab_district)?$this->lab_district->AMPHUR_NAME:null;
    }

    public function getLabProvinceNameAttribute() {
        return !empty($this->lab_province)?$this->lab_province->PROVINCE_NAME:null;
    }

    public function board_approve(){
        return $this->hasOne(ApplicationLabBoardApprove::class, 'application_no', 'ref_lab_application_no');
    }

    public function historys(){
        return $this->hasMany(LabsHistory::class, 'lab_id');
    }

    public function scope_standard(){
        return $this->hasMany(LabsScope::class, 'lab_id');
    }

    public function scope_standards(){
        return $this->hasMany(LabsScope::class, 'lab_id');
    }

    public function scope_standard_expire(){
        
        $expDate = Carbon::now()->subDays(60)->formatLocalized('%Y-%m-%d');
        // return $this->hasMany(LabsScope::class, 'lab_id')->where('state',1)->whereRaw('ABS(TO_DAYS(CURDATE())-TO_DAYS(end_date)) <= 20');
        // return $this->hasMany(LabsScope::class, 'lab_id')->whereNotNull('end_date')->where('state',1)->where('ABS(TO_DAYS(CURDATE())-TO_DAYS(end_date))', '<=', 20);
        // return $this->hasMany(LabsScope::class, 'lab_id')->whereNotNull('end_date')->where('state',1)->whereRaw('DATEDIFF(end_date,current_date) <= 60');
        return $this->hasMany(LabsScope::class, 'lab_id')->whereNotNull('end_date')->where('state',1)->whereDate('end_date', '<', $expDate); 
    }

    public function getScopeStandardAttribute(){

        $scope_standard = $this->labs_standard()->distinct()->get()->pluck('tis_tisno','tis_tisno')->implode(', ');
        return $scope_standard;
    }

    public function getScopeStandardActiveAttribute(){

        $scope_standard = $this->scope_standard()->select('tis_id')->where('state',1)->groupBy('tis_id')->get();
        $list = [];
        foreach( $scope_standard AS $item ){
            $tis_standards = $item->tis_standards;

            if( !is_null($tis_standards) ){
                $list[] = $tis_standards->tis_tisno;
            }

        }

        $txt = implode( ' ,',  $list );

        return $txt;
    }

    public function getScopeStandardHtmlAttribute(){

        $scope_standard = $this->scope_standard()->select('tis_id')->groupBy('tis_id')->get();
        $list = [];
        $show = [];
        $i_max = 0;
        foreach( $scope_standard AS $item ){
            $tis_standards = $item->tis_standards;

            if( !is_null($tis_standards) ){
                $list[$tis_standards->tb3_Tisno] = $tis_standards->tb3_Tisno;

                $i_max++;
                
                if($i_max <= 10){
                    $show[] = $tis_standards->tb3_Tisno;;
                }
            }

        }

        $txt = implode( ' ,',  $list );

        return '<span data-toggle="tooltip" data-placement="bottom" title="'.( $txt ).'">'.( implode( ' ,',  $show ) ).(  (  $i_max > 10 )?'....':'' ).'</span>';
    }

    /* Btn Switch Input*/
    public function getStateIconAttribute(){

        $btn = '';
        if ($this->state == 1 && ($this->scope_standard()->where('end_date', '>=', date('Y-m-d'))->count() >= 1) ) {
            $btn = '<i class="fa fa-check-circle fa-lg text-success"></i>';
        }else {
            $btn = '<i class="fa fa-times-circle fa-lg text-danger"></i>';
        }
        return $btn;

  	}

    //ข้อมูลผปก.
    public function user(){
        return $this->belongsTo(SSO_USER::class, 'lab_user_id');
    }

    public function labs_standard()
    {
        return $this->belongsToMany(Standard::class, (new LabsScope)->getTable() , 'lab_id', 'tis_id');
    }

    public function getContactDataAdressAttribute()
    {

        $lab_province = $this->lab_province;
        $lab_district = $this->lab_district;
        $lab_subdistrict = $this->lab_subdistrict;

        $text = '';
        $text .= (!empty($this->lab_address)?$this->lab_address:null);
        $text .= ' ';
        $text .= (!empty($this->lab_moo)?'หมู่ที่ '.$this->lab_moo:null);
        $text .= ' ';

        if(!is_null($this->agency_soi) &&  $this->agency_soi != '-'){
            $text .= (!empty($this->agency_soi)?'ตรอก/ซอย '.$this->agency_soi:null);
            $text .= ' ';
        }
        if(!is_null($this->lab_road) &&  $this->lab_road != '-'){
            $text .= (!empty($this->lab_road)?'ถนน '.$this->lab_road:null);
            $text .= ' ';
        }

        $subdistrict = (is_object($lab_province) && $lab_province->PROVINCE_ID == 1) ? 'แขวง' : 'ตำบล';
        $text .= (!empty($lab_subdistrict)?$subdistrict.' '.$lab_subdistrict->DISTRICT_NAME:null);
        $text .= ' ';

        $district_name = (is_object($lab_province) && $lab_province->PROVINCE_ID  == 1) ? 'เขต' : 'อำเภอ';
        $text .= (!empty($lab_district)?$district_name.' '.$lab_district->AMPHUR_NAME:null);
        $text .= ' ';

        $text .= (!empty($lab_province)?'จังหวัด '.$lab_province->PROVINCE_NAME:null);
        $text .= ' ';
        $text .= (!empty($this->lab_zipcode)?$this->lab_zipcode:null);
        $text .= (!empty($this->lab_phone)?'<div>โทรศัพท์ :'.$this->lab_phone.'</div>':null);
        $text .= (!empty($this->lab_fax)?'<div>โทรสาร :'.$this->lab_fax.'</div>':null);
        return  $text;
    }

    public function getScopesGroupAttribute()
    {
        return $this->scope_standards;
    }
}
