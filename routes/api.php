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
    Route::get('about_us/{locale}','Api\CmsController@cancel_policy_url')->name('cms.about_us');


    Route::middleware(['auth:api'])->group( function() {
        Route::post('logout', 'Api\Auth\AuthController@logout');
        Route::get('user/lang/{lang?}', 'Api\UserController@preferred_language');

        //UPDATE DEVICE TOKEN
        Route::post('update_device_token', 'Api\HomeController@update_device_token');

        // PROFILE
        Route::get('profile', 'Api\ProfileController@profile');
        Route::post('update_profile', 'Api\ProfileController@update_profile');
        Route::post('profile/update_image', 'Api\ProfileController@update_image');


        //CONTACT US
        Route::post('contact_us',  'Api\ContactUsController@contact_us');

        
        // Notification
        Route::get('notifications', 'Api\NotificationController@index');
        Route::get('clear_all_notifications', 'Api\NotificationController@clear_all_notifications');
        Route::post('notification/markAsRead', 'Api\NotificationController@markAsRead');
        Route::get('unread_notification/count', 'Api\NotificationController@unreadNotifCount');
        Route::post('delete_notification', 'Api\NotificationController@deleteNotification');      
    });