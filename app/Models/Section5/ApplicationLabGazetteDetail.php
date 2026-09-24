<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

class ApplicationLabGazetteDetail extends Model
{
    protected $table = 'section5_application_labs_gazette_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'app_lab_id',
        'application_no',
        'app_gazette_id',
        'lab_id',        // จะถูกเพิ่มใน Devopscenter migration
    ];

    public function gazette()
    {
        return $this->belongsTo(ApplicationLabGazette::class, 'app_gazette_id');
    }

    public function application()
    {
        return $this->belongsTo(ApplicationLab::class, 'app_lab_id');
    }
}
