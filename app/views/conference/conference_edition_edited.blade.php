@extends('layouts/main')

@section('head')
		<meta http-equiv="refresh" content="3; URL={{ URL::action('ConferenceController@getIndex', array('id' => $conference_id)) }}">
@stop

@section('content')
		<div id='main'>

		<div class="page-header">
   		<h1>Conference Edition @if($edited) Edited! @else Created! @endif</h1>
		</div>

		You should be forwarded to the conference page automatically. If not, click {{ HTML::linkAction('ConferenceController@getIndex', 'here', array('id' => $conference_id)) }}.

		</div>
@stop
