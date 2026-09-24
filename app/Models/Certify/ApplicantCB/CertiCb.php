<?php

namespace App\Models\Certify\ApplicantCB;

use Illuminate\Database\Eloquent\Model;

class CertiCb extends Model
{

    protected $table = "app_certi_cb";
    protected $primaryKey = 'id';
    protected $fillable = [
                            'app_no',
                            'name',
                            'cb_name',
                            'status',
                            'standard_change',
                            'type_standard',
                            'name_standard',
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
                             'check_badge',
                             'created_by', //tb10_nsw_lite_trader
                             'updated_by',
                             'get_date',
                             'tax_id','agent_id'

                            ];

}
