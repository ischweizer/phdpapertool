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
		$roles = UserRole::where('user_id', '=', $user->id)->get();
		foreach($roles as $role) {
			if($role->role_id == $roleId) 
				return $role;
		}      
		return null;            
	}
	
	public static function updateRole($user, $roleId, $isActive) {
		$role = UserRole::getRoleFromUser($user, $roleId);
		if($role != null) {
			$role->active = $isActive;
			return $role;
		}
		$newRole = new UserRole;
		$newRole->user_id = $user->id;
		$newRole->role_id = $roleId;
		$newRole->active = $isActive;
		$newRole->save();     
		return $newRole;
	}
}
