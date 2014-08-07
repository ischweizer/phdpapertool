@extends('layouts/main')

@section('head')
	{{-- expr --}}
@stop

@section('content')

	@if ($answer === 0)
		<h1>You succesfully declined this review request</h1>
	@else

		{{ Form::open(array('files' => true)) }}

			{{ Form::hidden('author_id', $author->id) }}
			{{ Form::hidden('review_request_id', $reviewRequest->id) }}
			{{ Form::hidden('auth_token', $auth_token) }}

			<h1>Answer a review request</h1>
			
			<p>User: {{{ $reviewRequest->user->formatName() }}} sent you a review request.</p>
			@if ($reviewRequest->message)
				{{ Form::label('message', 'Message:') }}
				<pre>{{{ $reviewRequest->message }}}</pre>
			@endif

			{{ Form::label('files', 'Files: ') }}
			@foreach ($reviewRequest->files as $file)
				<a href="{{ URL::action('ReviewController@anyAuth', array('author_id' => $author->id, 'review_request_id' => $reviewRequest->id, 'auth_token' => $auth_token, 'file_id' => $file->id)) }}" class="btn btn-xs btn-default" role="button">{{$file->name}}</a>
			@endforeach

			@if (is_null($answer))

				<br>
				<br>
				
				{{ Form::submit('Yes I do this review', array('name' => 'accept', 'class' => 'btn btn-success')) }}
				{{ Form::submit('No I decline this review', array('name' => 'decline', 'class' => 'btn btn-danger')) }}
				

			@elseif($answer)

				@if (!$review)
					<h1>Create the Review</h1>

					<div class="form-group">
						{{ Form::label('file', 'Upload Files') }}
						<input class="input-file" name="files[]" id="files" type="file" multiple>
					</div>

					<div class="form-group">
						{{ Form::label('message', 'Message') }}
						{{ Form::textarea('message', '', array('class' => 'form-control')) }}
					</div>

					{{ Form::submit('send', array('name' => 'create_review','class' => 'btn btn-primary')) }}
				@else
					<div class="alert alert-success">Thanks, you succesfully created a review</div>
				@endif

			@endif

		{{ Form::close() }}

	@endif





@stop