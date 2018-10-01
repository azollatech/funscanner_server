<?php

namespace App\Http\Controllers;

use App\Activity_Model as Activity_Model;
use App\Search_Model as Search_Model;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Response;
use View;
use Redirect;
use DB;


class ActivityController extends Controller
{
    public function createNewActivity(request $request) {

        $user = $request->user();
        $user_id = $user->getId();

        $category_id = 0;
        if( isset($_POST["category"]) ) {
            switch ($_POST["category"]) {
                case 'Social':
                    $category_id = 2;
                case 'Day Trip':
                    $category_id = 3;
                default:
                    $category_id = 1;
            }
        };
        
        $name = $_POST["activity_name"];
        $description = $_POST["activity_description"];
        $duration = $_POST["duration"];
        $datetime = $_POST["datetime"];
        $venue = $_POST["venue"];
        Activity_Model::createActivity($user_id, $name, $description, $category_id, $datetime, $duration, $venue);

        return response()->json(array("success"=>true));
    }

    public function getMyActivity(Request $request){
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }


        $activities = Activity_Model::myActivities($user_id);
        $imageToken = Search_Model::convertPeacherIDToImageToken($user_id);
        $profile_image = URL."api/image/".$imageToken;

        foreach ($activities as &$activity) {
            $activity['profile_image'] = $profile_image;
            $activity['time_elapsed'] = $this->time_elapsed_string($activity['created_at'], $full = false, $lang = 'en');
        }

        

        return response()->json(array('success' => true, 'data' => $activities));
    }
}

