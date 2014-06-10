<?php

class PaperController extends BaseController {
	/**
	 * List of papers.
	 */
	public function getIndex() {
		$papers = Auth::user()->author->papers;
		return View::make('paper/index', array('papers' => $papers));
	}

	/**
	 * Edit or create a paper.
	 */
	public function anyEdit($id = null) {
		$autorList = Author::all();
		$authors = array();
		$selectedauthors = array();
		$paper = null;
		$submissionEvent = null;

		// get authors
		foreach ($autorList as $author) {
			if (Auth::user()->author->id != $author->id) {
				$authors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
			} else {
				$selectedauthors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
			}
		}

		// get edit model
		if (!is_null($id)) {
			$paper = Paper::with('authors', 'submissions', 'submissions.event', 'submissions.event.detail')->find($id);
			if (!is_null($paper)) {
				$allowed = false;
				foreach ($paper->authors as $author) {
					if ($author->id == Auth::user()->author->id) {
						$allowed = true;
					}
					if (array_key_exists($author->id, $authors)) {
						$selectedauthors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
						unset($authors[$author->id]);
					}
				}
				if (!$allowed) {
					App::abort(404);
				}
				if ($paper->activeSubmission) {
					$submissionEvent = $paper->activeSubmission->event;
				}
			}
		}

		// get created event
		if (Session::has('conference_edition_id')) {
			$edition = ConferenceEdition::with('event')->find(Session::get('conference_edition_id'));
			if ($edition) {
				$submissionEvent = $edition->event;
			}
		} else if (Session::has('workshop_id')) {
			$workshop = Workshop::with('event')->find(Session::get('workshop_id'));
			if ($workshop) {
				$submissionEvent = $workshop->event;
			}
		}

		$submission = $this->getSubmissionArray($submissionEvent);

		return View::make('paper/edit', array('authors' => $authors, 'model' => $paper, 'selectedauthors' => $selectedauthors, 'submission' => $submission));
	}

	/**
	 * Returns the submission array used by the paper/edit view
	 */
	private function getSubmissionArray($event) {
		$submission = array();
		$submission['kind'] = 'none';
		$submission['activeDetailID'] = null;
		$submission['conferenceName'] = null;
		$submission['editionOption'] = array();
		$submission['editionName'] = null;
		$submission['workshopName'] = null;

		if ($event) {
			$submission['kind'] = $event->detail_type;
			$detail = $event->detail;
			$submission['activeDetailID'] = $detail->id;
			if ($detail->isWorkshop()) {
				$submission['workshopName'] = $detail->name;
			} else if ($detail->isConferenceEdition()) {
				$submission['conferenceName'] = $detail->conference->name;
				$submission['editionOption'] = array($detail->id => 'Dummy');
				$submission['editionName'] = $detail->edition;
			}
		}
		return $submission;
	}

	/**
	 * Handle edit/create result.
	 */
	public function postEditTarget() {
		$validation = Paper::validate(Input::all());

		if ($validation->fails()) {
			return Redirect::action('PaperController@anyEdit')->withErrors($validator)->withInput();
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
		// User has to be author
		$paper->authors()->attach(Auth::user()->author->id);
		
		if (isset($input['selectedauthors'])) {
			$authors = $input['selectedauthors'];
			if($authors != null) {
				foreach ($authors as $author) {
					if (Auth::user()->author->id != $author)
						$paper->authors()->attach($author);
				}
			}
		}

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
		
		return Redirect::action('PaperController@getIndex');	
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
}
