<?php

class PaperController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	
	public function getIndex() {
		if (Auth::check()) {
			return View::make('paper');
		} else {
			return 'You are not logged in!';
		}
	}

	/* POST METHOD */
	public function postCreate()
	{
		if (Auth::check()) {
			$input = Input::all();
			$paper = Paper::create( $input );
			$paper->authors()->attach(Auth::user()->author->id);
			return View::make('hello');
		} else {
			return 'You are not logged in!';
		}
	}

}
