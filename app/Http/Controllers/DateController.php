<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\User;
use App\Date_Model as Date_Model;
use App\Search_Model as Search_Model;
use App\Classes\PushNotification as PushNotification;

class DateController extends Controller
{
    public function sendRequest(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $peacher_id = $_POST['peacher_id'];
        $activity_id = $_POST['activity_id'];
        $start_datetime = $_POST['start_datetime'];
        $end_datetime = $_POST['end_datetime'];
        $place = $_POST['place'];
        $message_to_peacher = $_POST['message_to_peacher'];
        Date_Model::sendRequest($user_id, $peacher_id, $activity_id, $start_datetime, $end_datetime, $place, $message_to_peacher);

        // Notification
        Date_Model::makeNotification('request_received', $user_id, $peacher_id);
        // Push Notification
        ob_start();
        PushNotification::sendPushNotification($user_id, $peacher_id, "向你送出活動邀請");
        ob_end_clean();

        return response()->json(array('success' => true));
    }
    
    public function acceptRequest(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();
        // $user_id = 111;

        $request_id = $_POST['request_id'];
        Date_Model::changeRequestStatus($request_id, 'accepted');
        $request = Date_Model::getEachRequest($request_id, 'accepted')[0];
        // echo "<pre>", print_r($request, 1),"</pre>";
        Date_Model::moveToDate($request);

        // Notification
        Date_Model::makeNotification('request_accepted', $request['peacher_id'], $request['user_id']);
        // Push Notification
        ob_start();
        PushNotification::sendPushNotification($request['peacher_id'], $request['user_id'], "接納了你的活動邀請");
        ob_end_clean();
        return response()->json(array('success' => true));
    }

    public function declineRequest(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();
        // $user_id = 39;
        
        $request_id = $_POST['request_id'];
        Date_Model::changeRequestStatus($request_id, 'declined');
        $request = Date_Model::getEachRequest($request_id, 'accepted')[0];

        // Notification
        Date_Model::makeNotification('request_declined', $request['peacher_id'], $request['user_id']);
        // Push Notification
        ob_start();
        PushNotification::sendPushNotification($request['peacher_id'], $request['user_id'], "拒絕了你的活動邀請");
        ob_end_clean();
        return response()->json(array('success' => true));
    }

    public function selectRequest(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        $requests = Date_Model::getAllRequests($user_id, 'pending');
        foreach ($requests as &$request) {
            if ($request['is_peacher']) {
                $profile = Date_Model::getUserInfo($request['user_id']);
            } else {
                $profile = Date_Model::getUserInfo($request['peacher_id']);
            }
            if (!empty($profile['date_of_birth'])) {
                $profile['zodiac'] = $this->dateOfBirthToZodiac($profile['date_of_birth'], $lang);
                $profile['age'] = (string) $this->dateOfBirthToAge($profile['date_of_birth']);
                unset($profile['date_of_birth']);
            }

            // image token
            $imageToken = Search_Model::convertPeacherIDToImageToken($profile['user_id']);
            $profile['profile_image'] = URL."api/image/".$imageToken;

            // full user id
            $fullUserID = Search_Model::convertPeacherIDToFullUserID($profile['user_id']);
            $profile['full_user_id'] = $fullUserID;

            // put into request
            $request['profile'] = $profile;

            // time elapsed
            $request['time_elapsed'] = $this->time_elapsed_string($request['created_at'], false, $lang);

            // activity
            $item = [];
            $item['activity_name'] = $request['activity_name'];
            $item['category'] = $request['category'];
            $item['activity_id'] = $request['activity_id'];
            $item['price'] = $request['price'];
            $request['activity'] = $item;
            unset($request['activity_name']);
            unset($request['category']);
            unset($request['activity_id']);
            unset($request['price']);
        }
        return response()->json(array('success' => true, 'data' => $requests));
    }

