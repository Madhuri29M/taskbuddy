<?php
use Illuminate\Support\Facades\Route;


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
// Route::get('/', 'HomeController@index')->name('front');
// Route::get('/login', 'Auth\LoginController@showLoginForm');

Route::prefix('admin')->group( function() {
	Auth::routes(['verify' => true]);
});

Route::group(['prefix' => 'admin','namespace' => 'Admin'], function () {
	//Change Language
	Route::get('lang/{locale}', 'HomeController@lang')->name('locale');

	Route::middleware(['auth:web','verified'])->group( function() {	

	   	//roles and permisisons
	    Route::resource('roles','RoleController');
	    Route::resource('permissions','PermissionController')->except(['show','edit','update']);
	    
		//Home
	    Route::get('/home', 'HomeController@index')->name('home');
		Route::get('/admin/{user}/edit','UserController@admin_edit')->name('admin_edit_profile');
		Route::put('admin/{user}','UserController@admin_update')->name('admin_update_profile');
		Route::post('/user/change_password/','UserController@change_password')->name('user_password_change');

		//user_status
		Route::resource('user', 'UserController');
		Route::post('/user/ajax', 'UserController@index_ajax')->name('dt_user');
		Route::post('/user/status', 'UserController@status')->name('user_status');

		// Route::post('/user/send_notification/','UserController@send_notification')->name('send_notification');
		Route::get('/export/','UserController@export')->name('user.export');

		// Route::post('/user/send_notification/','UserController@send_notification')->name('send_notification');
		Route::get('/export/','UserController@export')->name('user.export');

		// Settings
		Route::resource('setting','SettingController')->only(['index','create','store']);
		Route::post('setting/update/','SettingController@update')->name('setting.update');

		//Cms
		Route::resource('cms', 'CmsController');
		Route::post('/cms/status', 'CmsController@status')->name('cms_status');
	    

		//CUSTOMER MASS MESSAGE
	    Route::resource('c_message', 'CustomerMessageController');
	    Route::post('c_message/send','CustomerMessageController@send')->name('user_message');
	    Route::post('/c_message/ajax', 'CustomerMessageController@index_ajax')->name('dt_user_index');

		// Countries
		Route::resource('country', 'CountryController');
		Route::post('/country/ajax', 'CountryController@index_ajax')->name('dt_country');
		Route::post('/country/status', 'CountryController@status')->name('country_status');

			// States
	    Route::resource('states', 'StateController');
	    Route::post('/states/ajax', 'StateController@index_ajax')->name('dt_states');
	    Route::post('/state/status', 'StateController@status')->name('status_states');
	    Route::get('get_states/{country_id}', 'HomeController@get_state')->name('states.list');

	    // Cities
	    Route::resource('cities', 'CityController');
	    Route::post('/cities/ajax', 'CityController@index_ajax')->name('dt_cities');
	    Route::post('/city/status', 'CityController@status')->name('status_cities');

	    

	    //customer
	    Route::resource('customers','CustomerController');
	    Route::post('/customers/ajax', 'CustomerController@index_ajax')->name('ajax_customers');
	    Route::post('/customers/status', 'CustomerController@status')->name('status');
	    Route::post('/customers/send_notification/','CustomerController@send_notification')->name('send_notification');
	    Route::get('customers/{customer_id}/tasks', 'CustomerController@tasks_index')->name('customers.tasks');
	    Route::post('/customers/tasks/ajax', 'CustomerController@tasks_index_ajax')->name('ajax_tasks');

	    // Contact Us
	    Route::resource('contact_us', 'ContactUsController');
	    Route::post('/contactus/ajax', 'ContactUsController@index_ajax')->name('ajax_contact_us');
	    Route::post('/contact/status', 'ContactUsController@status')->name('contact_status');
	    Route::post('contact_us/r_status','ContactUsController@r_status')->name('r_status');//r - resolved status 
	});
});
//SUPPORT
Route::get('clear-cache/all', 'CacheController@clear_cache');