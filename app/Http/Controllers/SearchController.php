<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use App\Search_Model as Search_Model;
use App\User;
use Response;
use Intervention\Image\ImageManagerStatic as Image;


class SearchController extends Controller
{

    public function filterSearch(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (!isset($_GET)) {
            return $this->search($request);
        }

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        // Prevent frequent search
        // $isTooFrequent = Search_Model::searchFrequencyCheck($user_id);
        // if ($isTooFrequent) {
        //     return response()->json(array('success' => false, 'error' => '搜尋次數太密了！請先休息一下，在半小時後再搜尋吧。'));
        // }

        // Gender
        $gender = ($_GET["gender"] != '-')?strtolower($_GET["gender"]):NULL;
        if ($gender == 'both') {
        	$gender = NULL;
        }

        // Age
        $ageRange = ($_GET["age"] != '-')?$_GET["age"]:NULL;
        if ($_GET["age"] != '-') {
            $ages = explode(" to ", $ageRange);
            $ageFrom = $ages[0];
            $ageTo = $ages[1];
        } else {
            $ageFrom = NULL;
            $ageTo = NULL;
        }

        // Date
        $date = ($_GET["date"] != '-')?$_GET["date"]:NULL;

        // Category
        if (isset($_GET["category"])) {
            $category = ($_GET["category"] != '-')?$_GET["category"]:NULL;
            switch ($category) {
                case 'Sports':
                    $category_id = 1;
                    break;
                case 'Social':
                    $category_id = 2;
                    break;
                case 'Movie':
                    $category_id = 3;
                    break;
                case 'Day Trip':
                    $category_id = 4;
                    break;
                default:
                    $category_id = NULL;
                    break;
            }
        } else {
            $category_id = NULL;
        }

        // Activity
        if (isset($_GET["activity"])) {
            $activity = ($_GET["activity"] != '-')?$_GET["activity"]:NULL;
            $activity_id = Search_Model::getActivityId($activity);
        } else {
            $activity_id = NULL;
        }

        $search_result = Search_Model::filterSearch($user_id, $gender, $ageFrom, $ageTo, $priceRange = NULL, $date, $venue = NULL, $category_id, $activity_id);
        foreach ($search_result as &$profile) {
	        if (!empty($profile['date_of_birth'])) {
	            $profile['zodiac'] = $this->dateOfBirthToZodiac($profile['date_of_birth'], $lang);
	            $profile['age'] = (string) $this->dateOfBirthToAge($profile['date_of_birth']);
	            unset($profile['date_of_birth']);
	        }
            if (!empty($profile['gender'])) {
                switch ($profile['gender']) {
                    case 'male':
                        if ($lang == 'zh-hk') {
                            $profile['gender'] = "男";
                        } else {
                            $profile['gender'] = "Male";
                        }
                        break;
                    case 'female':
                        if ($lang == 'zh-hk') {
                            $profile['gender'] = "女";
                        } else {
                            $profile['gender'] = "Female";
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            }
            $imageToken = Search_Model::convertPeacherIDToImageToken($profile['user_id']);
            $profile['profile_image'] = URL."api/image/".$imageToken;
            // unset($profile['user_id']);

            $profile['price_list'] = Search_Model::getPriceList($profile['user_id']);
            $profile['availability'] = json_decode(Search_Model::getAvailability($profile['user_id']));
        }

        // echo "<pre>", print_r($search_result, 1), "</pre>";
        Search_Model::saveSearchTime($user_id);

        return response()->json(array('success' => true, 'data' => $search_result));
    }

    public function image($imageToken) {
        $peacher_id = Search_Model::convertImageTokenToPeacherID($imageToken);

        $storage_path = storage_path('app/profile-images/');
        $files = glob($storage_path.$peacher_id."/*.{jpg,png,gif}", GLOB_BRACE);
        if (!empty($files)) {
            return Image::make($files[0])->response();
        } else {
            return Image::make($storage_path."profile-placeholder.png")->response();
        }
    }

    public function search(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        // $isTooFrequent = Search_Model::searchFrequencyCheck($user_id);
        // if ($isTooFrequent) {
        //     return response()->json(array('success' => false, 'error' => '搜尋次數太密了！請先休息一下，在半小時後再搜尋吧。'));
        // }

        $search_result = Search_Model::search();
        // echo "<pre>", print_r($search_result, 1), "</pre>";
        Search_Model::saveSearchTime($user_id);

        return response()->json($search_result);
    }
    
}
