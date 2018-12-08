<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('test', 'PaymentController@createCustomer');

// global contants for all requests
Route::group(['prefix' => ''], function() {
    define('URL','http://funscanner.co/');
});

// Route::get('test', 'PushNotiController@sendPushNotification');
// Route::get('calling', 'TestController@call');

// Home
Route::get('/', 'HomeController@loggedIn');
Route::get('logged-in', 'HomeController@loggedIn');

// Legacy
// Route::get('peacher-sign-up', 'GuestController@peacherSignUp');
// Route::post('peacher-sign-up/post', 'GuestController@peacherSignUpPost');

// Payment
// Route::get('payment', 'PaymentController@index');

// Admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
	Route::get('/', 'AdminController@index');
	Route::get('add-new-activity', 'AdminController@addNewActivity');
	// Route::get('users','AdminController@users');
	// Route::get('users/{Users}/{Peachers}/{Admin}', 'AdminController@users');
	// Route::get('peacher-approval', 'AdminController@peacherApproval');
	// Route::get('peacher-approval/post/{user_id}/{peacher_signup_id}', 'AdminController@approvePeacher');
	// Route::get('withdrawal', 'AdminController@withdrawal');
	// Route::get('withdrawal/withdraw/{peacher_id}/{current_balance}', 'AdminController@withdraw');
});

// Peacher
// Route::group(['prefix' => 'peacher', 'middleware' => 'auth'], function () {
// 	Route::get('/', 'PeacherController@index');
// 	Route::get('set-schedule', 'PeacherController@setSchedule');
// 	Route::post('set-schedule/post', 'PeacherController@PostSetSchedule');
// 	Route::get('edit-profile', 'PeacherController@editProfile');
// 	Route::post('edit-profile/post', 'PeacherController@editProfile_post');
// 	Route::get('set-price', 'PeacherController@setPrice');
// 	Route::post('set-price/post', 'PeacherController@postSetPrice');
// 	Route::get('delete-price/{activity_id}', 'PeacherController@deletePrice');
// 	Route::get('activity-records', 'PeacherController@activityRecords');
// 	Route::post('activity-records/withdraw/{total_earning}', 'PeacherController@withdraw');
// 	Route::post('add-bank-account', 'PeacherController@addBankAccount');
// 	Route::get('delete-bank', 'PeacherController@deleteBank');
// });

Route::post('forward-call', 'ForwardCallController@mapping');
// Route::post('incoming-call', 'ForwardCallController@getIncomingCall');
Route::post('incoming', 'ForwardCallController@getIncomingCall');

// Signin/Login/Logout
Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');
