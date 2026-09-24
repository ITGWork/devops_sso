<?php

namespace App\Models\Elicense;

use Illuminate\Database\Eloquent\Model;

class RosUsers extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table = 'ros_users';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'username', 'email', 'password', 'tax_number', 'nationality',
        'date_of_birth', 'date_niti', 'prefix_name', 'prefix_text',
        'address_no', 'building', 'street', 'moo', 'soi', 'subdistrict', 'district', 'province', 'zipcode',
        'tel', 'fax', 'id_card_no',
        'head_address_no', 'head_building', 'head_moo', 'head_soi', 'head_street',
        'head_subdistrict', 'head_district', 'head_province', 'head_zipcode',
        'head_tel', 'head_fax',
        'register_no', 'commercial_register_no',
        'department_id',
        'contact_name', 'contact_tax_id', 'contact_prefix_name', 'contact_prefix_text',
        'contact_first_name', 'contact_last_name',
        'contact_tel', 'contact_fax', 'contact_phone_number',
        // คอลัมน์ NOT NULL ไม่มี default ในตาราง ros_users (Joomla #__users) ต้องใส่เสมอตอน create()
        'block', 'sendEmail', 'registerDate', 'activation',
        'applicanttype_id', 'person_type', 'branch_type', 'branch_code',
        'person_first_name', 'person_last_name',
        'personfile', 'corporatefile',
        'params',
        'agency_tel', 'authorize_data',
    ];
}
