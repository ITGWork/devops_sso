<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'amphur';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'AMPHUR_ID';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['AMPHUR_CODE', 'AMPHUR_NAME', 'POSTCODE', 'GEO_ID', 'PROVINCE_ID', 'state', 'created_by', 'updated_by'];

    /*
      Province Relation
    */
    public function province(){
      return $this->belongsTo(Province::class, 'PROVINCE_ID');
    }

}
