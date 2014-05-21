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

Route::post('create/', 'CreateDomainController@index');
Route::get('enrollInGroup/', 'EnrollInGroupController@enroll');
Route::get('enroll/', 'EnrollInGroupController@index');
Route::get('registration/', 'RegistrationController@showForm');
Route::post('register/', 'RegistrationController@register');
Route::match(array('GET', 'POST'), 'login/', 'LoginController@authenticate');
Route::get('logout/', 'LoginController@logout');
Route::get('/', 'LoginController@showForm');
/*
Route::get('/', function()
{
	if (Auth::check()) {
		Auth::logout();
		return 'Logged in already, logging out';
	}
	else if (Auth::attempt(array('email' => 'admin@example.org', 'password' => '1234'))) {
		return 'Log in successful!<br>Author named ' . Auth::user()->author->first_name . ' with a paper-count of: ' . Auth::user()->author->papers()->count();
	} else {
		return 'Log in not successful';
	}
});
*/
Route::get('test/', function()
{
	return View::make('test');
});
Route::get('test/controller/', 'HomeController@showWelcome');


Route::controller('paper', 'PaperController');

Route::get('timeline.html', array('before' => 'auth', function() 
{
	return View::make('timeline');
}));

Route::get('profile.html', array('before' => 'auth', function() 
{
	return View::make('profile');
}));

Route::get('overview.html', array('before' => 'auth', function() 
{
	return View::make('overview');
}));

Route::get('data.html', array('before' => 'auth', function() 
{
	return View::make('data');
}));

