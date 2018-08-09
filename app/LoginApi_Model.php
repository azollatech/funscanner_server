<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class LoginApi_Model extends Model
{
	public static function saveGoogleLoginToUser($g_user_id, $email, $g_name, $g_given_name, $g_family_name, $username){
		DB::insert('INSERT INTO users (email, username, google) 
			VALUES (:email, :username, 1)', [
				"username" => $username,
				"email" => $email,
			]);
		$user_id = DB::getPdo()->lastInsertId();
		DB::insert('INSERT INTO peachy_user_info (user_id, g_user_id, g_name, g_given_name, g_family_name) 
			VALUES (:user_id, :g_user_id, :g_name, :g_given_name, :g_family_name)', [
				"user_id" => $user_id,
				"g_user_id" => $g_user_id,
				"g_name" => $g_name,
				"g_given_name" => $g_given_name,
				"g_family_name" => $g_family_name,
			]);
		return $user_id;
	}
	public static function saveFacebookLoginToUser($fb_user_id, $email, $fb_name, $fb_first_name, $fb_last_name, $username){
		DB::insert('INSERT INTO users (email, username, facebook) 
			VALUES (:email, :username, 1)', [
				"username" => $username,
				"email" => $email,
			]);
		$user_id = DB::getPdo()->lastInsertId();
		DB::insert('INSERT INTO peachy_user_info (user_id, fb_user_id, fb_name, fb_first_name, fb_last_name) 
			VALUES (:user_id, :fb_user_id, :fb_name, :fb_first_name, :fb_last_name)', [
				"user_id" => $user_id,
				"fb_user_id" => $fb_user_id,
				"fb_name" => $fb_name,
				"fb_first_name" => $fb_first_name,
				"fb_last_name" => $fb_last_name,
			]);
		return $user_id;
	}
	public static function saveEmailSignUpToUser($user_id){
		$result = DB::insert('INSERT INTO peachy_user_info (user_id) 
			VALUES (:user_id)', [
				"user_id" => $user_id
			]);
		return $result;
	}
	public static function createImageToken($user_id, $image_token){
		DB::insert('INSERT INTO peachy_img_token (user_id, token) 
			VALUES (:user_id, :image_token)', [
				"user_id" => $user_id,
				"image_token" => $image_token,
			]);
	}
	public static function createFullUserID($user_id, $full_user_id){
		DB::insert('INSERT INTO peachy_full_user_id (user_id, full_user_id) 
			VALUES (:user_id, :full_user_id)', [
				"user_id" => $user_id,
				"full_user_id" => $full_user_id,
			]);
	}
	public static function logout($user_id) {
		DB::delete('DELETE FROM peachy_device_token 
			WHERE user_id = :user_id', [
				"user_id" => $user_id
			]);
	}
}