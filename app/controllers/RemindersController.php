<?php

class RemindersController extends Controller {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind()
	{
		return View::make('passwordremind');
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postRemind()
	{
		switch ($response = Password::remind(Input::only('email'), function($message) {
		  $message->subject('Password reset');  
		}))
		{
			case Password::INVALID_USER:
				return View::make('passwordremind', array(
					'error' => Lang::get($response),
					'email' => Input::get('email')
				));

			case Password::REMINDER_SENT:
				return View::make('passwordremind', array(
					'status' => Lang::get($response),
					'email' => Input::get('email')
				));
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		if (/*is_null($token)*/!Input::has('token')) App::abort(404);
		$token = Input::get('token');
		return View::make('passwordreset')->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		$credentials = Input::only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:
				//return Redirect::back()->with('error', Lang::get($response));
				return View::make('passwordreset', array(
					'error' => Lang::get($response),
					'email' => Input::get('email'),
					'token' => Input::get('token')
				));

			case Password::PASSWORD_RESET:
				return Redirect::to('/');
		}
	}

}
