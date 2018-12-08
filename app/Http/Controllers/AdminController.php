<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use View;
use Redirect;
use Illuminate\Support\Facades\Validator;
use App\Admin_Model as Admin_Model;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Image;

class AdminController extends Controller
{
    public function index() {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        }
        return View::make('admin/dashboard')->with(array("user_id" => $user_id, "username" => $username));
    }
    public function addNewActivity() {
        $districts = DB::table('district')
            ->orderby('district_id')
            ->get();

        $categories = DB::table('peachy_activity_category')
            ->get();

        return View::make('admin/add-new-activity')->with(array("districts" => $districts, "categories" => $categories));
    }
    public function postNewActivity(){
        if (empty($_POST['category_id'])) {
            return "Category must not be blank.";
        }

        $category_id = $_POST['category_id'];
        unset($_POST['category_id']);

        // form data
        $data = $this->postParamsToData($_POST);

        // activity id
        // $data['activity_id'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 12);

        // image file data
        $data2 = $this->storeImageFile($data, $_FILES);

        // insert into activity table
        DB::table('peachy_activity')
            ->insert($data2);

        // insert into mapping table
        $activity_id = DB::getPdo()->lastInsertId();
        DB::table('peachy_activity')
            ->insert(array('category_id' => $category_id, 'activity_id' => $activity_id));

        return redirect('admin/add-new-activity')->with(array("success" => "Activity added."));
    }

    private function postParamsToData($post) {
        $data['activity_name'] = $post['activity_name'];
        $data['price'] = $post['price'];
        $data['details'] = $post['details'];
        $data['address'] = $post['address'];
        $data['district_id'] = !empty($post['district_id']) ? $post['district_id'] : NULL;
        $data['traffic'] = $post['traffic'];
        $data['must_know'] = $post['must_know'];
        $data['duration'] = $post['duration'];
        if (!empty($post['weekdays_open_hour']) && !empty($post['weekdays_open_mins']) && !empty($post['weekdays_close_hour']) && !empty($post['weekdays_close_mins'])) {
            $post['weekdays_open_hour'] = ltrim($post['weekdays_open_hour'], '0');
            $post['weekdays_close_hour'] = ltrim($post['weekdays_close_hour'], '0');
            $data['opening_hours'] =
            "Mon: ".$post['weekdays_open_hour'].":".$post['weekdays_open_mins'].$post['weekdays_open_apm']." - ".$post['weekdays_close_hour'].":".$post['weekdays_close_mins'].$post['weekdays_close_apm']."\n".
            "Tue: ".$post['weekdays_open_hour'].":".$post['weekdays_open_mins'].$post['weekdays_open_apm']." - ".$post['weekdays_close_hour'].":".$post['weekdays_close_mins'].$post['weekdays_close_apm']."\n".
            "Wed: ".$post['weekdays_open_hour'].":".$post['weekdays_open_mins'].$post['weekdays_open_apm']." - ".$post['weekdays_close_hour'].":".$post['weekdays_close_mins'].$post['weekdays_close_apm']."\n".
            "Thu: ".$post['weekdays_open_hour'].":".$post['weekdays_open_mins'].$post['weekdays_open_apm']." - ".$post['weekdays_close_hour'].":".$post['weekdays_close_mins'].$post['weekdays_close_apm']."\n".
            "Fri: ".$post['weekdays_open_hour'].":".$post['weekdays_open_mins'].$post['weekdays_open_apm']." - ".$post['weekdays_close_hour'].":".$post['weekdays_close_mins'].$post['weekdays_close_apm']."\n";
        }
        if (!empty($post['sats_open_hour']) && !empty($post['sats_open_mins']) && !empty($post['sats_close_hour']) && !empty($post['sats_close_mins']) && !empty($post['sats_close_apm']) && !empty($post['sats_close_apm'])){
            $post['sats_open_hour'] = ltrim($post['sats_open_hour'], '0');
            $post['sats_close_hour'] = ltrim($post['sats_close_hour'], '0');
            $data['opening_hours'] .= "Sat: ".$post['sats_open_hour'].":".$post['sats_open_mins'].$post['sats_open_apm']." - ".$post['sats_close_hour'].":".$post['sats_close_mins'].$post['sats_close_apm'];
        } else if (isset($post['sats_closed'])){
            $data['opening_hours'] .= "Sat: Closed \n";
        }
        if (!empty($post['suns_open_hour']) && !empty($post['suns_open_mins']) && !empty($post['suns_close_hour']) && !empty($post['suns_close_mins']) && !empty($post['suns_close_apm']) && !empty($post['suns_close_apm'])){
            $post['suns_open_hour'] = ltrim($post['suns_open_hour'], '0');
            $post['suns_close_hour'] = ltrim($post['suns_close_hour'], '0');
            $data['opening_hours'] .= "Sun: ".$post['suns_open_hour'].":".$post['suns_open_mins'].$post['suns_open_apm']." - ".$post['suns_close_hour'].":".$post['suns_close_mins'].$post['suns_close_apm'];
        } else if (isset($post['suns_closed'])){
            $data['opening_hours'] .= "Sun: Closed \n";
        }

        return $data;
    }
    private function storeImageFile($data, $files) {
        if (!isset($files["activity_photo"]) || empty($files["activity_photo"]["name"]) || empty($files["activity_photo"]["tmp_name"])) {
            // return response()->json(array("success"=>false, "error"=>"No file uploaded."));
        } else {
            $storage_path = public_path('activity_img/');
            $imageFileType = pathinfo($files["activity_photo"]["name"],PATHINFO_EXTENSION);
            $filename = $this->generateRandomString() . '.' . $imageFileType;
            $target_file = $storage_path . $filename;
            while (file_exists($target_file)) {
                $filename = $this->generateRandomString() . '.' . $imageFileType;
                $target_file = $storage_path . $filename;
            }

            // Check if image file is a actual image or fake image
            $check = getimagesize($files["activity_photo"]["tmp_name"]);
            if($check == false) {
                echo response()->json(array("success"=>false, "error"=>"File is not an image."));
                return false;
            }

            // Check file size
            if ($files["activity_photo"]["size"] > 100000000) {
                echo response()->json(array("success"=>false, "error"=>"Sorry, your file is too large."));
                return false;
            }

            // Allow certain file formats
            if($imageFileType != "jpg") {
                echo response()->json(array("success"=>false, "error"=>"Sorry, only JPG files are allowed."));
                return false;
            }

            // All pass
            if (!move_uploaded_file($files["activity_photo"]["tmp_name"], $target_file)) {
                echo response()->json(array("success"=>false, "error"=>"Sorry, there was an error uploading your file."));
                return false;
            }

            // create thumbnail
            $sqThumbPath = $storage_path.'sq_thumb/'.$filename;
            $recThumbPath = $storage_path.'rect_thumb/'.$filename;
            $sq = Image::make($target_file)
                ->fit(640, 640);
            $sq->save($sqThumbPath);
            $rect = Image::make($target_file)
                ->fit(360, 160);
            $rect->save($recThumbPath);

            // filename
            $data['activity_photo'] = $filename;
        }
        return $data;
    }
    private function generateRandomString($length = 24) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

