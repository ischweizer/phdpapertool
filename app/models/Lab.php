<?php

class Lab extends Eloquent {

	public function groups()
	{
		return $this->hasMany('Group');
	}

	public function getInactiveUsersQuery()
	{
		return User::where('active', '=', 0)
			->join('authors', 'authors.id', '=', 'users.author_id')
			->join('groups', 'groups.id', '=', 'authors.group_id')
			->join('labs', 'labs.id', '=', 'groups.lab_id')
			->where('labs.id', '=', $this->id)->select('users.*');
	}
        
        public static function getLabs($groups) {
            $labs = Lab::where('id', '!=', 1)->get();;
            if($groups == null)
                return $labs;
            $result = array();
            foreach($labs as $lab) {
                foreach($groups as $group) {
                    if($lab->id == $group->lab_id && $lab->id != 1)
                        $result[$lab->id] = $lab;
                }     
            }
            return $result;
        }
}
