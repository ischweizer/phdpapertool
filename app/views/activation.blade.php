@extends('layouts/main')

@section('content')
<div style="text-align: center;">
    @if($kind == "missingParameter")
	<div class="alert alert-danger">The parameter '{{{$parameter}}}' is missing.</div>
    @elseif($kind == "userNotFound")
	<div class="alert alert-danger">The user with email '{{{$email}}}' was not found. Please register again:
	{{link_to_action('RegistrationController@showForm', 'Go to registration', array(), array())}}.</div>
    @elseif($kind == "alreadyConfirmed")
	<div class="alert alert-danger">The account is already activated. Please log in:
	{{link_to_action("LoginController@showForm", 'Go to login', array(), array())}}.</div>
    @elseif($kind == "wrongCode")
	<div class="alert alert-danger">The activationcode does not match. Please contact the system's admin.</div>
    @elseif($kind == "successful")
	<div class="alert alert-success">The activation was successful. You may log in now:
	{{link_to_action("LoginController@showForm", 'Go to login', array(), array())}}.</div>
    @else
	<div class="alert alert-danger">Somethig went wrong. Please contact the system's admin.</div>
    @endif
</div>
@stop

