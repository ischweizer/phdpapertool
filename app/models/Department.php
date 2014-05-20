<?php

class Department extends Eloquent {
	public function university()
	{
		return $this->belongsTo('University');
	}

	public function labs()
	{
		return $this->hasMany('Lab');
	}

	public function getInactiveUsersQuery()
	{
		return User::where('active', '=', 0)
			->join('authors', 'authors.id', '=', 'users.author_id')
			->join('groups', 'groups.id', '=', 'authors.group_id')
			->join('labs', 'labs.id', '=', 'groups.lab_id')
			->join('departments', 'departments.id', '=', 'labs.department_id')
			->where('departments.id', '=', $this->id)->select('users.*');
	}
}
