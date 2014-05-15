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
}
