<?php

use Illuminate\Support\MessageBag;

class ConferenceEditionController extends BaseController {
	
	public function __construct() {
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	/**
	 * Edit or create a conference edition.
	 */
    public function getEdit($id = null) {
		$edition = null;
		if ($id != null) {
			$edition = ConferenceEdition::with('conference', 'event')->find($id);
		}
		return View::make('conference/conference_edition_edit')->with('edition', $edition);
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

		// return to set address or to success view
		if (Session::has('url.conference-edition-creation.return')) {
			Redirect::to(Session::get('url.conference-edition-creation.return'))->with('conference-edition-created', $edition->id);
		}
		return View::make('conference/conference_edition_edited')->with('conference_id', $edition->conference_id)->with('edited', $edit);
    }
}
