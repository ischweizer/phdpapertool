<?php

class Workshop extends Eloquent {
	protected $fillable = array('name');

	public function conferenceEdition()
	{
		return $this->belongsTo('ConferenceEdition');
	}

	public function event()
	{
		return $this->morphOne('EventModel', 'detail');
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
				'id'					=> 'Exists:workshops,id',
				'conference_edition_id'	=> 'Required|Exists:conference_editions,id',
				'name'					=> 'Required',
		);
		$rules = array_merge($rules, EventModel::getValidateRules());
		return Validator::make($input, $rules);
	}
}
