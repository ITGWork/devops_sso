<?php

namespace App\Models\Elicense\Basic;

use Illuminate\Database\Eloquent\Model;

class InspecStatus extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table = 'ros_rbasicdata_inspec_status';
    public $timestamps = false;

    protected $fillable = ['title', 'title_en', 'description', 'ordering', 'state'];

    public function scopeActive($query)
    {
        return $query->where('state', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ordering', 'asc');
    }

    public static function getStatusList($ids = null)
    {
        $query = self::active()->ordered();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        return $query->pluck('title', 'id')->toArray();
    }

    public static function getStatusText($id)
    {
        $status = self::find($id);
        return $status ? $status->title : '-';
    }
}
