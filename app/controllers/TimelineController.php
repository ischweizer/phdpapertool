<?php
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
    	$data = array(
    		'lanes' => array(),
    		'items' => array(),
    	);
    	
    	$count = 0;
    	$laneId = 0;
    	foreach(self::getPapers() as $paper) {
    		$data['lanes'][] = array(
    			'id' => $laneId,
    			'label' => $paper->title
    		);
    		
    		
    		$abstract_due = $paper->activeSubmission->event->abstract_due;
    		$paper_due = $paper->activeSubmission->event->paper_due;
    		$noti_due = $paper->activeSubmission->event->notification_date;
    		$cam_due = $paper->activeSubmission->event->camera_ready_due;
    		$format = 'm/d/Y';
    		
    		$data['items'][] = array(
    			'id' => $count++,
    			'desc' => 'Abstract Submission',
    			'class' => 'past',
    			'lane' => $laneId,
    			'start' => $abstract_due->copy()->subWeek()->format($format),
    			'end' => $abstract_due->format($format),
    		);
    		
    		$data['items'][] = array(
    			'id' => $count++,
    			'desc' => 'Paper Submission',
    			'class' => 'past',
    			'lane' => $laneId,
    			'start' => $abstract_due->copy()->addDay()->format($format),
    			'end' => $paper_due->format($format),
    		);
    		
    		$data['items'][] = array(
    			'id' => $count++,
    			'desc' => 'Notification',
    			'class' => 'past',
    			'lane' => $laneId,
    			'start' => $paper_due->copy()->addDay()->format($format),
    			'end' => $noti_due->format($format),
    		);
    		
    		$data['items'][] = array(
    			'id' => $count++,
    			'desc' => 'Camera Ready Submission',
    			'class' => 'past',
    			'lane' => $laneId,
    			'start' => $noti_due->copy()->addDay()->format($format),
    			'end' => $cam_due->format($format),
    		);
    		
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
