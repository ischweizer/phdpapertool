 <?php

class ConferenceController extends BaseController {
    public function getIndex() {
		return View::make('conferences');
    }

	public function anyData() {
        if(Request::ajax()) {
			$query = Conference::join('rankings', 'rankings.id', '=', 'conferences.ranking_id')
					->select(array('conferences.name', 'acronym', 'rankings.name AS ranking_name', 'field_of_research'));
			return Datatables::of($query)->make();
		} else {
			return null;
		}
	}

	public function getAutocomplete() {
		// because of problems with '/' in the query do not use a '/query', but '?q=query' 
        if(Request::ajax()) {
			// order for consistent results
			return Conference::select(array('id', 'name'))->where('name', 'LIKE', '%'.Input::get('q').'%')->orderBy('id', 'ASC')->take(5)->get()->toJson();
		} else {
			return null;
		}
	}
}
