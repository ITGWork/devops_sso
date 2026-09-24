<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\User;

class LogNotification extends Model
{
    protected $table = 'logs_notifications';

    protected $primaryKey = 'id';

    protected $fillable = [ 

        'id',
        'title',
        'details',
        'ref_applition_no',
        'ref_table',
        'ref_id',
        'status',
        'site',
        'root_site',
        'url',
        'read',
        'users_id' ,
        'read_all',
        'type',
        'ref_table_user'

    ];
}
