<?php

class ReviewRequest extends Eloquent{
	protected $dates = array('deadline');
	protected $fillable = array('user_id','paper_id', 'deadline', 'message');

	public function user(){
		return $this->belongsTo('User');
	}

	public function paper(){
		return $this->belongsTo('Paper');
	}

	public function files(){
		return $this->belongsToMany('FileObject', 'file_review_request', 'review_request_id', 'file_id');
	}

	public function authors(){
		return $this->belongsToMany('Author')->withPivot('answer')->withPivot('auth_token');
	}

	public function reviews(){
		return $this->hasMany('Review');
	}

	public function remindableUsers(){
		$users = array();
		foreach ($this->authors as $author) {
			if($author->user){
				if(is_null($author->pivot->answer))
					array_push($users, $author->user);
				if($author->pivot->answer){
					$finished = false;
					foreach ($this->reviews as $review) {
						if($review->author_id == $author->id){
							$finished = true;
							break;
						}
					}
					if(!$finished)
						array_push($users, $author->user);
				}
			}
		}
		return $users;
	}

	/**
	 * Convert a DateTime to a storable string.
	 * Overwrite fromDateTime to support the used format (e.g. May 26, 2014)
	 *
	 * @param  \DateTime|int  $value
	 * @return string
	 */
	public function fromDateTime($value) {
		if (is_string($value) && preg_match('/^([A-Z][a-z]+) (\d{2}), (\d{4})$/', $value)) {
			$value = Carbon::createFromFormat('M d, Y', $value)->startOfDay();
		}
		return parent::fromDateTime($value);
	}

	public function getPapers() {
	    return array(Paper::find($this->paper_id));
	}
}