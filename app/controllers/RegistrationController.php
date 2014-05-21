<?php
/**
 * Description of RegistrationController
 *
 * @author jost
 */
class RegistrationController extends BaseController {
    
    public function showForm() {
        return View::make('bootstrap_registrationsample');
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
            //FALLS EMAIL BEIM AUTHOR BEREITS EXISTIERT MUSS DIESER AUTHOR GENOMMEN WERDEN!!!
            $authors = Author::where('email', '=', Input::get('email'));
            if($authors->count() > 0)
                $author = $authors->first();
            else {
                $author = new Author;
                $author->last_name = Input::get('lastName');
                $author->first_name = Input::get('firstName');
                $author->email = Input::get('email');
                $author->save();
            }
            $user = new User;
            $user->password = Hash::make(Input::get('password'));
            $user->email = Input::get('email');
            $user->author_id = $author->id;
            $user->active = 1;
            
            $user->save();
            return "User created!";
        }
        return "User with email adress \"" . Input::get('email') . "\" already exists!";
    }
}
