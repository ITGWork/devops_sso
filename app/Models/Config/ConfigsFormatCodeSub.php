<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class ConfigsFormatCodeSub extends Model
{
        /**
     * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'configs_format_codes_sub';

    /**
     * Attributes that should be mass-assignable.
    *
    * @var array
    */
    protected $fillable = [
                            'format',
                            'data',
                            'sub_data',
                            'format_id'
                        ];
}
