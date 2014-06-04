<?php

class ConferenceController extends BaseController {
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
			return Conference::select(array('id', 'name', 'acronym'))->where('name', 'LIKE', $search)->orWhere('acronym', 'LIKE', $search)->orderBy('id', 'ASC')->take(5)->get()->toJson();
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
				return $result->editions()->get(array('id', 'edition'))->toJson();
			} else {
				return '[]';
			}
		} else {
			return null;
		}
	}
}
