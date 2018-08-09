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
require_once base_path() . "/vendor/autoload.php";
use Twilio\Rest\Client;
use Twilio\Twiml;

// Required if your environment does not handle autoloading
// require base_path() . '/vendor/autoload.php';

class TestController extends Controller
{	
    public function call() {
    	// $AccountSid = "AC8dc94ed7ed34fc2d52f7cc26750ef163";
	    // $AuthToken = "b1fbe57c1cb639b493ac5ab8de5c5c1e";

	    // // Step 3: Instantiate a new Twilio Rest Client
	    // $client = new Client($AccountSid, $AuthToken);

	    // try {
	    //     // Initiate a new outbound call
	    //     $call = $client->account->calls->create(
	    //         // Step 4: Change the 'To' number below to whatever number you'd like 
	    //         // to call.
	    //         "+85268439928",

	    //         // Step 5: Change the 'From' number below to be a valid Twilio number 
	    //         // that you've purchased or verified with Twilio.
	    //         "+85230083204 ",

	    //         // Step 6: Set the URL Twilio will request when the call is answered.
	    //         array("url" => "http://demo.twilio.com/welcome/voice/")
	    //     );
	    //     echo "Started call: " . $call->sid;
	    // } catch (Exception $e) {
	    //     echo "Error: " . $e->getMessage();
	    // }

	    $response = new Twiml();
		$dial = $response->dial();
		$dial->number('68439928');

		echo $response;
	}
}