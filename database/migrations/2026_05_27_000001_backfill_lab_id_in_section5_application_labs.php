<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BackfillLabIdInSection5ApplicationLabs extends Migration
{
    public function up()
    {
        // เติม lab_id ให้ record ที่มี lab_id = NULL แต่มี lab_code ที่ตรงกับ section5_labs
        DB::statement("
            UPDATE section5_application_labs AS app
            JOIN section5_labs AS lab ON lab.lab_code = app.lab_code
            SET app.lab_id = lab.id
            WHERE app.lab_id IS NULL
              AND app.lab_code IS NOT NULL
              AND app.lab_code != ''
        ");
    }

    public function down()
    {
        // ไม่ reverse เพราะเป็นการเติมข้อมูลที่ควรมีอยู่แล้ว
    }
}
