<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



    // LOGIN / SIGNUP
    Route::post('signup', 'Api\Auth\AuthController@signup');
    Route::post('verify_otp', 'Api\Auth\AuthController@verify_otp');
    Route::post('resend_otp', 'Api\Auth\AuthController@resendOTP');
    Route::post('login', 'Api\Auth\AuthController@login');
    Route::post('socialLogin', 'Api\Auth\AuthController@socialLogin');
    Route::post('forgot_password', 'Api\Auth\AuthController@forgot_password');

    //COUNTRY STATE CITY
    Route::get('countries', 'Api\CountryController@index');
    Route::get('states', 'Api\StateController@index');
    Route::get('cities/{state_id?}', 'Api\CityController@index');

    // CMS
    Route::get('cms','Api\CmsController@cms_page');
    Route::get('privacy_policy/{locale}','Api\CmsController@privacy_policy_url')->name('cms.privacy_policy');
    Route::get('terms_and_conditions/{locale}','Api\CmsController@customer_terms_conditions_url')->name('cms.terms_and_conditions');
    Route::get('about_us/{locale}','Api\CmsController@about_us_url')->name('cms.about_us');


    Route::middleware(['auth:api'])->group( function() {
        Route::post('logout', 'Api\Auth\AuthController@logout');
        Route::get('user/lang/{lang?}', 'Api\UserController@preferred_language');

        //UPDATE DEVICE TOKEN
        Route::post('update_device_token', 'Api\HomeController@update_device_token');

        // PROFILE
        Route::get('profile', 'Api\ProfileController@profile');
        Route::post('update_profile', 'Api\ProfileController@update_profile');
        Route::post('profile/update_image', 'Api\ProfileController@update_image');
        Route::post('profile/update/mobile', 'Api\ProfileController@update_mobile_number');
        Route::post('profile/verify/mobile', 'Api\ProfileController@verifyMobileNumber');


        //CONTACT US
        // Route::post('contact_us',  'Api\ContactUsController@contact_us');

        
        // Notification
        Route::get('notifications', 'Api\NotificationController@index');
        Route::get('clear_all_notifications', 'Api\NotificationController@clear_all_notifications');
        Route::post('notification/markAsRead', 'Api\NotificationController@markAsRead');
        Route::get('unread_notification/count', 'Api\NotificationController@unreadNotifCount');
        Route::post('delete_notification', 'Api\NotificationController@deleteNotification');

        //Buddies
        Route::post('find_new_buddy',  'Api\BuddyController@find_new_buddy');
        Route::post('add_buddy',  'Api\BuddyController@add_buddy');
        Route::post('remove_buddy',  'Api\BuddyController@remove_buddy');
        Route::get('buddy_requests',  'Api\BuddyController@buddy_requests');
        Route::post('accept_buddy_request',  'Api\BuddyController@accept_buddy_request');
        Route::post('reject_buddy_request',  'Api\BuddyController@reject_buddy_request');
        Route::post('buddy_list',  'Api\BuddyController@buddy_list');
        Route::post('buddy_profile',  'Api\BuddyController@buddy_profile');
        Route::get('favourite/{id}',   'Api\BuddyController@add_remove_favourite');
        Route::get('favourite_buddies',   'Api\BuddyController@favourite_buddies');
        Route::get('all_buddies',   'Api\BuddyController@all_buddies');

        //Tasks
        Route::post('create_task',  'Api\TaskController@create_task');
        Route::get('todays_tasks',  'Api\TaskController@todays_tasks');
        Route::post('move_to_trash',  'Api\TaskController@move_to_trash');
        Route::post('mark_as_done',  'Api\TaskController@mark_as_done');
        Route::post('reschedule_task',  'Api\TaskController@reschedule_task');
        Route::post('task_dates',  'Api\TaskController@task_dates');
        Route::post('assigned_to_me',  'Api\TaskController@assigned_to_me');
        Route::post('assigned_to_buddy',  'Api\TaskController@assigned_to_buddy');
        Route::post('buddy_assigned_to_me',  'Api\TaskController@buddy_assigned_to_me');
        Route::post('me_assigned_to_buddy',  'Api\TaskController@me_assigned_to_buddy');
        Route::post('pending_requests',  'Api\TaskController@pending_requests');
        Route::post('history',  'Api\TaskController@history');
        Route::post('accept_task',  'Api\TaskController@accept_task');
        Route::post('reject_task',  'Api\TaskController@reject_task');

        //dashboard
        Route::get('dashboard',  'Api\HomeController@dashboard');
    });