<?php

namespace App\Models\Csurv;

use Illuminate\Database\Eloquent\Model;

/**
 * รายงานผลการตรวจโรงงาน สำหรับ E-Surveillance (IB/CB)
 * Table: control_follow_ib_inspection (admin_dbtest)
 */
class ControlFollowIbInspection extends Model
{
    protected $table = 'control_follow_ib_inspection';

    // ใช้ timestamps แบบ custom ไม่ใช่ created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'list_id',
        'subject',
        'ref_no',
        'applicant_data',
        'factory_data',
        'product_scope',
        'result_data',
        'inspect_final_result',
        'inspect_comment',
        'inspect_report_file',
        'created',
        'created_by',
        'modified',
        'modified_by',
    ];

    protected $casts = [
        'inspect_report_file' => 'array',
    ];
}
