<?php

class Submission extends Eloquent {
	public function paper()
	{
		return $this->belongsTo('Paper');
	}

	public function event()
	{
		return $this->belongsTo('Event');
	}
}
