<?php

class FileObject extends Eloquent {
	protected $table = 'files';
	protected $fillable = array('author_id', 'paper_id', 'name', 'comment', 'filepath');
	
	public function paper()
	{
		return $this->belongsTo('Paper');
	}

	public function author()
	{
		return $this->belongsTo('Author');
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
				'name'	=> 'Required'
		);
		return Validator::make($input, $rules);
	}
}
