<?php

namespace App\Models\Agents;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\User;
use App\AttachFile;
use App\Models\Agents\AgentSystem;
class Agent extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'sso_agent';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'user_id',
                            'user_taxid',
                            'agent_id',
                            'agent_taxid',
                            'select_all',
                            'issue_type',
                            'start_date',
                            'end_date',
                            'state',
                            'head_name','head_address_no','head_village','head_moo','head_soi','head_subdistrict','head_district','head_province','head_telephone',
                            'agent_name','agent_address_no','agent_village','agent_moo','agent_soi','agent_subdistrict','agent_district','agent_province','agent_telephone',
                            'head_street', 'agent_street', 'created_by','agent_zipcode', 'head_zipcode',
                            'confirm_status','confirm_date','remarks_delete', 'delete_by', 'delete_at','head_mobile', 'agent_mobile'
                        ];
    /*
      Sorting
    */
    public $sortable = [
                            'user_id',
                            'user_taxid',
                            'agent_id',
                            'agent_taxid',
                            'select_all',
                            'issue_type',
                            'start_date',
                            'end_date',
                            'state'  ,
                            'head_name','head_address_no','head_village','head_moo','head_soi','head_subdistrict','head_district','head_province','head_telephone',
                            'agent_name','agent_address_no','agent_village','agent_moo','agent_soi','agent_subdistrict','agent_district','agent_province','agent_telephone',
                            'confirm_status','confirm_date','revoke_date','revoke_detail','revoke_by', 'head_street', 'agent_street', 'created_by', 'agent_zipcode', 'head_zipcode',
                            'remarks_delete', 'delete_by', 'delete_at','head_mobile', 'agent_mobile'
                        ];

    public function user_head_created(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function user_agent_created(){
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function agent_detail(){
        return $this->hasMany(AgentSystem::class, 'sso_agent_id');
    }

    public function getStateTextAttribute(){
        $state = '';
         if ($this->state == 1) {
            $state = 'มอบสิทธิ์';
        }else if ($this->state == 2) {
            $state = 'ดำเนินการตามรับมอบ';
        }else if ($this->state == 3) {
            $state = 'สิ้นสุดการดำเนินการ';
        }else if ($this->state == 4) {
            $state = 'หมดอายุ';
        }else if ($this->state == 5) {
            $state = 'ไม่ยืนยันการรับมอบ';
        }else {
            $state = '';
        }
        return $state;
    }

    public function getAgentAddressAttribute(){

        $txt = '';

        $province_txt = str_replace(' ', '', $this->agent_province );

        if ( !empty( $this->agent_address_no ) ) {
            $txt .= 'เลขที่ '.$this->agent_address_no.' ';
        }

        if ( !empty( $this->agent_village ) ) {
            $txt .= 'อาคาร/หมู่บ้าน '.$this->agent_village.' ';
        }

        if ( !empty( $this->agent_soi ) ) {
            $txt .= 'ตรอก/ซอย '.$this->agent_soi.' ';
        }

        if ( !empty( $this->agent_moo ) ) {
            $txt .= 'หมู่ '.$this->agent_moo.' ';
        }

        if ( !empty( $this->agent_street ) ) {
            $txt .= 'ถนน '.$this->agent_street.' ';
        }

        if ( !empty( $this->agent_subdistrict ) ) {

            if( $province_txt  == 'กรุงเทพมหานคร' ){
                $txt .= 'แขวง '.$this->agent_subdistrict.' ';
            }else{
                $txt .= 'ตำบล '.$this->agent_subdistrict.' ';
            }

        }

        if ( !empty( $this->agent_district ) ) {

            if( $province_txt  == 'กรุงเทพมหานคร' ){
                $txt .= 'เขต '.$this->agent_district.' ';
            }else{
                $txt .= 'อำเภอ '.$this->agent_district.' ';
            }

        }

        if ( !empty( $this->agent_province ) ) {

            if( $province_txt  == 'กรุงเทพมหานคร' ){
                $txt .= $this->agent_province.' ';
            }else{
                $txt .= 'จังหวัด '.$this->agent_province.' ';
            }

        }

        if ( !empty( $this->agent_zipcode ) ) {
            $txt .= $this->agent_zipcode;
        }

        return $txt;

    }

    public function getAgentSystemAttribute(){

        $txt = '';
        if( !empty($this->select_all) ){
            $txt = 'ทุกระบบ';
        }else{
            $agent_detail = $this->agent_detail()->get();
            $list = [];
            foreach( $agent_detail AS $item ){
                $setting_system = $item->setting_system;

                if( !is_null($setting_system) ){
                    $list[] = $setting_system->title;
                }
            }


            $txt = implode( ' ,',  $list );

        }
        return $txt;
    }

    // เอกสารแนบ
     public function FileAttachSection2To()
     {

          $tb = new Agent;
         return $this->belongsTo(AttachFile::class, 'id','ref_id')
                                ->where('ref_table',$tb->getTable())
                                ->where('section',2)
                                ->orderby('id','desc');
     }


}
