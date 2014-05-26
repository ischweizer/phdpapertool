<?php

class Author extends Eloquent {
	
	protected $fillable = array('last_name', 'first_name', 'email');
	
	public function user()
	{
		return $this->hasOne('User');
	}

	public function papers()
	{
		return $this->belongsToMany('Paper');
	}
}
