<?php

namespace App\Models\Elicense\Rform;

use Illuminate\Database\Eloquent\Model;

class ProductLab extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table      = 'ros_rform_product_lab';
    public $timestamps    = false;

    protected $fillable = ['status', 'checking_by', 'checking_date', 'checking_comment'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function lab()
    {
        return $this->belongsTo(\App\Models\Elicense\RosUsers::class, 'lab_id');
    }

    public function status_list()
    {
        return [
            '1' => '<span class="label label-warning">รอการตอบรับ</span>',
            '2' => '<span class="label label-info">รับคำขอ</span>',
            '3' => '<span class="label label-danger">ไม่รับคำขอ</span>',
            '4' => '<span class="label label-default">ยกเลิก</span>',
            '5' => '<span class="label label-primary">อยู่ระหว่างการทดสอบ</span>',
            '6' => '<span class="label label-warning">ขอตัวอย่างเพิ่มเติม</span>',
            '7' => '<span class="label label-info">แจ้งผล</span>',
            '8' => '<span class="label label-success">สรุปผล</span>',
        ];
    }
}
