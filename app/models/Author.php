<?php

class Author extends Eloquent {
	
	protected $fillable = array('last_name', 'first_name', 'email');
	
	public function user()
	{
		return $this->hasOne('User');
	}

	public function papers()
	{
		return $this->belongsToMany('Paper')->withPivot('order_position');
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
				'email'				=> 'required|email',
				'first_name'		=> 'Required',
				'last_name'			=> 'Required'
		);
		return Validator::make($input, $rules);
	}
}
