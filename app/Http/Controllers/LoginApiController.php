<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use App\LoginApi_Model as LoginApi_Model;
use DateTime;
use Google_Client;
use App\User as User;
use Validator;
use Hash;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class LoginApiController extends Controller
{

    public function googleLogin() {
        $this->writeToLog(date("Y-m-d H:i:s"));
        $this->writeToLog("Google Login");
        if (!empty($_POST['id_token']) && !empty($_POST['email'])) {
            $id_token = $_POST['id_token'];
            $client = new Google_Client(['client_id' => '871826341744-rh8tnfec340v0sd52an7q18lf6p6n03l.apps.googleusercontent.com']);
            $payload = $client->verifyIdToken($id_token);
            if ($payload) {
                $userid = $payload['sub'];
                if (!empty($payload['email']) && !empty($payload['name']) && !empty($payload['given_name']) && !empty($payload['family_name'])) {
                    $email = $payload['email'];
                    $name = $payload['name'];
                    $given_name = $payload['given_name'];
                    $family_name = $payload['family_name'];

                    $user = User::where('email', $email)->first();
                    if ($user) {
                        $user_id = $user->getId();
                        $first_time = false;
                    } else {
                        $username_temp = strtolower(preg_replace("/[^a-z]/i", "", $name));
                        while (User::where('username', $username_temp)->first()) {
                            $username_temp = $username_temp.rand(10,300);
                        }
                        $username = $username_temp;
                        $user_id = LoginApi_Model::saveGoogleLoginToUser($userid, $email, $name, $given_name, $family_name, $username);
                        $user = User::find($user_id);
                        $first_time = true;

                        // LoginApi_Model::createImageToken($user_id, $this->generateRandomString());
                        LoginApi_Model::createFullUserID($user_id, $this->generateRandomString());
                    }

                    // grant access token
                    $token = $user->createToken("google")->accessToken;

                    return response()->json(['success' => 1, 'user_id' => $user_id, 'access_token' => $token, 'first_time' => $first_time]);

                } else {
                    // User info not returned from google
                    return response()->json(['success' => 0, 'error' => 302]);
                }
            } else {
                // Invalid ID token
                return response()->json(['success' => 0, 'error' => 301]);
            }
        } else {
            // Empty parameters
            return response()->json(['success' => 0, 'error' => 300]);
        }
    }

    public function facebookLogin() {
        $this->writeToLog(date("Y-m-d H:i:s"));
        $this->writeToLog("Facebook Login");
        if (!empty($_POST['access_token'])) {
            $access_token = $_POST['access_token'];
            $input_token = $access_token;
            $app_token = '131873067366212|LipgeeNVQKDsjjvi2ehIOwTQlV4';
            $url = 'https://graph.facebook.com/debug_token?input_token='.$input_token.'&access_token='.$app_token;

			$response = file_get_contents($url);
			$array = json_decode($response, true);
			// print_r($array);

            if (isset($array['data']['is_valid'])) {

            	if ($array['data']['is_valid'] == true) {
					$user_details = "https://graph.facebook.com/me?fields=email,first_name,last_name,name&access_token=" .$access_token;
					$response = file_get_contents($user_details);
					$response = json_decode($response, true);

		            if (!empty($response['email']) && !empty($response['name']) && !empty($response['first_name']) && !empty($response['last_name'])) {
		                $email = $response['email'];
		                $name = $response['name'];
		                $first_name = $response['first_name'];
		                $last_name = $response['last_name'];
		                $userid = $response['id'];

		                $user = User::where('email', $email)->first();
		                if ($user) {
		                    $user_id = $user->getId();
                            $first_time = false;
		                } else {
		                    $username_temp = strtolower(preg_replace("/[^a-z]/i", "", $name));
		                    while (User::where('username', $username_temp)->first()) {
		                        $username_temp = $username_temp.rand(10,300);
		                    }
		                    $username = $username_temp;
		                    $user_id = LoginApi_Model::saveFacebookLoginToUser($userid, $email, $name, $first_name, $last_name, $username);
		                    $user = User::find($user_id);
                            $first_time = true;

                            // LoginApi_Model::createImageToken($user_id, $this->generateRandomString());
                            LoginApi_Model::createFullUserID($user_id, $this->generateRandomString());
		                }

		                // grant access token
		                $token = $user->createToken("facebook")->accessToken;
                        $this->writeToLog("success");

		                return response()->json(['success' => 1, 'user_id' => $user_id, 'access_token' => $token, 'first_time' => $first_time]);

		            } else {
		                // User info not returned from google
                        $this->writeToLog("403");
		                return response()->json(['success' => 0, 'error' => 403]);
		            }
            	} else {
			        // Invalid access token
                    $this->writeToLog("402");
			        return response()->json(['success' => 0, 'error' => 402]);
            	}
            } else {
		        // Invalid access token
                $this->writeToLog("401");
		        return response()->json(['success' => 0, 'error' => 401]);
            }
        } else {
            // Empty parameters
            $this->writeToLog("400");
            return response()->json(['success' => 0, 'error' => 400]);
        }
    }

    public function signup() {
        if (!empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password'])) {
            $array['username'] = $_POST['username'];
            $array['email'] = $_POST['email'];
            $array['password'] = $_POST['password'];

            $rules = array('username' => 'unique:users,username');
            $validator = Validator::make($array, $rules);
            if ($validator->fails()) {
                return response()->json(['success' => 0, 'error' => 101]);
            }

            $rules = array('password' => 'min:6');
            $validator = Validator::make($array, $rules);
            if ($validator->fails()) {
                return response()->json(['success' => 0, 'error' => 102]);
            }

            $rules = array('email' => 'unique:users,email');
            $validator = Validator::make($array, $rules);
            if ($validator->fails()) {
                return response()->json(['success' => 0, 'error' => 104]);
            }

            $rules = array('email' => 'email');
            $validator = Validator::make($array, $rules);
            if ($validator->fails()) {
                return response()->json(['success' => 0, 'error' => 103]);
            }

            $user = new User();
            $user->password = Hash::make($array['password']);
            $user->email = $array['email'];
            $user->username = $array['username'];
            $user->save();

            $user_id = $user->getId();
            LoginApi_Model::saveEmailSignUpToUser($user_id);
            // LoginApi_Model::createImageToken($user_id, $this->generateRandomString());
            LoginApi_Model::createFullUserID($user_id, $this->generateRandomString());

            $client = new Client(); //GuzzleHttp\Client
            $response = $client->post(URL . "oauth/token", [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => '2',
                    'client_secret' => 'to5Wwb8Swvk61Bn5qQXZSMNeQSWuBxXbahFyyS5V',
                    'username' => $array['username'],
                    'password' => $array['password']
                ]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            // print_r($data);

            return response()->json(['success' => 1, 'response' => $data]);
        } else {
            return response()->json(['success' => 0, 'error' => 100]);
        }
    }
    public function logout(Request $request) {
        $user = $request->user();
        $user_id = $user->getId();
        $accessToken = $user->token();

        // remove device_token
        if (!empty($_POST['device_token'])) {
            $device_token = $_POST['device_token'];
            LoginApi_Model::removeDeviceToken($device_token, $user_id);
        }

        // revoke access_token
        LoginApi_Model::revokeAccessToken($accessToken);

        return response()->json(['success' => true]);
    }

    private function writeToLog($text) {

        $myfile = fopen("../customlog/access.log", "a") or die("Unable to open file!");
        fwrite($myfile, $text."\n");
        fclose($myfile);
    }
    private function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
