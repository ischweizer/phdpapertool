<?php
/**
 * Description of ProfileController
 *
 * @author Binh Vu
 */
class ProfileController extends BaseController {
    
    public function getIndex($id = null) {
    	$user = User::find(Auth::id());
    	$author = Author::find($user->author_id);
    	$group = false;

    	if (!empty($author->group_id)) {
    		$group = Group::find($author->group_id);
    	}
    	
        return View::make(
				'profile', 
				array(	
					'user' => $user,				
					'author' => $author,
					'group' => $group,
				)
			);
    }
    
    public function save() {
    	$user = Auth::user();
    	$author = $user->author;
    	$group = $user->group;
    	
        $input = self::readInput(array('password', 'first_name', 'last_name'));
        $form = array(
        	'author' => $input,
			'user' => $user,
			'group' => $group,
        );
        
        if (self::isEmpty($input, array('first_name', 'last_name'))) {
        	$form['msg'] = array(
				'success' => false,
				'content' => "Please fill in all required fields"
			);
        	return View::make('profile', $form);
        }
    	
    	$author->first_name = $input['first_name'];
    	$author->last_name = $input['last_name'];
    	
    	if (!$author->save()) {
    		$form['msg'] = array(
				'success' => false,
				'content' => "Your profile can not be updated"
			);		
    		return View::make('profile', $form);
    	}
    	
    	if (!empty($input['password'])) {
    		$user->password = Hash::make($input['password']);
    		if (!$user->save()) {
				$form['msg'] = array(
					'success' => false,
					'content' => "Your profile can not be updated"
				);
				return View::make('profile', $form);
    		}
    	}
    	
    	$form['msg'] = array(
			'success' => true,
			'content' => "Your profile has been updated successful"
		);
    	return View::make('profile', $form);
    }
    
    private function readInput($fields) {
    	$input = array();
    	foreach ($fields as $field) {
    		$input[$field] = Input::get($field, '');
    	}
    	return $input;
    }
    
    private function isEmpty($input, $fields) {
    	foreach ($fields as $field) {
    		if (empty($input[$field])) {
    			return true;
    		}
    	}
    	return false;
    }
}
