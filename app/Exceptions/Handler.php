<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        $message = $exception->getMessage();
        if(strpos($message, 'SQLSTATE[HY000] [2002]')!==false){//เชื่อมต่อฐานข้อมูลไม่ได้
            return response()->view('errors.503-database', [], 503);
        }elseif(strpos($message, 'Connection could not be established with host')!==false && (strpos($message, ':stream_socket_client(): unable to connect to')!==false || strpos($message, ':stream_socket_client(): SSL: Connection reset by peer')!==false)){//ส่งอีเมลไม่ได้ MailGoThai
            return response()->view('errors.503-mail', [], 503);
        }elseif(strpos($message, 'Authenticator LOGIN returned Expected response code 235')!==false){//ส่งอีเมลไม่ได้ hotmail
            return response()->view('errors.503-mail', [], 503);
        }elseif(strpos($message, 'Authenticator LOGIN returned Expected response code 250 but got an empty response')!==false){//ส่งอีเมลไม่ได้ workd.go.th
            return response()->view('errors.503-mail', [], 503);
        }

        return parent::render($request, $exception);
    }
}
