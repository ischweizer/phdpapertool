<?php

class Author extends Eloquent {
	public function user()
	{
		return $this->hasOne('User');
	}

	public function papers()
	{
		return $this->belongsToMany('Paper');
	}
}
