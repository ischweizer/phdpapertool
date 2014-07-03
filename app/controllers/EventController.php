<?php

use Illuminate\Support\MessageBag;

class EventController extends BaseController {
	
	public function getIndex() {
		$events = EventModel::where('abstract_due', '>=', new DateTime())->orderBy('abstract_due', 'ASC')->get();
		
		$ceditions = array();
		$workshops = array();
		
		foreach ($events as $event) {
			if ($event->detail_type == 'ConferenceEdition') {
				array_push($ceditions, $event);
			} else if ($event->detail_type == 'Workshop') {
				array_push($workshops, $event);
			}
		}
		
		return View::make('event/index')->with('conferenceeditions', $ceditions)->with('workshops', $workshops);
	}
}
