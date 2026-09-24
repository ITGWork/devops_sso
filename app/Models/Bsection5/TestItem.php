<?php

namespace App\Models\Bsection5;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\User;
use App\Models\Tis\Standard;
use App\Models\Bsection5\TestMethod;
use App\Models\Bsection5\Unit;
use App\Models\Bsection5\TestTool;
use App\Models\Bsection5\TestItemTools;
use Illuminate\Support\Facades\DB;

class TestItem extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bsection5_test_item';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 'tis_id', 'tis_tisno', 'title', 'type', 'no',  'unit_id', 'parent_id', 'test_method_id',  'test_tools_id', 'input_result',  'state', 'main_topic_id', 'level', 'criteria', 'amount_test_list', 'test_summary',  'created_by', 'updated_by' ];

    /*
      Sorting
    */
    public $sortable = [ 'tis_id', 'tis_tisno', 'title', 'type', 'no',  'unit_id', 'parent_id', 'test_method_id',  'test_tools_id', 'input_result',  'state', 'main_topic_id', 'level', 'criteria', 'amount_test_list', 'test_summary', 'created_by', 'updated_by'];

        /*
      User Relation
    */
    public function user_created(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getCreatedNameAttribute() {
        return $this->user_created->reg_fname.' '.$this->user_created->reg_lname;
    }

    public function getUpdatedNameAttribute() {
        return @$this->user_updated->reg_fname.' '.@$this->user_updated->reg_lname;
    }

    public function getTypeNameAttribute() {
        $type_arr = [ '1' => 'หัวข้อทดสอบ', '2' => 'รายทดสอบ' ];
        return array_key_exists( $this->type,  $type_arr )? $type_arr[ $this->type ]:null;
    }

    public function standard(){
        return $this->belongsTo(Standard::class, 'tis_id');
    }

    public function test_method(){
        return $this->belongsTo(TestMethod::class, 'test_method_id');
    }

    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function parent_test_item(){
        return $this->belongsTo(TestItem::class, 'parent_id');
    }

    
    public function main_test_item(){
        return $this->belongsTo(TestItem::class, 'main_topic_id');
    }

    /* Btn Switch Input*/
    public function getStateIconAttribute(){

        $btn = '';
        if ($this->state == 1) {
            $btn = '<div class="checkbox"><input class="js-switch" name="state" type="checkbox" value="'.$this->id.'" checked></div>';
        }else {
            $btn = '<div class="checkbox"><input class="js-switch" name="state" type="checkbox" value="'.$this->id.'"></div>';
        }
        return $btn;

  	}

    public function TestItemToolsData()
    {
        return $this->hasMany(TestItemTools::class,'bsection5_test_item_id');
    }

    public function getToolsNameAttribute() {

        $tools = $this->TestItemToolsData;

        $txt = '';
        $i =0;
        foreach(  $tools as $k => $item ){
            if(!empty($item->ToolsName)){
                $i++;
                $txt .= '<div>'.($i).'. '.(!empty($item->ToolsName)?$item->ToolsName:null).'</div>';
            }
        }

        return @$txt;
    }

    public function main_test_item_parent_data()
    {

        $orderby = "CAST(SUBSTRING_INDEX(no,'.',1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',2),'.',-1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',3),'.',-1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',4),'.',-1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',5),'.',-1) as UNSIGNED)";

        return $this->hasMany(TestItem::class,'main_topic_id', 'id')->orderby(DB::raw( $orderby ));
    }

    public function TestItemParentData()
    {
        $orderby = "CAST(SUBSTRING_INDEX(no,'.',1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',2),'.',-1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',3),'.',-1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',4),'.',-1) as UNSIGNED),";
        $orderby .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no,'.',5),'.',-1) as UNSIGNED)";

        return $this->hasMany(TestItem::class,'parent_id', 'id')->orderby(DB::raw( $orderby ));
    }


}
