<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ApplicationLabGazette extends Model
{
    protected $table = 'section5_application_labs_gazette';

    protected $primaryKey = 'id';

    protected $fillable = [
        'issue',
        'year',
        'announcement_date',
        'sign_id',
        'sign_name',
        'sign_position',
        'created_by',
        'updated_by',
    ];

    public function details()
    {
        return $this->hasMany(ApplicationLabGazetteDetail::class, 'app_gazette_id');
    }

    // "ฉบับที่ 3 (พ.ศ. 2569) เมื่อวันที่ 1 เมษายน 2569"
    public function getFormattedLabelAttribute(): string
    {
        static $thaiMonths = [
            1 => 'มกราคม',   2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
            4 => 'เมษายน',   5 => 'พฤษภาคม',    6 => 'มิถุนายน',
            7 => 'กรกฎาคม',  8 => 'สิงหาคม',     9 => 'กันยายน',
            10 => 'ตุลาคม',  11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
        ];

        $parts = [];

        if (!empty($this->issue)) {
            $parts[] = 'ฉบับที่ ' . $this->issue;
        }

        if (!empty($this->year)) {
            $parts[] = '(พ.ศ. ' . ((int) $this->year + 543) . ')';
        }

        if (!empty($this->announcement_date)) {
            $date    = Carbon::parse($this->announcement_date);
            $parts[] = 'เมื่อวันที่ ' . $date->day . ' ' . ($thaiMonths[$date->month] ?? '') . ' ' . ($date->year + 543);
        }

        return implode(' ', $parts);
    }
}
