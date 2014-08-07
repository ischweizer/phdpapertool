@extends('layouts/mail')
@section('mailContent')
	An author with your email-address was added on the plattform <a href="{{ URL::to('/') }}">PHD Paper Tool</a>. You can register to PHD Paper Tool <a href="{{ URL::action('RegistrationController@showForm', array('email' => $email)) }}">here</a>.
@stop