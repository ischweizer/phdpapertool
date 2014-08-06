<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CronjobController
 *
 * @author jost
 */
class CronjobController extends BaseController {
    
    //relative reminder dates in seconds (positive and negative numbers are allowed)
    //delete attributes which are not supposed to reminded
    //[tableName][attributeName]
    var $tablesAttributes = array(
	"events" => array(
	    "start" => 0, 
	    "end" => 0, 
	    "abstract_due" => 0, 
	    "paper_due" => 0, 
	    "notification_date" => 0, 
	    "camera_ready_due" => 0),
	"review_requests" => array(
	    "deadline" => 0),
	/*"review_submissions" => array(
	    "finished_at" => 0)*/
    );
    
    //in seconds
    const intervalLength = 86400; //every day
    
    //tableName, workshop, conferenceEdition, conference, {papers}, attrName, entry
    var $usersMailContents = array();
    var $usersAuthors = array();
    
    public function index() {
	$timestamp = time();
	$earlierBound = $timestamp - $this::intervalLength/2;
	$laterBound = $timestamp + $this::intervalLength/2;
	$tablesData = array(
	    "events" => EventModel::/*where('abstract_due', '<=', DB::raw('CURDATE()'))->*/where('end', '>=', DB::raw('CURDATE()'))->get(),
	    "review_requests" => ReviewRequest::where('deadline', '>=', DB::raw('CURDATE()'))->get());
	foreach($tablesData as $tableName => $entries) {
	    foreach($entries as $entry) {
		$this::checkEntry($tableName, $entry, $earlierBound, $laterBound);
	    }
	}
	
	$this->informUsers();
	
	
	/*foreach($this->usersAuthors as $userId => $author) {
	    $authorName = $author->first_name." ".$author->last_name;
	    return View::make('emails/reminder', array(
		'name' => $authorName, 
		'contents' => $this->usersMailContents[$userId]));
	}*/
	
	//return View::make('hello');
    }
    
    private function checkEntry($tableName, $entry, $earlierBound, $laterBound) {
	foreach($this->tablesAttributes[$tableName] as $attrName => $relTime) {
	    if($entry->$attrName->timestamp + $relTime > $earlierBound &&
		$entry->$attrName->timestamp + $relTime <= $laterBound) {
		$this::addToUsers($tableName, $entry, $attrName);
	    }
	}	
    }
    
    private function addToUsers($tableName, $entry, $attrName) {
	$workshop = null;
	$conferenceEdition = null;
	$conference = null;
	if($tableName == "events") {
	    $users = $this::getUsersFromEvent($entry);
	    if($attrName == "start" || $attrName == "end") {
		if($entry->detail_type == 'Workshop') {
		    $workshop = $entry->getWorkshop();
		    $conferenceEdition = ConferenceEdition::find($workshop->conference_edition_id);
		    $conference = Conference::find($conferenceEdition->conference_id);
		} else if($entry->detail_type == "ConferenceEdition") {
		    $conferenceEdition = $entry->getConferenceEdition();
		    $conference = Conference::find($conferenceEdition->conference_id);
		}
	    }
	}
	else if($tableName == "review_requests")
	    $users = $this::getUsersFromReviewRequest($entry);
	else
	    return;
	
	$papers = $entry->getPapers();
	foreach($users as $user) {
	    if(!isset($this->usersAuthors[$user->id])) {
		$this->usersAuthors[$user->id] = Author::find($user->author_id);
		$this->usersMailContents[$user->id] = array();
	    }
	    $this->usersMailContents[$user->id][] = array(
		"tableName" => $tableName,
		"workshop" => $workshop,
		"conferenceEdition" => $conferenceEdition,
		"conference" => $conference,
		"papers" => $papers,
		"attrName" => $attrName,
		"entry" => $entry);
	}
    }
    
    private function getUsersFromEvent($entry) {
	$papers = Paper::withEvent($entry)->get();
	if(count($papers) == 0)
	    return array();
	$authors = Author::fromPapers($papers)->get();
	if(count($authors) == 0)
	    return array();
	$users = User::fromAuthors($authors)->get();
	return $users;
    }
    
    private function getUsersFromReviewRequest($entry) {
	return array(User::find($entry->user_id));
    }
    
    private function informUsers() {
	foreach($this->usersAuthors as $userId => $author) {
	    $authorName = $author->first_name." ".$author->last_name;
	    Mail::send('emails/reminder', array('name' => $authorName, 'contents' => $this->usersMailContents[$userId]), function($message) use ($author, $authorName) {
		$message->to($author->email, $authorName)
			->subject('Reminder')
			->from('noreply@da-sense.de', 'PHDPapertool');
	    });
	    /*echo $author->first_name." ".$author->last_name.": <br>";
	    foreach($this->usersMailContents[$userId] as $content) {
		echo $content["tableName"].": ".$content["entry"]->id." (".$content["attrName"]."):<br>";
		foreach($content["papers"] as $paper) {
		    echo " ".$paper->title."<br>";
		}
	    }
	    echo "<br>";*/
	}
    }
}
