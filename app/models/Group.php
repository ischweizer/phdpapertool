<?php

class Group extends Eloquent {
	public function lab()
	{
		return $this->belongsTo('Lab');
	}

	public function users() {
		return $this->hasMany('User');
	}

	public static function getGroups($users) {
		$groups = Group::where('id', '!=', 1)->get();
		if($users == null)
			return $groups;
		$result = array();
		foreach($groups as $group) {
			foreach($users as $user) {
				if($group->id == $user->group_id && $group->id != 1) {
					$result[$group->id] = $group;
					continue 2;
				}
			}
		}
		return $result;
	}

	public function getAdmin(){
		$users = $this->users;
		foreach ($users as $user) {
			if (UserRole::hasUserRole($user, UserRole::GROUP_LEADER)) {
				return $user;
			}
		}
		return $this->lab->getAdmin();
	}
	
	public static function getGroupsFromLabs($labs) {
		$groups = Group::where('id', '!=', 1)->get();
		if($labs == null)
			return $groups;
		$result = array();
		foreach($groups as $group) {
			foreach($labs as $lab) {
				if($group->lab_id == $lab->id && $group->id != 1) {
					$result[$group->id] = $group;
					continue 2;
				}
			}
		}
		return $result;
	}
}
