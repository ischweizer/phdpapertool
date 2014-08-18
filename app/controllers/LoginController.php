<?php
/**
 * Description of LoginController
 *
 * @author jost
 */
class LoginController extends BaseController {
	public function showForm() {
		if(Auth::check()) {
			return Redirect::to('timeline'); //"You are logged in as " . Auth::user()->email;
		} else {
			return View::make('login', array('mode' => 'login'));
		}
	}

	public function authenticate() {
		if (Auth::check() && Auth::user()->email_confirmed == 1) {
			return View::make(
							'login',
							array(
									'mode' => 'logged',
									'msg' => array(
											'success' => false,
											'content' => "You are already logged in as " . Auth::user()->email . ". <a href='logout/'>Logout?</a>"
									),
							)
					);
		}

		if(!Input::has('email')) {
			return View::make(
				'login',
				array(
					'mode' => 'login',
					'msg' => array(
						'success' => false,
						'content' => "Please enter your email adress"
					),
				)
			);
		}

		if(!Input::has('password')) {
			return View::make(
				'login',
				array(
					'mode' => 'login',
					'msg' => array(
						'success' => false,
						'content' => "Please enter your password"
					),
					'input' => Input::get()
				)
			);
		}

	$entry = User::where('email', '=', Input::get('email'))->first();
	if($entry != null) {
		if($entry->email_confirmed == 0) {
			return View::make(
					'login',
					array(
						'mode' => 'login',
						'msg' => array(
							'success' => false,
							'content' => "The email-address was not confirmed yet."
						),
						'input' => Input::get()
					)
				);
		}

		$isRembered = Input::has('isRembered') && Input::get('isRembered');
		if (Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')), $isRembered))
			return Redirect::intended('timeline'); //"Login successful! " . ($isRembered ? '<br>you will be rembered' : '');
	}

		return View::make(
				'login',
				array(
					'mode' => 'login',
					'msg' => array(
						'success' => false,
						'content' => "Login failed"
					),
				)
			);
	}

	public function logout() {
		if(Auth::check()) {
			Auth::logout();

			return View::make(
				'login',
				array(
					'mode' => 'logout',
					'msg' => array(
						'success' => true,
						'content' => "Logout successful"
					),
				)
			);
		}
		return View::make('login', array('mode' => 'login'));
	}
}
