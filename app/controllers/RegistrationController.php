<?php
/**
 * Description of RegistrationController
 *
 * @author jost
 */
class RegistrationController extends BaseController {
    
    public function showForm() {
        return View::make('register', array('mode' => 'register'));
    }
    
    public function register() {
        if(!Input::has('email'))
            return "Please enter your email adress";
        if(!Input::has('lastName'))
            return "Please enter your first name";
        if(!Input::has('firstName'))
            return "Please enter your last name";
        if(!Input::has('password'))
            return "Please enter a password"; 
        if(!Input::has('repeatPassword') || Input::get('password') != Input::get('repeatPassword'))
            return "Please repeat the password correctly"; 
        if(!User::where('email', '=', Input::get('email'))->count()) {
            $author = Author::where('email', '=', Input::get('email'))->first();
            if($author == null) {
                $author = new Author;
                $author->email = Input::get('email');
            }
            $author->last_name = Input::get('lastName');
            $author->first_name = Input::get('firstName');
            $author->save();
            $user = new User;
            $user->password = Hash::make(Input::get('password'));
            $user->email = Input::get('email');
            $user->author_id = $author->id;
            $user->group_confirmed = 0;
            $user->email_confirmed = 1; //TODO nur zum testen, später false
            
            $user->save();
            
            return View::make(
				'register', 
				array(
					'mode' => 'result', 
					'msg' => array(
						'success' => true,
						'content' => "User created!"
					),
				)
			);
        }
        return View::make(
			'register', 
			array(
				'mode' => 'result', 
				'msg' => array(
					'success' => false,
					'content' => "User with email adress \"" . Input::get('email') . "\" already exists!"
				),
			)
		);
    }
}
