<?php
/**
 * Description of TimelineController
 *
 * @author Binh Vu
 */
class PaperManagerController extends BaseController {
    
    public function getIndex() {
    	$users = User::getUsers(self::myGroups(), Auth::user()->id);

		return View::make(
			'papermanager', 
			array(	
				'mode' => 'userlist',
				'users' => $users,
			)
		);
    }
    
    public function getPapers($id = null) {
    	$user = User::find($id);
    	
    	if (!$user || !self::isAuthorized($user)) {
			return View::make(
				'papermanager', 
				array(	
					'mode' => 'unauthorized',
				)
			);
    	}
    	
    	$papers = $user->author->papers;
		return View::make(
			'papermanager', 
			array(	
				'mode' => 'paperlist',
				'papers' => $papers,
			)
		);
    }
    
    private function myGroups() {
    	$user = Auth::user();
    	$groups = array();
    	if ($user->isLabLeader()) {
    		$lab = array($user->group->lab);
    		$groups = Group::getGroupsFromLabs($lab);
    	} elseif ($user->isGroupLeader()) {
    		$groups[] = $user->group;
    	}
    	return $groups;
    }
    
    private function isAuthorized($user) {
    	$authorized = false;
    	foreach (self::myGroups() as $group) {
    		if ($group->id == $user->group->id) {
    			$authorized = true;
    			break;
    		}
    	}
    	return $authorized;
    }
}
