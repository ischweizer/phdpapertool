<?php

use Carbon\Carbon;

class Review extends Eloquent {
	
	protected $dates = array('deadline');
	protected $fillable = array('author_id', 'review_request_id', 'message');
	
	public function author()
	{
		return $this->belongsTo('Author');
	}

	public function files()
	{
		return $this->hasMany('FileObject');
	}

	public function reviewRequest()
	{
		return $this->belongsTo('ReviewRequest');
	}

	/**
	 * Convert a DateTime to a storable string.
	 * Overwrite fromDateTime to support the used format (e.g. May 26, 2014)
	 *
	 * @param  \DateTime|int  $value
	 * @return string
	 */
	public function fromDateTime($value) {
		if (is_string($value) && preg_match('/^([A-Z][a-z]+) (\d{2}), (\d{4})$/', $value)) {
			$value = Carbon::createFromFormat('M d, Y', $value)->startOfDay();
		}
		return parent::fromDateTime($value);
	}
}
