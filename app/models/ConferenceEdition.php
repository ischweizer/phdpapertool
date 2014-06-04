<?php

class ConferenceEdition extends Eloquent {
	protected $fillable = array('conference_id', 'location', 'edition');

	public function conference()
	{
		return $this->belongsTo('Conference');
	}

	public function event()
	{
		return $this->morphOne('EventModel', 'detail');
	}

	public function workshops()
	{
		return $this->hasMany('Workshop');
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
				'id'			=> 'Exists:conference_editions,id',
				'conference_id'	=> 'Required|Exists:conferences,id',
				'location'		=> 'Required',
				'edition'		=> 'Required'
		);
		$rules = array_merge($rules, EventModel::getValidateRules());
		return Validator::make($input, $rules);
	}
}
