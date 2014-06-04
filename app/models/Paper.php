<?php

class Paper extends Eloquent {
	
	protected $fillable = array('title', 'abstract', 'repository_url');
	
	public function authors()
	{
		return $this->belongsToMany('Author');
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
				'title'	=> 'Required'
		);
		return Validator::make($input, $rules);
	}
}
