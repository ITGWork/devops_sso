<?php

namespace App\Models\Csurv;

use Illuminate\Database\Eloquent\Model;

/**
 * ผลการตรวจ QC สำหรับ E-Surveillance (IB/CB)
 * Table: control_follow_ib_qc_results (admin_dbtest)
 */
class ControlFollowIbQcResult extends Model
{
    protected $table = 'control_follow_ib_qc_results';

    public $timestamps = false;

    protected $fillable = [
        'list_id',
        'item_id',
        'result_text',
        'summary_status',
        'summary_detail',
        'created',
        'created_by',
    ];
}
