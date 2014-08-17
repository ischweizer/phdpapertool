@extends('layouts/main')

@section('content')

    @if (isset($error))
	<div class="alert alert-danger">{{ $error }} </div>
    @elseif(isset($status))
	<div class="alert alert-info">{{ $status }} </div>
    @endif
    <form role="form" action="{{ action('RemindersController@postRemind') }}" method="POST">
	<div class="form-group">
	    <label>Email *</label>
	    <input type="email" class="form-control" name="email" placeholder="Email" value="{{{ $email or ''}}}">
	</div>

	<div class="uiButton" style="text-align:center">
	    <input type="submit" class="btn btn-primary btn-lg" value="Send Reminder">
	</div>
    </form>

@stop
