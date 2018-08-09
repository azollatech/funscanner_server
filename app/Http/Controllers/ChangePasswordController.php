<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\User;
use App\User_Model as User_Model;
use Hash;

class ChangePasswordController extends Controller
{
    public function getPasswordStatus(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $exist = User_Model::checkPasswordExist($user_id);
        return response()->json(array('success' => true, 'data' =>  ['exist' => $exist] ));
    }
    public function changePassword(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        if (!empty($_POST['original_pw'])) {
            $original_pw = $_POST['original_pw'];
        }
        if (!empty($_POST['new_pw'])) {
            $new_pw = $_POST['new_pw'];
        } else {
            return;
        }

        $exist = User_Model::checkPasswordExist($user_id);
        if ($exist) {
            $match = Hash::check($original_pw, $user->password);
            if ($match) {
                $user->password = Hash::make($new_pw);
                $user->save(); 
            } else {
                return response()->json(array('success' => false, 'error' => 'Original password is incorrect.'));
            }
        } else {
            $user->password = Hash::make($new_pw);
            $user->save();
        }
        return response()->json(array('success' => true));
    }
}
