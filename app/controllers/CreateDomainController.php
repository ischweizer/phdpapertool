<?php
/**
 * Description of CreateDomainController
 *
 * @author jost
 */
class CreateDomainController extends BaseController {
    #
    public function index() {
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
    }
    
    private function createGroup($name, $lapId) {
        $group = new Group;
        $group->name = $name;
        $group->lap_id = $lapId;
        $group->save();
        return $group;
    }
    
    private function createLab($name, $departmentId) {
        $lab = new Lap;
        $lab->name = $name;
        $lab->department_id = $departmentId;
        $lab->save();
        return $lab;
    }    
    
    private function createDepartment($name, $universityId) {
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
    }     
}
