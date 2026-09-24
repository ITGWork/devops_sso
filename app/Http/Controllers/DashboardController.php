<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sessions;
use Session;
class DashboardController extends Controller
{

    public function e_accreditation(){

        $cookie   = session()->getId();
        $sessions = Sessions::where('id', $cookie)->first();


        $URL = 'http://192.168.1.123:8000/api/v1/login';
        $data = array(
                        'session_id' => $cookie,
                        'user_agent' => $sessions
                     );


           $ch = curl_init();

            $headers = array();
            $headers[] = 'Content-Type: application/form-data'; // set content type

            // set request url
            curl_setopt($ch, CURLOPT_URL, $URL); // set CitizenID replace %s
            // set header
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // return header when response
            curl_setopt($ch, CURLOPT_HEADER, true);

            // return the response
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //POST Method
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            // send the request and store the response to $data
            $data =  curl_exec($ch);
            //echo $data;
            // get httpcode
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


            //echo $httpcode;exit();
            if ($httpcode == 200) { // if response ok
                // separate header and body
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

                $header = substr($data, 0, $header_size);
                $body = substr($data, $header_size);
            } else {
                $body = "";
            }
            // end session
            return response()->json([
                                    'success' => true,
                                    'auditors' => $body
                                 ]);
            curl_close($ch);
    }
}
