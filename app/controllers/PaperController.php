<?php

class PaperController extends BaseController {
	
	public function getIndex($id = null) {
		$papers = Auth::user()->author->papers;//Paper::all();
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
	
	    $validation = Paper::validate();
		
		if ($validation->fails())
	    {
	    	return $validator->messages()->toJson();//"Validation Failed";
	        //return Redirect::to('register')->with_input()->with_errors($validation);
	    }
		
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
		}
		
		$paper->authors()->detach();
		$authors = $input['selectedauthors'];
		if($authors != null) {
			foreach ($authors as $author) {
				$paper->authors()->attach($author);
			}
		}
		$paper->authors()->attach(Auth::user()->author->id);
		
		return Redirect::to('paper/index');	
	}
	
	public function postCreateAuthor() {
		$input = Input::all();
		
		$validation = Author::validate();
		if ($validation->fails())
	    {
	        return "Validation Failed";
	        //return Redirect::to('register')->with_input()->with_errors($validation);
	    }
		$author = Author::create( $input );
		
		return Response::json(array($author->id => $author->last_name . " " . $author->first_name . " (" . $author->email . ")"));
	}
	
	/*public function missingMethod($parameters = array())
	{
	    var_dump($parameters);
	}*/

}
