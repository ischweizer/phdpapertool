<?php

use Illuminate\Support\MessageBag;

class WorkshopController extends BaseController {
	
	public function __construct() {
		$this->beforeFilter('csrf', array('only' => array('postEditTarget')));
	}

	/**
	 * Show workshop information.
	 */
	public function getDetails($id) {
		$workshop = Workshop::with('event', 'conferenceEdition', 'conferenceEdition.conference', 'conferenceEdition.event')->find($id);
		if (!is_null($workshop)) {
			return View::make('conference/workshop')->with('workshop', $workshop);
		} else {
			App::abort(404);
		}
    }

	/**
	 * Edit or create a workshop.
	 */
    public function anyEdit($id = null) {
		$initialName = null;
		// save requested return information
		if (Input::get('workshop-create-return-url')) {
			Session::set('workshop-create-return', Input::all());
			$initialName = Input::get('workshop-create-name');
		}

		// get edit model
		$workshop = null;
		$editionOption = array();
		if ($id != null) {
			$workshop = Workshop::with('conferenceEdition', 'conferenceEdition.conference', 'event')->find($id);
			if ($workshop) {
				$editionOption = array($workshop->conference_edition_id => 'Dummy');
			}
		}

		// get created conference edition
		$initialConferenceEditionId = null;
		$initialConferenceName = null;
		if (Session::has('conference_edition_id') || (Input::has('conference_edition_id') && $workshop == null)) {
			$edition = ConferenceEdition::with('conference')->find(Session::get('conference_edition_id') ?: Input::get('conference_edition_id'));
			if ($edition) {
				$initialConferenceId = $edition->id;
				$initialConferenceName = $edition->conference->name;
				$editionOption = array($edition->id => 'Dummy');
				if ($workshop == null) {
					$workshop = array(
						'conference_edition_id' => $edition->id,
						'conferenceEdition' => $edition->toArray()
					);
				}
			}
		}
		return View::make('conference/event_edit')->
			with('model', $workshop)->
			with('type', 'Workshop')->
			with('action', 'WorkshopController@postEditTarget')->
			with('backAction', 'WorkshopController@postBack')->
			with('conferenceName', 'conferenceEdition[conference][name]')->
			with('editionOption', $editionOption)->
			with('initialConferenceEditionId', $initialConferenceEditionId)->
			with('initialConferenceName', $initialConferenceName)->
			with('initialName', $initialName);
    }

	/**
	 * Handle edit/create result.
	 */
	public function postEditTarget() {
		// validate
		$validator = Workshop::validate(Input::all());
		if ($validator->fails()) {
			return Redirect::action('WorkshopController@anyEdit')->withErrors($validator)->withInput();
		}

		$workshop = null;
		$success = null;

		// insert/update
		$edit = (bool) Input::get('id');
		if ($edit) {
			$workshop = Workshop::with('event')->find(Input::get('id'));
			$workshop->fill(Input::all());
			$workshop->event->fill(Input::get('event'));
			$success = $workshop->push();
		} else {
			$workshop = new Workshop(Input::all());
			$success = $workshop->save();
			if ($success) {
				$event = new EventModel(Input::get('event'));
				$success = (bool) $workshop->event()->save($event);
				if (!$success) {
					// could not save event -> clean workshop, too
					$workshop->delete();
				}
			}
		}

		// check for success
		if (!$success) {
			return Redirect::action('WorkshopController@anyEdit')->
				withErrors(new MessageBag(array('Sorry, couldn\'t save models to database.')))->
				withInput();
		}

		if (Session::has('workshop-create-return')) {
			$input = Session::get('workshop-create-return');
			Session::forget('workshop-create-return');
			return Redirect::to($input['workshop-create-return-url'])->withInput($input)->with('workshop_id', $workshop->id);
		}

		return View::make('conference/edit_successful')->
			with('type', 'Workshop')->
			with('action', 'WorkshopController@getDetails')->
			with('id', $workshop->id)->
			with('edited', $edit);
    }

	/**
	 * Handle back button.
	 */
	public function postBack() {
		if (Session::has('workshop-create-return')) {
			$input = Session::get('workshop-create-return');
			Session::forget('workshop-create-return');
			return Redirect::to($input['workshop-create-return-url'])->withInput($input);
		} else {
			return Redirect::action('ConferenceEditionController@getDetails', array('id' => Input::get('conference_edition_id')));
		}
	}

	/**
	 * Autocomplete for workshops.
	 */
    public function getAutocomplete($query) {
        if(Request::ajax()) {
			// order for consistent results
			$search = '%'.$query.'%';
			return Workshop::select(array('id', 'name'))->where('name', 'LIKE', $search)->orderBy('id', 'ASC')->take(5)->get()->toJson();
		} else {
			return null;
		}
	}

	/**
	 * Check the given workshop name for existence.
	 */
    public function anyCheck($name = null) {
        if(Request::ajax()) {
			if (is_null($name)) {
				$name = Input::get('name');
			}
			$exists = Workshop::where('name', '=', $name)->first();
			return json_encode(array(
				'valid' => (bool) $exists,
			));
		} else {
			return null;
		}
	}

	/**
	 * Return the id of the workshop with the given name or nothing if it doesn't exist.
	 */
    public function anyId($name = null) {
		if (Request::ajax()) {
			if (is_null($name)) {
				$name = Input::get('name');
			}
			$result = Workshop::where('name', '=', $name)->first();
			if ($result) {
				return $result->id;
			} else {
				return '';
			}
		} else {
			return null;
		}
	}
}
