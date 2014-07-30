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
    
    //relative reminder dates (positive and negative numbers are allowed)
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
    
    /*var $tablesModels = array(
	"events" => EventModel::getClass, 
	"review_requests" => ReviewRequest::getClass);*/
    
    //in seconds
    const intervalLength = 86400; //every day
    
    var $usersMailContents = array();
    var $usersAuthors = array();
    
    public function index() {
	$timestamp = time();
	$earlierBound = $timestamp - $this::intervalLength/2;
	$laterBound = $timestamp + $this::intervalLength/2;
	$tablesData = array(
	    "events" => EventModel::all(), 
	    "review_requests" => ReviewRequest::all());//$this::getTablesData();
	foreach($tablesData as $tableName => $entries) {
	    foreach($entries as $entry) {
		$this::checkEntry($tableName, $entry, $earlierBound, $laterBound);
	    }
	}
	
	$this->informUsers();
    }
    
    private function checkEntry($tableName, $entry, $earlierBound, $laterBound) {
	foreach($this->tablesAttributes[$tableName] as $attrName => $relTime) {
	    
	    if($entry->$attrName + $relTime > $earlierBound &&
		$entry->$attrName + $relTime <= $laterBound) {
		$this::addToUsers($tableName, $entry, $attrName);
	    }
		
	}	
    }
    
    private function addToUsers($tableName, $entry, $attrName) {
	//finde betroffene users und schicke ihnen eine benachrichtigung via email
	if($tableName == "events")
	    $users = $this::getUsersFromEvent($entry);
	else if($tableName == "review_requests")
	    $users = $this::getUsersFromReviewRequest($entry);
	else
	    return;
	
	foreach($users as $user) {
	    if(!isset($this->usersAuthors[$user->id])) {
		$this->usersAuthors[$user->id] = Author::find($user->author_id);
		$this->usersMailContents[$user->id] = array();
	    }
	    $this->usersMailContents[$user->id][] = array(
		"tableName" => $tableName,
		"attrName" => $attrName,
		"entry" => $entry);
	    //echo $user->email." ".$tableName." ".$entry->id."<br>";
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
	    Mail::send('emails/reminder', array('name' => $authorName, 'contents' => $this->usersMailContents[$userId]), function($message) use ($author) {
		$message->to($author->email, $authorName)
			->subject('Reminder')
			->from('noreply@da-sense.de', 'PHDPapertool');
	    });
	    /*echo $author->first_name." ".$author->last_name.": <br>";
	    foreach($this->usersMailContents[$userId] as $content) {
		echo $content["tableName"].": ".$content["entry"]->id." (".$content["attrName"].")<br>";
	    }
	    echo "<br>";*/
	}
    }
}
