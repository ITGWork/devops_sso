<?php

namespace App\Models\Officer;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\User;
use App\Models\Basic\SubDepartment;

class Department extends Model
{

    use Sortable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'department';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'did';

    /* ประเภท primaryKey */
    public $keyType = 'string';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['depart_name', 'depart_nameShort', 'depart_name_engshort'];

    //Add Auto
    protected $appends = array('sub_departments');

    /* Relation SubDepartment */
    public function sub_department(){
      return $this->hasMany(SubDepartment::class, 'did', 'did');
    }

    public function getSubDepartmentsAttribute() {
  		return $this->sub_department->pluck('sub_departname', 'sub_id');
  	}

}
