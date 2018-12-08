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
        return View::make('admin/add-new-activity')->with(array("districts" => $districts));
    }

    public function peacherApproval() {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }

        $all_peachers = Admin_Model::getPeachers();
        return View::make('admin/peacher-approval')->with(array("user_id" => $user_id, "username" => $username, "all_peachers" => $all_peachers));
    }

    public function withdrawal() {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }

        $all_current_balance = Admin_Model::getCurrentBalance('all');
        return View::make('admin/withdrawal')->with(array("user_id" => $user_id, "username" => $username, "all_current_balance" => $all_current_balance));
    }
    public function users(){
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        $all_users = Admin_Model::getUsers();
        $all_peachers = Admin_Model::getAllPeachers();
        $all_admins = Admin_Model::getAllAdmins();
        // echo "<pre>". print_r($all_users,1). "</pre>";
        return View::make('admin/users')->with(array("user_id" => $user_id, "username" => $username, "all_users" => $all_users, "all_peachers"=>$all_peachers,"all_admins"=>$all_admins));

    }

    public function approvePeacher($user_id, $peacher_signup_id) {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        $error_message = [];
        $success_message = [];

        $result = Admin_Model::approvePeacher($user_id, $peacher_signup_id);
        return redirect('admin/peacher-approval')->with('error_message', $error_message)->with('success_message', $success_message);
    }

    public function withdraw($peacher_id, $current_balance) {
        if (Auth::check()){
            $user_id = Auth::user()->getId();
            $username = Auth::user()->getUsername();
        } else {
            return Redirect::to('/');
        }
        $error_message = [];
        $success_message = [];

        $result = Admin_Model::withdraw($peacher_id, $current_balance);
        return redirect('admin/withdrawal')->with('error_message', $error_message)->with('success_message', $success_message);
    }
}
