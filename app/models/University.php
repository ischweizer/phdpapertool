<?php

class University extends Eloquent implements ManagedLevel {
	public function administration()
	{
		return $this->belongsTo('Administration');
	}

	public function departments()
	{
		return $this->hasMany('Department');
	}

	public function getInactiveUsersQuery()
	{
		return User::where('active', '=', 0)
			->join('authors', 'authors.id', '=', 'users.author_id')
			->join('groups', 'groups.id', '=', 'authors.group_id')
			->join('labs', 'labs.id', '=', 'groups.lab_id')
			->join('departments', 'departments.id', '=', 'labs.department_id')
			->join('universities', 'universities.id', '=', 'departments.university_id')
			->where('universities.id', '=', $this->id)->select('users.*');
	}
}
