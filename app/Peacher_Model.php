<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Peacher_Model extends Model
{
	public static function getAllCategories(){
		$data = DB::select('SELECT * FROM peachy_activity_category');		
		return $data;
	}
	public static function getAllCategoriesAndActivities(){
		$data = DB::select('SELECT * FROM peachy_activity_name
			INNER JOIN peachy_activity_category
			ON peachy_activity_name.category_id = peachy_activity_category.category_id');		
		return $data;
	}
	public static function getActivityOptions() {
		$data = DB::select('SELECT * from peachy_activity_name');
		return $data;
	}
	public static function getUserPriceInfo ($user_id) {
		$data = DB::select('SELECT * from peachy_set_price
			left join peachy_activity_category
			on peachy_set_price.category_id = peachy_activity_category.category_id
			left join peachy_activity_name
			on peachy_set_price.activity_id = peachy_activity_name.activity_id
			where user_id = :user_id' ,[
				"user_id" => $user_id
			]);
		return $data;
	}
	public static function updatePrice($user_id, $category_id, $activity_id, $price){
		$data = DB::update('UPDATE IGNORE peachy_set_price
			SET category_id = :category_id, activity_id = :activity_id, price = :price
			WHERE user_id = :user_id',[
				"user_id" => $user_id,
				"category_id" => $category_id,
				"activity_id" => $activity_id,
				"price" => $price
			]);
		return $data;
	}
	// public static function deletePriceFromUser($user_id){
	// 	DB::delete('DELETE FROM peachy_set_price
	// 		WHERE user_id = :user_id', [
	// 			"user_id" => $user_id
	// 		]);
	// }
	public static function insertPrice($user_id, $category_id, $activity_id, $price){
			try{
		DB::insert('INSERT INTO peachy_set_price (user_id, category_id, activity_id, price)
			VALUES (:user_id, :category_id, :activity_id, :price)', [
				"user_id" => $user_id,
				"category_id" => $category_id,
				"activity_id" => $activity_id,
				"price" => $price
			]);
			return 1;
		} catch (\Exception $e){
			return 0;
		}

	}
	public static function deletePrice($user_id, $activity_id) {
		DB::delete('DELETE from peachy_set_price
			where user_id = :user_id
			and activity_id = :activity_id',[
				"user_id" => $user_id,
				"activity_id" => $activity_id
			]);
	}
	public static function getSchedule($user_id) {
		$schedule = DB::select('SELECT schedule_json FROM peachy_set_schedule
			WHERE user_id = :user_id',[
				"user_id" => $user_id
			]);		
		return $schedule;
	}
	public static function setSchedule($user_id, $schedule_json) {
		// DB::insert('INSERT INTO peachy_set_schedule (user_id, schedule_json)
		// 	VALUES (:user_id, :schedule_json)', [
		// 		"user_id" => $user_id,
		// 		"schedule_json" => $schedule_json
		// 	]);
		DB::insert('REPLACE INTO peachy_set_schedule (user_id, schedule_json)
			VALUES (:user_id, :schedule_json)', [
				"user_id" => $user_id,
				"schedule_json" => $schedule_json
			]);
	}
	public static function getActivities($user_id, $status) {
		if ($status == 'started'){
			$activities = DB::select('SELECT peachy_date.user_id, nickname, peachy_activity_name.activity, start_datetime, end_datetime, place, peachy_set_price.price, TIME_TO_SEC(TIMEDIFF(end_datetime,start_datetime))/3600*peachy_set_price.price AS amount FROM peachy_date 
			INNER JOIN peachy_user_info 
			ON peachy_date.user_id = peachy_user_info.user_id
			INNER JOIN peachy_activity_name 
			ON peachy_date.activity_id = peachy_activity_name.activity_id
			INNER JOIN peachy_set_price 
			ON peachy_date.activity_id = peachy_set_price.activity_id AND peachy_date.peacher_id = peachy_set_price.user_id
			WHERE end_datetime <= NOW() AND peacher_id = :peacher_id', [
				"peacher_id" => $user_id
			]);
		}elseif ($status == 'pending') {
			$activities = DB::select('SELECT peachy_date.user_id, nickname, peachy_activity_name.activity, start_datetime, end_datetime, place, peachy_set_price.price, TIME_TO_SEC(TIMEDIFF(end_datetime,start_datetime))/3600*peachy_set_price.price AS amount FROM peachy_date 
			INNER JOIN peachy_user_info 
			ON peachy_date.user_id = peachy_user_info.user_id
			INNER JOIN peachy_activity_name 
			ON peachy_date.activity_id = peachy_activity_name.activity_id
			INNER JOIN peachy_set_price 
			ON peachy_date.activity_id = peachy_set_price.activity_id AND peachy_date.peacher_id = peachy_set_price.user_id
			WHERE end_datetime >= NOW() AND peacher_id = :peacher_id', [
				"peacher_id" => $user_id
			]);
		}
		
		return $activities;
	}
	public static function withdraw($user_id, $amount_to_withdraw) {
		DB::insert('INSERT INTO peachy_withdrawals (user_id, amount, status)
			VALUES (:user_id, :amount_to_withdraw, "pending")', [
				"user_id" => $user_id,
				"amount_to_withdraw" => $amount_to_withdraw
			]);
	}
	public static function getWithdrawals($user_id) {
		return DB::select('SELECT * FROM peachy_withdrawals
			WHERE user_id = :user_id',[
			"user_id" => $user_id]);
	}
	public static function getWithdrawalsTotalAmount($user_id) {
		return DB::select('SELECT SUM(amount) as total FROM peachy_withdrawals
			WHERE user_id = :user_id',[
			"user_id" => $user_id, 
			]);
	}
	public static function addBankAccount($user_id, $account_name, $account_num, $account_holder_name) {
		return DB::insert('INSERT INTO peachy_bank_account (user_id, account_name, account_num, account_holder_name) VALUES (:user_id, :account_name, :account_num, :account_holder_name)',[
			"user_id" => $user_id, 
			"account_name" => $account_name, 
			"account_num" => $account_num, 
			"account_holder_name" => $account_holder_name, 
			]);
	}
	public static function getBankInfo($user_id) {
		return DB::select('SELECT * FROM peachy_bank_account
			WHERE user_id = :user_id', [
			"user_id" => $user_id,
			]);
	}
	public static function deleteBank($user_id) {
		return DB::delete('DELETE FROM peachy_bank_account
			WHERE user_id = :user_id', [
			"user_id" => $user_id,
			]);
	}
}