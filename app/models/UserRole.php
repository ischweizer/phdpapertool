<?php

class UserRole extends Eloquent {
	public function user()
	{
		return $this->belongsTo('User');
	}

	public function role()
	{
		return $this->morphTo();
	}
}
