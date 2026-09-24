<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Basic\Province;
use App\Models\Basic\District;
use App\Models\Basic\Subdistrict;
use App\Models\Basic\Prefix;

use App\Models\Section5\InspectorsScope;

use App\User;
use stdClass;

class Inspectors extends Model
{
        /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_inspectors';

    protected $primaryKey = 'id';

    protected $fillable = [
        'inspectors_code',
        'inspectors_prefix',
        'inspectors_first_name',
        'inspectors_last_name',
        'inspectors_taxid',
        'inspectors_address',
        'inspectors_moo',
        'inspectors_soi',
        'inspectors_road',
        'inspectors_subdistrict',
        'inspectors_district',
        'inspectors_province',
        'inspectors_zipcode',
        'inspectors_position',
        'inspectors_phone',
        'inspectors_fax',
        'inspectors_mobile',
        'inspectors_email',
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
        'inspector_first_date',
        'state',
        'ref_inspector_application_no',
        'created_by',
        'updated_by'

    ];

    public function inspectors_subdistricts(){
        return $this->belongsTo(Subdistrict::class, 'inspectors_subdistrict');
    }

    public function inspectors_districts(){
        return $this->belongsTo(District::class,  'inspectors_district');
    }

    public function inspectors_provinces(){
        return $this->belongsTo(Province::class, 'inspectors_province');
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

    public function getInspectorSubdistrictNameAttribute() {
        return !empty($this->inspectors_subdistricts)?$this->inspectors_subdistricts->DISTRICT_NAME:null;
    }

    public function getInspectorDistrictNameAttribute() {
        return !empty($this->inspectors_districts)?$this->inspectors_districts->AMPHUR_NAME:null;
    }

    public function getInspectorProvinceNameAttribute() {
        return !empty($this->inspectors_provinces)?$this->inspectors_provinces->PROVINCE_NAME:null;
    }

    public function getAgencySubdistrictNameAttribute() {
        return !empty($this->agency_subdistricts)?$this->agency_subdistricts->DISTRICT_NAME:null;
    }

    public function getAgencyDistrictNameAttribute() {
        return !empty($this->agency_districts)?$this->agency_districts->AMPHUR_NAME:null;
    }

    public function getAgencyProvinceNameAttribute() {
        return !empty($this->agency_provinces)?$this->agency_provinces->PROVINCE_NAME:null;
    }

    public function getAgencyFullNameAttribute() {
        return (!empty($this->inspectors_prefix)?$this->inspectors_prefix:null).($this->inspectors_first_name).' '.($this->inspectors_last_name);
    }

    public function scopes(){
        return $this->hasMany(InspectorsScope::class, 'inspectors_id');
    }

    public function getScopeGroupAttribute(){

        $app_scope = $this->scopes()->select('branch_group_id')->groupBy('branch_group_id')->get();
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

    public function getScopeShowAttribute(){

        $app_scope = $this->scopes()->select('branch_group_id')->groupBy('branch_group_id')->get();

        $html = '<ul  class="list-unstyled">';
        foreach( $app_scope AS $item ){
            $bs_branch_group = $item->bs_branch_group;

            if( !is_null($bs_branch_group) ){

                $html .= '<li>'.($bs_branch_group->title).'</li>';
                $scope = $this->scopes()->where('branch_group_id', $bs_branch_group->id )->select('branch_id')->get();
                $html .= '<li>';
                $html .= '<ul>';
                $list = [];
                foreach( $scope as $branch ){
                    $bs_branch =  $branch->bs_branch;
                    $list[] = $bs_branch->title;
                }
                $html .= '<li>'.( implode( ' ,',  $list ) ).'</li>';
                $html .= '</ul>';
                $html .= '</li>';
            }

        }
        $html .= '</ul>';

        return $html;
    }

    public function getScopeDataSetAttribute(){
        $app_scope = $this->scopes()->select('branch_group_id')->groupBy('branch_group_id')->get();

        $list = [];
        foreach( $app_scope AS $item ){
            $bs_branch_group = $item->bs_branch_group;

            if( !is_null($bs_branch_group) ){
                $scope = $this->scopes()->where('branch_group_id', $bs_branch_group->id)->get();

                //เก็บรายสาขา
                $list_branch = [];
                foreach($scope as $branch){
                    $bs_branch = $branch->bs_branch;
                    if( !is_null($bs_branch) ){
                        $dataB = new stdClass;
                        $dataB->branch_title         = (string)$bs_branch->title;
                        $dataB->branch_id            = (string)$bs_branch->id;
                        $dataB->tis_nos             = $branch->ins_scopes_tis->pluck('tis_no')->toArray();//มาตรฐานมอก.
                        $list_branch[$bs_branch->id] = $dataB;

                    }
                }

                //เก็บสาขาผลิตภัณฑ์
                $data = new stdClass;
                $data->branch_group_title = (string)$bs_branch_group->title;
                $data->branch_group_id = (string)$bs_branch_group->id;
                $data->branch = $list_branch;
                $list[$bs_branch_group->id] = $data; //เก็บรายสาขาของสาขาผลิตภัณฑ์
            }

        }
        return $list;
    }

    public function getAgencyDataAdressAttribute()
    {

        $agency_provinces = $this->agency_provinces;
        $agency_districts = $this->agency_districts;
        $agency_subdistricts = $this->agency_subdistricts;


        $text = '';
        $text .= (!empty($this->agency_address)?$this->agency_address:null);
        $text .= ' ';
        $text .= (!empty($this->agency_moo)?'หมู่ที่ '.$this->agency_moo:null);
        $text .= ' ';

        if(!is_null($this->agency_soi) &&  $this->agency_soi != '-'){
            $text .= (!empty($this->agency_soi)?'ตรอก/ซอย '.$this->agency_soi:null);
            $text .= ' ';
        }
        if(!is_null($this->agency_road) &&  $this->agency_road != '-'){
            $text .= (!empty($this->agency_road)?'ถนน '.$this->agency_road:null);
            $text .= ' ';
        }

        $subdistrict = ($agency_provinces->PROVINCE_ID == 1) ? 'แขวง' : 'ตำบล';
        $text .= (!empty($agency_subdistricts)?$subdistrict.' '.$agency_subdistricts->DISTRICT_NAME:null);
        $text .= ' ';

        $district_name = ($agency_provinces->PROVINCE_ID  == 1) ? 'เขต' : 'อำเภอ';
        $text .= (!empty($agency_districts)?$district_name.' '.$agency_districts->AMPHUR_NAME:null);
        $text .= ' ';

        $text .= (!empty($agency_provinces)?'จังหวัด '.$agency_provinces->PROVINCE_NAME:null);
        $text .= ' ';
        $text .= (!empty($this->agency_zipcode)?$this->agency_zipcode:null);

        return  $text;
    }

    public function getStateIconAttribute(){

        $btn = '';
        if ($this->state == 1) {
            $btn = '<i class="fa fa-check-circle fa-lg text-success"></i>';
        }else {
            $btn = '<i class="fa fa-times-circle fa-lg text-danger"></i>';
        }
        return $btn;

  	}

    public function user_created(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getCreatedNameAttribute() {
  		return $this->user_created->reg_fname.' '.$this->user_created->reg_lname;
  	}

    public function getUpdatedNameAttribute() {
  		return @$this->user_updated->reg_fname.' '.@$this->user_updated->reg_lname;
  	}
}
