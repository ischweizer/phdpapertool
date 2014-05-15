<?php

class Paper extends Eloquent {
	public function authors()
	{
		return $this->belongsToMany('Author');
	}
}
