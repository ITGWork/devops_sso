<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class ConfigsManual extends Model
{
    /**
     * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'configs_manuals';

    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
    *
    * @var array
    */
    protected $fillable = [
                            'title',
                            'details',
                            'site',
                            'file',
                            'created_by',
                            'updated_by'
                        ];
}
