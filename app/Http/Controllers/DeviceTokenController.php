<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use App\DeviceToken_Model as DeviceToken_Model;
use App\User;
use Response;


class DeviceTokenController extends Controller
{

    public function saveDeviceToken(Request $request) { // Peacher
        $user = $request->user();
        $user_id = $user->getId();

        if (!empty($_POST['device_token'])) {
            $device_token = $_POST['device_token'];
            $result = DeviceToken_Model::saveDeviceToken($device_token, $user_id);
            if ($result) {
                return response()->json(array("success"=>true));
            } else {
                return response()->json(array("success"=>false, "error"=>"Datebase error."));
            }
        } else {
            return response()->json(array("success"=>false, "error"=>"Device Token is empty."));
        }
    }
    
}
