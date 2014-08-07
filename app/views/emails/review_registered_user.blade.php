@extends('layouts/mail')

@section('mailContent')
	The user {{{ $reviewRequest->user->formatName() }}} requested a review from you.<br> Please accept or decline the request at the <a href="{{ URL::to('review') }}">My Review</a> Page.<br> The deadline for the review is {{{ @date_format($reviewRequest->deadline, 'M d, Y') }}}.<br>
	@if ($reviewRequest->message)
		{{{ $reviewRequest->user->formatName() }}} also added a message for you:
		<pre>{{{ $reviewRequest->message }}}</pre>
	@endif
	
@stop