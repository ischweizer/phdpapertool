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
            $groups = Group::all();
            if($users == null)
                return $groups;
            $result = array();
            foreach($users as $user) {
                foreach($groups as $group) {
                    if($group->id == $user->group_id)
                        $result[] = $group;
                }
            }
            return $result;
        }
        
        public static function getGroupsFromLabs($labs) {
            $groups = Group::all();
            if($labs == null)
                return array();
            $result = array();
            foreach($labs as $lab) {
                foreach($groups as $group) {
                    if($group->lab_id == $lab->id)
                        $result[] = $group;
                }
            }
            return $result;
        }
}
