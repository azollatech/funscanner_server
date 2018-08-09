<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\User;
use App\PeacherSignup_Model as PeacherSignup_Model;

class PeacherSignupController extends Controller
{
    public function peacherSignup(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $full_name = $_POST['full_name'];

        $result = PeacherSignup_Model::savePeacherSignup($user_id, $full_name);
        if ($result) {
            return response()->json(array('success' => true));
        } else {
            return response()->json(array('success' => false));
        }
    }
}
