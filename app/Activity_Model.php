<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Activity_Model extends Model
{
	// public static function createActivity($user_id, $name, $description, $category_id, $datetime, $duration, $venue){
	// 	$data = DB::select('INSERT INTO peachy_activity_details (user_id, activity_name, activity_description, category_id, datetime, duration, venue)
	// 		VALUES (:user_id, :activity_name, :activity_description, :category_id, :datetime, :duration, :venue)', [
	// 		'user_id'=>$user_id,
	// 		'activity_name'=>$name,
	// 		'activity_description'=>$description,
	// 		'category_id'=>$category_id,
	// 		'datetime'=>$datetime,
	// 		'duration'=>$duration,
	// 		'venue'=>$venue
	// 	]);
	//
	// 	return $data;
	// }
	//
	// public static function getParticularActivity($activity_id) {
	// 	$data = DB::select('SELECT pad.activity_name, pad.activity_description, pad.datetime, pad.category_id, pui.nickname, pad.participants_no, pad.created_at
	// 		FROM peachy_activity_details pad
	// 		INNER JOIN peachy_user_info pui
	// 		ON pui.user_id = pad.user_id
	// 		WHERE pad.user_id = :user_id
	// 		ORDER BY created_at desc', [
	// 			'user_id'=>$user_id
	// 		]);
	// 	return $data;
	// }

	public static function getActivitiesByCategory($category_id) {
		$data = DB::select('SELECT pa.activity_id, pa.activity_name, pa.price, pa.details, pa.address, pa.region, pa.traffic, pa.hours, pa.must_know, pa.created_at, pa.rect_img, pa.sq_img
			FROM peachy_activity pa
			INNER JOIN peachy_activity_mapping pam
			ON pa.activity_id = pam.activity_id
			WHERE pam.category_id = :category_id
			ORDER BY created_at desc', [
				'category_id'=>$category_id
			]);
		return $data;
	}

	public static function getActivityOptions($activity_id) {
		$data = DB::select('SELECT package_id, package_name, package_price
			FROM peachy_activity_package
			WHERE activity_id = :activity_id', [
				'activity_id'=>$activity_id
			]);
		return $data;
	}

}
