<?php
/**
 * Description of ErrolInGroupController
 *
 * @author jost
 */
class EnrolInGroupController extends BaseController {
    
    public function index() {
        if(Request::ajax())
            return $this::getDomain();
        return $this::showUniversities();
    }
    
    public function showUniversities() {
        $universities = University::all();
        return View::make('enrol_in_group')->with('domains', $universities);
    }  
    
    public function getDomain() {
        if(Input::has('lab')) 
            return $this::getGroups(Input::get('lab'));
        if(Input::has('department'))
            return $this::getLabs(Input::get('department'));
        if(Input::has('university'))
            return $this::getDepartments(Input::get('university'));
        return null;
    }
    
    private function getDepartments($universityId) {
        $departments = Department::where('university_id', '=', $universityId);
        return Response::json($departments);
    }
    
    private function getLabs($departmentId) {
        $labs = Lab::where('department_id', '=', $departmentId);
        return Response::json($labs);
    }    
    
    private function getGroups($labId) {
        $groups = Group::where('lab_id', '=', $labId);
        return Response::json($groups);
    }    
}
