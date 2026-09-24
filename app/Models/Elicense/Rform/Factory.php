<?php

namespace App\Models\Elicense\Rform;

use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table = 'ros_rform_factory';
    public $timestamps = false;

    public function factory_detail()
    {
        return $this->hasMany(FactoryDetail::class, 'factory_id', 'id');
    }

    public function qcResults()
    {
        return $this->hasMany(FactoryQcResult::class, 'factory_id', 'id');
    }
}
