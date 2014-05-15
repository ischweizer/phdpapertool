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
}
