<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Basic\Province;
use App\Models\Basic\District;
use App\Models\Basic\Subdistrict;

use App\Models\Section5\IbcbsScope;
use App\Models\Section5\IbcbsCertificate;

use App\Models\Basic\Branch;
use App\Models\Basic\BranchGroup;

class Ibcbs extends Model
{
    protected $table = 'section5_ibcbs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'ibcb_code',
        'ibcb_type',
        'name',
        'taxid',
        'ibcb_name',
        'ibcb_user_id',
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
        'ibcb_start_date',
        'ibcb_end_date',
        'state',
        'ref_ibcb_application_no',
        'created_by',
        'updated_by',
        'type'
    ];

    public function ibcb_subdistrict(){
        return $this->belongsTo(Subdistrict::class, 'ibcb_subdistrict_id');
    }

    public function ibcb_district(){
        return $this->belongsTo(District::class,  'ibcb_district_id');
    }

    public function ibcb_province(){
        return $this->belongsTo(Province::class, 'ibcb_province_id');
    }

    public function getIbcbSubdistrictNameAttribute() {
        return !empty($this->ibcb_subdistrict)?$this->ibcb_subdistrict->DISTRICT_NAME:null;
    }

    public function getIbcbDistrictNameAttribute() {
        return !empty($this->ibcb_district)?$this->ibcb_district->AMPHUR_NAME:null;
    }

    public function getIbcbProvinceNameAttribute() {
        return !empty($this->ibcb_province)?$this->ibcb_province->PROVINCE_NAME:null;
    }

    public function getIbcbPostcodeNameAttribute() {
        return !empty($this->ibcb_zipcode)?$this->ibcb_zipcode:null;
    }

    /* Btn Switch Input*/
    public function getStateIconAttribute(){

        $btn = '';
        if ($this->state == 1) {
            $btn = '<i class="fa fa-check-circle fa-lg text-success"></i>';
        }else {
            $btn = '<i class="fa fa-times-circle fa-lg text-danger"></i>';
        }
        return $btn;

  	}

    public function scopes_group(){
        return $this->hasMany(IbcbsScope::class, 'ibcb_id');
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

        $txt = implode( ', ',  $list );

        return $txt;
    }

    public function ibcbs_certify(){
        return $this->hasMany(IbcbsCertificate::class, 'ibcb_id');
    }

    public function getContactDataAdressAttribute()
    {

        $ibcb_province = $this->ibcb_province;
        $ibcb_district = $this->ibcb_district;
        $ibcb_subdistrict = $this->ibcb_subdistrict;

        $text = '';
        $text .= (!empty($this->ibcb_address)?$this->ibcb_address:null);
        $text .= ' ';
        $text .= (!empty($this->ibcb_moo)?'หมู่ที่ '.$this->ibcb_moo:null);
        $text .= ' ';

        if(!is_null($this->agency_soi) &&  $this->agency_soi != '-'){
            $text .= (!empty($this->agency_soi)?'ตรอก/ซอย '.$this->agency_soi:null);
            $text .= ' ';
        }
        if(!is_null($this->ibcb_road) &&  $this->ibcb_road != '-'){
            $text .= (!empty($this->ibcb_road)?'ถนน '.$this->ibcb_road:null);
            $text .= ' ';
        }

        $subdistrict = (is_object($ibcb_province) && $ibcb_province->PROVINCE_ID == 1) ? 'แขวง' : 'ตำบล';
        $text .= (!empty($ibcb_subdistrict)?$subdistrict.' '.$ibcb_subdistrict->DISTRICT_NAME:null);
        $text .= ' ';

        $district_name = (is_object($ibcb_province) && $ibcb_province->PROVINCE_ID  == 1) ? 'เขต' : 'อำเภอ';
        $text .= (!empty($ibcb_district)?$district_name.' '.$ibcb_district->AMPHUR_NAME:null);
        $text .= ' ';

        $text .= (!empty($ibcb_province)?'จังหวัด '.$ibcb_province->PROVINCE_NAME:null);
        $text .= ' ';
        $text .= (!empty($this->ibcb_zipcode)?$this->ibcb_zipcode:null);
        $text .= (!empty($this->ibcb_phone)?'<div>โทรศัพท์ :'.$this->ibcb_phone.'</div>':null);
        $text .= (!empty($this->ibcb_fax)?'<div>โทรสาร :'.$this->ibcb_fax.'</div>':null);
        return  $text;
    }


    public function ibcbs_scope_tis()
    {
        return $this->belongsToMany(IbcbsScopeTis::class, (new IbcbsScope)->getTable() , 'ibcb_id', 'id');
    }

    public function ibcbs_scope_detail()
    {
        return $this->belongsToMany(IbcbsScopeDetail::class, (new IbcbsScope)->getTable() , 'ibcb_id', 'id');
    }

    public function ibcb_scope(){
        return $this->hasMany(IbcbsScope::class, 'ibcb_id');
    }

    public function ibcbs_branch_group()
    {
        return $this->belongsToMany(BranchGroup::class, (new IbcbsScope)->getTable() , 'ibcb_id', 'branch_group_id');
    }

    public function ibcbs_branch()
    {
        return $this->belongsToMany(Branch::class, (new IbcbsScopeDetail)->getTable() , 'ibcb_id', 'branch_id');
    }

    public function getBranchGroupBranchNameAttribute() {

        $expenses  = $this->ibcbs_branch->groupBy('BranchGroupName');

        $html = '';

        foreach($expenses as $bs_branch_group => $datas){
            $html .= ($bs_branch_group)."<br>";
            $arr = $datas->pluck('title')->implode(', ');
            $html .= "<small><i>(".$arr.")</i></small><br>";
        }

        return $html;
    }


}
