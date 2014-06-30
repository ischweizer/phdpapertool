<?php

use Illuminate\Support\MessageBag;

class EventController extends BaseController {
	
	public function getIndex() {
		$events = EventModel::where('abstract_due', '>=', new DateTime())->orderBy('abstract_due', 'ASC')->get();
		
		return View::make('event/index')->with('eventlist', $events);
	}
}
