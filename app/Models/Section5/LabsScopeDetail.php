<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Bsection5\TestTool;

class LabsScopeDetail extends Model
{
    protected $table = 'section5_labs_scopes_details';

    protected $primaryKey = 'id';

    protected $fillable = [ 

        'lab_id',
        'lab_code',
        'lab_scope_id',
        'test_tools_id',
        'test_tools_no',
        'capacity',
        'range',
        'true_value',
        'fault_value',
        'state',
        'ref_lab_application_no',
        'ref_lab_application_scope_id',
        'start_date',
        'end_date',
        'test_duration',
        'test_price',
        'type'
        
    ];

    public function test_tools(){
        return $this->belongsTo(TestTool::class, 'test_tools_id');
    } 

    public function getTestToolTitleAttribute() {
        return @$this->test_tools->title;
    }
}
