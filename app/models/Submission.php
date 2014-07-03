<?php

use Carbon\Carbon;

// submissions should be set to inactive 
// - as soon as a retarget is done
// - as soon as a negative result is reported

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
					  ->whereBetween('abstract_due', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)'), DB::raw('CURDATE()')));
			})->orWhere(function($query)
			{
				$query->whereNull('paper_submitted')
					  ->whereBetween('paper_due', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)'), DB::raw('CURDATE()')));
			})->orWhere(function($query)
			{
				$query->whereNull('notification_result')
					  ->whereBetween('notification_date', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)'), DB::raw('CURDATE()')));
			})->orWhere(function($query)
			{
				$query->whereNull('camera_ready_submitted')
					  ->whereBetween('camery_ready_due', array(DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 DAY)'), DB::raw('CURDATE()')));
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

	/**
	 * Returns whether "abstract submitted" may be set now. It may be set if it isn't set yet and "abstract due" passed.
	 */
	public function isAbstractReadyToSet() {
		return $this->abstract_submitted === null && $this->event->abstract_due->lte(Carbon::now());
	}

	/**
	 * Returns whether "paper submitted" may be set now. It may be set if "abstract submitted" is set, it isn't set yet and "paper due" passed.
	 */
	public function isPaperReadyToSet() {
		return $this->abstract_submitted && $this->paper_submitted === null && $this->event->paper_due->lte(Carbon::now());
	}

	/**
	 * Returns whether "notification result" may be set now. It may be set if "paper submitted" and "abstract submitted" are set, it isn't set yet and "notification date" passed.
	 */
	public function isNotificationReadyToSet() {
		return $this->abstract_submitted && $this->paper_submitted && $this->notification_result === null && $this->event->notification_date->lte(Carbon::now());
	}

	/**
	 * Returns whether "camera ready submitted" may be set now. It may be set if "paper submitted", "abstract submitted" and "notification date" are set, it isn't set yet and "camera ready due" passed.
	 */
	public function isCameraReadyReadyToSet() {
		return $this->abstract_submitted && $this->paper_submitted && $this->notification_result && $this->camery_ready_submitted === null && $this->event->notification_date->lte(Carbon::now());
	}
}
