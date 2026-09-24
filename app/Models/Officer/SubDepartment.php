<?php

namespace App\Models\Officer;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

use App\User;
use App\Models\Officer\Department;

class SubDepartment extends Model
{

    use Sortable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sub_department';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'sub_id';

    /* ประเภท primaryKey */
    public $keyType = 'string';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['did', 'sub_departname', 'sub_depart_shortname'];

    /*
      Department Relation
    */
    public function department(){
      return $this->belongsTo(Department::class, 'did', 'did');
    }

}
