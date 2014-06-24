<?php

use Carbon\Carbon;

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
		$autorList = Author::notAdmin()->get();
		$authors = array();
		$selectedauthors = array();
		$paper = null;
		$submissionEvent = null;
		$files = array();

		// get authors
		foreach ($autorList as $author) {
			if (Auth::user()->author->id != $author->id) {
				$authors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
			}
		}
		// User has to be the author
		$selectedauthors[Auth::user()->author->id] = Auth::user()->author->last_name . " " . Auth::user()->author->first_name . " (" . Auth::user()->author->email . ")";

		// get edit model
		if (!is_null($id)) {
			$paper = Paper::with('authors', 'activeSubmission', 'activeSubmission.event', 'activeSubmission.event.detail')->find($id);
			if (!is_null($paper)) {
				if (!$this->checkAccess($paper)) {
					App::abort(404);
				}
				$paperAuthors = $paper->authors;
				foreach ($paperAuthors as $author) {
					if (array_key_exists($author->id, $selectedauthors)) {
						unset($selectedauthors[$author->id]);
					}
					$selectedauthors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
					if (array_key_exists($author->id, $authors)) {
						unset($authors[$author->id]);
					}
				}
				if ($paper->activeSubmission) {
					$submissionEvent = $paper->activeSubmission->event;
				}
			}
			
			$files = $paper->files()->get();
		}

		$sessionEvent = $this->getSessionEvent();
		if ($sessionEvent) {
			$submissionEvent = $sessionEvent;
		}

		$submission = $this->getSubmissionArray($submissionEvent);

		return View::make('paper/edit', array('authors' => $authors, 'model' => $paper, 'selectedauthors' => $selectedauthors, 'submission' => $submission, 'files' => $files));
	}

	/**
	 * Returns an event object which was stored in the session or null.
	 */
	private function getSessionEvent() {
		$submissionEvent = null;

		// get event of old input (overwrites model event)
		// cannot use hasOldInput as it returns true for empty strings
		if (Session::getOldInput('conference_edition_id')) {
			$edition = ConferenceEdition::with('event')->find(Session::getOldInput('conference_edition_id'));
			if ($edition) {
				$submissionEvent = $edition->event;
			}
		} else if (Session::getOldInput('workshop_id')) {
			$workshop = Workshop::with('event')->find(Session::getOldInput('workshop_id'));
			if ($workshop) {
				$submissionEvent = $workshop->event;
			}
		}

		// get created event (overwrites old input event)
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

		return $submissionEvent;
	}

	/**
	 * Returns the submission array used by the paper/edit view.
	 *
	 * @param boolean $defaultToOldInput whether the 'kind' field is defaulted to old input if existing
	 */
	private function getSubmissionArray($event, $defaultToOldInput = true) {
		$submission = array();
		$submission['kind'] = 'none';
		$submission['activeDetailID'] = null;
		$submission['conferenceName'] = null;
		$submission['editionOption'] = array();
		$submission['editionName'] = null;
		$submission['workshopName'] = null;
		if ($defaultToOldInput && Input::old('submissionKind')) {
			$submission['kind'] = Input::old('submissionKind');
		}

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
		$validator = Paper::validate(Input::all());

		if ($validator->fails()) {
			return Redirect::action('PaperController@anyEdit')->withErrors($validator)->withInput();
		}

		$edit = (bool) Input::get('id');
		$success = false;
		$paper = null;
		if ($edit) {
			$paper = Paper::find(Input::get('id'));
			if (!$paper || !$this->checkAccess($paper)) {
				App::abort(404);
			}
			$paper->fill(Input::all());
		} else {
			$paper = new Paper(Input::all());
		}

		$success = $paper->save();
		
		// check for success
		if (!$success) {
			return Redirect::action('PaperController@anyEdit')->
				withErrors(new MessageBag(array('Sorry, couldn\'t save models to database.')))->
				withInput();
		}

		$input = Input::all();
		$paper->authors()->detach();
		$currentPosition = 1;
		$authorListed = false;
		if (isset($input['selectedauthors'])) {
			
			$authors = $input['selectedauthors'];
			if($authors != null) {
				foreach ($authors as $author) {
					if ($author == 1) { // skip admin
						continue;
					}
					$paper->authors()->attach($author, array('order_position' => $currentPosition));
					$currentPosition++;
					if (Auth::user()->author->id == $author) {
						$authorListed = true;
					}
				}
			}
		}
		// User has to be author
		if (!$authorListed)
			$paper->authors()->attach(Auth::user()->author->id, array('order_position' => $currentPosition));

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

		return View::make('common/edit_successful')->
			with('type', 'Paper')->
			with('action', 'PaperController@getDetails')->
			with('id', $paper->id)->
			with('edited', $edit);
	}

	/**
	 * Show paper information.
	 */
	public function getDetails($id) {
		$paper = Paper::with('authors', 'activeSubmission', 'activeSubmission.event', 'activeSubmission.event.detail')->find($id);
		$selectedauthors = array();
		if (!is_null($paper)) {
			$allowed = false;
			foreach ($paper->authors as $author) {
				if ($author->id == Auth::user()->author->id) {
					$allowed = true;
				}
				$selectedauthors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
			}
			$submissionEvent = null;
			if ($paper->activeSubmission) {
				$submissionEvent = $paper->activeSubmission->event;
			}
			$submission = $this->getSubmissionArray($submissionEvent);
			if (!$allowed) {
				App::abort(404);
			}
			$files = $paper->files()->get();
			
			return View::make('paper/detail')->with('paper', $paper)->with('selectedauthors', $selectedauthors)->with('submission', $submission)->with('files', $files);
		} else {
			App::abort(404);
		}
	}

	/**
	 * Create a requested author.
	 */
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

	/**
	 * Retarget submission.
	 *
	 * @param int $id a paper id
	 */
	public function anyRetarget($id) {
		$paper = Paper::with('activeSubmission', 'activeSubmission.event', 'activeSubmission.event.detail')->find($id);

		if (!$paper || !$this->checkAccess($paper)) {
			App::abort(404);
		}

		$currentSubmissionEvent = null;
		if ($paper->activeSubmission) {
			$currentSubmissionEvent = $paper->activeSubmission->event;
		}
		$newSubmissionEvent = $this->getSessionEvent();

		$currentSubmission = $this->getSubmissionArray($currentSubmissionEvent, false);
		$newSubmission = $this->getSubmissionArray($newSubmissionEvent);

		return View::make('paper/retarget')->with('paper', $paper)->with('submission', $currentSubmission)->with('newSubmission', $newSubmission);
	}

	/**
	 * Handle retarget submission result.
	 */
	public function postRetargetTarget() {
		$paper = Paper::find(Input::get('id'));
		if (!$paper || !$this->checkAccess($paper)) {
			App::abort(404);
		}
		$oldActiveSubmission = $paper->activeSubmission;
		$submission = null;
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
				if (!$submission->save()) {
					return Redirect::action('PaperController@anyRetarget')->
						withErrors(new MessageBag(array('Sorry, couldn\'t save models to database.')))->
						withInput();
				}
			} else {
				return Redirect::action('PaperController@anyRetarget')->
					withErrors(new MessageBag(array('Sorry, couldn\'t find selected event.')))->
					withInput();
			}
		}
		if ($oldActiveSubmission) {
			$oldActiveSubmission->active = 0;
			$oldActiveSubmission->finished_at = new Carbon;
			// could create new submission? but not update old?
			if (!$oldActiveSubmission->save()) {
				// try deleting new and return error
				if ($submission) {
					$submission->delete();
				}
				return Redirect::action('PaperController@anyRetarget')->
						withErrors(new MessageBag(array('Sorry, couldn\'t save models to database.')))->
						withInput();
			}
		}

		return View::make('common/edit_successful')->
			with('type', 'Paper Submission Target')->
			with('action', 'PaperController@getDetails')->
			with('id', $paper->id)->
			with('edited', true);
	}
	
	/**
	 * Handle file uploads for given paper
	 */
	public function postUploadFile($paperId) {
		if (!is_null($paperId)) {
			$paper = Paper::with('authors')->find($paperId);
			if (!is_null($paper)) {
				$files = Input::file('files');
				foreach ($files as $file) {
					$destinationPath = 'uploads/';
					$filename = time()."_".$file->getClientOriginalName();
					$uploadSuccess = $file->move($destinationPath, $filename);
					
					if($uploadSuccess) {
						$fileObject = new FileObject();
						$fileObject->author_id = Auth::user()->author->id;
						$fileObject->paper_id = $paper->id;
						$fileObject->name = $file->getClientOriginalName();
						$fileObject->filepath = public_path()."/".$destinationPath.$filename;
						$fileObject->save();
					} else {
						return Response::json(array('success' => 0, 'error' => 'Error uploading file'));
					}
				}
				
				return Response::json(array('success' => 1));
			} else {
				return Response::json(array('success' => 0, 'error' => 'No Paper with given id found!'));
			}
		} else {
			return Response::json(array('success' => 0, 'error' => 'No Paper id given!'));
		}
	}
	
	public function getEditFile($id) {
		if (!is_null($id)) {
			$file = FileObject::with('paper')->find($id);
			
			return View::make('file/edit', array('model' => $file, 'edit' => true));
		}
	}
	
	public function getFileDetails($id) {
		if (!is_null($id)) {
			$file = FileObject::with('paper')->find($id);
			
			return View::make('file/edit', array('model' => $file, 'edit' => false));
		}
	}
	
	public function postEditFile($id) {
		if (!is_null($id)) {
			$validator = FileObject::validate(Input::all());

			if ($validator->fails()) {
				return Redirect::action('PaperController@getEditFile')->withErrors($validator)->withInput();
			}
			$file = FileObject::find($id);
			$file->fill(Input::all());
			
			$success = $file->save();
			// check for success
			if (!$success) {
				return Redirect::action('PaperController@getEditFile')->
					withErrors(new MessageBag(array('Sorry, couldn\'t save file to database.')))->
					withInput();
			}
			
			return Redirect::action('PaperController@getFileDetails', $id);
		}
		App::abort(404);
	}

	/**
	 * Checks whether the currently authed user is an author of the given paper
	 *
	 * @param $paper the paper model
	 */
	private function checkAccess($paper) {
		foreach ($paper->authors as $author) {
			if ($author->id == Auth::user()->author->id) {
				return true;
			}
		}
		return false;
	}
}
