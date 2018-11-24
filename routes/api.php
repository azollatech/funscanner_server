<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:api')->group(function() {
	// Activity
	Route::get('/activities', 'ActivityController@getActivitiesByCategory');
	Route::get('/activity-options', 'ActivityController@getActivityOptions');
	// Route::post('/create-new-activity', 'ActivityController@createNewActivity');
	// Route::get('/my-activity', 'ActivityController@getMyActivity');
	// Profile
	Route::get('/profile', 'ProfileController@profile');
	Route::post('/save-new-profile', 'ProfileController@saveNewProfile');
	Route::get('/profile-image', 'ProfileController@profileImage');
	Route::post('/save-profile-image', 'ProfileController@saveProfileImage');
	Route::get('/user-info', 'ProfileController@userInfo');
	Route::post('/save-basic-info', 'ProfileController@saveBasicInfo');
	Route::get('/get-profile', 'DateController@getOthersProfile');

	// QR Code
	Route::get('/date-token', 'QRController@generateNewDatingCode');
	Route::post('/start-dating', 'QRController@startDating');

	Route::post('/send-request', 'DateController@sendRequest');
	Route::post('/accept-request', 'DateController@acceptRequest');
	Route::post('/decline-request', 'DateController@declineRequest');
	Route::get('/select-request', 'DateController@selectRequest');
	Route::get('/select-date', 'DateController@selectDate');
	Route::get('/select-notification', 'DateController@selectNotification');
	Route::get('/select-history', 'DateController@selectHistory');

	// Search
	// Route::get('/filter-search', 'SearchController@filterSearch');
	Route::get('/image/{token}', 'SearchController@image');

	// Device Token
	Route::post('/save-device-token', 'DeviceTokenController@saveDeviceToken');

	// Review
	Route::get('/get-unreviewed-dates', 'ReviewController@getUnreviewedDates');
	Route::post('/submit-review', 'ReviewController@submitReview');
	Route::get('/get-all-startup-info', 'ReviewController@getAllInfoWhenStartUp');

	// Change Password
	Route::get('/password-status', 'ChangePasswordController@getPasswordStatus');
	Route::post('/change-password', 'ChangePasswordController@changePassword');

	// Suggestions
	Route::post('/give-suggestions', 'SuggestionsController@giveSuggestions');
	Route::post('/report', 'SuggestionsController@report');

	// Peacher Sign Up
	// Route::post('/peacher-signup', 'PeacherSignupController@peacherSignup');

	// Peacher make call
	// Route::post('/call', 'ForwardCallController@callPlivoNumber');

	// Logout
	Route::post('/logout', 'LoginApiController@logout');
});
// Route::get('/search', 'SearchController@search');
// Route::get('/test-search', 'SearchController@testSearch');
Route::post('/google-login', 'LoginApiController@googleLogin');
Route::post('/facebook-login', 'LoginApiController@facebookLogin');
Route::post('/signup', 'LoginApiController@signup');


// Panda Bubble
Route::post('panda-bubble-suggestions', function() {
	if (!empty($_POST['name']) || !isset($_POST['email']) || !isset($_POST['suggestions'])) {
		return response()->json([ 'success' => false, 'name' => $_POST['name'], 'email' => $_POST['email'], 'suggestions' => $_POST['suggestions'] ]);
	}
    DB::table('panda_bubble_suggestions')
        ->insert(['name' => $_POST['name'], 'email' => $_POST['email'], 'suggestions' => $_POST['suggestions']]);

    return response()->json(['success' => true]);
});
