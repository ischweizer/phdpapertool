<?php

class Paper extends Eloquent {
	
	protected $fillable = array('title', 'abstract', 'repository_url');
	
	public function authors()
	{
		return $this->belongsToMany('Author')->withPivot('order_position')->orderBy('order_position', 'asc');
	}

	public function submissions()
	{
		return $this->hasMany('Submission');
	}

	public function activeSubmission()
	{
		return $this->hasOne('Submission')->where('active', '=', 1);
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
