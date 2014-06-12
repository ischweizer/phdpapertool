<?php

class ConferenceController extends BaseController {
	
	public function __construct() {
		$this->beforeFilter('csrf', array('only' => array('postEditTarget')));
	}

	/**
	 * List of conferences.
	 */
    public function getIndex() {
		return View::make('conference/conferences');
    }

    /**
	 * Details of the specified conference.
	 */
    public function getDetails($id) {
		$conference = Conference::with('editions', 'editions.event')->find($id);
		if (!is_null($conference)) {
			return View::make('conference/conference')->with('conference', $conference);
		} else {
			App::abort(404);
		}
    }

	/**
	 * Edit or create a conference.
	 */
    public function anyEdit($id = null) {
		$initialName = null;
		if (Input::get('conference-create-return-url')) {
			Session::set('conference-create-return', Input::all());
			$initialName = Input::get('conference-create-name');
		}
		$conference = null;
		if ($id != null) {
			$conference = Conference::with('ranking')->find($id);
			if (!$conference) {
				App::abort(404);
			}
		}
		$rankings = Ranking::orderBy('id', 'ASC')->get();
		$rankingOptions = $rankings->lists('name', 'id');
		$defaultRanking = $conference ? null : Ranking::where('name', '=', 'none')->first()->id;
		return View::make('conference/conference_edit')->
			with('model', $conference)->
			with('rankingOptions', $rankingOptions)->
			with('defaultRanking', $defaultRanking)->
			with('initialName', $initialName);
    }

	/**
	 * Handle edit/create result.
	 */
	public function postEditTarget() {
		// validate
		$validator = Conference::validate(Input::all());
		if ($validator->fails()) {
			return Redirect::action('ConferenceController@anyEdit')->withErrors($validator)->withInput();
		}

		$conference = null;
		$success = null;

		// insert/update
		$edit = (bool) Input::get('id');
		if ($edit) {
			$conference = Conference::find(Input::get('id'));
			if (!$conference) {
				App::abort(404);
			}
			$conference->fill(Input::all());
		} else {
			$conference = new Conference(Input::all());
		}
		$success = $conference->save();

		// check for success
		if (!$success) {
			return Redirect::action('ConferenceController@anyEdit')->
				withErrors(new MessageBag(array('Sorry, couldn\'t save models to database.')))->
				withInput();
		}

		if (Session::has('conference-create-return')) {
			$input = Session::get('conference-create-return');
			Session::forget('conference-create-return');
			return Redirect::to($input['conference-create-return-url'])->withInput($input)->with('conference_id', $conference->id);
		}

		return View::make('common/edit_successful')->
			with('type', 'Conference')->
			with('action', 'ConferenceController@getDetails')->
			with('id', $conference->id)->
			with('edited', $edit);
    }

	/**
	 * Handle back button.
	 */
	public function postBack() {
		if (Session::has('conference-create-return')) {
			$input = Session::get('conference-create-return');
			Session::forget('conference-create-return');
			return Redirect::to($input['conference-create-return-url'])->withInput($input);
		} else {
			return Redirect::to(Input::get('conferenceBackTarget'));
		}
	}

	/**
	 * Asynchronous loading of conference list.
	 */
    public function getData() {
        if(Request::ajax()) {
			$query = Conference::join('rankings', 'rankings.id', '=', 'conferences.ranking_id')
					->select(array('conferences.name', 'acronym', 'rankings.name AS ranking_name', 'field_of_research', 'conferences.id'));
			return Datatables::of($query)->make();
		} else {
			return null;
		}
	}

	/**
	 * Autocomplete for conferences.
	 */
    public function getAutocomplete($query) {
        if(Request::ajax()) {
			// order for consistent results
			$search = '%'.$query.'%';
			return Conference::select(array('id', 'name', 'acronym'))->
				where('name', 'LIKE', $search)->
				orWhere('acronym', 'LIKE', $search)->
				orderBy('id', 'ASC')->take(5)->get()->toJson();
		} else {
			return null;
		}
	}

	/**
	 * Check the given conference name for existence.
	 */
    public function anyCheck($name = null) {
        if(Request::ajax()) {
			if (is_null($name)) {
				$name = Input::get('name');
			}
			$exists = Conference::where('name', '=', $name)->first();
			return json_encode(array(
				'valid' => (bool) $exists,
			));
		} else {
			return null;
		}
	}

	/**
	 * Return the id of the conference with the given name or nothing if it doesn't exist.
	 */
    public function anyId($name = null) {
		if (Request::ajax()) {
			if (is_null($name)) {
				$name = Input::get('name');
			}
			$result = Conference::where('name', '=', $name)->first();
			if ($result) {
				return $result->id;
			} else {
				return '';
			}
		} else {
			return null;
		}
	}

	/**
	 * Return a JSON of conference editions (id and edition string) of the conference with the given name.
	 */
    public function anyEditions($name = null) {
		if (Request::ajax()) {
			if (is_null($name)) {
				$name = Input::get('name');
			}
			$result = Conference::where('name', '=', $name)->first();
			if ($result) {
				return $result->editions()->orderBy('created_at', 'DESC')->get(array('id', 'edition'))->toJson();
			} else {
				return '[]';
			}
		} else {
			return null;
		}
	}
}
