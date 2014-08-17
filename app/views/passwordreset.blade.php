@extends('layouts/main')

@section('content')

    @if (isset($error))
	<div class="alert alert-danger">{{ $error }} </div>
    @endif

    <form role="form" action="{{ action('RemindersController@postReset') }}" method="POST">
	
	<input type="hidden" name="token" value="{{ $token }}">
	
	<div class="form-group">
	    <label>Email *</label>
	    <input type="email" class="form-control" name="email" placeholder="Email" value="{{{ $email or ''}}}">
	</div>

	<div class="form-group">
	    <label>Password *</label>
	    <input type="password" class="form-control" name="password" placeholder="Password">
	</div>
	
	<div class="form-group">
	    <label>Password confirmation*</label>
	    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm assword">
	</div>

	<div class="uiButton" style="text-align:center">
	    <input type="submit" class="btn btn-primary btn-lg" value="Reset password">
	</div>
    </form>

@stop
