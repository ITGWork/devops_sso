<?php

namespace App\Models\Elicense\Rform;

use Illuminate\Database\Eloquent\Model;

class FactoryInspection extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table = 'ros_rform_factory_inspection';
    public $timestamps = false;

    protected $fillable = [
        'factory_detail_id',
        'start_inspect_date',
        'end_inspect_date',
        'result',
        'is_finalized',
        'defect',
        'remark',
        'att_name',
        'att_file',
        'created_by',
        'modified_by',
    ];

    public function factoryDetail()
    {
        return $this->belongsTo(FactoryDetail::class, 'factory_detail_id', 'id');
    }
}
