<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReminderSettingsController
 *
 * @author jost
 */
class ReminderSettingsController extends BaseController {
    
    public function getForm() {

	return View::make("reminderSettings", array("settings" => $this->getSettings()));
    }
    
    
    public function saveSettings() {
	$userId = Auth::user()->id;
	$settings = $this::getSettings();
	foreach($settings as $tableName => $isChecked) {
	    if(Input::has($tableName) != $isChecked) {
		if(Input::has($tableName)) {
		    $reminderEntry = new EmailReminder;
		    $reminderEntry->user_id = $userId;
		    $reminderEntry->table = $tableName;
		    $reminderEntry->save();
		} else {
		    EmailReminder::where('user_id', $userId)
			    ->where('table', $tableName)->first()->delete();
		}
	    }
	}
	return View::make("reminderSettings", array(
	    "settings" => $this::getSettings(),
	    "saved" => true
	));
    }
    
    private function getSettings() {
	$settings = array();
	foreach(CronjobController::$tablesAttributes as $tableName => $attributes) {
	    $settings[$tableName] = false;
	}
	
	$reminderEntries = EmailReminder::where('user_id', Auth::user()->id)->get();
	foreach($reminderEntries as $entry) {
	    $settings[$entry->table] = true;
	}

	return $settings;
    }
}
