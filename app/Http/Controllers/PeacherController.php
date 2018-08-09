<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use View;
use Redirect;
use Illuminate\Support\Facades\Validator;
use App\Peacher_Model as Peacher_Model;
use Illuminate\Http\Request;
use App\Http\Requests;

class PeacherController extends Controller
{
    public function index() {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        return View::make('peacher/index')->with(array("user_id" => $user_id, "username" => $username));
    }
	public function setSchedule() {
    	if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }

        $schedule = Peacher_Model::getSchedule($user_id);
        $schedule_json = $schedule[0]['schedule_json'];
        // $schedule_json = json_encode($schedule);
        return View::make('peacher/set-schedule')->with(array("user_id" => $user_id, "username" => $username, "schedule_json" => $schedule_json));
    }
    public function PostSetSchedule(){

        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        
        $error_message = [];
        $success_message = [];
        
        $schedules = $_POST['schedule'];
        $data = Peacher_Model::setSchedule($user_id, $schedules);
        // if ($data == 1)
            $success_message[] = 'Schedule is updated.';
        // else
        //     $error_message[] = 'Schedule is not updated'; 

        // return redirect('peacher/set-schedule')->with(array("user_id" => $user_id, "username" => $username, 'success_message', $success_message));
        return redirect('peacher/set-schedule')->with('error_message', $error_message)->with('success_message', $success_message);
        // return View::make('peacher/set-schedule')->with(array("user_id" => $user_id, "username" => $username));

        // $a = 1;
        // foreach ($updates as $update) {
        //     $data = Peacher_Model::updatePrice($user_id, $update['category'], $update['activity'], $update['price']);
        //     if(!empty($data) && $data == 1)
        //         $success_message[] =  '#'.$a.': Record has been edited successfully.';
        //     $a++;
        //     // echo '<pre>' . print_r($data,1) . '</pre>';
        // }
        // if(!empty($_POST['new'])){
        //     // echo '<pre>' . print_r($_POST['new'],1) . '</pre>';
        //     $news = $_POST['new'];

        //     foreach ($news as $new) {
                
        //         if(empty($new['category']) || empty($new['activity']) || empty($new['price']))
        //             $error_message[] = '#'.$a.': Record cannot be saved! Activity, Category and Price must not be empty.';
        //         else{                
        //             $data =  Peacher_Model::insertPrice($user_id, $new['category'], $new['activity'], $new['price']);
        //             if ($data == 1)
        //                 $success_message[] = '#'.$a.': Record has been added successfully.';
        //             else
        //                 $error_message[] = '#'.$a.': Record cannot be saved. Duplicated entry of the same activity.'; 
        //             // echo '<pre>' . print_r($data,1) . '</pre>';
                    
        //         }
        //         $a++;
        //     }
        // }
       

        // // Peacher_Model::deletePriceFromUser($user_id);
        // // foreach ($_POST['new'] as $row) {
        // //             // echo '<pre>' . print_r($row,1) . '</pre>';
        // //     Peacher_Model::insertPrice($user_id, $row['category'], $row['activity'], $row['price']);
        // // }


        // return redirect('peacher/set-price')->with('error_message', $error_message)->with('success_message', $success_message);
    }
    public function editProfile() {
    	if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        return View::make('peacher/edit-profile')->with(array("user_id" => $user_id, "username" => $username));
    }
     public function editProfile_post() {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }

    	$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		    if($check !== false) {
		        echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } else {
		        echo "File is not an image.";
		        $uploadOk = 0;
		    }
		}
    }

    public function setPrice() {
    	if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }

        $userPriceInfo = Peacher_Model::getUserPriceInfo($user_id);
        $count = sizeof($userPriceInfo);
        // echo '<pre>' . print_r($userPriceInfo,1) . '</pre>';
        $categories = Peacher_Model::getAllCategories();
        $raw = Peacher_Model::getAllCategoriesAndActivities();
        $activityOptions = Peacher_Model::getActivityOptions();

        foreach ($raw as $item) {
            $activities[$item['category_id'].":::".$item['category']][]= $item['activity_id'].":::".$item['activity'];
        }
        $activities_json = json_encode($activities);
        return View::make('peacher/set-price')->with(array("user_id" => $user_id, "username" => $username, "userPriceInfo" => $userPriceInfo, "categories" => $categories, "activityOptions" => $activityOptions,"activities_json" => $activities_json, "count" => $count));
    }

    public function postSetPrice() {

        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        
        $error_message = [];
        $success_message = [];
        $a = 1;
        
        if(!empty($_POST['update'])){
            $updates = $_POST['update'];
            // echo '<pre>' . print_r($updates,1) . '</pre>';
            // $user_id = $_POST['user_id'];
            // Peacher_Model::deletePriceFromUser($user_id);
            
            foreach ($updates as $update) {
                $data = Peacher_Model::updatePrice($user_id, $update['category'], $update['activity'], $update['price']);
                if(!empty($data) && $data == 1)
                    $success_message[] =  '#'.$a.': Record has been edited successfully.';
                $a++;
                // echo '<pre>' . print_r($data,1) . '</pre>';
            }
        }
        
        if(!empty($_POST['new'])){
            // echo '<pre>' . print_r($_POST['new'],1) . '</pre>';
            $news = $_POST['new'];

            foreach ($news as $new) {
                
                if(empty($new['category']) || empty($new['activity']) || empty($new['price']))
                    $error_message[] = '#'.$a.': Record cannot be saved! Activity, Category and Price must not be empty.';
                else{                
                    $data =  Peacher_Model::insertPrice($user_id, $new['category'], $new['activity'], $new['price']);
                    if ($data == 1)
                        $success_message[] = '#'.$a.': Record has been added successfully.';
                    else
                        $error_message[] = '#'.$a.': Record cannot be saved. Duplicated entry of the same activity.'; 
                    // echo '<pre>' . print_r($data,1) . '</pre>';
                    
                }
                $a++;
            }
        }
       

        // Peacher_Model::deletePriceFromUser($user_id);
        // foreach ($_POST['new'] as $row) {
        //             // echo '<pre>' . print_r($row,1) . '</pre>';
        //     Peacher_Model::insertPrice($user_id, $row['category'], $row['activity'], $row['price']);
        // }


        return redirect('peacher/set-price')->with('error_message', $error_message)->with('success_message', $success_message);


    //     $student_name = !empty($_POST['student_name'])?$_POST['student_name']:"";
    //     $email = !empty($_POST['email'])?$_POST['email']:"";
    //     $university = !empty($_POST['university'])?$_POST['university']:"";
    //     $major = !empty($_POST['major'])?$_POST['major']:"";
    //     $year_of_graduation = !empty($_POST['year_of_graduation'])?$_POST['year_of_graduation']:"";
    //     if (empty($_POST['password'])){
    //         // Password not changed
    //         UserManagement_Model::editUser($user_id, $student_name, $email, $university, $major, $year_of_graduation);
    //     } else {
    //         $password = $_POST['password'];
    //         UserManagement_Model::editUserAndPassword($user_id, $password, $student_name, $email, $university, $major, $year_of_graduation);    
    //     }
    //     return redirect('admin/user-management')->with('success_message', "Student info edited successfully.");
   

                // echo '<pre>' . print_r($_POST , 1) . '</pre>';
    }
    public function deletePrice($activity_id) {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        Peacher_Model::deletePrice($user_id, $activity_id);
        return redirect('peacher/set-price');

    }
    public function activityRecords() {
    	if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }

        $past_activities = Peacher_Model::getActivities($user_id, 'started');
        $total_earning = 0;
        $upcoming_earning = 0;
        foreach($past_activities as $activity){
            $total_earning = $total_earning + $activity['amount'];
        }
        $upcoming_activities = Peacher_Model::getActivities($user_id, 'pending');
        foreach($upcoming_activities as $activity){
            $upcoming_earning = $upcoming_earning + $activity['amount'];
        }
        $withdrawals = Peacher_Model::getWithdrawals($user_id);
        // $total_pending_withdrawals_amount = Peacher_Model::getWithdrawalsTotalAmount($user_id, 'pending');
        $total_confirmed_withdrawals_amount = Peacher_Model::getWithdrawalsTotalAmount($user_id);
        $bank_info = Peacher_Model::getBankInfo($user_id);
        return View::make('peacher/activity-records')->with(array(
            "user_id" => $user_id, 
            "username" => $username, 
            "past_activities" => $past_activities, 
            "upcoming_activities" => $upcoming_activities, 
            "total_earning" => $total_earning, 
            "upcoming_earning" => $upcoming_earning, 
            "withdrawals" => $withdrawals, 
            // "total_pending_withdrawals_amount" => $total_pending_withdrawals_amount,
            "total_confirmed_withdrawals_amount" => $total_confirmed_withdrawals_amount,
            "bank_info" => $bank_info,
            ));
    }
    public function withdraw($total_earning) {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        $error_message = [];
        $success_message = [];

        $total_pending_withdrawals_amount = Peacher_Model::getWithdrawalsTotalAmount($user_id, 'pending');

        $amount_to_withdraw = $total_earning - $total_pending_withdrawals_amount[0]['total'];
        if ($amount_to_withdraw > 100) {
            Peacher_Model::withdraw($user_id, $amount_to_withdraw);
        } else {
            $error_message[] = "Minimum amount of $100 is required for each withdrawal.";
        }
        return redirect('peacher/activity-records')->with('error_message', $error_message)->with('success_message', $success_message);
    }
    public function addBankAccount() {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        $error_message = [];
        $success_message = [];

        if (empty($_POST['bank_name']) or empty($_POST['bank_account_num']) or empty($_POST['bank_account_holder_name'])) 
            $error_message[] = 'Bank information should not be empty.';
        else {
            $result = Peacher_Model::addBankAccount($user_id, $_POST['bank_name'], $_POST['bank_account_num'], $_POST['bank_account_holder_name']);
            if ($result)
                $success_message[] = 'Bank account added.';
            else 
                $error_message[] = 'Bank account cannot be added.';
        }

        return redirect('peacher/activity-records')->with('error_message', $error_message)->with('success_message', $success_message);
    }
    public function deleteBank() {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        $error_message = [];
        $success_message = [];

        $result = Peacher_Model::deleteBank($user_id);
        if ($result)
                $success_message[] = 'Bank account deleted.';
            else 
                $error_message[] = 'Bank account cannot be deleted.';
        return redirect('peacher/activity-records')->with('error_message', $error_message)->with('success_message', $success_message);
    }
}

