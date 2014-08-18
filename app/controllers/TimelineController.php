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
			$groups = $user->group->lab->groups;
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

	public function getGraphData() {
		// grab supplied paper ids
		$paperIds = explode(',', Input::get('paperIds'));
		$papers = Paper::with('authors', 'authors.user', 'activeSubmission', 'activeSubmission.event')->notArchived()->whereIn('id', $paperIds)->get();
		// overwrite ordering of database
		$papers->sort(function ($a, $b) use ($paperIds) {
			return array_search($a->id, $paperIds) - array_search($b->id, $paperIds);
		});
		// collect users whose papers this user may see (excluding reviewer case
		$user = Auth::user();
		$allowedUsers = array($user->id);
		if ($user->isLabLeader()) {
			$allowedUsers = $user->group->lab->users->modelKeys();
		} elseif ($user->isGroupLeader()) {
			$allowedUsers = $user->group->users->modelKeys();
		}


		$format = 'm/d/Y'; // format for JS "new Date()" to understand

		// initialize  used data
		$data = array(
			'lanes' => array(),
			'items' => array(),
		);
		$laneId = 0;
		$count = 0;

		foreach ($papers as $paper) {
			$paperUsers = array();
			$paper->authors->each(function ($author) use (&$paperUsers) {
				if ($author->user) {
					$paperUsers[] = $author->user->id;
				}
			});
			if (count(array_intersect($allowedUsers, $paperUsers)) == 0) {
				continue; // paper access not allowed
			}
			if (!$paper->activeSubmission) {
				continue; // paper has no active submission
			}
			$event = $paper->activeSubmission->event;

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

			if ($paper->authors->contains($user->author)) {
				foreach ($paper->reviewRequests as $reviewRequest) {
					$data['items'][] = array(
						'id' => $count++,
						'desc' => 'Review Request',
						'class' => 'paper-review',
						'lane' => $laneId,
						'start' => $reviewRequest->deadline->format($format),
						'end' => $reviewRequest->deadline->addDay()->format($format),
					);
				}
			}

			$laneId++;
		}

		if (Input::has('reviews') && Input::get('reviews')) {
			// include review lane if some data fits in
			$receivedReviewRequests = $user->author->reviewRequests->load('paper');
			$reviewLane = false;
			foreach($receivedReviewRequests as $reviewRequest) {
				$reviewLink = null;
				if (is_null($reviewRequest->pivot->answer)) {
					$reviewLink = URL::action('ReviewController@getIndex');
				} else if ($reviewRequest->pivot->answer) {
					$review = Review::where('author_id', '=', $user->author_id)->where('review_request_id', '=', $reviewRequest->id)->first();
					if (!$review) {
						$reviewLink = URL::action('ReviewController@getCreate', array($reviewRequest->id));
					}
				}
				if ($reviewLink) {
					$reviewLane = true;
					$data['items'][] = array(
						'id' => $count++,
						'desc' => 'Received Review Request',
						'class' => 'review-request',
						'lane' => $laneId,
						'complex' => true,
						'link' => $reviewLink,
						'link-desc' => $reviewRequest->paper->title,
						'start' => $reviewRequest->deadline->format($format),
						'end' => $reviewRequest->deadline->addDay()->format($format),
					);
				}
			}

			if ($reviewLane) {
				$data['lanes'][] = array(
					'id' => $laneId,
					'label' => 'Reviews'
				);

				$laneId++;
			}
		}

		return Response::json($data);
	}

	public function getTableData() {
		$userIds = array(Auth::user()->id);
		if(Input::has('groupIds')) {
			$user = Auth::user();
			$allowedGroupIds = array();
			if ($user->isLabLeader()) {
				$allowedGroupIds = $user->group->lab->groups->modelKeys();
			} elseif ($user->isGroupLeader()) {
				$allowedGroupIds[] = $user->group->id;
			}
			$groupIds = explode(',', Input::get('groupIds'));
			$groupIds = array_filter($groupIds, function ($groupId) use ($allowedGroupIds) {
				return in_array($groupId, $allowedGroupIds);
			});
			if (count($groupIds) > 0) {
				$userIds = User::whereIn('group_id', $groupIds)->get()->modelKeys();
			}
		}

		return Response::json($this->buildTable($this->getPapers($userIds)));
	}

	private function buildTable($papers) {
		$result = array();
		foreach ($papers as $paper) {
			$id = '<td>' . $paper->id . '</td>';
			$title = '<td>'. $paper->title. '</td>';
			$abstract = '<td></td>';
			$papersubmit = '<td></td>';
			$notification = '<td></td>';
			$camera = '<td></td>';

			$isAuthor = false;
			foreach ($paper->authors as $author) {
				if ($author->id == Auth::user()->author->id) {
					$isAuthor = true;
				}
			}

			if ($paper->activeSubmission) {
				$abstract = self::buildHTML($paper, $isAuthor, 'abstract', 'abstract_submitted', 'abstract_due', $paper->activeSubmission->isAbstractReadyToSet());
				$papersubmit = self::buildHTML($paper, $isAuthor, 'paper', 'paper_submitted', 'paper_due',$paper->activeSubmission->isPaperReadyToSet());
				$notification = self::buildHTML($paper, $isAuthor, 'notification', 'notification_result', 'notification_date', $paper->activeSubmission->isNotificationReadyToSet());
				$camera = self::buildHTML($paper, $isAuthor, 'camera', 'camera_ready_submitted', 'camera_ready_due', $paper->activeSubmission->isCameraReadyReadyToSet());
			}

			$action = '<td>'
					. Form::open(array('action' => array('PaperController@getDetails', 'id' => $paper->id), 'method' => 'GET', 'style' => 'display:inline'))
					. '<button type="submit" class="btn btn-xs btn-primary">Details</button>'
					. Form::close();

			if (!$paper->activeSubmission && $isAuthor) {
				$action .= Form::open(array('action' => array('PaperController@anyRetarget', 'id' => $paper->id), 'style' => 'display:inline'))
						.  Form::hidden('paperRetargetBackTarget', URL::to('timeline'))
						.  '<button type="submit" class="btn btn-xs btn-primary">Set Target</button>'
						.  Form::close();
			}
			$action .= '</td>';

			$result[] = '<tr>'.
				$id.
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

	private function buildHTML($paper, $isAuthor, $type, $type_submitted, $type_due, $isReadyToSet) {
		$result = '<td></td>';

		if($paper->activeSubmission->$type_submitted === null) {
			if($isReadyToSet && $isAuthor) {
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

	private function getPapers($usersIds) {
		return Paper::with('authors', 'activeSubmission', 'activeSubmission.event')
			->notArchived()
			->join('author_paper', 'papers.id', '=', 'author_paper.paper_id') // needed for users
			->join('users', 'author_paper.author_id', '=', 'users.author_id') // needed for whereIn userIds
			->whereIn('users.id', $usersIds)
			->select('papers.*') // for distinct to work correctly
			->distinct() // include each paper only once (can occur multiple times with several user ids
			->get();
	}
}
