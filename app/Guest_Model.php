<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Guest_Model extends Model
{
	public static function savePeacherSignUp($data){
		$users = DB::insert('INSERT INTO peachy_peacher_signup_temp (fullname, nickname, date_of_birth, email, phone) 
			VALUES (:fullname, :nickname, :date_of_birth, :email, :phone)', [
			"fullname" => $data['fullname'],
			"nickname" => $data['nickname'],
			"date_of_birth" => $data['date_of_birth'],
			"email" => $data['email'],
			"phone" => $data['phone'],
			]);
		return $users;
	}
}