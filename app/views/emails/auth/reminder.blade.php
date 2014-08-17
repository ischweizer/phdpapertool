@extends('layouts/mail')
@section('mailContent')
    To reset your password, complete this form: <br>
    <br>
    {{link_to_action('RemindersController@getReset', 'Password reset form', array('token' => $token), array())}}
    <br>
@stop