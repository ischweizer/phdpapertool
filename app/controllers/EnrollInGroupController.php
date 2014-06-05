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
        return $this::showLabs();
    }
    
    public function enroll() {
        if(!Input::has('group') || !Auth::check()  || (Auth::user()->group_id != null && Auth::user()->group_confirmed != 1))
            return Response::json(false);
        $groupId = Input::get('group');
        $group = Group::find($groupId);
        if($group == null || $group->active != 1)
            return Response::json(false);
        $user = Auth::user();
        $user->group_id = $groupId;
        $user->group_confirmed = 0;
        $user->save();
        return Response::json(true);
    }
    
    public function showLabs() {
        //TODO Nur active Labs
        $labs = Lab::where('active', '=', '1')->get();
        $groupAccepted = false;
        $user = Auth::user();

        if($user->hasGroup()){
            $labGroups = Group::where('active','=','1')->where('lab_id', '=', $user->group->lab_id)->get();
            $groupAccepted = $user->group_confirmed;
        } else {
            $labGroups = false;
        }



        return View::make('enroll_in_group')->with('labs', $labs)->with('labGroups', $labGroups)->with('groupAccepted', $groupAccepted);
    }   
    
    public function getDomain() {

        //TODO Nur active groups
        if(Input::has('lab')) 
            return $this::getGroups(Input::get('lab'));
        /*if(Input::has('department'))
            return $this::getLabs(Input::get('department'));
        if(Input::has('university'))
            return $this::getDepartments(Input::get('university'));*/
        return null;
    }
    
    /*private function getDepartments($universityId) {
        $departments = Department::where('university_id', '=', $universityId)->get();
        return Response::json($departments);
    }*/
    
    /*private function getLabs($departmentId) {
        $labs = Lab::where('department_id', '=', $departmentId)->get();
        return Response::json($labs);
    }*/    
    
    private function getGroups($labId) {
        $groups = Group::where('active','=','1')->where('lab_id', '=', $labId)->get();
        return Response::json($groups);
    }    
}
