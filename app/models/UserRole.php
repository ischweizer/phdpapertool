<?php

class UserRole extends Eloquent {
        

        const SUPER_ADMIN = 1;
        const LAB_LEADER = 2;
        const GROUP_LEADER = 3;
    
	public function user()
	{
		return $this->belongsTo('User');
	}

	public function role()
	{
		return $this->morphTo();
	}
        
        public static function getUserRole($roleId) {
            return UserRole::getRoleFromUser(Auth::user(), $roleId);
        }    
        
        public static function getRoleFromUser($user, $roleId) {
            return UserRole::where('user_id', '=', $user->id)->where('role_id', '=', $roleId)->first();         
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
        
        public static function getUsersRoles($users, $roleId) {
            $roles = UserRole::where('user_id', '!=', 1)->where('role_id', '=', $roleId)->get();
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
            $roles = UserRole::where('user_id', '!=', 1)->where('user_id', '!=', Auth::user()->id)->where('role_id', '=', $roleId)->get();
            foreach($roles as $role) {
                foreach($users as $user) {
                    if($role->user_id == $user->id)
                        return true;
                }
            }
            return false;
        }
        
        public static function hasUserRole($user, $roleId) {
            $numberOfRoles = UserRole::where('user_id', '=', $user->id)->where('role_id', '=', $roleId)->count();
            if($numberOfRoles == 0)
                return false;
            return true;
        }
}
