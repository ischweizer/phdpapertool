 <?php

class ConferenceEditionController extends BaseController {
    public function getIndex() {
		return View::make('conference_edition_create');
    }
}
