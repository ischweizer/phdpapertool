<?php
/**
 * Description of RequestDomainController
 *
 * @author jost
 */
class AdminController extends BaseController {
   
    public function index() {
        $roleAdmin = UserRole::getUserRole(UserRole::SUPER_ADMIN);
        if($roleAdmin != null && $roleAdmin->active == 1) {
            $users = User::getUsers(null);
            $groups = Group::getGroups($users);
            $labs = Lab::getLabs($groups);
            return View::make('admin')->with('users', $users)->with('groups', $groups)->with('labs', $labs)->with('roleId', UserRole::SUPER_ADMIN);
        }
        $roleLabLeader = UserRole::getUserRole(UserRole::LAB_LEADER);
        $userGroup = Group::find(Auth::user()->group_id);
        $labs[$userGroup->lab_id] = Lab::find($userGroup->lab_id);
        if($roleLabLeader !=  null && $roleLabLeader->active == 1) {
            $groups = Group::getGroupsFromLabs($labs);
            $users = User::getUsers($groups);
            return View::make('admin')->with('users', $users)->with('groups', $groups)->with('labs', $labs)->with('roleId', UserRole::LAB_LEADER);
        }
        $roleGroupLeader = UserRole::getUserRole(UserRole::GROUP_LEADER);
        if($roleGroupLeader != null && $roleGroupLeader->active == 1) {
            $groups[$userGroup->id] = $userGroup;
            $users = User::getUsers($groups);
            return View::make('admin')->with('users', $users)->with('groups', $groups)->with('labs', $labs)->with('roleId', UserRole::GROUP_LEADER);
        }
        return View::make('admin')->with('users', array())->with('groups', array())->with('labs', array());
    }
    
    public function confirm() {
        if(Input::has('labId'))
            $this::confirmLab();
        else if(Input::has('groupId'))
            $this::confirmGroup();
        else if(Input::has('userId'))
            $this::confirmUser();
        return $this::index();
    }
    
    public function refuse() {
         if(Input::has('labId'))
            $this::refuseLab();
        else if(Input::has('groupId'))
            $this::refuseGroup();
        else if(Input::has('userId'))
            $this::refuseUser();
        return $this::index();       
    }
    
    public function confirmUser() {
        if(!Input::has('userId'))
            return Response::json(false);
        return $this::confirmUserId(User::find(Input::get('userId')));
    }
    
    private function confirmUserId($confirmedUser) {
        if(!$this::isAbleToDecideAboutUser($confirmedUser) || $confirmedUser->group_id == null)
            return Response::json(false);
        //$confirmedUser = User::find($userId);
        $confirmedUser->group_confirmed = 1;
        $confirmedUser->save();
        return Response::json(true);        
    }
    
    public function confirmGroup() {
        if(!Input::has('groupId'))
            return Response::json(false);
        return $this::confirmGroupId(Group::find(Input::get('groupId')));
    }
    
    private function confirmGroupId($confirmedGroup, $isCreatingLab = false) {
        if(!$this::isAble2DecideAboutGroup($confirmedGroup))
            return Response::json(false);
        //$confirmedGroup = Group::find($groupId);
        $confirmedGroup->active = 1;
        $confirmedGroup->save();
        $groupLeader = User::where('group_id', '=', $confirmedGroup->id)->first();
        if(!$isCreatingLab)
            UserRole::updateRole($groupLeader, UserRole::GROUP_LEADER, true);
        return $this::confirmUserId(User::find($groupLeader->id));        
    }
    
    public function confirmLab() {
        if(!Input::has('labId'))
            return Response::json(false);
        return $this::confirmLabId(Lab::find(Input::get('labId')));        
    }
    
    private function confirmLabId($confirmedLab) {
        if(!$this::isAble2DecideAboutLab($confirmedLab))
            return Response::json(false);
        //$confirmedLab = Lab::find($labId);
        $confirmedLab->active = 1;
        $confirmedLab->save();
        
        $group = Group::where('lab_id', '=', $confirmedLab->id)->first();
        $labLeader = User::where('group_id', '=', $group->id)->first();
        UserRole::updateRole($labLeader, UserRole::LAB_LEADER, true);
        //$this::confirmUserId($labLeader);              
        return $this::confirmGroupId($group, true);  
    }    
    
    
    public function refuseUser() {
        if(!Input::has('userId'))
            return Response::json(false);
        //entnehme rechte -> es muss noch boolean übergeben werden, ob ganze gruppe gelöscht wird
        return $this::refuseUserId(User::find(Input::get('userId')));        
    }
    
    private function refuseUserId($refusedUser, $isDeletingWholeGroup = false) {
        if(!$this::isAbleToDecideAboutUser($refusedUser))
           return Response::json(false);
        if(!$isDeletingWholeGroup) {
            $roles = UserRole::where('user_id', '=', $refusedUser->id)->get();
            foreach($roles as $role)
                $role->delete();
        }
        $refusedUser->group_id = null;
        $refusedUser->group_confirmed = 0;
        $refusedUser->save();
        return Response::json(true);
    }
    
    public function refuseGroup() {
        if(!Input::has('groupId'))
            return Response::json(false);
        return $this::refuseGroupId(Group::find(Input::get('groupId')));
    }
    
