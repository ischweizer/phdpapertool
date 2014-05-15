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
}
