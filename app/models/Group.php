<?php

class Group extends Eloquent {
	public function lab()
	{
		return $this->belongsTo('Lab');
	}

	public function authors()
	{
		return $this->hasMany('Author');
	}

	public function getInactiveUsersQuery()
	{
		return User::where('active', '=', 0)
			->join('authors', 'authors.id', '=', 'users.author_id')
			->join('groups', 'groups.id', '=', 'authors.group_id')
			->where('groups.id', '=', $this->id)->select('users.*');
	}
        
        public static function getGroups($users) {
            $groups = Group::where('id', '!=', 1)->get();
            if($users == null)
                return $groups;
            $result = array();
            foreach($groups as $group) {
                foreach($users as $user) {
                    if($group->id == $user->group_id && $group->id != 1)
                        $result[$group->id] = $group;
                }
            }
            return $result;
        }
        
        public static function getGroupsFromLabs($labs) {
            $groups = Group::where('id', '!=', 1)->get();
            if($labs == null)
                return $groups;
            $result = array();
            foreach($groups as $group) {
                foreach($labs as $lab) {
                    if($group->lab_id == $lab->id && $group->id != 1)
                        $result[$group->id] = $group;
                }
            }
            return $result;
        }
}
