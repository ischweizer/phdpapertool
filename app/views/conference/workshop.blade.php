@extends('layouts/main')

@section('content')
		<div class="page-header">
			<h1>{{{ $workshop->name }}}</h1>
			{{ HTML::linkAction('WorkshopController@getEdit', 'edit', array('id' => $workshop->id)) }}
		</div>

   		<h3>Workshop Information</h3>
		<table class="table" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Start</th>
					<th>End</th>
					<th>Abstract Due</th>
					<th>Paper Due</th>
					<th>Notification Date</th>
					<th>Camera Ready Due</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{{ $workshop->event->start->format('M d, Y') }}}</td>
					<td>{{{ $workshop->event->end->format('M d, Y') }}}</td>
					<td>{{{ $workshop->event->abstract_due->format('M d, Y') }}}</td>
					<td>{{{ $workshop->event->paper_due->format('M d, Y') }}}</td>
					<td>{{{ $workshop->event->notification_date->format('M d, Y') }}}</td>
					<td>{{{ $workshop->event->camera_ready_due->format('M d, Y') }}}</td>
				</tr>
			</tbody>
		</table>

   		<h3>Co-located Conference Edition Information</h3>
		{{ HTML::linkAction('ConferenceEditionController@getDetails', 'details', array('id' => $workshop->conferenceEdition->id)) }}</td>
		<table class="table" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Location</th>
					<th>Edition</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{{ $workshop->conferenceEdition->conference->name }}}</td>
					<td>{{{ $workshop->conferenceEdition->location }}}</td>
					<td>{{{ $workshop->conferenceEdition->edition }}}</td>
					<td>{{{ $workshop->conferenceEdition->event->start->format('M d, Y') }}} - {{{ $workshop->conferenceEdition->event->end->format('M d, Y') }}}</td>
				</tr>
			</tbody>
		</table>
@stop
