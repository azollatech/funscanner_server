<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use App\Guest_Model as Guest_Model;
use DateTime;

class GuestController extends Controller
{

    public function peacherSignUp() {
        return View::make('guest/peacher-sign-up');
    }

    public function peacherSignUpPost() {
        $errors = [];
    	if (empty($_POST['fullname'])) {
            $errors["fullname"] = "Full Name must be filled in.";
    	}
    	if (empty($_POST['nickname'])) {
            $errors["nickname"] = "Nickname must be filled in.";
    	}
    	if (empty($_POST['date_of_birth'])) {
            $errors["date_of_birth"] = "Date of Birth must be filled in.";
    	}
    	if (empty($_POST['email'])) {
            $errors["email"] = "Email Address must be filled in.";
    	}
    	if (empty($_POST['phone'])) {
            $errors["phone"] = "Phone Number must be filled in.";
    	}

        if (!empty($errors)) {
            return Redirect::to('peacher-sign-up')->withErrors($errors)->withInput();
        }

		$dt = DateTime::createFromFormat("Y-m-d", $_POST['date_of_birth']);
		if (!($dt !== false && !array_sum($dt->getLastErrors()))) {
    		return Redirect::to('peacher-sign-up')->withErrors("date_of_birth", "Date of Birth is not in correct format.")->withInput();
		}
		Guest_Model::savePeacherSignUp($_POST);

        return View::make('guest/peacher-sign-up-success');
    }
    
}
