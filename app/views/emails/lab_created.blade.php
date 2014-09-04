@extends('layouts/mail')

@section('mailContent')
	A lab was created by user {{{ $user->formatName() }}}. Please confirm or deny the lab creation <a href="{{URL::to('handle')}}">here</a>.
@stop