<?php

use Carbon\Carbon;

class Review extends Eloquent {
	
	protected $dates = array('deadline');
	protected $fillable = array('user_id', 'paper_id', 'deadline');
	
	public function user()
	{
		return $this->belongsTo('User');
	}

	public function paper()
	{
		return $this->belongsTo('Paper');
	}

	public function files()
	{
		return $this->belongsToMany('FileObject', 'file_review', 'review_id', 'file_id');
	}

	public function users()
	{
		return $this->belongsToMany('User')->withPivot('answer');
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
