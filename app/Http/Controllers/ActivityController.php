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
    // public function createNewActivity(request $request) {
    //
    //     $user = $request->user();
    //     $user_id = $user->getId();
    //
    //     $category_id = 0;
    //     if( isset($_POST["category"]) ) {
    //         switch ($_POST["category"]) {
    //             case 'Social':
    //                 $category_id = 2;
    //             case 'Day Trip':
    //                 $category_id = 3;
    //             default:
    //                 $category_id = 1;
    //         }
    //     };
    //
    //     $name = $_POST["activity_name"];
    //     $description = $_POST["activity_description"];
    //     $duration = $_POST["duration"];
    //     $datetime = $_POST["datetime"];
    //     $venue = $_POST["venue"];
    //     Activity_Model::createActivity($user_id, $name, $description, $category_id, $datetime, $duration, $venue);
    //
    //     return response()->json(array("success"=>true));
    // }

    // public function getParticularActivity(Request $request){
    //     $user = $request->user();
    //     $user_id = $user->getId();
    //
    //     if (isset($_GET["lang"])) {
    //         $lang = $_GET["lang"];
    //     } else {
    //         $lang = 'en';
    //     }
    //
    //     if (!isset($_GET["activity_id"])) {
    //         return response()->json(array('success' => false));
    //     }
    //
    //     $data = Activity_Model::getParticularActivity($activity_id);
    //
    //     // foreach ($activities as &$activity) {
    //     //     // $activity['profile_image'] = $profile_image;
    //     //     // $activity['time_elapsed'] = $this->time_elapsed_string($activity['created_at'], $full = false, $lang = 'en');
    //     // }
    //
    //     return response()->json(array('success' => true, 'data' => $data));
    // }

    public function getActivitiesByCategory(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        if (!isset($_GET["category_id"])) {
            return response()->json(array('success' => false));
        }

        $category_id = $_GET["category_id"];
        $activities = Activity_Model::getActivitiesByCategory($category_id);

        foreach ($activities as &$activity) {
            $activity['price'] = (string) $activity['price'];
            $activity['rect_img'] = URL."activity_img/rect_thumb/".$activity['activity_photo'];
            $activity['sq_img'] = URL."activity_img/sq_thumb/".$activity['activity_photo'];

            $district = DB::table('district')
                ->where('district_id', $activity['district_id'])
                ->first();
            $activity['region'] = !empty($district) ? $district['district_zh_hk'] : "";
            unset($activity['district_id']);
            // $activity['time_elapsed'] = $this->time_elapsed_string($activity['created_at'], $full = false, $lang = 'en');
        }

        return response()->json(array('success' => true, 'data' => $activities));
    }

    public function getActivityOptions(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        if (!isset($_GET["activity_id"])) {
            return response()->json(array('success' => false));
        }

        $activity_id = $_GET["activity_id"];
        $packages = Activity_Model::getActivityOptions($activity_id);

        // foreach ($packages as &$package) {
            // $package['price'] = (string) $activity['price'];
            // $activity['activity_image'] = $profile_image;
            // $activity['time_elapsed'] = $this->time_elapsed_string($activity['created_at'], $full = false, $lang = 'en');
        // }

        return response()->json(array('success' => true, 'data' => $packages));
    }
}
