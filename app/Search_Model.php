<?php
namespace App;
use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;
class Search_Model extends Model
{

// ---------------------
	public static function getActivityViaCategoryID($category_id){
		$data = DB::select('SELECT activity_details_id, pad.user_id, category_id, activity_name, activity_description, pad.created_at, username, pui.nickname, pui.gender, pui.date_of_birth, pui.bio, pui.occupation, pui.education, pui.religion, pui.hobby, pui.fav_movie, pui.fav_book, pui.language FROM peachy_activity_details pad
			INNER JOIN users ON users.id = pad.user_id
			INNER JOIN peachy_user_info pui ON pui.user_id = pad.user_id
			WHERE category_id = :category_id 
			ORDER BY created_at DESC', 
			[
				'category_id'=> $category_id
			]);
		return $data;
	}
// ---------------------
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
	public static function getActivityId($activity){
		$result = DB::select('SELECT activity_id FROM peachy_activity_name 
			WHERE activity = :activity', [
				"activity" => $activity
			]);
		return $result[0]["activity_id"];
	}
	public static function filterSearch($user_id, $gender, $ageFrom, $ageTo, $priceRange, $date, $venue, $category_id){
		// echo $category_id;
		// echo $activity_id;
		$search_result = DB::select('SELECT pui.user_id, pui.nickname, pui.gender, pui.price, pui.date_of_birth, pui.bio, pui.occupation, pui.education, pui.religion, pui.hobby, pui.fav_movie, pui.fav_book, pui.language
			FROM users
			INNER JOIN peachy_user_info AS pui
			ON users.id = pui.user_id
			WHERE peacher = 1 
			AND ((:gender1 IS NOT NULL AND gender = :gender2) OR (:gender3 IS NULL))
			AND ((DATEDIFF(CURRENT_DATE,pui.date_of_birth)/365 > :ageFrom1 AND DATEDIFF(CURRENT_DATE,pui.date_of_birth)/365 < :ageTo1) OR (:ageFrom2 IS NULL OR :ageTo2 IS NULL))
			AND (EXISTS (SELECT psp.set_price_id
                    FROM peachy_set_price AS psp
                    WHERE  psp.user_id = users.id AND psp.category_id = :category_id1)
                OR :category_id2 IS NULL)
            -- AND (EXISTS (SELECT psp.set_price_id
            --         FROM peachy_set_price AS psp
            --         WHERE  psp.user_id = users.id AND psp.activity_id = :activity_id1)
            --     OR :activity_id2 IS NULL)
            AND pui.user_id != :user_id
			ORDER BY RAND()', [
				"gender1" => $gender,
				"gender2" => $gender,
				"gender3" => $gender,
				"ageFrom1" => $ageFrom,
				"ageTo1" => $ageTo,
				"ageFrom2" => $ageFrom,
				"ageTo2" => $ageTo,
				"category_id1" => $category_id,
				"category_id2" => $category_id,
				// "activity_id1" => $activity_id,
				// "activity_id2" => $activity_id,
				"user_id" => $user_id
			]);
		return $search_result;
	}
	public static function search(){
		$search_result = DB::select('SELECT pui.user_id, pui.nickname, pui.price, pui.date_of_birth, pui.bio, pui.occupation, pui.education, pui.religion, pui.hobby, pui.fav_movie, pui.fav_book
			FROM users
			INNER JOIN peachy_user_info AS pui
			ON users.id = pui.user_id
			WHERE peacher = 1 
			ORDER BY RAND()');
		return $search_result;
	}
	public static function getPriceList($user_id) {
		$priceList = DB::select('SELECT psp.activity_id, psp.price, pac.category, pan.activity
			FROM peachy_set_price AS psp
			INNER JOIN peachy_activity_category AS pac
			ON psp.category_id = pac.category_id
			INNER JOIN peachy_activity_name AS pan
			ON psp.activity_id = pan.activity_id 
			WHERE psp.user_id = :user_id', [
				"user_id" => $user_id
			]);
		return $priceList;
	}
	public static function getAvailability($user_id) {
		$availability = DB::select('SELECT schedule_json
			FROM peachy_set_schedule
			WHERE user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		if (!empty($availability)) {
			return $availability[0]["schedule_json"];
		} else {
			return "[]";
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
	public static function convertPeacherIDToFullUserID($user_id){
		$full_user_id = DB::select('SELECT full_user_id
			FROM peachy_full_user_id
			WHERE user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		return $full_user_id[0]['full_user_id'];
	}
}