<?php

class PaperController extends BaseController {
	
	public function getIndex($id = null) {
		$papers = Paper::all();
		return View::make('paper/index', array('papers' => $papers));
	}
	
	public function getCreate($id = null) {
		$autorList = Author::all();
		$authors = array();
		$selectedauthors = array();
		$paper = new Paper();
		
		foreach ($autorList as $author) {
			if($author->id != Auth::user()->author->id)
				$authors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
		}
		
		// Edit Paper
		if (!is_null($id)) {
			$paper = Paper::with('authors')->find($id);
			if (!is_null($paper)) {
				
				foreach ($paper->authors as $author) {
					if (array_key_exists($author->id, $authors)) {
						$selectedauthors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
						unset($authors[$author->id]);
					}
				}
			}
		}
		
		// New Paper
		return View::make('paper/create', array('authors' => $authors, 'paper' => $paper, 'selectedauthors' => $selectedauthors));
	}

	public function postCreate($id = null)
	{
		$input = Input::all();
		
		if (!is_null($id)) {
			$paper = Paper::find($id);
			$paper->title = $input['title'];
			$paper->abstract = $input['abstract'];
			$paper->repository_url = $input['repository_url'];
			
			if(!$paper->save()) {
				return "Problem with updating paper!";
			}
		} else {
			$paper = Paper::create( $input );
			$paper->authors()->attach(Auth::user()->author->id);
		}
		
		$paper->authors()->detach();
		$authors = $input['selectedauthors'];
		if($authors != null) {
			foreach ($authors as $author) {
				$paper->authors()->attach($author);
			}
		}
		
		return Redirect::to('paper/index');
		
	}
	
	/*public function missingMethod($parameters = array())
	{
	    var_dump($parameters);
	}*/

}
