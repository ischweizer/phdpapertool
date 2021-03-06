<?php

class Author extends Eloquent {
	
	protected $fillable = array('last_name', 'first_name', 'email');
	
	public function user()
	{
		return $this->hasOne('User');
	}

	public function papers()
	{
		return $this->belongsToMany('Paper')->withPivot('order_position')->where('archived', '<>', '1');
	}
	
	public function archivedPapers()
	{
		return $this->belongsToMany('Paper')->where('archived', '<>', '0');
	}

	public function scopeNotAdmin($query) {
		return $query->where('id', '<>', '1');
	}

	/**
	 * Relation to get review requests to this author.
	 */
	public function reviewRequests(){
		return $this->belongsToMany('ReviewRequest')->withPivot('answer')->withPivot('auth_token');
	}

	public function countReviewRequestNotAnswered(){
		return $this->reviewRequests()->whereNull('answer')->count(); //TODO
	}

	/**
	 * Relation to get review answers created by this author.
	 */
	public function reviews() {
		return $this->hasMany('Review');
	}

	/**
	 * return the name of this as a readable String
	 */
	public function formatName()
	{
		return $this->first_name." ".$this->last_name." (".$this->email.")";
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
				'email'				=> 'Required|Email|Unique:authors,email' . $uniqueIgnore,
				'first_name'		=> 'Required',
				'last_name'			=> 'Required'
		);
		return Validator::make($input, $rules);
	}
	
	public function scopeFromPapers($query, $papers) {
	    $papersIds = array();
	    foreach($papers as $paper)
		$papersIds[] = $paper->id;
	    $query->whereExists(function($query2) use ($papersIds) {
		$query2->select(DB::raw(1))
			->from('author_paper')
			->where('author_paper.author_id', DB::raw('authors.id'))
			->whereIn('author_paper.paper_id', $papersIds);
	    });
	}
}
