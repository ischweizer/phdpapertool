<?php

class UserRole extends Eloquent {
        
        const GROUP_LEADER = 0;
        const LAB_LEADER = 1;
        const SUPER_ADMIN = 2;
    
	public function user()
	{
		return $this->belongsTo('User');
	}

	public function role()
	{
		return $this->morphTo();
	}
        
        public static function getUserRole($roleId) {
            $roles = UserRole::where('user_id', '=', Auth::user()->id);
            foreach($roles as $role) {
                if($role->role_id == $roleId) 
                    return $role;
            }      
            return null;
        }    
        
        public static function updateRole($user, $roleId, $isActive) {
            $role = UserRole::getUserRole($roleId);
            if($role != null) {
                $role->active = $isActive;
                return $role;
            }
            $newRole = new User_role;
            $newRole->user_id = $user->id;
            $newRole->role_id = $roleId;
            $newRole->active = $isActive;
            $newRole->save();     
            return $newRole;
        }
}
