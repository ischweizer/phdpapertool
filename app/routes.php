<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//test
Route::get('cronjob', 'CronjobController@index');

// Sites which should be accessible in both states
// / should be accessible in both states. We can still redirect to another site if we want to (as it currently does).
// either way it should become our "welcome" page.
Route::get('/', 'LoginController@showForm');
Route::get('imprint', 'HomeController@getImprint');
Route::get('passwordShowReminder', 'RemindersController@getRemind');
Route::post('passwordSendReminder', 'RemindersController@postRemind');
Route::get('passwordShowReset', 'RemindersController@getReset');
Route::post('passwordPostReset', 'RemindersController@postReset');

// In here go sites which are accessible as a guest
Route::group(array('before' => 'guest'), function() {
	// filter is "before", so the login attempt belongs here when the user is still a guest
	Route::post('login', 'LoginController@authenticate');

	Route::get('registration', 'RegistrationController@showForm');
	Route::post('register', 'RegistrationController@register');


});

Route::get('activate', 'RegistrationController@activate');

Route::any('email_review', 'ReviewController@anyAuth');

// In here go sites which are accessible as a authenticated user
Route::group(array('before' => 'auth'), function()
{
	Route::controller('paper', 'PaperController');
	
	Route::controller('file', 'FileController');
	
	Route::controller('event', 'EventController');

	Route::controller('timeline', 'TimelineController');

	Route::controller('review', 'ReviewController');

	Route::get('overview', function() 
	{
		return View::make('overview');
	});

	Route::get('data', function() 
	{
		return View::make('data');
	});
	
	Route::controller('profile', 'ProfileController');
	Route::post('profile', 'ProfileController@save');

	Route::controller('conferences', 'ConferenceController');
	Route::controller('conference-editions', 'ConferenceEditionController');
	Route::controller('workshops', 'WorkshopController');

	// again, filter is "before" so logout belongs into this section
	Route::get('logout', 'LoginController@logout');

	Route::get('refuse', 'AdminController@refuse');
	Route::get('confirm', 'AdminController@confirm');
	Route::get('handle', 'AdminController@index');
	Route::get('giveRole', 'AdminController@giveUserRole');
	Route::get('removeRole', 'AdminController@deleteUserRole');
	Route::get('create', 'CreateDomainController@index');
	Route::get('enrollInGroup', 'EnrollInGroupController@enroll');
	Route::get('enroll', 'EnrollInGroupController@index');
        Route::get('leaveGroupLab', 'ProfileController@leaveGroupLab');
        Route::get('leaveAdminRole', 'ProfileController@leaveAdminRole');
	Route::get('reminderSettings', 'ReminderSettingsController@getForm');
	Route::post('reminderSaveSettings', 'ReminderSettingsController@saveSettings');
});

