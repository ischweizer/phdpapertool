<?php

class Workshop extends Eloquent {
	protected $fillable = array('name', 'conference_edition_id');

	public function conferenceEdition()
	{
		return $this->belongsTo('ConferenceEdition');
	}

	public function event()
	{
		return $this->morphOne('EventModel', 'detail');
	}

	public function isWorkshop() {
		return true;
	}

	public function isConferenceEdition() {
		return false;
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
				'id'					=> 'Exists:workshops,id',
				'conference_edition_id'	=> 'Required|Exists:conference_editions,id',
				'name'					=> 'Required|Unique:workshops,name' . $uniqueIgnore
		);
		$rules = array_merge($rules, EventModel::getValidateRules());
		return Validator::make($input, $rules);
	}
}
