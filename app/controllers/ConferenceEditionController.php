 <?php

class ConferenceEditionController extends BaseController {
	/**
	 * Edit or create a conference edition.
	 */
    public function getEdit($id = null) {
		$edition = null;
		if ($id != null) {
			$edition = ConferenceEdition::find($id);
		}
		return View::make('conference_edition_edit')->with('edition', $edition);
    }

	/**
	 * Handle edit/create result
	 */
	public function postEdit() {
		return View::make('conference_edition_create');
    }
}
