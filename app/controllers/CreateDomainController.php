<?php
/**
 * Description of CreateDomainController
 *
 * @author jost
 */
class CreateDomainController extends BaseController {
    #
    /*public function index() {
        if(!Input::has('upperDomainId') && !Input::has('university'))
            return null;
        $upperDomainId = Input::get('upperDomainId');
        if(Input::has('group')) 
            return $this::createGroup(Input::get('group'), $upperDomainId);        
        if(Input::has('lab')) 
            return $this::createLab(Input::get('lab'), $upperDomainId);
        if(Input::has('department'))
            return $this::createDeparment(Input::get('department'), $upperDomainId);
        if(Input::has('university'))
            return $this::createUniversity(Input::get('university'));        
        return null;
    }*/
    
    public function index() {
        if(!Input::has('groupName'))
            return null;
        if(Auth::user()->group_confirmed != 1 && Auth::user()->group_id != null && UserRole::getUserRole(UserRole::SUPER_ADMIN) == null)
            return null;
        if(!Input::has('labId')) {
            if(!Input::has('labName'))
                return null;
            $labId = $this::createLab(Input::get('labName'))->id;
            $labCreated = true;
        } else {
            $labId = Input::get('labId');
            $labCreated = false;
        }
        $this::createGroup(Input::get('groupName'), $labId, $labCreated);
        return null;
    }
    
    private function createGroup($name, $labId, $labCreated) {    
        if(!$labCreated && Lab::find($labId)->active == 0)
            return null;
        $isGroupActive = UserRole::getUserRole(UserRole::LAB_LEADER) != null;
        $group = new Group;
        $group->name = $name;
        $group->lab_id = $labId;
        $group->active = $isGroupActive;
        $group->save();
        $user = Auth::user();
        $user->group_confirmed = $isGroupActive;
        $user->group_id = $group->id;
        $user->save();
        //$this::updateRole(UserRole::GROUP_LEADER, $isGroupActive);
        return $group;
    }
    
    private function createLab($name) {//, $departmentId) {
        $lab = new Lab;
        $lab->name = $name;
        //$lab->department_id = $departmentId;
        $lab->active = 0;
        $lab->save();
        //$this::updateRole(UserRole::LAB_LEADER, false);
        return $lab;
    }    
    
   /* private function updateRole($roleId, $isActive) {
        $role = UserRole::getUserRole($roleId);
        if($role != null) {
            $role->active = $isActive;
            return $role;
        }
        $newRole = new User_role;
        $newRole->user_id = Auth::user()->id;
        $newRole->role_id = $roleId;
        $newRole->active = $isActive;
        $newRole->save();     
        return $newRole;
    }*/
    
   /*private function createDepartment($name, $universityId) {
        $department = new Department;
        $department->name = $name;
        $department->university_id = $universityId;
        $department->save();
        return $department;
    }        
    
    private function createUniversity($name) {
        $university = new University;
        $university->name = $name;
        $university->save();
        return $university;
    }*/     
}
