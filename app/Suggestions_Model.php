<?php

namespace App;

use DB;
use Config;
use PDO;
use Illuminate\Database\Eloquent\Model;

class Suggestions_Model extends Model
{
	public static function saveSuggestions($user_id, $suggestion){
		$result = DB::insert('INSERT INTO peachy_suggestion (user_id, suggestion) VALUES (:user_id, :suggestion)', [
				"user_id" => $user_id,
				"suggestion" => $suggestion
			]);
		return $result;
	}
	public static function report($user_id, $report){
		$result = DB::insert('INSERT INTO peachy_report (user_id, report) VALUES (:user_id, :report)', [
				"user_id" => $user_id,
				"report" => $report
			]);
		return $result;
	}
}