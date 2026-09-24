<?php
namespace App\Models\WS;

use Illuminate\Database\Eloquent\Model;
use Request;
use Kyslik\ColumnSortable\Sortable;
use App\Models\WS\Client;

class Log extends Model
{
    use Sortable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ws_log';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['app_name',
                           'client_title',
                           'api_name',
                           'ip',
                           'status',
                           'message',
                           'request_time'
                          ];

    public $timestamps = false;

    /*
      Sorting
    */
    public $sortable = ['app_name',
                        'client_title',
                        'api_name',
                        'ip',
                        'status',
                        'message',
                        'request_time'
                       ];

   /* บันทึกข้อมูล */
   static function Add($app_name, $api_name, $status, $message, $client_title=null){

       if(is_null($client_title)){//ถ้าไม่มีส่งเข้ามา
           $web_service_client = Client::where('app_name', $app_name)->first();//ดึงข้อมูลผู้ใช้งาน webservice
           $client_title = !is_null($web_service_client) ? $web_service_client->title : null ;
       }

       $log = new Log;
       $log->app_name = $app_name;
       $log->client_title = $client_title;
       $log->api_name = strtolower($api_name);
       $log->ip = Request::ip();
       $log->status = $status;
       $log->message = is_array($message) || is_object($message) ? json_encode($message, JSON_UNESCAPED_UNICODE) : $message;
       $log->save();

   }

}
