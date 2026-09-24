<?php

namespace App\Models\Certify\ApplicantIB;

use Illuminate\Database\Eloquent\Model;

class CertiIb extends Model
{

    protected $table = "app_certi_ib";
    protected $primaryKey = 'id';
    protected $fillable = [
                            'org_name',
							'app_no',
                            'name',
                            'status',
                            'standard_change',
                            'type_unit',
                            'name_unit',
                            'checkbox_address',
                            'address',
                            'allay',
                            'village_no',
                            'road',
                            'province_id',
                            'amphur_id',
                            'district_id',
                            'postcode',
                            'tel',
                            'tel_fax',
                            'contactor_name',
                            'email',  
                            'contact_tel',
                            'telephone',
                            'petitioner',
                            'details',
                            'desc_delete',
                            'review',
                            'token',
                            'save_date',
                            'checkbox_confirm',
                            'created_by', //tb10_nsw_lite_trader
                            'updated_by', 
                            'created_at',
                            'updated_at',
                            'get_date',
                            'tax_id',
                            'agent_id'
                        ];
                         
}