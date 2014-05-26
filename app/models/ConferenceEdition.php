<?php

use Carbon\Carbon;

class ConferenceEdition extends Eloquent {
	protected $dates = array('start', 'end', 'abstract_due', 'paper_due', 'notification_date', 'camera_ready_due');
	protected $fillable = array('location', 'edition', 'start', 'end', 'abstract_due', 'paper_due', 'notification_date', 'camera_ready_due');

	public function conference()
	{
		return $this->belongsTo('Conference');
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

	/**
	 * Validate the given input.
	 *
	 * @param  array
	 * @return \Illuminate\Validation\Validator
	 */
	public static function validate($input = null) {
		if (is_null($input)) {
			$input = Input::all();
		}
		$rules = array(
				'id'				=> 'Exists:conference_editions,id',
				'conference.name'	=> 'Required|Exists:conferences,name',
				'location'			=> 'Required',
				'edition'			=> 'Required',
				'abstract_due'		=> 'Required|date_format2:M d, Y|before_or_equal:paper_due',
				'paper_due'			=> 'Required|date_format2:M d, Y|before_or_equal:notification_date',
				'notification_date'	=> 'Required|date_format2:M d, Y|before_or_equal:camera_ready_due',
				'camera_ready_due'	=> 'Required|date_format2:M d, Y|before_or_equal:start',
				'start'				=> 'Required|date_format2:M d, Y|before_or_equal:end',
				'end'				=> 'Required|date_format2:M d, Y'
		);
		return Validator::make($input, $rules);
	}
}
