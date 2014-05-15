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
Route::get('test/', function()
{
	return View::make('hello');
});
Route::get('test/controller/', 'HomeController@showWelcome');
