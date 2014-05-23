<?php

class PaperController extends BaseController {
	
	public function getIndex() {
		return View::make('paper');
	}

	/* POST METHOD */
	public function postCreate()
	{
		$input = Input::all();
		$paper = Paper::create( $input );
		$paper->authors()->attach(Auth::user()->author->id);
		return View::make('hello');
	}

}
