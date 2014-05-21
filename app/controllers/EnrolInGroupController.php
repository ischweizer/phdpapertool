<?php
/**
 * Description of ErrolInGroupController
 *
 * @author jost
 */
class EnrolInGroupController extends BaseController {
    
    public function showUniversities() {
        $universities = University::all();
        return View::make('enrol_in_group')->with('universities', $universities);
    }
}