// public function peacherApproval() {
//     if (Auth::check()){
//         $user_id = Auth::user()->getId();
//         $username = Auth::user()->getUsername();
//     } else {
//         return Redirect::to('/');
//     }
//
//     $all_peachers = Admin_Model::getPeachers();
//     return View::make('admin/peacher-approval')->with(array("user_id" => $user_id, "username" => $username, "all_peachers" => $all_peachers));
// }
//
// public function withdrawal() {
//     if (Auth::check()){
//         $user_id = Auth::user()->getId();
//         $username = Auth::user()->getUsername();
//     } else {
//         return Redirect::to('/');
//     }
//
//     $all_current_balance = Admin_Model::getCurrentBalance('all');
//     return View::make('admin/withdrawal')->with(array("user_id" => $user_id, "username" => $username, "all_current_balance" => $all_current_balance));
// }

//     public function users(){
//         if (Auth::check()){
//             $user_id = Auth::user()->getId();
//             $username = Auth::user()->getUsername();
//         } else {
//             return Redirect::to('/');
//         }
//         $all_users = Admin_Model::getUsers();
//         $all_peachers = Admin_Model::getAllPeachers();
//         $all_admins = Admin_Model::getAllAdmins();
//         // echo "<pre>". print_r($all_users,1). "</pre>";
//         return View::make('admin/users')->with(array("user_id" => $user_id, "username" => $username, "all_users" => $all_users, "all_peachers"=>$all_peachers,"all_admins"=>$all_admins));
//
//     }
//
//     public function approvePeacher($user_id, $peacher_signup_id) {
//         if (Auth::check()){
//             $user_id = Auth::user()->getId();
//             $username = Auth::user()->getUsername();
//         } else {
//             return Redirect::to('/');
//         }
//         $error_message = [];
//         $success_message = [];
//
//         $result = Admin_Model::approvePeacher($user_id, $peacher_signup_id);
//         return redirect('admin/peacher-approval')->with('error_message', $error_message)->with('success_message', $success_message);
//     }
//
//     public function withdraw($peacher_id, $current_balance) {
//         if (Auth::check()){
//             $user_id = Auth::user()->getId();
//             $username = Auth::user()->getUsername();
//         } else {
//             return Redirect::to('/');
//         }
//         $error_message = [];
//         $success_message = [];
//
//         $result = Admin_Model::withdraw($peacher_id, $current_balance);
//         return redirect('admin/withdrawal')->with('error_message', $error_message)->with('success_message', $success_message);
//     }
// }
