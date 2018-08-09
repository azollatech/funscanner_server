<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\User;
use App\Suggestions_Model as Suggestions_Model;

class SuggestionsController extends Controller
{
    public function giveSuggestions(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $suggestion = $_POST['suggestions'];

        $result = Suggestions_Model::saveSuggestions($user_id, $suggestion);
        if ($result) {
            return response()->json(array('success' => true));
        } else {
            return response()->json(array('success' => false));
        }
    }
    public function report(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();

        $report = $_POST['report'];

        $result = Suggestions_Model::report($user_id, $report);
        if ($result) {
            return response()->json(array('success' => true));
        } else {
            return response()->json(array('success' => false));
        }
    }
}
