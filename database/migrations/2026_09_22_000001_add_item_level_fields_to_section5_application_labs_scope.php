<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemLevelFieldsToSection5ApplicationLabsScope extends Migration
{
    // ข้อมูลระดับ "รายการทดสอบ" ที่ LAB กรอกตอนขอเพิ่มขอบข่าย (หน้า labs/show) — เก็บซ้ำในทุกแถวเครื่องมือของรายการนั้น
    public function up()
    {
        Schema::table('section5_application_labs_scope', function (Blueprint $table) {
            if (!Schema::hasColumn('section5_application_labs_scope', 'test_price_per_set')) {
                $table->text('test_price_per_set')->nullable();      // ราคาค่าทดสอบ/ต่อชุดตัวอย่าง (freetext)
            }
            if (!Schema::hasColumn('section5_application_labs_scope', 'test_method_type')) {
                $table->tinyInteger('test_method_type')->nullable(); // วิธีทดสอบของ LAB: 1=ตาม มอก., 2=วิธีเทียบเท่า, 3=อื่นๆ
            }
            if (!Schema::hasColumn('section5_application_labs_scope', 'test_method_other')) {
                $table->text('test_method_other')->nullable();       // ระบุวิธีทดสอบ เมื่อเลือก 3=อื่นๆ
            }
            if (!Schema::hasColumn('section5_application_labs_scope', 'lab_remark')) {
                $table->text('lab_remark')->nullable();              // หมายเหตุจาก LAB (ไม่ใช่ remark ของเจ้าหน้าที่)
            }
        });
    }

    public function down()
    {
        Schema::table('section5_application_labs_scope', function (Blueprint $table) {
            foreach (['test_price_per_set', 'test_method_type', 'test_method_other', 'lab_remark'] as $col) {
                if (Schema::hasColumn('section5_application_labs_scope', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