    private function refuseGroupId($refusedGroup, $isDeletingWholeLab = false) {
        if(!$this::isAble2DecideAboutGroup($refusedGroup))
            return Response::json(false);
        //$refusedUser = User::where('group_id', '=', $refusedGroup->id)->first();
        //$this::refuseUserId($refusedUser);   
        $refusedUsers = User::getUsers(array($refusedGroup));
        if(!$isDeletingWholeLab && UserRole::hasAUserRole($refusedUsers, UserRole::LAB_LEADER))
            return Response::json(false);
        foreach($refusedUsers as $refusedUser) {
            $this::refuseUserId($refusedUser, true);
        }
        $roles = UserRole::getUsersRoles($refusedUsers, UserRole::GROUP_LEADER);
        foreach($roles as $role) {
            $role->delete();
        }
        $refusedGroup->delete();
        return Response::json(true);
    }
    
    public function refuseLab() {
        if(!Input::has('labId'))
            return Response::json(false);
        return $this::refuseLabId(Lab::find(Input::get('labId')));
    }
    
    private function refuseLabId($refusedLab) {
        if(!$this::isAble2DecideAboutLab($refusedLab))
            return Response::json(false);
        //$refusedGroup = Group::where('lab_id', '=', $refusedLab->id)->first();
        //$this::refuseGroupId($refusedGroup);
        $refusedGroups = Group::getGroupsFromLabs(array($refusedLab));
        $users = User::getUsers($refusedGroups);
        if(UserRole::hasAUserRole($users, UserRole::LAB_LEADER))
           return Response::json(false); 
        foreach($refusedGroups as $refusedGroup) {
            $this::refusedGroupId($refusedGroup);
        }
        $refusedLab->delete();
        return Response::json(true);
    }
    
    private function isAbleToDecideAboutUser($confirmedUser) {
        //$confirmedUser = User::find((integer)Input::get($userId));
        if($confirmedUser == null || /*$confirmedUser->group_confirmed == 1 ||*/ UserRole::hasUserRole($confirmedUser, UserRole::SUPER_ADMIN))
            return false;
        /*$roleAdmin = UserRole::getUserRole(UserRole::SUPER_ADMIN);
        if($roleAdmin != null && $roleAdmin->active)*/
        if(UserRole::hasUserRole(Auth::user(), UserRole::SUPER_ADMIN))
            return true;
        if(UserRole::hasUserRole($confirmedUser, UserRole::LAB_LEADER))
            return false;
        if(!UserRole::hasUserRole($confirmedUser, UserRole::GROUP_LEADER)) {
            /*$roleGroup = UserRole::getUserRole(UserRole::GROUP_LEADER);
            if($roleGroup != null && $confirmedUser->group_id == Auth::user()->group_id && $roleGroup->active == 1) */
            if($confirmedUser->group_id == Auth::user()->group_id && UserRole::hasUserRole(Auth::user(), UserRole::GROUP_LEADER))    
                return true;
        }
        /*$roleLab = UserRole::getUserRole(UserRole::LAB_LEADER);
        if($roleLab == null || $roleLab->active != 1)*/
        if(!UserRole::hasUserRole(Auth::user(), UserRole::LAB_LEADER))    
            return false;
        $confirmedUserGroup = Group::find($confirmedUser->group_id);
        $userGroup = Group::find(Auth::user()->group_id);
        return $confirmedUserGroup->lab_id == $userGroup->lab_id;
    }
    
    private function isAble2DecideAboutGroup($confirmedGroup) {
        //$confirmedGroup = Group::find($groupId);
        if($confirmedGroup == null || /*$confirmedGroup->active == 1 ||*/ $confirmedGroup->id == 1) 
            return false;
        $roleAdmin = UserRole::getUserRole(UserRole::SUPER_ADMIN);
        if($roleAdmin != null && $roleAdmin->active)
            return true;
        $roleLab = UserRole::getUserRole(UserRole::LAB_LEADER);
        if($roleLab == null || $roleLab->active != 1)
            return false;
        return $confirmedGroup->lab_id == Group::find(Auth::user()->group_id)->lab_id;
    }
    
    private function isAble2DecideAboutLab($confirmedLab) {
        if($confirmedLab == null || /*$confirmedLab->active == 1 ||*/ $confirmedLab->id == 1)
            return false;
        $roleAdmin = UserRole::getUserRole(UserRole::SUPER_ADMIN);
        return $roleAdmin != null && $roleAdmin->active;
    }
    
       
    public function giveUserRole() {
        if(!Input::has('userId') || !Input::has('roleId'))
            return $this::index();
        $this::giveUserRoleId(Input::get('userId'), Input::get('roleId'));
        return $this::index();
    }
    
    private function giveUserRoleId($userId, $roleId) {
        if(!UserRole::isGreaterThan(Auth::user(), $roleId))
            return false;
        $user = User::find($userId);
        if(!$this::isAbleToDecideAboutUser($user))
            return false;
        UserRole::updateRole($user, $roleId, true);
        return true;       
    }
    
    public function deleteUserRole() {
        if(!Input::has('userId') || !Input::has('roleId'))
            return $this::index();
        $this::deleteUserRoleId(Input::get('userId'), Input::get('roleId'));
        return $this::index();
    }
    
    private function deleteUserRoleId($userId, $roleId) {
        $user = User::find($userId);
        if(!$this::isAbleToDecideAboutUser($user))
            return false;
        
        $role = UserRole::getRoleFromUser($user, $roleId);
        if($role != null)
            $role->delete();
        return false;       
    }
}
