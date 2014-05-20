<?php

class Lab extends Eloquent {
	public function department()
	{
		return $this->belongsTo('Department');
	}

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
}
