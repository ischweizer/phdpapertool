<?php
/**
 * Description of LoginController
 *
 * @author jost
 */
class LoginController extends BaseController {
    
    public static $view = 'bootstrap_loginsample';
    
    public function showForm() {
        if(Auth::check())
            return "You are logged in as " . Auth::user()->email;
        return View::make(LoginController::$view);
    }

    public function authenticate() {
        if (Auth::check()) 
            return "You are already logged in as " . Auth::user()->email . "<br><a href='logout/'>Logout?</a>";
        if(!Input::has('email'))
            return "Please enter your email adress";
        if(!Input::has('password'))
            return "Please enter your password";   
        $isRembered = Input::has('isRembered') && Input::get('isRembered');
        if (Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')), $isRembered)) 
            return "Login successful! " . ($isRembered ? '<br>you will be rembered' : '');
        return "Login failed!";
    }    
    
    public function logout() {
        if(Auth::check()) {
            Auth::logout();
            return "Logout successfull";
        }
        return View::make(LoginController::$view);
    }
}
