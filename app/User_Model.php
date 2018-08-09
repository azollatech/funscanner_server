<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class User_Model extends Model
{
	public static function searchFrequencyCheck($user_id){
		$data = DB::select('SELECT * FROM peachy_search_history 
			WHERE user_id = :user_id 
			AND HOUR(TIMEDIFF(search_time, NOW())) < 1', [
				"user_id" => $user_id
			]);
		if (count($data) >= 5) {
			return true;
		} else {
			return false;
		}
	}
	public static function saveSearchTime($user_id){
		$result = DB::insert('INSERT INTO peachy_search_history(user_id)
			VALUES (:user_id)', [
				"user_id" => $user_id
			]);
	}
	public static function filterSearch($gender, $ageFrom, $ageTo, $priceRange, $date, $venue){
		$search_result = DB::select('SELECT pui.user_id, pui.nickname, pui.price, pui.date_of_birth, pui.bio, pui.occupation, pui.education, pui.religion, pui.hobby, pui.fav_movie, pui.fav_book
			FROM users
			INNER JOIN peachy_user_info AS pui
			ON users.id = pui.user_id
			WHERE peacher = 1 
			AND ((:gender1 IS NOT NULL AND gender = :gender2) OR (:gender3 IS NULL))
			AND ((DATEDIFF(CURRENT_DATE,pui.date_of_birth)/365 > :ageFrom1 AND DATEDIFF(CURRENT_DATE,pui.date_of_birth)/365 < :ageTo1) OR (:ageFrom2 IS NULL OR :ageTo2 IS NULL))
			ORDER BY RAND() LIMIT 5', [
				"gender1" => $gender,
				"gender2" => $gender,
				"gender3" => $gender,
				"ageFrom1" => $ageFrom,
				"ageTo1" => $ageTo,
				"ageFrom2" => $ageFrom,
				"ageTo2" => $ageTo,
			]);
		return $search_result;
	}
	public static function search(){
		$search_result = DB::select('SELECT pui.user_id, pui.nickname, pui.price, pui.date_of_birth, pui.bio, pui.occupation, pui.education, pui.religion, pui.hobby, pui.fav_movie, pui.fav_book
			FROM users
			INNER JOIN peachy_user_info AS pui
			ON users.id = pui.user_id
			WHERE peacher = 1 
			ORDER BY RAND() LIMIT 5');
		return $search_result;
	}
	public static function checkPasswordExist($user_id) {
		$password = DB::select('SELECT password
			FROM users
			WHERE id = :user_id', [
				'user_id' => $user_id
			]);
		if (!is_null($password[0]['password'])) {
			return true;
		} else {
			return false;
		}
	}
	public static function convertImageTokenToPeacherID($imageToken){
		$peacher_id = DB::select('SELECT user_id
			FROM peachy_img_token
			WHERE token = :token LIMIT 1', [
				"token" => $imageToken
			]);
		return $peacher_id[0]['user_id'];
	}
	public static function convertPeacherIDToImageToken($user_id){
		$imageToken = DB::select('SELECT token
			FROM peachy_img_token
			WHERE user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		return $imageToken[0]['token'];
	}
}