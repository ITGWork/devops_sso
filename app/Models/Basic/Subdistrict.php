<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'district';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'DISTRICT_ID';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['DISTRICT_CODE', 'DISTRICT_NAME', 'AMPHUR_ID', 'PROVINCE_ID', 'GEO_ID', 'state', 'created_by', 'updated_by'];

    /*
      Amphur Relation
    */
    public function amphur(){
      return $this->belongsTo(Amphur::class, 'AMPHUR_ID');
    }

    /*
      Province Relation
    */
    public function province(){
      return $this->belongsTo(Province::class, 'PROVINCE_ID');
    }

}
