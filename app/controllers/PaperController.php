<?php

class PaperController extends BaseController {
	
	public function getIndex($id = null) {
		$papers = Auth::user()->author->papers;
		return View::make('paper/index', array('papers' => $papers));
	}
	
	public function getEdit($id = null) {
		$autorList = Author::all();
		$authors = array();
		$selectedauthors = array();
		$paper = null;

		// active submission information
		$submission = array();
		$submission['kind'] = 'none';
		$submission['active'] = null;
		$submission['activeDetailID'] = null;
		$submission['conferenceName'] = null;
		$submission['editionOption'] = array();
		$submission['workshopName'] = null;
		
		foreach ($autorList as $author) {
			if($author->id != Auth::user()->author->id)
				$authors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
		}
		
		// Edit Paper
		if (!is_null($id)) {
			$paper = Paper::with('authors', 'submissions', 'submissions.event', 'submissions.event.detail')->find($id);
			if (!is_null($paper)) {
				foreach ($paper->authors as $author) {
					if (array_key_exists($author->id, $authors)) {
						$selectedauthors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
						unset($authors[$author->id]);
					}
				}
				if (!$paper->submissions->isEmpty()) {
					$activeSubmission = $paper->submissions->first();
					$submission['active'] = $activeSubmission;
					$submission['kind'] = $activeSubmission->event->detail_type;
					$detail = $activeSubmission->event->detail;
					$submission['activeDetailID'] = $detail->id;
					if ($detail->isWorkshop()) {
						$submission['workshopName'] = $detail->name;
					} else if ($detail->isConferenceEdition()) {
						$submission['conferenceName'] = $detail->conference->name;
						$submission['editionOption'] = array($detail->id => 'Dummy');
					}
				}
			}
		}

		// New Paper
		return View::make('paper/create', array('authors' => $authors, 'model' => $paper, 'selectedauthors' => $selectedauthors, 'submission' => $submission));
	}

	public function postEdit() {
		$validation = Paper::validate(Input::all());

		if ($validation->fails()) {
			return Redirect::action('PaperController@getEdit')->withErrors($validator)->withInput();
		}

		$edit = (bool) Input::get('id');
		if ($edit) {
			$paper = Paper::find(Input::get('id'));
			$paper->fill(Input::all());
			
			if(!$paper->save()) {
				return "Problem with updating paper!";
			}
		} else {
			$paper = Paper::create(Input::all());
		}
		
		$input = Input::all();
		$paper->authors()->detach();
		if (isset($input['selectedauthors'])) {
			$authors = $input['selectedauthors'];
			if($authors != null) {
				foreach ($authors as $author) {
					$paper->authors()->attach($author);
				}
			}
		}
		
		$paper->authors()->attach(Auth::user()->author->id);

		$submissionKind = Input::get('submissionKind');
		if($submissionKind != 'none') {
			$detail = null;
			if ($submissionKind == 'ConferenceEdition') {
				$detail = ConferenceEdition::with('event')->find(Input::get('conference_edition_id'));
			} else if ($submissionKind == 'Workshop') {
				$detail = Workshop::with('event')->find(Input::get('workshop_id'));
			}
			if ($detail) {
				$submission = new Submission();
				$submission->paper_id = $paper->id;
				$submission->event_id = $detail->event->id;
				$submission->save();
			}
		}
		
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
