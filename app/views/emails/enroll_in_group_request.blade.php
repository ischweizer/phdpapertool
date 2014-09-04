@extends('layouts/mail')

@section('mailContent')
	The user {{{ $user->formatName() }}} wants to join your group {{ $group->name }}. Please confirm or deny this <a href="{{URL::to('handle')}}">here</a>.
@stop