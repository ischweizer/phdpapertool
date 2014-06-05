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

// Sites which should be accessible in both states
// / should be accessible in both states. We can still redirect to another site if we want to (as it currently does).
// either way it should become our "welcome" page.
Route::get('/', 'LoginController@showForm');

// In here go sites which are accessible as a guest
Route::group(array('before' => 'guest'), function() {
	// filter is "before", so the login attempt belongs here when the user is still a guest
	Route::post('login', 'LoginController@authenticate');

	Route::get('registration', 'RegistrationController@showForm');
	Route::post('register', 'RegistrationController@register');
});


// In here go sites which are accessible as a authenticated user
Route::group(array('before' => 'auth'), function()
{
	Route::controller('paper', 'PaperController');

	Route::get('timeline', function() 
	{
		return View::make('timeline');
	});

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

	Route::get('refuse', 'RequestDomainController@refuse');
	Route::get('confirm', 'RequestDomainController@confirm');
	Route::get('handle', 'RequestDomainController@index');
	Route::get('create', 'CreateDomainController@index');
	Route::get('enrollInGroup', 'EnrollInGroupController@enroll');
	Route::get('enroll', 'EnrollInGroupController@index');
});

