<?php

class Paper extends Eloquent {
	
	protected $fillable = array('title', 'abstract', 'repository_url');
	
	public function authors()
	{
		return $this->belongsToMany('Author');
	}
}
