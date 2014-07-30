@extends('layouts/main')

@section('head')
	{{-- expr --}}
@stop

@section('content')

	<h1>Create a Review</h1>

	{{ Form::open(array('action' => 'ReviewController@postCreate')) }}
	{{ Form::hidden('reviewRequestId', $reviewRequest->id) }}

	<div class="form-group">
		{{ Form::label('files', 'Upload Files') }}

		{{--

		TODO:
			implement upload, so that somehow a array of File Id's can be provided for 
			the Controller who handles this Page. ReviewController@postCreate needs a 
			Input with the Name "files" which is an array (ex. array(5, 6, 17)) of 
			corresponding file ids 

			- Anton 

		--}}

	</div>

	<div class="form-group">
		{{ Form::label('message', 'Message') }}
		{{ Form::textarea('message', '', array('class' => 'form-control')) }}
	</div>

	{{ Form::submit('send', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop