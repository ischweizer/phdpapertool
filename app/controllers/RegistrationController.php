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
            $user->email_confirmed = 0;
	    $user->remember_token = rand(10000000, 99999999); //aktivierungscode
            $user->save();
	    
	    Mail::send('emails/auth/activation', 
		    array('name' => $user->first_name.' '.$user->last_name,
			'email' => $user->email,
			'code' => $user->remember_token), function($message) use($user)
	    {
		$message->to($user->email, $user->first_name.' '.$user->last_name)
			->subject('Your Registration at PHDPapertool');
	    });
            
            return View::make(
				'register', 
				array(
					'mode' => 'result', 
					'msg' => array(
						'success' => true,
						'content' => "User created! Activation mail was sent to your adress."
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
    
    
    public function activate() {
	if(!Input::has('email'))
	    return View::make('activation', 
		    array('kind' => 'missingParameter',
			'parameter' => 'email'));
	if(!Input::has('code'))
	    return View::make('activation', 
		    array('kind' => 'missingParameter',
			'parameter' => 'code'));
	
	$user = User::where('email', '=', Input::get('email'))->first();
	if($user == null)
	    return View::make('activation', 
		    array('kind' => 'userNotFound',
			'email' => Input::get('email')));
	if($user->email_confirmed == 1)
	    return View::make('activation', array('kind' => 'alreadyConfirmed'));
	if($user->remember_token == Input::get('code')) {
	    $user->remember_token = null;
	    $user->email_confirmed = 1;
	    $user->save();
	    return View::make('activation', array('kind' => 'successful'));
	}
	return View::make('activation', array('kind' => 'wrongCode'));
    }
}
