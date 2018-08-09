<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Review_Model extends Model
{
	public static function getUnreviewedDates($user_id){
		$data = DB::select('SELECT *, peacher_id = :user_id1 AS is_peacher FROM peachy_date 
			WHERE end_datetime <= NOW() AND ((user_id = :user_id2 AND user_reviewed = 0) OR (peacher_id = :user_id3 AND peacher_reviewed = 0))', [
				"user_id1" => $user_id,
				"user_id2" => $user_id,
				"user_id3" => $user_id
			]);		
		return $data;
	}
	public static function changeDateToReviewed_peacher($user_id, $date_id, $forbidden_action){
		DB::update('UPDATE peachy_date 
			SET peacher_reviewed = 1, peacher_forbidden_action = :forbidden_action
			WHERE date_id = :date_id AND peacher_id = :user_id', [
				"date_id" => $date_id,
				"user_id" => $user_id,
				"forbidden_action" => $forbidden_action
			]);
	}
	public static function changeDateToReviewed_user($user_id, $date_id, $forbidden_action){
		DB::update('UPDATE peachy_date 
			SET user_reviewed = 1, user_forbidden_action = :forbidden_action
			WHERE date_id = :date_id AND user_id = :user_id', [
				"date_id" => $date_id,
				"user_id" => $user_id,
				"forbidden_action" => $forbidden_action
			]);
	}
	public static function storeReview($user_id, $date_id, $rating){
		DB::insert('INSERT IGNORE INTO peachy_review (user_id, date_id, rating) 
			VALUES (:user_id, :date_id, :rating)', [
				"user_id" => $user_id,
				"date_id" => $date_id,
				"rating" => $rating
			]);
	}
}