@extends('layouts/main')

@section('head')
	{{-- expr --}}
@stop

@section('content')
	
	<div>
	<h1>Review Requests</h1>
		{{--<ul class="list-group">
			@foreach ($unansweredReviewRequests as $reviewRequest)
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
		</ul>--}}
		<table class="table table-bordered table-hover">
			<tr>
				<th colspan="5">
					<h4>Unanswered requests:</h4>
				</th>
			</tr>
			@if (count($unansweredReviewRequests) > 0)
				<tr>
					<th>From</th>
					<th>Deadline</th>
					<th>Message</th>
					<th>Files</th>
					<th>Action</th>
				</tr>
				@foreach ($unansweredReviewRequests as $reviewRequest)
					<tr>
						<td>
							{{{ $reviewRequest->user->formatName() }}}
						</td>
						<td>
							{{{ @date_format($reviewRequest->deadline, 'M d, Y') }}} <br>
						</td>
						<td>
						@if ($reviewRequest->message)
							{{ Form::label('message', 'Message') }}
							<pre>{{{ $reviewRequest->message }}}</pre>
						@endif
						</td>
						<td>
							@foreach ($reviewRequest->files as $file)
								<a href="{{ URL::action('FileController@getFile', $file->id) }}" type="submit" class="btn btn-xs btn-default" role="button">{{$file->name}}</a><br>
							@endforeach
						</td>
						<td>
							<a href="{{ URL::action('ReviewController@getAccept', $reviewRequest->id) }}"><span class="glyphicon glyphicon-ok" title="accept request"></span></a> 
							<a href="{{ URL::action('ReviewController@getDecline', $reviewRequest->id) }}"><span class="glyphicon glyphicon-remove" title="decline request"></span></a>
						</td>
					</tr>
				@endforeach
			@else
				<tr>
					<td colspan="5"> 
						No new review requests.
					</td>
				</tr>
			@endif
			<tr>
				<th colspan="5">
					<h4>Reviews to do:</h4>
				</th>
			</tr>
			@if(count($acceptedReviewRequests) > 0)
				<tr>
					<th>From</th>
					<th>Deadline</th>
					<th>Message</th>
					<th>Files</th>
					<th>Action</th>
				</tr>
				@foreach ($acceptedReviewRequests as $reviewRequest)
					<tr>
						<td>
							{{{ $reviewRequest->user->formatName() }}}
						</td>
						<td>
							{{{ @date_format($reviewRequest->deadline, 'M d, Y') }}} <br>
						</td>
						<td>
						@if ($reviewRequest->message)
							{{ Form::label('message', 'Message') }}
							<pre>{{{ $reviewRequest->message }}}</pre>
						@endif
						</td>
						<td>
							@foreach ($reviewRequest->files as $file)
								<a href="{{ URL::action('FileController@getFile', $file->id) }}" type="submit" class="btn btn-xs btn-default" role="button">{{$file->name}}</a><br>
							@endforeach
						</td>
						<td>
							<a href="{{ URL::action('ReviewController@getCreate', $reviewRequest->id) }}" class="btn btn-xs btn-primary">create review</a>
						</td>
					</tr>
				@endforeach
			@else
				<tr>
					<td colspan="5"> 
						No reviews to do.
					</td>
				</tr>
			@endif
			{{--<tr>
				<th colspan="5">
					<h4>Finished reviews:</h4>
				</th>
			</tr>
			@if (count($finishedReviewRequests) > 0)
				@foreach ($finishedReviewRequests as $reviewRequest)
					<tr>
						<td>
							
						</td>
					</tr>
				@endforeach 
			@else
				<tr>
					<td colspan="5"> 
						No finished reviews.
					</td>
				</tr>
			@endif--}}
		</table>

	</div>

@stop