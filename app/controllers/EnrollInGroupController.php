<?php
/**
 * Description of ErrolInGroupController
 *
 * @author jost
 */
class EnrollInGroupController extends BaseController {
    
    public function index() {
        if(Request::ajax())
            return $this::getDomain();
        return $this::showUniversities();
    }
    
    public function enroll() {
        if(!Input::has('group') || !Auth::check())
            return false;
        $groupId = Input::get('group');
        $group = Group::find($groupId);
        if($group == null)
            return false;
        $author = Author::where('email', '=', Auth::user()->email)->first();
        $author->group_id = $groupId;
        $author->save();
        return true;
    }
    
    public function showUniversities() {
        $universities = University::all();
        return View::make('enroll_in_group')->with('universities', $universities);
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
        $departments = Department::where('university_id', '=', $universityId)->get();
        return Response::json($departments);
    }
    
    private function getLabs($departmentId) {
        $labs = Lab::where('department_id', '=', $departmentId)->get();
        return Response::json($labs);
    }    
    
    private function getGroups($labId) {
        $groups = Group::where('lab_id', '=', $labId)->get();
        return Response::json($groups);
    }    
}
