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
}
