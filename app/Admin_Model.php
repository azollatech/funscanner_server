<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Admin_Model extends Model
{
	public static function getPeachers() {
		$data = DB::select('SELECT * FROM peachy_peacher_signup');
		return $data;
	}

	public static function getCurrentBalance($criteria) {
		if ($criteria == 'all') {
			// $data = DB::select('SELECT * FROM peachy_withdrawals');

			$data = DB::select('SELECT username, peachy_date.peacher_id,SUM(TIME_TO_SEC(TIMEDIFF(end_datetime,start_datetime))/3600*peachy_set_price.price)-IFNULL(withdrawalstable.amount,0) AS current_balance 
				FROM peachy_date 
				INNER JOIN peachy_activity_name 
				ON peachy_date.activity_id = peachy_activity_name.activity_id 
				INNER JOIN peachy_set_price 
				ON peachy_date.activity_id = peachy_set_price.activity_id AND peachy_date.peacher_id = peachy_set_price.user_id 
				LEFT JOIN 
					(
						SELECT user_id, IFNULL(SUM(amount),0) AS amount
						FROM peachy_withdrawals
						GROUP BY user_id
					) AS withdrawalstable
				ON peachy_date.peacher_id = withdrawalstable.user_id 
				LEFT JOIN users ON peachy_date.peacher_id = users.id
				WHERE end_datetime <= NOW() 
				GROUP BY peachy_date.peacher_id');
		}
		return $data;
	}

	public static function approvePeacher($user_id, $peacher_signup_id) {
		$result = DB::update('UPDATE peachy_peacher_signup 
			SET status = "approved" 
			WHERE id = :peacher_signup_id AND user_id = :user_id',[
				"peacher_signup_id" => $peacher_signup_id,
				"user_id" => $user_id
			]);
		if (!$result) {
			return;
		}
		$info = DB::select('SELECT * FROM peachy_peacher_signup 
			WHERE id = :peacher_signup_id AND user_id = :user_id LIMIT 1',[
				"peacher_signup_id" => $peacher_signup_id,
				"user_id" => $user_id
			]);
		DB::update('UPDATE users 
			SET peacher = 1 
			WHERE id = :user_id',[
				"user_id" => $user_id
			]);
		DB::update('UPDATE peachy_user_info 
			SET full_name = :fullname
			WHERE user_id = :user_id',[
				"user_id" => $user_id,
				"fullname" => $info[0]["fullname"]
			]);
	}
	public static function getUsers(){
		$users = DB::select('SELECT users.id,username,peachy_user_info.nickname,
			peachy_user_info.gender FROM peachy_user_info INNER JOIN users on users.id = 
			peachy_user_info.user_id');
		return $users;
	}
	public static function getAllPeachers(){
		$all_peachers = DB::select('SELECT users.id,username,peachy_user_info.nickname,peachy_user_info.gender FROM peachy_user_info INNER JOIN users on users.id = peachy_user_info.user_id WHERE peacher = 1');
		return $all_peachers;
	}
	
	public static function getAllAdmins(){
		$all_admins = DB::select('SELECT users.id,username,peachy_user_info.nickname,peachy_user_info.gender FROM peachy_user_info INNER JOIN users on users.id = peachy_user_info.user_id WHERE admin = 1');
		return $all_admins;
	}

	public static function withdraw($peacher_id, $current_balance) {
		DB::insert('INSERT INTO peachy_withdrawals (user_id, amount)
			VALUES (:user_id, :current_balance)',[
				"user_id" => $peacher_id,
				"current_balance" => $current_balance
			]);
	}
}