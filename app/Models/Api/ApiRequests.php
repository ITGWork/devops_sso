<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApiRequests extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'api_requests';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'requester',
                            'api_code',
                            'agent_id',
                            'parameter',
                            'url',
                            'ip_request',
                            'user_agent',
                            'request_status',
                            'response_status',
                            'response_msg',
                            'created_at',
                            'updated_at',
                        ];


}
