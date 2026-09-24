<?php

namespace App\Models\Section5;

use App\Models\Basic\Tis;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tis\Standard;

use App\Models\Bsection5\TestTool;
use App\Models\Bsection5\TestItemTools;
use App\Models\Bsection5\TestItem;

class ApplicationLabScope extends Model
{
    protected $table = 'section5_application_labs_scope';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'application_lab_id',
        'application_no',
        'tis_tisno',
        'test_item_id',
        'test_tools_id',
        'test_tools_no',
        'capacity',
        'range',
        'true_value',
        'fault_value',
        'tis_id',
        'audit_result',
        'remark',
        'test_duration',
        'test_price',
        'lab_id',
        'lab_code',
        'type',
        'remarks_reduce',
        'live_scope_id',
        'test_price_per_set',
        'test_method_type',
        'test_method_other',
        'lab_remark'

    ];

    public function standards(){
        return $this->belongsTo(Tis::class, 'tis_id');
    }  

    public function test_items(){
        return $this->belongsTo(TestItem::class, 'test_item_id');
    }

    public function getTestItemFullNameAttribute() {

        $test_items = $this->test_items;
        $mains    = !empty( $test_items->main_test_item )?$test_items->main_test_item:null;
        $mains_title = !is_null($mains) ? ( ( !empty( $mains->no )?$mains->no.' ' :null ).$mains->title ) : '' ;

        return ( !empty( $test_items->no )?$test_items->no.' ' :'' ).$test_items->title.( !is_null($mains) ? ' <em>(ภายใต้หัวข้อทดสอบ '.$mains_title.')</em>' : '' );
    }

    
    public function getTestItemNameAttribute() {
        return @$this->test_items->title;
    }

    public function test_tools(){
        return $this->belongsTo(TestTool::class, 'test_tools_id');
    }

    public function getToolsNameAttribute() {
        return @$this->test_tools->title;
    }

    public function application_lab(){
        return $this->belongsTo(ApplicationLab::class, 'application_lab_id');
    }

    public function getTestItemMainAttribute() {
        return @$this->test_items->main_test_item;
    }

    public function getScopeTypeTitleAttribute() {
        $type = @$this->type ?? @$this->application_lab->applicant_type;
        $type_arr = [ '1' => 'ขอบข่ายเดิม', '2' => 'ขอเพิ่มขอบข่าย', '3' => 'ขอลดขอบข่าย' ];
        return array_key_exists( $type,  $type_arr )?$type_arr[ $type ]:'-';
    }

    public function getStateIconAttribute(){
        return '<i class="fa fa-clock-o fa-lg text-warning" title="อยู่ระหว่างดำเนินการ"></i>';
    }
}
