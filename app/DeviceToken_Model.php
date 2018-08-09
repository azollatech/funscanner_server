<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class DeviceToken_Model extends Model
{
	public static function saveDeviceToken($device_token, $user_id){
		$result = DB::insert('REPLACE INTO peachy_device_token (user_id, device_token) 
		VALUES (:user_id, :device_token)', [
			"user_id" => $user_id,
			"device_token" => $device_token,
		]);
		return $result;
	}
}