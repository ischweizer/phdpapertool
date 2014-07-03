<?php

use Carbon\Carbon;

class PaperController extends BaseController {
	/**
	 * List of papers.
	 */
	public function getIndex() {
		$papers = Auth::user()->author->papers;
		$temp = Author::all();
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
		$paperAuthors = $this->getOldSelectedAuthors();
		
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
				$paperAuthors = (count($paperAuthors) == 0) ? $paper->authors : $paperAuthors;
				
				if ($paper->activeSubmission) {
					$submissionEvent = $paper->activeSubmission->event;
				}
			}
			
			$files = $paper->files()->get();
		}
		
		foreach ($paperAuthors as $author) {
			if (array_key_exists($author->id, $selectedauthors)) {
				unset($selectedauthors[$author->id]);
			}
			$selectedauthors[$author->id] = $author->last_name . " " . $author->first_name . " (" . $author->email . ")";
			if (array_key_exists($author->id, $authors)) {
				unset($authors[$author->id]);
			}
		}

		$sessionEvent = $this->getSessionEvent();
		if ($sessionEvent) {
			$submissionEvent = $sessionEvent;
		}

		$submission = $this->getSubmissionArray($submissionEvent);

		return View::make('paper/edit', array('authors' => $authors, 'model' => $paper, 'selectedauthors' => $selectedauthors, 'submission' => $submission, 'files' => $files));
	}
	
	private function getOldSelectedAuthors() {
		$selectedAuthors = array();
		if (Session::getOldInput('selectedauthors')) {
			$authors = Session::getOldInput('selectedauthors');
			foreach ($authors as $authorid) {
				$author = Author::find($authorid);
				if ($author != null) {
					array_push($selectedAuthors, $author);
				}
			}
		}
		return $selectedAuthors;
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

			$fileNames = array();
			foreach ($files as $file) {
				$fileNames[$file->id] = $file->name;
			}

			$userNames = array();
			foreach (User::notAdmin()->where('id', '<>', Auth::user()->id)->get() as $user) {
					$userNames[$user->id] = $user->formatName();
			}

			$reviews = Review::where('paper_id', '=', $paper->id)->get();

			/*$userFiles = DB::table('users')
				->leftjoin('review_user', 'users.id', '=', 'review_user.user_id')
				->leftjoin('file_review', 'review_user.review_id', '=', 'file_review.file_id')
				->leftjoin('files', 'files.id', '=', 'file_review.file_id')
				->select('users.id', 'review_user.review_id', 'file_review.file_id')
				->get();*/


			return View::make('paper/detail')
				->with('paper', $paper)
				->with('selectedauthors', $selectedauthors)
				->with('submission', $submission)
				->with('files', $files)
				->with('userNames', $userNames)
				->with('fileNames', $fileNames)
				->with('reviews', $reviews);
				//->with('userFiles', $userFiles);

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
	        return Response::json(array('success' => 0, 'authors' => array()));
	    }
		$author = Author::create( $input );
		
		return Response::json(array('success' => 1, 'authors' => array($author->id => $author->last_name . " " . $author->first_name . " (" . $author->email . ")")));
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
			if ($paper->activeSubmission->camera_ready_submitted) {
				// no beautiful UI, because the user will never be navigated here by us
				return "The submission for this paper finished successfully already.";
			}
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
		
		if ($paper->activeSubmission && $paper->activeSubmission->camera_ready_submitted) {
			// no beautiful UI, because the user will never be navigated here by us
			return "The submission for this paper finished successfully already.";
		}

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
	

	public function postCreateReviewRequest(){
		if (Input::has('deadline') && Input::has('selectedUsers') && Input::has('selectedFiles') && Input::has('paperId')) {
			$review = new Review(array("user_id" => Auth::user()->id, "deadline" => Input::get('deadline'), 'paper_id' => Input::get('paperId')));
			if(Input::has('message'))
				$review->message = Input::get('message');
			$review->save();
			$review->users()->sync(Input::get('selectedUsers'));
			$review->files()->sync(Input::get('selectedFiles'));
			
			return Redirect::action('PaperController@getDetails', array(Input::get('paperId')));
		} else
			return App::abort(404);
	}

	/**
	 * Target for asynchronous submission updates.
	 *
	 * @param $paperId the paper id whose submission to update
	 * @param $type which submission field to set
	 * @param $success 0/1
	 * @return ajax object with success field and in case of success=false a error string
	 */
	public function getUpdateSubmission($paperId, $type, $success) {
		if (Request::ajax()) {
			$paper = Paper::find($paperId);
			// paper has to exist be editable by the current user and have an active submission
			if (!$paper || !$this->checkAccess($paper) || !$paper->activeSubmission) {
				App::abort(404);
			}
			// success must be 0/1
			if (!is_numeric($success) || ($success != 0 && $success != 1)) {
				App::abort(404);
			}
			$submission = $paper->activeSubmission;
			$error = null;
			// no date checks, as it generally is allowed to set fields for future dates
			// it only is an error if the field is already set
			// -left out fields will be set to '1'
			// -it cannot be that a previous field is '0' otherwise the submission would be inactive
			switch($type) {
				case 'abstract':
					if ($submission->abstract_submitted !== null) {
						$error = 'Abstract submitted is already set.';
					} else {
						$submission->abstract_submitted = $success;
					}
					break;
				case 'paper':
					if ($submission->paper_submitted !== null) {
						$error = 'Paper submitted is already set.';
					} else {
						$submission->abstract_submitted = 1;
						$submission->paper_submitted = $success;
					}
					break;
				case 'notification':
					if ($submission->notification_result !== null) {
						$error = 'Notification result is already set.';
					} else {
						$submission->abstract_submitted = 1;
						$submission->paper_submitted = 1;
						$submission->notification_result = $success;
					}
					break;
				case 'camera':
					if ($submission->camera_ready_submitted !== null) {
						$error = 'Camera ready submitted is already set.';
					} else {
						$submission->abstract_submitted = 1;
						$submission->paper_submitted = 1;
						$submission->notification_result = 1;
						$submission->camera_ready_submitted = $success;
					}
					break;
				default:
					App::abort(404);
			}
			if (!$error) {
				if ($success == 0) {
					$submission->active = 0;
					$submission->finished_at = new Carbon;
				}
				if (!$submission->save()) {
					$error = 'Sorry, couldn\'t update submission in database.';
				}
			}
			return Response::json(array('success' => !$error, 'error' => $error));
		} else {
			return null;
		}
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
