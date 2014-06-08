<?php

class Conference extends Eloquent {
	protected $fillable = array('name', 'acronym', 'ranking_id', 'field_of_research');

	public function ranking()
	{
		return $this->belongsTo('Ranking');
	}

	public function editions()
	{
		return $this->hasMany('ConferenceEdition');
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
		// nothing to validate in acronym
		// FoR not validated (for now?)
		$rules = array(
				'id'					=> 'Exists:conferences,id',
				'ranking_id'			=> 'Required|Exists:rankings,id',
				'name'					=> 'Required|Unique:conferences,name' . $uniqueIgnore
		);
		return Validator::make($input, $rules);
	}
}
