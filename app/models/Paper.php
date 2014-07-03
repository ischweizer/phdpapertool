<?php

class Paper extends Eloquent {
	
	protected $fillable = array('title', 'abstract', 'repository_url', 'archived');
	
	public function authors()
	{
		return $this->belongsToMany('Author')->withPivot('order_position')->orderBy('order_position', 'asc');
	}

	public function submissions()
	{
		return $this->hasMany('Submission');
	}
	
	public function files()
	{
		return $this->hasMany('FileObject');
	}

	public function activeSubmission()
	{
		return $this->hasOne('Submission')->where('active', '=', 1);
	}
	
	public function scopeUsers($fluentQuery, $usersIds) {
	    $fluentQuery->whereExists(function($query) use ($usersIds) {
		$query->select(DB::raw(1))
			->from('papers')
			->join('author_paper', 'papers.id', '=', DB::raw('author_paper.paper_id'))
			->join('users', 'author_paper.author_id', '=', DB::raw('users.author_id'))
			->whereIn('users.id', $usersIds);
	    });
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
