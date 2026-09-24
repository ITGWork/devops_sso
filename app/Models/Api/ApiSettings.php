<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApiSettings extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'api_settings_api';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'code',
                            'name',
                            'url',
                            'dynamic_param',
                            'static_param',
                            'status',
                            'created_at',
                            'updated_at',
                        ];


}
