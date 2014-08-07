@extends('layouts/mail')

@section('mailContent')

	The user {{{ $reviewRequest->user->formatName() }}} requested a review from you on the platform <a href="{{ URL::to('/') }}">PHD Paper Tool</a>. You don't need to be registered to make the review. 
	<br><br>
	Click <a href="{{ URL::action('ReviewController@anyAuth', array('author_id' => $author->id, 'review_request_id' => $reviewRequest->id, 'auth_token' => $auth_token)) }}">here</a> for unregistered reviewing. <br>
	Click <a href="{{ URL::action('RegistrationController@showForm', array('email' => $author->email)) }}">here</a> to register at PHD Paper Tool. (and get free access to cool paper managment tools) 
	<br><br>
	If you don't want to make a review, please decline the review request by clicking <a href="{{ URL::action('ReviewController@anyAuth', array('author_id' => $author->id, 'review_request_id' => $reviewRequest->id, 'auth_token' => $auth_token, 'decline' => 1)) }}"> here</a>.
	<br><br>
	@if ($reviewRequest->message)
		{{{ $reviewRequest->user->formatName() }}} also added a message for you:
		<pre>{{{ $reviewRequest->message }}}</pre>
	@endif
	<br><br>
@stop


