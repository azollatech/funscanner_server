<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Profile_Model extends Model
{
	public static function getUserInfo($user_id){
		$userData = DB::select('SELECT google, facebook, peacher 
			FROM users
			WHERE users.id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		$infoData = DB::select('SELECT * FROM peachy_user_info
			WHERE peachy_user_info.user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		$userInfo = $userData[0] + $infoData[0];
		return $userInfo;
	}
	public static function getProfile($user_id){
		$userData = DB::select('SELECT google, facebook, peacher 
			FROM users
			WHERE users.id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		$infoData = DB::select('SELECT * FROM peachy_user_info
			WHERE peachy_user_info.user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		$userInfo = $userData[0] + $infoData[0];
		return $userInfo;
	}
	public static function saveNewProfile($user_id, $post){
		$result = DB::update('UPDATE peachy_user_info 
			SET phone_number = :phone_number, language = :language, occupation = :occupation, education = :education, religion = :religion, hobby = :hobby, fav_movie = :fav_movie, fav_book = :fav_book, bio = :bio
			WHERE user_id = :user_id', [
				"phone_number" => empty($post['phone_number'])?"":$post['phone_number'],
				"language" => empty($post['language'])?"":$post['language'], 
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
			SET nickname = :nickname, date_of_birth = :date_of_birth, gender = :gender, first_time = 0
			WHERE user_id = :user_id', [
				"nickname" => $nickname, 
				"date_of_birth" => $date_of_birth, 
				"gender" => $gender, 
				"user_id" => $user_id
			]);
		return $result;
	}
	public static function checkBasicInfo($user_id) {
		$first_time = DB::select('SELECT first_time FROM peachy_user_info 
			WHERE user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		return $first_time[0]['first_time'];
	}
	public static function checkPhoneNumber($user_id) {
		$phone_number = DB::select('SELECT phone_number FROM peachy_user_info 
			WHERE user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		if (!empty($phone_number[0]['phone_number'])) {
			return true;
		} else {
			return false;
		}
	}
}