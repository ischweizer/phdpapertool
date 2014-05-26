 <?php

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
			$edition = ConferenceEdition::with('conference')->find($id);
		}
		return View::make('conference/conference_edition_edit')->with('edition', $edition);
    }

	/**
	 * Handle edit/create result.
	 */
	public function postEdit() {
		$validator = ConferenceEdition::validate();
		if ($validator->fails()) {
			return Redirect::action('ConferenceEditionController@getEdit')->withErrors($validator)->withInput();
		}
		$edition = null;
		$edit = (bool) Input::get('id');
		// check for edit...
		if ($edit) {
			$edition = ConferenceEdition::find(Input::get('id'));
			$edition->fill(Input::all());
		} else {
			$edition = new ConferenceEdition(Input::all());
		}
		$edition->conference_id = Conference::where('name', '=', Input::get('conference')['name'])->first()->id;
		$edition->save();
		if (Session::has('url.conference-edition-creation.return')) {
			Redirect::to(Session::get('url.conference-edition-creation.return'))->with('conference-edition-created', $edition->id);
		}
		return View::make('conference/conference_edition_created')->with('conference_id', $edition->conference_id)->with('edited', $edit);
    }
}
