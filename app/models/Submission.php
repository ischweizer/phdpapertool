<?php

class Submission extends Eloquent {
	public function paper()
	{
		return $this->belongsTo('Paper');
	}

	public function event()
	{
		return $this->belongsTo('EventModel', 'event_id');
	}

	/**
	 * Scope to select submissions which belong to the current user.
	 */
	public function scopeCurrentUser($query) {
		$query->whereExists(function($query) {
			$query->select(DB::raw(1))
				  ->from('author_paper')
				  ->where('author_paper.paper_id', '=', DB::raw('submissions.paper_id'))
				  ->where('author_paper.author_id', '=', Auth::user()->author->id);
            });
	}

	/**
	 * Scope to select active submissions.
	 */
	public function scopeActive($query) {
		$query->where('active', '=', '1');
	}

	/**
	 * Scope to select submissions where a date is due tomorrow (and is not yet set).
	 */
	public function scopeTomorrow($query) {
		return $query->join('events', 'events.id', '=', 'submissions.event_id')->where(function($query)
			{
				$query->whereNull('abstract_submitted')
					  ->whereBetween('abstract_due', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)', DB::raw('CURDATE()'))));
			})->orWhere(function($query)
			{
				$query->whereNull('paper_submitted')
					  ->whereBetween('paper_due', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)', DB::raw('CURDATE()'))));
			})->orWhere(function($query)
			{
				$query->whereNull('notification_result')
					  ->whereBetween('notification_date', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)', DB::raw('CURDATE()'))));
			})->orWhere(function($query)
			{
				$query->whereNull('camera_ready_submitted')
					  ->whereBetween('camery_ready_due', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)', DB::raw('CURDATE()'))));
			})->select('submissions.*');
	}

	/**
	 * Scope to select submissions where a date has passed (and is not yet set). 
	 */
	public function scopePassed($query) {
		return $query->join('events', 'events.id', '=', 'submissions.event_id')->where(function($query)
			{
				$query->whereNull('abstract_submitted')
					  ->where('abstract_due', '>', DB::raw('CURDATE()'));
			})->orWhere(function($query)
			{
				$query->whereNull('paper_submitted')
					  ->whereBetween('paper_due', '>', DB::raw('CURDATE()'));
			})->orWhere(function($query)
			{
				$query->whereNull('notification_result')
					  ->whereBetween('notification_date', '>', DB::raw('CURDATE()'));
			})->orWhere(function($query)
			{
				$query->whereNull('camera_ready_submitted')
					  ->whereBetween('camery_ready_due', '>', DB::raw('CURDATE()'));
			})->select('submissions.*');
	}
}
