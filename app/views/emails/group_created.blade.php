@extends('layouts/mail')

@section('mailContent')
	A group was created by user {{{ $user->formatName() }}}. Please confirm or deny the group creation <a href="{{URL::to('handle')}}">here</a>.
@stop