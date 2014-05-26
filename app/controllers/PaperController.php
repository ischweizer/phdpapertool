<?php

class PaperController extends BaseController {
	
	public function getIndex($id = null) {
		$papers = Paper::all();
		return View::make('paper/index', array('papers' => $papers));
	}
	
	public function getCreate() {
		$autorList = Author::all();
		$authors = array();
		
		foreach ($autorList as $author) {
			if($author->id != Auth::user()->author->id)
				$authors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
		}
		
		return View::make('paper/create', array('authors' => $authors));
	}

	/* POST METHOD */
	public function postCreate()
	{
		$input = Input::all();
		
		$paper = Paper::create( $input );
		$paper->authors()->attach(Auth::user()->author->id);
		
		$authors = $input['selectedauthors'];
		if($authors != null) {
			foreach ($authors as $author) {
				$paper->authors()->attach($author);
			}
		}
		
		return $this->getIndex();
	}
	
	public function missingMethod($parameters = array())
	{
	    var_dump($parameters);
	}

}
