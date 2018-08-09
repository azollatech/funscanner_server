<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class ForwardCall_Model extends Model
{
	public static function addToTable($user_id, $date_id) {
		$result = DB::insert('INSERT INTO peachy_get_phone_number (user_id, date_id) 
			VALUES (:user_id, :date_id)', [
			'user_id' => $user_id, 
			'date_id' => $date_id
			]);
		return $result;
	}
	public static function getOpponentInfo($from_number) {
		$caller = DB::select('SELECT * FROM peachy_get_phone_number 
			INNER JOIN peachy_user_info
			ON peachy_user_info.user_id = peachy_get_phone_number.user_id
			WHERE phone_number = :from_number
			ORDER BY created_at DESC 
			LIMIT 1',[
			'from_number' => $from_number
			]);
		$caller_id = $caller[0]['user_id'];
		$date_id = $caller[0]['date_id'];
		// return $caller;

		$date = DB::select('SELECT *, peacher_id = :user_id AS is_peacher FROM peachy_date 
			WHERE date_id = :date_id',[
			'date_id' => $date_id,
			'user_id' => $caller_id,
			]);
		// return $date;

		if ($date[0]['is_peacher']) {
			$opponent_id = $date[0]['user_id'];
		} else {
			$opponent_id = $date[0]['peacher_id'];
		}
		$opponent_info = DB::select('SELECT * FROM peachy_user_info 
			WHERE user_id = :user_id',[
			'user_id' => $opponent_id
			]);
		return $opponent_info;
	}
}