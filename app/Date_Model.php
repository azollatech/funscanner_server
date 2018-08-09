<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Date_Model extends Model
{
	public static function sendRequest($user_id, $peacher_id, $activity_id, $start_datetime, $end_datetime, $place, $message_to_peacher) {
		DB::insert('INSERT INTO peachy_request (user_id, peacher_id, activity_id, start_datetime, end_datetime, place, message_to_peacher, expires_at) 
			VALUES (:user_id, :peacher_id, :activity_id, :start_datetime, :end_datetime, :place, :message_to_peacher, NOW() + INTERVAL 3 DAY)', [
			"user_id" => $user_id,
			"peacher_id" => $peacher_id,
			"activity_id" => $activity_id,
			"start_datetime" => $start_datetime,
			"end_datetime" => $end_datetime,
			"place" => $place,
			"message_to_peacher" => $message_to_peacher,
			]);
	}
	public static function changeRequestStatus($request_id, $status) {
		DB::update('UPDATE peachy_request 
			SET status = :status 
			WHERE request_id = :request_id', [
			"request_id" => $request_id,
			"status" => $status
			]);
	}
	public static function makeNotification($notification_type, $sender_id, $receiver_id,  $notification_text = NULL) {
		DB::insert('INSERT INTO peachy_notification (notification_type, sender_id, receiver_id, notification_text) 
		VALUES (:notification_type, :sender_id, :receiver_id, :notification_text)', [
		"notification_type" => $notification_type, 
		"sender_id" => $sender_id,
		"receiver_id" => $receiver_id,
		"notification_text" => $notification_text
		]);
	}
	public static function getImgToken($sender_id) {
		$img_token_id_array = DB::select('SELECT img_token_id FROM peachy_img_token 
			WHERE user_id = :sender_id', [
			"sender_id" => $sender_id
			]);
		return $img_token_id_array[0]['img_token_id'];
	}
	public static function getAllRequests($user_id, $status) {
		$request_array = DB::select('SELECT request_id, peachy_request.user_id, peacher_id, start_datetime, end_datetime, place, message_to_peacher, expires_at, created_at, peacher_id = :user_id AS is_peacher, pac.category, pan.activity AS activity_name, psp.price, psp.activity_id FROM peachy_request 
			LEFT JOIN peachy_set_price AS psp
			ON psp.user_id = peachy_request.peacher_id AND psp.activity_id = peachy_request.activity_id
			LEFT JOIN peachy_activity_category AS pac
			ON psp.category_id = pac.category_id
			LEFT JOIN peachy_activity_name AS pan
			ON psp.activity_id = pan.activity_id 
			WHERE (peachy_request.user_id = :user_id_1 OR peacher_id = :user_id_2) AND status = :status ORDER BY request_id DESC', [
			"user_id" => $user_id,
			"user_id_1" => $user_id,
			"user_id_2" => $user_id,
			"status" => $status
			]);
		return $request_array;
	}
	public static function getEachRequest($request_id, $status) {
		$request_array = DB::select('SELECT * FROM peachy_request 
			WHERE request_id = :request_id', [
			"request_id" => $request_id
			]);
		return $request_array;
	}
	public static function getUserInfo($user_id) {
		$user_info = DB::select('SELECT user_id, nickname, price, date_of_birth, bio, occupation, education, religion, hobby, fav_movie, fav_book FROM peachy_user_info 
			WHERE user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		return $user_info[0];
	}

	public static function moveToDate($request) {
		DB::insert('INSERT INTO peachy_date (user_id, peacher_id, activity_id, start_datetime, end_datetime, place) 
			VALUES (:user_id, :peacher_id, :activity_id, :start_datetime, :end_datetime, :place)', [
			"user_id" => $request["user_id"],
			"peacher_id" => $request["peacher_id"],
			"activity_id" => $request["activity_id"],
			"start_datetime" => $request["start_datetime"],
			"end_datetime" => $request["end_datetime"],
			"place" => $request["place"]
			]);
	}

	public static function getAllDates($user_id, $status) {
		$request_array = DB::select('SELECT date_id, peachy_date.user_id, peacher_id, start_datetime, end_datetime, place, peacher_id = :user_id AS is_peacher, pac.category, pan.activity AS activity_name, psp.price, psp.activity_id FROM peachy_date 
			LEFT JOIN peachy_set_price AS psp
			ON psp.user_id = peachy_date.peacher_id AND psp.activity_id = peachy_date.activity_id
			LEFT JOIN peachy_activity_category AS pac
			ON psp.category_id = pac.category_id
			LEFT JOIN peachy_activity_name AS pan
			ON psp.activity_id = pan.activity_id 
			WHERE (peachy_date.user_id = :user_id1 OR peacher_id = :user_id2) AND status = :status 
			AND end_datetime > NOW()
			ORDER BY start_datetime DESC', [
			"user_id" => $user_id,
			"user_id1" => $user_id,
			"user_id2" => $user_id,
			"status" => $status
			]);
		return $request_array;
	}

	public static function getAllNotifications($user_id) {
		$notifications = DB::select('SELECT notification_id, sender_id, receiver_id, notification_type, notification_text, read_already, created_at FROM peachy_notification 
			WHERE receiver_id = :user_id ORDER BY notification_id DESC', [
				"user_id" => $user_id
			]);
		return $notifications;
	}

	public static function getAllHistories($user_id) {
		$histories = DB::select('SELECT date_id, peachy_date.user_id, peacher_id, start_datetime, end_datetime, place, peacher_id = :user_id AS is_peacher, pac.category, pan.activity AS activity_name, psp.price, psp.activity_id FROM peachy_date 
			LEFT JOIN peachy_set_price AS psp
			ON psp.user_id = peachy_date.peacher_id AND psp.activity_id = peachy_date.activity_id
			LEFT JOIN peachy_activity_category AS pac
			ON psp.category_id = pac.category_id
			LEFT JOIN peachy_activity_name AS pan
			ON psp.activity_id = pan.activity_id  
			WHERE (peachy_date.user_id = :user_id1 OR peacher_id = :user_id2) AND end_datetime <= NOW() ORDER BY end_datetime DESC', [
				"user_id" => $user_id,
				"user_id1" => $user_id,
				"user_id2" => $user_id
			]);
		return $histories;
	}

	// Notifications related
	public static function getSenderName($user_id) {
		$user_info = DB::select('SELECT nickname FROM peachy_user_info 
			WHERE user_id = :user_id LIMIT 1', [
				"user_id" => $user_id
			]);
		return $user_info[0]['nickname'];
	}
	public static function countNonReadNotifications($user_id) {
		$count = DB::select('SELECT COUNT(notification_id) AS count FROM peachy_notification
			WHERE receiver_id = :user_id AND `read_already` is false
			GROUP BY receiver_id', [
				"user_id" => $user_id
			]);
		if (empty($count)) {
			return 0;
		}
		return $count[0]['count'];
	}
	public static function updateNotificationsToReadAlready($user_id) {
		DB::update('UPDATE peachy_notification 
			SET read_already = true 
			WHERE receiver_id = :user_id', [
			"user_id" => $user_id
			]);
	}
	public static function getDeviceToken($user_id) {
		$device_token = DB::select('SELECT device_token FROM peachy_device_token
			WHERE user_id = :user_id', [
				"user_id" => $user_id
			]);
		if (!empty($device_token)) {
			return $device_token[0]['device_token'];
		} else {
			return false;
		}
	}
	public static function getPeacherIdByDateToken($date_token) {
		$peacher_id = DB::select('SELECT peacher_id FROM peachy_date 
			INNER JOIN peachy_date_token
			ON peachy_date.date_id = peachy_date_token.date_id
			WHERE peachy_date_token.token = :date_token', [
				"date_token" => $date_token
			]);
		if (!empty($peacher_id)) {
			return $peacher_id[0]['peacher_id'];
		} else {
			return false;
		}
	}
	public static function getProfileFromFullUserID($fullUserID) {
		$profile = DB::select('SELECT pui.user_id, pui.nickname, pui.gender, pui.price, pui.date_of_birth, pui.bio, pui.occupation, pui.education, pui.religion, pui.hobby, pui.fav_movie, pui.fav_book, pui.language, peacher AS is_peacher
			FROM users
			INNER JOIN peachy_user_info AS pui
			ON users.id = pui.user_id 
			INNER JOIN peachy_full_user_id AS pfui 
			ON pfui.user_id = pui.user_id 
			WHERE pfui.full_user_id = :full_user_id', [
				"full_user_id" => $fullUserID
			]);
		if (!empty($profile)) {
			return $profile[0];
		} else {
			return false;
		}
	}
}