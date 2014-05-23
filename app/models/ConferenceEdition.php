<?php

class ConferenceEdition extends Eloquent {
	public function conference()
	{
		return $this->belongsTo('Conference');
	}
}
