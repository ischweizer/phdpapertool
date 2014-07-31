@extends('layouts/main')

@section('content')
	@if ( $errors->count() > 0 )
	<div class="alert alert-danger fade in">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<p>The following errors have occurred:</p>
		<ul>
		@foreach( $errors->all() as $message )
			<li>{{{ $message }}}</li>
		@endforeach
		</ul>
	</div>
	@endif
	<h2>Review Details: </h2>
	{{ Form::label('author', 'Review From: ') }}
	{{{ $review->author->formatName() }}}<br>
	@if ($review->message)
		{{ Form::label('message', 'Message: ') }}
		<pre>{{{ $review->message }}}</pre>
	@endif
	{{ Form::label('files', 'Files: ') }}
	@foreach ($review->files as $file) 
		<a href="{{ URL::action('FileController@getFile', $file->id) }}" type="submit" class="btn btn-xs btn-default" role="button">{{$file->name}}</a>
	@endforeach


@stop