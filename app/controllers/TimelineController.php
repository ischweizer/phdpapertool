<?php
use Carbon\Carbon;

/**
 * Description of TimelineController
 *
 * @author Binh Vu
 */
class TimelineController extends BaseController {
    
    public function getIndex($id = null) {
		return View::make(
			'timeline', 
			array(	
				'papers' => self::getPapers(),
			)
		);
    }
    
    public function getData() {
    	$config = array(
    		'left' => 3,
    		'right' => 6,
    		'format' => 'm/d/Y',
    	);
    
    	$data = array(
    		'lanes' => array(),
    		'items' => array(),
    	);
    	
    	$count = 0; $laneId = 0;
    	foreach(self::getPapers() as $paper) {
    		$event = $paper->activeSubmission->event;  
    		
    		$now = Carbon::now();
    		$left_limit = $now->copy()->subMonths($config['left']);
    		$right_limit = $now->copy()->addMonths($config['right']);
    		
    		if ($event->end->lt($left_limit) || $event->abstract_due->gte($right_limit)) {
    			continue;
    		}
    	
    		$data['lanes'][] = array(
    			'id' => $laneId,
    			'label' => $paper->title
    		);
 			
 			if ($event->abstract_due->gte($left_limit)) {
				$data['items'][] = array(
					'id' => $count++,
					'desc' => 'Paper Submission',
					'class' => 'paper',
					'lane' => $laneId,
					'start' => $event->abstract_due->format($config['format']),
					'end' => $event->paper_due->format($config['format']),
				);
			}
    		
    		if ($event->paper_due->gte($right_limit)) {
 				continue;
 			}
 			
 			if ($event->paper_due->gte($left_limit)) {
				$data['items'][] = array(
					'id' => $count++,
					'desc' => 'Notification',
					'class' => 'noti',
					'lane' => $laneId,
					'start' => $event->paper_due->format($config['format']),
					'end' => $event->notification_date->format($config['format']),
				);
			}
    		
    		if ($event->notification_date->gte($right_limit)) {
 				continue;
 			}
    		
    		if ($event->notification_date->gte($left_limit)) {
				$data['items'][] = array(
					'id' => $count++,
					'desc' => 'Camera Ready Submission',
					'class' => 'cam',
					'lane' => $laneId,
					'start' => $event->notification_date->format($config['format']),
					'end' => $event->camera_ready_due->format($config['format']),
				);
			}
    		
    		if ($event->start->gte($right_limit)) {
 				continue;
 			}
    		
    		if ($event->start->gte($left_limit)) {
				$data['items'][] = array(
					'id' => $count++,
					'desc' => 'Conference',
					'class' => 'con',
					'lane' => $laneId,
					'start' => $event->start->format($config['format']),
					'end' => $event->end->format($config['format']),
				);
			}
    		
    		$laneId++;
    	}
    	
    	die(json_encode($data));
    }
    
    private function getPapers() {
    	$user = Auth::user();
    	$user->load('author', 'author.papers', 'author.papers.activeSubmission', 'author.papers.activeSubmission.event');
    	return $user->author->papers;
    }
}
