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
}
