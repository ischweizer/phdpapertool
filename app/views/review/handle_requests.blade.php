@extends('layouts/main')

@section('head')
	{{-- expr --}}
@stop

@section('content')
	
	<div>
	<h2>Review Requests:</h2>
		<ul class="list-group">
			@foreach ($reviewRequests as $reviewRequest)
				<li class="list-group-item">
					{{ Form::label('from', 'From: ') }}
					{{{ $reviewRequest->user->formatName() }}} <br>
					{{ Form::label('deadline', 'Deadline') }}
					{{{ @date_format($reviewRequest->deadline, 'M d, Y') }}} <br>
					@if ($reviewRequest->message)
						{{ Form::label('message', 'Message') }}
						<pre>{{{ $reviewRequest->message }}}</pre>
					@endif
					{{ Form::label('files', 'Files') }}
					@foreach ($reviewRequest->files as $file)
						<a href="{{ URL::action('FileController@getFile', $file->id) }}" type="submit" class="btn btn-xs btn-default" role="button">{{$file->name}}</a>
					@endforeach
				</li>
			@endforeach
		</ul>
	</div>

@stop