<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class PeacherSignup_Model extends Model
{
	public static function savePeacherSignup($user_id, $full_name){
		$result = DB::insert('INSERT INTO peachy_peacher_signup (user_id, fullname) VALUES (:user_id, :full_name)', [
				"user_id" => $user_id,
				"full_name" => $full_name
			]);
		return $result;
	}
}