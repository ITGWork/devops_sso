<?php

namespace App\Models\Bsection5;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\User;
use App\Models\Bsection5\TestTool;
use App\Models\Bsection5\TestItem;


class TestItemTools extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bsection5_test_item_tools';

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
    protected $fillable = [ 'bsection5_test_item_id', 'test_tools_id' ];

    /*
      Sorting
    */
    public $sortable = [ 'bsection5_test_item_id', 'test_tools_id' ];

    public function test_tool(){
        return $this->belongsTo(TestTool::class, 'test_tools_id');
    }

    public function test_item(){
        return $this->belongsTo(TestItem::class, 'bsection5_test_item_id', 'id');
    }

    public function getToolsNameAttribute() {
        return @$this->test_tool->title;
    }
}
