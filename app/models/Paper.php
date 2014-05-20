<?php

class Paper extends Eloquent {
	
	protected $fillable = array('title', 'abstract');
	
	public function authors()
	{
		return $this->belongsToMany('Author');
	}
}
