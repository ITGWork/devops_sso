<?php

namespace App\Models\Elicense\Rform;

use Illuminate\Database\Eloquent\Model;

class FactoryDetail extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table = 'ros_rform_factory_detail';
    public $timestamps = false;

    public function factory()
    {
        return $this->belongsTo(Factory::class, 'factory_id', 'id');
    }

    public function inspections()
    {
        return $this->hasMany(FactoryInspection::class, 'factory_detail_id', 'id');
    }

    public static function status_list()
    {
        return \App\Models\Elicense\Basic\InspecStatus::getStatusList([5, 6, 7, 8, 10, 11]);
    }

    public function inspect_status_list()
    {
        return [
            1 => 'ตรวจโรงงาน',
            2 => 'แก้ไขข้อบกพร่อง',
            3 => 'ประเมินผลแล้ว',
            4 => 'จัดทำรายงานแล้ว',
        ];
    }

    public function inspect_result_list()
    {
        return [1 => 'ผ่าน', 2 => 'ไม่ผ่าน'];
    }
}
