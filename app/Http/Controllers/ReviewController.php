<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\User;
use App\Profile_Model as Profile_Model;
use App\Review_Model as Review_Model;
use App\Date_Model as Date_Model;
use App\Search_Model as Search_Model;

class ReviewController extends Controller
{
    public function getUnreviewedDates(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $unreviewed_dates = Review_Model::getUnreviewedDates($user_id);
        foreach ($unreviewed_dates as &$unreviewed_date) {
            if ($unreviewed_date['is_peacher']) {
                $profile = Date_Model::getUserInfo($unreviewed_date['user_id']);
            } else {
                $profile = Date_Model::getUserInfo($unreviewed_date['peacher_id']);
            }
            if (!empty($profile['date_of_birth'])) {
                $profile['zodiac'] = $this->dateOfBirthToZodiac($profile['date_of_birth']);
                $profile['age'] = (string) $this->dateOfBirthToAge($profile['date_of_birth']);
                unset($profile['date_of_birth']);
            }
            $imageToken = Search_Model::convertPeacherIDToImageToken($profile['user_id']);
            $profile['profile_image'] = URL."api/image/".$imageToken;
            $unreviewed_date['profile'] = $profile;
        }

        return response()->json(array('success' => true, 'data' => $unreviewed_dates));
    }
    public function submitReview(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (empty($_POST['rating']) || empty($_POST['date_id'] || empty($_POST['is_peacher']))) {
            return;
        }
        if (isset($_POST['forbidden_action'])) {
            $forbidden_action = $_POST['forbidden_action'];
        } else {
            $forbidden_action = NULL;
        }
        if ($_POST['is_peacher'] == "true") {
            Review_Model::changeDateToReviewed_peacher($user_id, $_POST['date_id'], $forbidden_action);
        } else {
            Review_Model::changeDateToReviewed_user($user_id, $_POST['date_id'], $forbidden_action);
        }
        Review_Model::storeReview($user_id, $_POST['date_id'], $_POST['rating']);

        return response()->json(array('success' => true));
    }
    private function getBadges($user_id) {
        $badges = Date_Model::countNonReadNotifications($user_id);
        return $badges;
    }
    private function checkBasicInfo($user_id) {
        $first_time = Profile_Model::checkBasicInfo($user_id);
        return $first_time;
    }
    private function checkPhoneNumber($user_id) {
        $havePhoneNumber = Profile_Model::checkPhoneNumber($user_id);
        return $havePhoneNumber;
    }
    private function getUnreviewedDates_startUp($user_id) {
        $unreviewed_dates = Review_Model::getUnreviewedDates($user_id);
        foreach ($unreviewed_dates as &$unreviewed_date) {
            if ($unreviewed_date['is_peacher']) {
                $profile = Date_Model::getUserInfo($unreviewed_date['user_id']);
            } else {
                $profile = Date_Model::getUserInfo($unreviewed_date['peacher_id']);
            }
            if (!empty($profile['date_of_birth'])) {
                $profile['zodiac'] = $this->dateOfBirthToZodiac($profile['date_of_birth']);
                $profile['age'] = (string) $this->dateOfBirthToAge($profile['date_of_birth']);
                unset($profile['date_of_birth']);
            }
            $imageToken = Search_Model::convertPeacherIDToImageToken($profile['user_id']);
            $profile['profile_image'] = URL."api/image/".$imageToken;
            $unreviewed_date['profile'] = $profile;
        }

        return $unreviewed_dates;
    }
    public function getAllInfoWhenStartUp(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $badges = $this->getBadges($user_id);
        $first_time = $this->checkBasicInfo($user_id);
        $havePhoneNumber = $this->checkPhoneNumber($user_id);
        $unreviewed_dates = $this->getUnreviewedDates_startUp($user_id);

        return response()->json(array('success' => true, 'data' => ['badges' => $badges, 'first_time' => $first_time, 'unreviewed_dates' => $unreviewed_dates, 'have_phone_number' => $havePhoneNumber]));
    }
}
