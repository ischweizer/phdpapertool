<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailTestController
 *
 * @author jost
 */
class MailTestController extends BaseController {
    
    public function index() {
	if(Auth::user()->id != 1)
	    return;
	$adresses = array('jost.schultz@gmx.de', 'anton.rohr@gmx.de', 'kai@schwierczek.de' , 'bekirm.bayrak@googlemail.com' , 'vuducbinh2001@yahoo.com');//, 'schweizer@tk.informatik.tu-darmstadt.de');
	foreach($adresses as $adress) {
	    Mail::send('hello', array(), function($message) use ($adress)
	    {
		$message->to($adress, 'John Smith')->subject('Welcome!');
	    });	
	}
	
	return View::make('hello');
    }
}
