<?php

class UserRole extends Eloquent {
	const SUPER_ADMIN = 1;
	const LAB_LEADER = 2;
	const GROUP_LEADER = 3;
	
	public function user()
	{
		return $this->belongsTo('User');
	}
		

	public static function getUserRole($roleId) {
		return UserRole::getRoleFromUser(Auth::user(), $roleId);
	}    
	
	public static function getRoleFromUser($user, $roleId) {
		return UserRole::where('user_id', '=', $user->id)->where('role_id', '=', $roleId)->first();         
	}
	
	public static function updateRole($user, $roleId, $isActive) {
		//$role = UserRole::getRoleFromUser($user, $roleId);
		$role = UserRole::where('user_id', '=', $user->id)->first();
		if($role != null) {
			$role->active = $isActive;
			$role->role_id = $roleId;
			$role->save();
			return $role;
		}
		$newRole = new UserRole;
		$newRole->user_id = $user->id;
		$newRole->role_id = $roleId;
		$newRole->active = $isActive ? 1 : 0;
		$newRole->save();     
		return $newRole;
	}
	
	public static function getUsersRoles($users, $roleId) {
		$roles = UserRole::where('user_id', '!=', 1)->where('role_id', '=', $roleId)->where('active', '=', 1)->get();
		$result = array();
		foreach($roles as $role) {
			foreach($users as $user) {
				if($role->user_id == $user->id) {
					$result[] = $role;
					continue 2;
				}
			}
		}
		return $roles;
	}
	
	public static function hasAUserRole($users, $roleId) {
		$roles = UserRole::where('user_id', '!=', 1)->where('user_id', '!=', Auth::user()->id)->where('role_id', '=', $roleId)->where('active', '=', 1)->get();
		foreach($roles as $role) {
			foreach($users as $user) {
				if($role->user_id == $user->id)
					return true;
			}
		}
		return false;
	}
	
	public static function hasUserRole($user, $roleId) {
		$numberOfRoles = UserRole::where('user_id', '=', $user->id)->where('role_id', '=', $roleId)->where('active', '=', 1)->count();
		if($numberOfRoles == 0)
			return false;
		return true;
	}
	
	public static function isGreaterThan($user, $roleId2) {
		if($roleId2 == UserRole::SUPER_ADMIN)
			return false;            
		$roleId1 = -1;
		if(UserRole::hasUserRole($user, UserRole::SUPER_ADMIN))
			$roleId1 = UserRole::SUPER_ADMIN;
		else if(UserRole::hasUserRole($user, UserRole::LAB_LEADER))
			$roleId1 = UserRole::LAB_LEADER;
		else if(UserRole::hasUserRole($user, UserRole::GROUP_LEADER))
			$roleId1 = UserRole::GROUP_LEADER;
		if($roleId1 == -1)
			return false;
		
		if($roleId1 == UserRole::SUPER_ADMIN)
			return true;
		if($roleId1 == UserRole::LAB_LEADER && $roleId2 == UserRole::GROUP_LEADER)
			return true;
		return false;
	}

}
