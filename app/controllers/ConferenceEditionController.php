<?php

use Illuminate\Support\MessageBag;

class ConferenceEditionController extends BaseController {
	
	public function __construct() {
		$this->beforeFilter('csrf', array('only' => array('postEdit')));
	}

	/**
	 * Show conference edition information and co-located workshops.
	 */
	public function getDetails($id) {
		$edition = ConferenceEdition::with('conference', 'event', 'workshops', 'workshops.event')->find($id);
		if (!is_null($edition)) {
			return View::make('conference/conference_edition')->with('edition', $edition);
		} else {
			App::abort(404);
		}
    }

	/**
	 * Edit or create a conference edition.
	 */
    public function getEdit($id = null) {
		$edition = null;
		if ($id != null) {
			$edition = ConferenceEdition::with('conference', 'event')->find($id);
		}
		return View::make('conference/event_edit')->with('model', $edition)->with('type', 'Conference Edition')->with('action', 'ConferenceEditionController@postEdit')->with('conferenceName', 'conference[name]');
    }

	/**
	 * Handle edit/create result.
	 */
	public function postEdit() {
		// validate
		$validator = ConferenceEdition::validate(Input::all());
		if ($validator->fails()) {
			return Redirect::action('ConferenceEditionController@getEdit')->withErrors($validator)->withInput();
		}

		$edition = null;
		$success = null;

		// insert/update
		$edit = (bool) Input::get('id');
		if ($edit) {
			$edition = ConferenceEdition::with('event')->find(Input::get('id'));
			$edition->fill(Input::all());
			$edition->event->fill(Input::get('event'));
			$success = $edition->push();
		} else {
			$edition = new ConferenceEdition(Input::all());
			$success = $edition->save();
			if ($success) {
				$event = new EventModel(Input::get('event'));
				$success = (bool) $edition->event()->save($event);
				if (!$success) {
					// could not save event -> clean edition, too
					$edition->delete();
				}
			}
		}

		// check for success
		if (!$success) {
			return Redirect::action('ConferenceEditionController@getEdit')->withErrors(new MessageBag(array('Sorry, couldn\'t save models to database.')))->withInput();
		}

		return View::make('conference/event_edited')->with('type', 'Conference Edition')->with('action', 'ConferenceEditionController@getDetails')->with('id', $edition->id)->with('edited', $edit);
    }
}
