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

Route::get('registration/', 'RegistrationController@showForm');
Route::post('register/', 'RegistrationController@register');
Route::post('login/', 'LoginController@authenticate');
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

Route::get('paper/', function()
{
	return View::make('paper');
});

Route::post('paper/create', 'PaperController@createPaper');

Route::get('timeline.html', function() 
{
	return View::make('timeline');
});

Route::get('profile.html', function() 
{
	return View::make('profile');
});

Route::get('overview.html', function() 
{
	return View::make('overview');
});

Route::get('data.html', function() 
{
	return View::make('data');
});