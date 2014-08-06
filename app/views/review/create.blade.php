@extends('layouts/main')

@section('head')
	{{-- expr --}}
@stop

@section('content')

	<h1>Create a Review</h1>

	{{ Form::open(array('action' => 'ReviewController@postCreate', 'files' => true)) }}
	{{ Form::hidden('reviewRequestId', $reviewRequest->id) }}

	<div class="form-group">
		{{ Form::label('file', 'Upload Files') }}
		<input class="input-file" name="files[]" id="files" type="file" multiple>
	</div>

	<div class="form-group">
		{{ Form::label('message', 'Message') }}
		{{ Form::textarea('message', '', array('class' => 'form-control')) }}
	</div>

	{{ Form::submit('send', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop