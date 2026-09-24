<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class SettingSystemGroup extends Model
{

    use Sortable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'setting_systems_group';

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
    protected $fillable = [
                           'title',
                           'ordering',
                           'created_by',
                           'updated_by',
                           'updated_at'
                          ];

    /*
      Sorting
    */
    public $sortable = [
                        'title',
                        'ordering',
                        'created_by',
                        'updated_by',
                        'updated_at'
                       ];

}
