@extends('layouts/mail')
@section('mailContent')
    An Auhtor with your email-address was added on our plattform 
    {{link_to_action('LoginController@showForm', 'PHDPapertool', array(), array())}}
    .
@stop