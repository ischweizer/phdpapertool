<?php

class ConferenceController extends BaseController {
    public function getIndex($id = null) {
		if (!is_null($id)) {
			$conference = Conference::with('editions')->find($id);
			if (!is_null($conference)) {
				return View::make('conference/conference')->with('conference', $conference);
			}
		}
		return View::make('conference/conferences');
    }

	public function getData() {
        if(Request::ajax()) {
			$query = Conference::join('rankings', 'rankings.id', '=', 'conferences.ranking_id')
					->select(array('conferences.name', 'acronym', 'rankings.name AS ranking_name', 'field_of_research', 'conferences.id'));
			return Datatables::of($query)->make();
		} else {
			return null;
		}
	}

	public function getAutocomplete($query) {
        if(Request::ajax()) {
			// order for consistent results
			$search = '%'.$query.'%';
			return Conference::select(array('id', 'name', 'acronym'))->where('name', 'LIKE', $search)->orWhere('acronym', 'LIKE', $search)->orderBy('id', 'ASC')->take(5)->get()->toJson();
		} else {
			return null;
		}
	}

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
}
