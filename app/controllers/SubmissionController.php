<?php

class SubmissionController extends BaseController {
	
	public function __construct() {
	}

	/**
	 * List of conferences.
	 */
    public function getIndex() {
		return View::make('submission')->with('submissions', Submission::currentUser()->active()->get());
    }
}
