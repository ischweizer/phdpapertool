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
		
		return View::make(
			'timeline', 
			array(	
				'groups' => $groups,
				'selectedGroups' => array(),
			)
		);
	}

	public function getData() {
		$format = 'm/d/Y';

		// no limits currently as navigation is implemented
		$pastLimit = 0;
		$futureLimit = 0;

		$data = array(
			'lanes' => array(),
			'items' => array(),
		);
		
		$sort = '';
		$order = '';
		if (Input::has('sort') && Input::has('order')) {
			$sort = Input::get('sort');
			$order = Input::get('order');
		}

		if(Input::has('groupids')) {
		    $groupsIds = explode(',', Input::get('groupids'));
		    $users = User::getUsers(Group::whereIn('id', $groupsIds)->get());
		    $usersIds = array();
		    foreach($users as $user) 
			$usersIds[] = $user->id;
		}
		else
			
		$usersIds = array(Auth::user()->id);	
		$count = 0; $laneId = 0; $papers = array();
		foreach($this->getSubmissions($usersIds, $pastLimit, $futureLimit, $sort, $order) as $submission) {
			$paper = $submission->paper;
			$event = $submission->event;
			$papers[] = $paper;

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
		
		$table = false;
		if (Input::has('update') && Input::get('update') == 1) {
			$table = self::buildTable($papers);
		}

		return Response::json(array(
			'graph' => $data,
			'table' => $table,
		));
	}
	
	private function buildTable($papers) {
		$result = array();
		foreach ($papers as $paper) {
			$title = '<td>'. $paper->title. '</td>';
			$abstract = '<td></td>';
			$papersubmit = '<td></td>';
			$notification = '<td></td>';
			$camera = '<td></td>';
			
			if ($paper->activeSubmission) {
				$abstract = self::buildHTML($paper, 'abstract', 'abstract_submitted', 'abstract_due', $paper->activeSubmission->isAbstractReadyToSet());
				$papersubmit = self::buildHTML($paper, 'paper', 'paper_submitted', 'paper_due',$paper->activeSubmission->isPaperReadyToSet());
				$notification = self::buildHTML($paper, 'notification', 'notification_result', 'notification_date', $paper->activeSubmission->isNotificationReadyToSet());
				$camera = self::buildHTML($paper, 'camera', 'camera_ready_submitted', 'camera_ready_due', $paper->activeSubmission->isCameraReadyReadyToSet());
			}
			
			$action = '<td>'
					. Form::open(array('action' => array('PaperController@getDetails', 'id' => $paper->id), 'method' => 'GET', 'style' => 'display:inline'))
					. '<button type="submit" class="btn btn-xs btn-primary">Details</button>'
					. Form::close();
					
			if (!$paper->activeSubmission) {
				$action .= Form::open(array('action' => array('PaperController@anyRetarget', 'id' => $paper->id), 'style' => 'display:inline'))
						.  Form::hidden('paperRetargetBackTarget', URL::to('timeline'))
						.  '<button type="submit" class="btn btn-xs btn-primary">Set Target</button>'
						.  Form::close();
			}
			$action .= '</td>';
			
			$result[] = '<tr>'.
				$title.
				$abstract.
				$papersubmit.
				$notification.
				$camera.
				$action.
				'</tr>'
			;
		}
		
		return $result;
	}
	
	private function buildHTML($paper, $type, $type_submitted, $type_due, $isReadyToSet) {
		$result = '<td></td>';
		
		if($paper->activeSubmission->$type_submitted === null) {
			if($isReadyToSet) {
				$result = '<td align="center" class="info" id="cell_'. $paper->id. '_'. $type. '">'. $paper->activeSubmission->event->$type_due->format('M d, Y').
			 			'<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission('. $paper->id. ', \''. $type. '\', 1)"><span class="glyphicon glyphicon-ok"></span></button>'.
						'<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission('. $paper->id. ', \''. $type. '\', 0)"><span class="glyphicon glyphicon-remove"></span></button>'.
						'</td>';
			} elseif ($paper->activeSubmission->event->$type_due->lte(Carbon::now())) {
				$result = '<td align="center" class="info" id="cell_'. $paper->id. '_'. $type. '">'. $paper->activeSubmission->event->$type_due->format('M d, Y'). '</td>';
			} else {
				$result = '<td align="center" class="warning" id="cell_'. $paper->id. '_'. $type. '">'. $paper->activeSubmission->event->$type_due->format('M d, Y'). '</td>';
			}
		} else {
			if ($type_submitted) {
				$result = '<td align="center" class="success" id="cell_'. $paper->id. '_'. $type. '">'. $paper->activeSubmission->event->$type_due->format('M d, Y'). '</td>';
			} else {
				$result = '<td align="center" class="danger" id="cell_'. $paper->id. '_'. $type. '">'. $paper->activeSubmission->event->$type_due->format('M d, Y'). '</td>';
			}
		}
		
		return $result;			
	}
	
	private function getPapers($usersIds, $sortByColumn = 'abstract_due', $order = 'asc') {
	    return Paper::
			  join('author_paper', 'papers.id', '=', 'author_paper.paper_id') // needed for users
			->join('users', 'author_paper.author_id', '=', 'users.author_id') // needed for whereIn userIds
			->leftJoin('submissions', 'papers.id', '=', 'submissions.paper_id') // needed for events
			->where('submissions.active', '=', 1) // use the active submission
			->join('events', 'events.id', '=', 'submissions.event_id') // needed for sorting by deadlines
			->whereIn('users.id', $usersIds)
			->select('papers.*') // for distinct to work correctly
			->distinct() // include each paper only once (can occur multiple times with several user ids
			->orderBy($sortByColumn, $order) // sorting
			->get();
	}

	private function getSubmissions($usersIds, $pastLimit = 0, $futureLimit = 0, $sortByColumn = 'abstract_due', $order = 'asc') {
		$query = Submission::with('paper', 'event')
			//->currentUser()
			//->groups($groupsIds)
			->users($usersIds)
			->active()
			->join('events', 'events.id', '=', 'submissions.event_id')
			->join('papers', 'papers.id', '=', 'submissions.paper_id')
			->select('submissions.*')
			->orderBy($sortByColumn, $order);
		if ($pastLimit > 0) {
			$query = $query->where('end', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL '. $pastLimit .' MONTH)'));
		}
		if ($futureLimit > 0) {
			$query = $query->where('abstract_due', '<', DB::raw('DATE_ADD(CURDATE(), INTERVAL '. $futureLimit .' MONTH)'));
		}
		return $query->get();
	}
}
