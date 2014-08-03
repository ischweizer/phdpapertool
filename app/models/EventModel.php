<?php

use Carbon\Carbon;

// EventModel instead of Event because of name conflict with laravel "Event"
// Setting a Namespace is not a working solution, as relations won't work.
class EventModel extends Eloquent {
	protected $table = 'events';
	protected $dates = array('start', 'end', 'abstract_due', 'paper_due', 'notification_date', 'camera_ready_due');
	protected $fillable = array('start', 'end', 'abstract_due', 'paper_due', 'notification_date', 'camera_ready_due');

	public function detail()
	{
		return $this->morphTo();
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

	/**
	 * Get validation rules.
	 */
	public static function getValidateRules() {
		$rules = array(
				'event.abstract_due'		=> 'Required|date_format2:M d, Y|before_or_equal:event.paper_due',
				'event.paper_due'			=> 'Required|date_format2:M d, Y|before_or_equal:event.notification_date',
				'event.notification_date'	=> 'Required|date_format2:M d, Y|before_or_equal:event.camera_ready_due',
				'event.camera_ready_due'	=> 'Required|date_format2:M d, Y|before_or_equal:event.start',
				'event.start'				=> 'Required|date_format2:M d, Y|before_or_equal:event.end',
				'event.end'					=> 'Required|date_format2:M d, Y'
		);
		return $rules;
	}
	
	
	public function getPapers() {
	    $eventId = $this->id;
	    return Paper::whereExists(function($query) use($eventId) {
		$query->select(DB::raw(1))
			->from('submissions')
			->where('submissions.paper_id', DB::raw('papers.id'))
			->where('submissions.event_id', DB::raw($eventId))
			->where('submissions.active', DB::raw(1));
	    })->get();
	}
	
	public function getWorkshop() {
	    if($this->detail_type == "Workshop")
		return Workshop::find($this->detail_id);
	    return null;
	}
	
	public function getConferenceEdition() {
	    if($this->detail_type == "ConferenceEdition")
		return ConferenceEdition::find($this->detail_id);
	    return null;
	}
}
