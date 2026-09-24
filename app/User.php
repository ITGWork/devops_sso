<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

use App\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Kyslik\ColumnSortable\Sortable;
use App\RoleUser;
use App\Sessions;
use App\Models\Agents\Agent;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sso_users';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'username', 'password',
        'picture', 'email',
        'contact_name', 'contact_tax_id',
        'contact_prefix_name', 'contact_prefix_text',
        'contact_first_name', 'contact_last_name',
        'contact_tel', 'contact_fax',
        'contact_phone_number', 'block',
        'sendEmail', 'registerDate',
        'lastvisitDate', 'params',
        'lastResetTime', 'resetCount',
        'applicanttype_id', 'date_niti',
        'person_type', 'tax_number',
        'nationality', 'date_of_birth',
        'branch_code', 'prefix_name',
        'prefix_text', 'person_first_name',
        'person_last_name', 'address_no',
        'building', 'street',
        'moo', 'soi',
        'subdistrict', 'district',
        'province', 'zipcode',
        'tel', 'fax',
        'contact_address_no', 'contact_building',
        'contact_street', 'contact_moo',
        'contact_soi', 'contact_subdistrict',
        'contact_district', 'contact_province',
        'contact_zipcode', 'personfile',
        'corporatefile', 'remember_token',
        'state', 'branch_type',
        'contact_position',
        'latitude','longitude','juristic_status','juristic_cause_quit','check_api',
        'name_en','address_en','moo_en','soi_en','street_en','subdistrict_en','district_en','province_en','zipcode_en',
        'contact_address_en','contact_moo_en','contact_soi_en','contact_street_en','contact_subdistrict_en','contact_district_en','contact_province_en','contact_zipcode_en',
        'i_industry_lastlogin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google2fa_status', 'google2fa_secret'
    ];

    public function profile(){
        return $this->hasOne(Profile::class);
    }

    public function permissionsList(){
        $roles = $this->roles;
        $permissions = [];
        foreach ($roles as $role){
            $permissions[] = $role->permissions()->pluck('name')->implode(',');
        }
       return collect($permissions);
    }

    public function permissions(){
        $permissions = [];
        $role = $this->roles->first();
        $permissions = $role->permissions()->get();
        return $permissions;
    }

    public function isAdmin(){
       $is_admin =$this->roles()->where('name','admin')->first();
       if($is_admin != null){
           $is_admin = true;
       }else{
           $is_admin = false;
       }
       return $is_admin;
    }

    //  รับมอบอำนาจ ยืนยันตัวตนเรีบยร้อยแล้ว
    public function agent_confirms(){
        return $this->hasMany(Agent::class, 'agent_id')->where('confirm_status','1');
    }

    public function getContactFullNameAttribute() {
        return trim($this->contact_prefix_text).trim($this->contact_first_name).' '.trim($this->contact_last_name);
    }

    public function getApplicantTypeTitleAttribute() {
            $applicanttype =  ['1'=>'นิติบุคคล','2'=>'บุคคลธรรมดา','3'=>'คณะบุคคล','4'=>'ส่วนราชการ','5'=>'อื่นๆ'];
        return  array_key_exists($this->applicanttype_id,$applicanttype) ?  $applicanttype[$this->applicanttype_id] : null;
    }

    public function getBranchTypeTitleAttribute() {
        $branch_types =  ['1' => 'สำนักงานใหญ่', '2' => 'สาขา'];
        return array_key_exists($this->branch_type,$branch_types)?$branch_types[$this->branch_type]:null;
    }

    public function getTypeaheadDropdownTitleAttribute() {
        $name = '';
        switch ($this->branch_type) {
            case 1:
                $name = $this->name.' | '.$this->tax_number.' | '.$this->BranchTypeTitle;
            break;
            case 2:
                $name = $this->name.' | '.$this->tax_number.' | '.$this->BranchTypeTitle.' ('.$this->branch_code.')';
            break;
            default:
                $name = $this->name.' | '.$this->tax_number;
        }
        return $name;
    }

    //ข้อมูลบัญชีผู้ใช้งานที่กำลังดำเนินการแทนให้อยู่ null = ดำเนินการในฐานะตัวเอง
    public function getActInsteadAttribute(){
        $session = Sessions::find(session()->getId());
        return $session->get_act_instead;
    }

}
