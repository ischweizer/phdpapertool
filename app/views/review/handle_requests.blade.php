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
					From: {{{ $reviewRequest->user->formatName() }}} <br>
					Deadline: {{{ @date_format($reviewRequest->deadline, 'M d, Y') }}}
					@foreach ($reviewRequest->files as $file)
						@if ($file->author_id == $reviewRequest->user->author->id)
							<a href="{{ URL::action('FileController@getFile', $file->id) }}" type="submit" class="btn btn-xs btn-default" role="button">{{$file->name}}</a>
						@endif
					@endforeach
				</li>		
			@endforeach	
		</ul>
	</div>

@stop