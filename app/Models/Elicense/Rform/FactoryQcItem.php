<?php

namespace App\Models\Elicense\Rform;

use Illuminate\Database\Eloquent\Model;

class FactoryQcItem extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table = 'ros_rform_factory_qc_items';
    public $timestamps = false;

    protected $fillable = [
        'ordering',
        'question_text',
        'published',
        'created',
        'created_by',
        'modified',
        'modified_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('published', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ordering', 'asc');
    }
}
