<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use App\QR_Model as QR_Model;
use App\User;
use Response;
use App\Classes\PushNotification as PushNotification;


class QRController extends Controller
{

    public function generateNewDatingCode(Request $request) { // Peacher
        $user = $request->user();
        $user_id = $user->getId();

        if (!empty($_GET['date_id'])) {
            $date_id = $_GET['date_id'];
            $date_token = $this->generateRandomString();
            $result = QR_Model::saveDatingCode($date_token, $date_id, $user_id);
            if ($result) {
                return response()->json(array("success"=>true, "date_token"=>$date_token));
            } else {
                return response()->json(array("success"=>false, "error"=>"Datebase error."));
            }
        } else {
            return response()->json(array("success"=>false, "error"=>"Date ID is empty."));
        }
    }

    private function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function startDating(Request $request) { // User
        $user = $request->user();
        $user_id = $user->getId();

        if (!empty($_POST['date_token'])) {
            $date_token = $_POST['date_token'];
            $result = QR_Model::startDating($date_token, $user_id);
            if ($result) {
                ob_start();
                PushNotification::notifyQRCodeScanned($date_token);
                ob_end_clean();
                return response()->json(array("success"=>true));
            } else {
                return response()->json(array("success"=>false, "error"=>"Datebase error."));
            }
        } else {
            return response()->json(array("success"=>false, "error"=>"Date token is empty."));
        }
    }

    private function writeToLog($text) {

        $myfile = fopen("../customlog/access.log", "a") or die("Unable to open file!");
        fwrite($myfile, $text."\n");
        fclose($myfile);
    }
    
}
