<?php

class PaperController extends BaseController {
	
	public function getIndex() {
		$papers = Paper::all();
		return View::make('paper/index', array('papers' => $papers));
	}
	
	public function getCreate() {
		return View::make('paper/create');
	}

	/* POST METHOD */
	public function postCreate()
	{
		$input = Input::all();
		
		$paper = Paper::create( $input );
		$paper->authors()->attach(Auth::user()->author->id);
		
		return $this->getIndex();
	}
	
	public function missingMethod($parameters = array())
	{
	    var_dump($parameters);
	}

}
