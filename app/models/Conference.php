<?php

class Conference extends Eloquent {
	public function ranking()
	{
		return $this->belongsTo('Ranking');
	}

	public function editions()
	{
		return $this->hasMany('ConferenceEdition');
	}
}