    public function selectDate(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        $dates = Date_Model::getAllDates($user_id, 'pending');
        // echo "<pre>", print_r($dates, 1),"</pre>";
        foreach ($dates as &$date) {
            if ($date['is_peacher']) {
                $profile = Date_Model::getUserInfo($date['user_id']);
            } else {
                $profile = Date_Model::getUserInfo($date['peacher_id']);
            }
            if (!empty($profile['date_of_birth'])) {
                $profile['zodiac'] = $this->dateOfBirthToZodiac($profile['date_of_birth'], $lang);
                $profile['age'] = (string) $this->dateOfBirthToAge($profile['date_of_birth']);
                unset($profile['date_of_birth']);
            }

            // image token
            $imageToken = Search_Model::convertPeacherIDToImageToken($profile['user_id']);
            $profile['profile_image'] = URL."api/image/".$imageToken;

            // full user id
            $fullUserID = Search_Model::convertPeacherIDToFullUserID($profile['user_id']);
            $profile['full_user_id'] = $fullUserID;

            // put into date
            $date['profile'] = $profile;

            // time elapsed
            $date['time_elapsed'] = $this->time_elapsed_string($date['start_datetime'], false, $lang);

            // activity
            $item = [];
            $item['activity_name'] = $date['activity_name'];
            $item['category'] = $date['category'];
            $item['activity_id'] = $date['activity_id'];
            $item['price'] = $date['price'];
            $date['activity'] = $item;
            unset($date['activity_name']);
            unset($date['category']);
            unset($date['activity_id']);
            unset($date['price']);
        }
        return response()->json(array('success' => true, 'data' => $dates));
    }

    public function selectNotification(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        // Get notifications
        $notifications = Date_Model::getAllNotifications($user_id);
        // echo "<pre>", print_r($dates, 1),"</pre>";
        foreach ($notifications as &$notification) {
            $profile = Date_Model::getUserInfo($notification['sender_id']);
            $imageToken = Search_Model::convertPeacherIDToImageToken($notification['sender_id']);
            $profile['profile_image'] = URL."api/image/".$imageToken;
            $notification['profile'] = $profile;
            $notification['time_elapsed'] = $this->time_elapsed_string_long($notification['created_at'], $lang);
        }
        
        // Update notifications to read already
        Date_Model::updateNotificationsToReadAlready($user_id);

        return response()->json(array('success' => true, 'notification' => $notifications));
    }

    public function selectHistory(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        $histories = Date_Model::getAllHistories($user_id);
        // echo "<pre>", print_r($dates, 1),"</pre>";
        foreach ($histories as &$history) {
            if ($history['is_peacher']) {
                $profile = Date_Model::getUserInfo($history['user_id']);
            } else {
                $profile = Date_Model::getUserInfo($history['peacher_id']);
            }
            if (!empty($profile['date_of_birth'])) {
                $profile['zodiac'] = $this->dateOfBirthToZodiac($profile['date_of_birth'], $lang);
                $profile['age'] = (string) $this->dateOfBirthToAge($profile['date_of_birth']);
                unset($profile['date_of_birth']);
            }
            $imageToken = Search_Model::convertPeacherIDToImageToken($profile['user_id']);
            $profile['profile_image'] = URL."api/image/".$imageToken;
            $fullUserID = Search_Model::convertPeacherIDToFullUserID($profile['user_id']);
            $profile['full_user_id'] = $fullUserID;
            $history['profile'] = $profile;
            $history['time_elapsed'] = $this->time_elapsed_string($history['end_datetime'], false, $lang);
            
            // activity
            $item = [];
            $item['activity_name'] = $history['activity_name'];
            $item['category'] = $history['category'];
            $item['activity_id'] = $history['activity_id'];
            $item['price'] = $history['price'];
            $history['activity'] = $item;
            unset($history['activity_name']);
            unset($history['category']);
            unset($history['activity_id']);
            unset($history['price']);
        }
        return response()->json(array('success' => true, 'data' => $histories));
    }

    public function getOthersProfile(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $fullUserID = $_GET["full_user_id"];

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        $profile = Date_Model::getProfileFromFullUserID($fullUserID);
        if ($profile) {
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
            $profile['price_list'] = Search_Model::getPriceList($profile['user_id']);
            $profile['availability'] = json_decode(Search_Model::getAvailability($profile['user_id']));
            // unset($profile['user_id']);
            return response()->json(array('success' => true, 'data' => $profile));
        } else {
            return response()->json(array('success' => false));
        }

    }

    // public function test() {
    //     $result = $this->time_elapsed_string_long("2017-10-14 11:00:00");
    //     // echo $result;
    //     echo $result;
    // }
}
