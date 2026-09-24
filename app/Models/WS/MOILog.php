<?php
namespace App\Models\WS;

use Illuminate\Database\Eloquent\Model;
use Request;
use Kyslik\ColumnSortable\Sortable;

class MOILog extends Model
{
    use Sortable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ws_request_moi_log';

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
    protected $fillable = [
                            'source_url',
                            'input_number',
                            'destination_url',
                            'destination_type',
                            'client_ip',
                            'request_id',
                            'request_start',
                            'request_end',
                            'response_http',
                            'response_error'
                          ];

    public $timestamps = false;

    /*
      Sorting
    */
    public $sortable = [
                        'id',
                        'source_url',
                        'input_number',
                        'destination_url',
                        'destination_type',
                        'client_ip',
                        'request_id',
                        'request_start',
                        'request_end',
                        'response_http',
                        'response_error'
                       ];

    /* บันทึกข้อมูล */
    static function Add($input_number, $destination_url, $destination_type, $request_start, $http_response_header, $message=null, $request_id=null){

        $log = new MOILog;
        $log->source_url       = url()->current();
        $log->input_number     = $input_number;
        $log->destination_url  = $destination_url;
        $log->destination_type = $destination_type;
        $log->client_ip        = Request::ip();
        $log->request_id       = $request_id;
        $log->request_start    = $request_start;
        $log->request_end      = date('Y-m-d H:i:s');
        $log->response_http    = self::getHttpCode($http_response_header);
        $log->response_error   = is_array($message) || is_object($message) ? json_encode($message, JSON_UNESCAPED_UNICODE) : $message;
        $log->save();

    }

    static function getHttpCode($http_response_header)
    {
        if(is_array($http_response_header) && array_key_exists(0, $http_response_header))
        {
            $parts=explode(' ',$http_response_header[0]);
            if(count($parts)>1) //HTTP/1.0 <code> <text>
                return intval($parts[1]); //Get code
        }
        return null;
    }

    static function destination_types(){
        return  [
                    'corporation' => 'นิติบุคคล DBD',
                    'person' => 'บุคคลธรรมดา DOPA',
                    'person-house' => 'ทะเบียนบ้าน DOPA',
                    'rd' => 'ผู้เสียภาษี RD',
                    'industry' => 'โรงงาน DIW',
                    'industry2' => 'โรงงาน DIW (API 2)',
                    'industry3' => 'โรงงาน DIW (API 3)',
                ];
    }

    public function getDestinationTypeTextAttribute() {
  		return array_key_exists($this->destination_type, self::destination_types()) ? self::destination_types()[$this->destination_type] : '-' ;
  	}

    static function response_https(){
        return  [
                    '200' => 'success'
                ];
    }

    public function getResponseHttpHtmlAttribute() {
        $css  = array_key_exists($this->response_http, self::response_https()) ? self::response_https()[$this->response_http] : (is_null($this->response_http) ? 'default' : 'warning');
        $text = !is_null($this->response_http) ? $this->response_http : '&nbsp;&nbsp;&nbsp;&nbsp;' ;
        return '<span class="label label-'.$css.'">'.$text.'</span>';
  	}

}
