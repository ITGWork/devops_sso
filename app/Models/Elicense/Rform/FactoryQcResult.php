<?php

namespace App\Models\Elicense\Rform;

use Illuminate\Database\Eloquent\Model;

class FactoryQcResult extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table = 'ros_rform_factory_qc_results';
    public $timestamps = false;

    protected $fillable = [
        'factory_id',
        'item_id',
        'result_text',
        'summary_status',
        'summary_detail',
        'created',
        'created_by',
    ];

    public function factory()
    {
        return $this->belongsTo(Factory::class, 'factory_id', 'id');
    }

    public function qcItem()
    {
        return $this->belongsTo(FactoryQcItem::class, 'item_id', 'id');
    }
}
