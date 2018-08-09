<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class QR_Model extends Model
{
	public static function saveDatingCode($token, $date_id, $user_id){
		$dateExists = DB::select('SELECT * FROM peachy_date
			WHERE peacher_id = :user_id AND date_id = :date_id LIMIT 1', [
				"user_id" => $user_id,
				"date_id" => $date_id
			]);
		if (!empty($dateExists)) {
			$result = DB::insert('INSERT INTO peachy_date_token (date_id, token, status, verified_at) 
			VALUES (:date_id, :token, "pending", NOW())', [
				"date_id" => $date_id,
				"token" => $token,
			]);
			return $result;
		} else {
			return false;
		}
	}
	public static function startDating($date_token, $user_id){
		$date_id_array = DB::select('SELECT date_id FROM peachy_date_token
			WHERE token = :token LIMIT 1', [
				"token" => $date_token
			]);
		if (empty($date_id_array)) {
			return false;
		}
		$result = DB::update('UPDATE peachy_date 
			SET status = "started", started_at = NOW() 
			WHERE date_id = :date_id AND user_id = :user_id', [
				"user_id" => $user_id,
				"date_id" => $date_id_array[0]['date_id'],
			]);
		return $result;
	}
	public static function saveNewProfile($user_id, $post){
		$result = DB::update('UPDATE peachy_user_info 
			SET occupation = :occupation, education = :education, religion = :religion, hobby = :hobby, fav_movie = :fav_movie, fav_book = :fav_book, bio = :bio
			WHERE user_id = :user_id', [
				"occupation" => empty($post['occupation'])?"":$post['occupation'], 
				"education" => empty($post['education'])?"":$post['education'], 
				"religion" => empty($post['religion'])?"":$post['religion'], 
				"hobby" => empty($post['hobby'])?"":$post['hobby'], 
				"fav_movie" => empty($post['fav_movie'])?"":$post['fav_movie'], 
				"fav_book" => empty($post['fav_book'])?"":$post['fav_book'],
				"bio" => empty($post['bio'])?"":$post['bio'],
				"user_id" => $user_id
			]);
		return $result;
	}
	public static function saveBasicInfo($user_id, $nickname, $date_of_birth, $gender){
		$result = DB::update('UPDATE peachy_user_info 
			SET nickname = :nickname, date_of_birth = :date_of_birth, gender = :gender
			WHERE user_id = :user_id', [
				"nickname" => $nickname, 
				"date_of_birth" => $date_of_birth, 
				"gender" => $gender, 
				"user_id" => $user_id
			]);
		return $result;
	}
}