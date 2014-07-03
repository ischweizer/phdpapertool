<?php
use Carbon\Carbon;

/**
 * Description of TimelineController
 *
 * @author Binh Vu
 */
class TimelineController extends BaseController {
	public function getIndex($id = null) {
		$user = Auth::user();
		$groups = array();
		if ($user->isLabLeader()) {
			$lab = array($user->group->lab);
			$groups = Group::getGroupsFromLabs($lab);
		} elseif ($user->isGroupLeader()) {
			$groups[] = $user->group;
		}
	//die(var_dump($groups));
		return View::make(
			'timeline', 
			array(	
				'papers' => self::getPapers(),
				'groups' => $groups,
				'selectedGroups' => array(),
			)
		);
	}

	public function getData() {
		$format = 'm/d/Y';
		$pastLimit = 3;
		$futureLimit = 3;

		$data = array(
			'lanes' => array(),
			'items' => array(),
		);

		if(Input::has('groupids'))
			$groupsIds = explode(',', Input::get('groupids'));
		else
			$groupsIds = array(Auth::user()->group_id);
		$count = 0; $laneId = 0;
		foreach($this->getSubmissions($groupsIds, $pastLimit, $futureLimit) as $submission) {
			$paper = $submission->paper;
			$event = $submission->event;

			$data['lanes'][] = array(
				'id' => $laneId,
				'label' => $paper->title
			);

			$data['items'][] = array(
				'id' => $count++,
				'desc' => 'Paper Submission',
				'class' => 'paper',
				'lane' => $laneId,
				'start' => $event->abstract_due->format($format),
				'end' => $event->paper_due->addDay()->format($format),
			);

			$data['items'][] = array(
				'id' => $count++,
				'desc' => 'Notification',
				'class' => 'noti',
				'lane' => $laneId,
				'start' => $event->paper_due->format($format),
				'end' => $event->notification_date->addDay()->format($format),
			);

			$data['items'][] = array(
				'id' => $count++,
				'desc' => 'Camera Ready Submission',
				'class' => 'cam',
				'lane' => $laneId,
				'start' => $event->notification_date->format($format),
				'end' => $event->camera_ready_due->addDay()->format($format),
			);

			$eventStart = $event->start;
			$eventEnd = $event->end;
			if ($event->detail->isWorkshop()) {
				$ceEvent = $event->detail->conferenceEdition->event;
				if ($eventStart->gt($ceEvent->start)) {
					$eventStart = $ceEvent->start;
				}
				if ($eventEnd->lt($ceEvent->end)) {
					$eventEnd = $ceEvent->end;
				}
			}

			$data['items'][] = array(
				'id' => $count++,
				'desc' => 'Conference',
				'class' => 'con',
				'lane' => $laneId,
				'start' => $eventStart->format($format),
				'end' => $eventEnd->addDay()->format($format),
			);
			
			if ($event->detail->isWorkshop()) {
				$data['items'][] = array(
					'id' => $count++,
					'desc' => 'Workshop',
					'class' => 'workshop',
					'lane' => $laneId,
					'start' => $event->start->format($format),
					'end' => $event->end->addDay()->format($format),
				);
			}

			$laneId++;
		}

		return Response::json($data);
	}
	
	private function getPapers() {
		$user = Auth::user();
		$user->load('author', 'author.papers', 'author.papers.activeSubmission', 'author.papers.activeSubmission.event');
		return $user->author->papers;
	}

	private function getSubmissions($groupsIds, $pastLimit = 0, $futureLimit = 0) {
		$query = Submission::with('paper', 'event')
			//->currentUser()
			->groups($groupsIds)
			->active()
			->join('events', 'events.id', '=', 'submissions.event_id')
			->select('submissions.*')
			->orderBy('events.abstract_due', 'ASC');
		if ($pastLimit > 0) {
			$query = $query->where('end', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL '. $pastLimit .' MONTH)'));
		}
		if ($futureLimit > 0) {
			$query = $query->where('abstract_due', '<', DB::raw('DATE_ADD(CURDATE(), INTERVAL '. $futureLimit .' MONTH)'));
		}
		return $query->get();
	}
}
