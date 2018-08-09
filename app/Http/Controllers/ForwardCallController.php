<?php

namespace App\Http\Controllers;

use App\ForwardCall_Model as ForwardCall_Model;
// use Plivo\Response;
use Response;
use Illuminate\Http\Request;
use App\Http\Requests;

class ForwardCallController extends Controller
{
    public function getIncomingCall(Request $request) {
        $twilioNumber = $request->input('To');
        
        $from_number = $request->input('From');
        $from_number_without852 = substr($from_number, 4);
        // print_r($from_number_without852);
        
        $opponent_info = ForwardCall_Model::getOpponentInfo($from_number_without852);
        // echo "<pre>",print_r($opponent_info,1),"</pre>";
        $opponent_number_without852 = $opponent_info[0]['phone_number'];
        // $opponent_number_without852 = 97541211;
        $opponent_number = '+852'.$opponent_number_without852;
        // echo '<br>';
        // print_r($opponent_number);
        $xml = '<Response>
                  <Dial callerId="'.$twilioNumber.'">'
                  .$opponent_number.
                    // +85297541211
                  '</Dial>
                </Response>';
        $response = Response::make($xml, 200);
        $response->header('Content-Type', 'text/xml');
        return $response;
    }

    public function callPlivoNumber(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();
        $date_id = $_POST["date_id"];
        
        $result = ForwardCall_Model::addToTable($user_id, $date_id);
        if ($result) {
            return response()->json(array('success' => true));
        } else {
            return response()->json(array('success' => false));
        }
    }
    // public function mapping() {
    //     // Fetch the from_number from the URL
    //     $from_number = $_REQUEST['From'];
    //     $r = new Response();
    //     // Add Dial tag
    //     $params = array(
    //         'callerId' => $from_number # Caller ID
    //     );

    //     $opponent_info = ForwardCall_Model::getOpponentInfo($from_number);
    //     // $data = ForwardCall_Model::mapping(96, 114);
    //     // $data = ForwardCall_Model::mapping(12345678);
    //     // echo "<pre>",print_r($opponent_info,1),"</pre>";

    //     $d = $r->addDial($params);
    //     $number = $opponent_info[0]['phone_number'];
    //     $d->addNumber($number);
    //     Header('Content-type: text/xml');
    //     echo($r->toXML());
    // }

}

?>