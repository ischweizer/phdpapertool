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

	public function isWorkshop() {
		return false;
	}

	public function isConferenceEdition() {
		return true;
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

		$uniqueIgnore = '';
		if (isset($input['id'])) {
			$id = (int) $input['id'];
			if ($id > 0) {
				$uniqueIgnore = ',' . $id;
			}
		}
		$rules = array(
				'id'			=> 'Exists:conference_editions,id',
				'conference_id'	=> 'Required|Exists:conferences,id|Unique_with:conference_editions,edition' . $uniqueIgnore,
				'location'		=> 'Required',
				'edition'		=> 'Required'
		);
		$rules = array_merge($rules, EventModel::getValidateRules());
		return Validator::make($input, $rules);
	}
}
