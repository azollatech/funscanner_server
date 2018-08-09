<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use App\Profile_Model as Profile_Model;
use App\User;
use Response;


class ProfileController extends Controller
{

    public function userInfo(Request $request) {
        $user = $request->user();

        $user_id = $user->getId();

        $userInfo = Profile_Model::getUserInfo($user_id);

        return response()->json(array("success"=>true, "data"=>$userInfo));
        // return $userInfo;
    }

    public function profile(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
        } else {
            $lang = 'en';
        }

        $profile = Profile_Model::getProfile($user_id);

        if (!empty($profile['date_of_birth'])) {
            $profile['zodiac'] = $this->dateOfBirthToZodiac($profile['date_of_birth'], $lang);
            $profile['age'] = $this->dateOfBirthToAge($profile['date_of_birth']);
            unset($profile['date_of_birth']);
        }

        return response()->json(array("success"=>true, "data"=>$profile));
        // return $userInfo;
    }
    
    public function saveNewProfile(Request $request) {
        $user = $request->user();

        $user_id = $user->getId();

        if (Profile_Model::saveNewProfile($user_id, $_POST)) {
            return response()->json(array("success"=>true));
        } else {
            return response()->json(array("success"=>false));
        }
    }

    public function profileImage(Request $request) {
        $user = $request->user();

        $user_id = $user->getId();

        $storage_path = storage_path('app/profile-images/');
        $files = glob($storage_path.$user_id."/*.{jpg,png,gif}", GLOB_BRACE);
        return Response::download($files[0]);
    }

    public function saveProfileImage(Request $request) {
        $user = $request->user();

        $user_id = $user->getId();


        $this->writeToLog(print_r($_FILES,1));

        if (!isset($_FILES["file"]) || empty($_FILES["file"]["name"]) || empty($_FILES["file"]["tmp_name"])) {
            return response()->json(array("success"=>false, "error"=>"No file uploaded."));
        }

        $storage_path = storage_path('app/profile-images/');
        $target_dir = $storage_path.$user_id.'/';
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
        } else {
            // echo "File is not an image.";
            return response()->json(array("success"=>false, "error"=>"File is not an image."));
        }

        // Check if directory already exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir);
        }

        // Delete all files in the directory
        $files = glob($target_dir.'*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }

        // Check file size
        if ($_FILES["file"]["size"] > 100000000) {
            return response()->json(array("success"=>false, "error"=>"Sorry, your file is too large."));
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            return response()->json(array("success"=>false, "error"=>"Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
        }

        // All pass
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            return response()->json(array("success"=>false, "error"=>"Sorry, there was an error uploading your file."));
        }

        return response()->json(array("success"=>true));
    }

    public function saveBasicInfo(Request $request) {
        $user = $request->user();

        $user_id = $user->getId();

        $this->writeToLog(print_r($_POST,1));

        if (empty($_POST["nickname"]) || empty($_POST["dob"]) || empty($_POST["gender"])) {
            return response()->json(array("success"=>false, "error"=>"Nickname, date of birth and gender must not be empty."));
        }
        if (Profile_Model::saveBasicInfo($user_id, $_POST["nickname"], $_POST["dob"], $_POST["gender"])) {
            return response()->json(array("success"=>true));
        } else {
            return response()->json(array("success"=>false, "error"=>"Database Error.".$user_id));
        }
    }

    private function writeToLog($text) {

        $myfile = fopen("../customlog/access.log", "a") or die("Unable to open file!");
        fwrite($myfile, $text."\n");
        fclose($myfile);
    }
    
}
