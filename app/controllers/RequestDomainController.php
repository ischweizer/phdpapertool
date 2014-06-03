<?php
/**
 * Description of RequestDomainController
 *
 * @author jost
 */
class RequestDomainController {
   
    public function index() {
        $roles = UserRole::where('user_id', '=', Auth::user()->id);
        foreach($roles as $role) {
            
        }
    }
    
    public function confirmUser() {
        if(!Input::has('userId'))
            return Response::json(false);
        return $this::confirmUserId(Input::get('userId'));
    }
    
    private function confirmUserId($userId) {
        if(!isAbleToDecideAboutUser($userId))
            return Response::json(false);
        $confirmedUser = User::find($userId);
        $confirmedUser->group_confirmed = 1;
        $confirmedUser->save();
        return Response::json(true);        
    }
    
    public function confirmGroup() {
        if(!Input::has('groupId'))
            return Response::json(false);
        return $this::confirmGroupId(Input::get('groupId'));
    }
    
    private function confirmGroupId($groupId) {
        if(!$this::isAble2DecideAboutGroup())
            return Response::json(false);
        $confirmedGroup = Group::find($groupId);
        $confirmedGroup->active = 1;
        $confirmedGroup->save();
        $groupLeader = User::where('group_id', '=', $groupId)->first();
        UserRole::updateRole($groupLeader, UserRole::GROUP_LEADER, true);
        $this::confirmUserId($groupLeader->id);        
    }
    
    public function confirmLab() {
        if(!Input::has('labId'))
            return Response::json(false);
        return $this::confirmLabId(Input::get('labId'));        
    }
    
    private function confirmLabId($labId) {
        if(!$this::isAble2DecideAboutLab())
            return Response::json(false);
        $confirmedLab = Lab::find($labId);
        if($confirmedLab == null) 
            return Response::json(false);
        $confirmedLab->active = 1;
        $confirmedLab->save();
        
        $group = Group::where('lab_id', '=', $labId)->first();
        $labLeader = User::where('group_id', '=', $group->id)->first();
        UserRole::updateRole($labLeader, UserRole::LAB_LEADER, true);
        confirmUserId($labLeader->id);              
        $this::confirmGroupId($group->id);  
    }    
    
    
    public function refuseUser() {
        if(!Input::has('userId'))
            return Response::json(false);
        return $this::refuseUserId(Input::get('userId'));        
    }
    
    private function refuseUserId($userId) {
        if(!$this::isAbleToDecideAboutUser($userId))
           return Response::json(false);
        $refusedUser = User::find($userId);
        $refusedUser->group_id = null;
        $refusedUser->save();
        return Response::json(true);
    }
    
    public function refuseGroup() {
        if(!Input::has('groupId'))
            return Response::json(false);
        return $this::refuseGroupId(Input::get('groupId'));
    }
    
    private function refuseGroupId($groupId) {
        if(!$this::isAble2DecideAboutGroup($groupId))
            return Response::json(false);
        $refusedUser = User::where('group_id', '=', $groupId)->first();
        refuseUserId($refusedUser->id);
        $refuseGroup = Group::find($groupId);
        $refuseGroup->delete();
    }
    
    public function refuseLab() {
        if(!Input::has('labId'))
            return Response::json(false);
        return $this::refuseLabId(Input::get('labId'));
    }
    
    private function refuseLabId($labId) {
        if(!$this::isAble2DecideAboutLab())
            return Response::json(false);
        $refusedGroup = Group::where('lab_id', '=', $labId)->first();
        $this::refuseGroupId($refusedGroup->id);
        $refusedLab = Lab::find($labId);
        $refusedLab->delete();
    }
    
    private function isAbleToDecideAboutUser($userId) {
        $confirmedUser = User::find(Input::get($userId));
        if($confirmedUser == null)
            return false;
        $roleAdmin = UserRole::getUserRole(UserRole::SUPER_ADMIN);
        if($roleAdmin != null && $roleAdmin->active)
            return true;
        $roleGroup = UserRole::getUserRole(UserRole::GROUP_LEADER);
        if($roleGroup != null && $confirmedUser->group_id == Auth::user()->group_id && $roleGroup->active == 1)
            return true;
        $roleLab = UserRole::getUserRole(UserRole::LAB_LEADER);
        if($roleLab == null || $roleLab->active != 1)
            return false;
        $confirmedUserGroup = Group::find($confirmedUser->group_id);
        $userGroup = Group::find(Auth::user()->group_id);
        return $confirmedUserGroup->lab_id == $userGroup->lab_id;
    }
    
    private function isAble2DecideAboutGroup($groupId) {
        $confirmedGroup = Group::find($groupId);
        if($confirmedGroup == null) 
            return false;
        $roleAdmin = UserRole::getUserRole(UserRole::SUPER_ADMIN);
        if($roleAdmin != null && $roleAdmin->active)
            return true;
        $roleLab = UserRole::getUserRole(UserRole::LAB_LEADER);
        if($roleLab == null || $roleLab->active != 1)
            return false;
        return $confirmedGroup->lab_id != Group::find(Auth::user()->group_id)->lab_id;
    }
    
    private function isAble2DecideAboutLab() {
        $roleAdmin = UserRole::getUserRole(UserRole::SUPER_ADMIN);
        return $roleAdmin != null && $roleAdmin->active;
    }
}
