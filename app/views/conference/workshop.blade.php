@extends('layouts/main')

@section('content')
		<div class="page-header">
			{{ Form::open(array('action' => array('WorkshopController@anyEdit', 'id' => $workshop->id))) }}
			<h1>{{{ $workshop->name }}} <button type="submit" class="btn btn-xs btn-primary">Edit</button></h1>
				{{ Form::hidden('workshopBackTarget', URL::action('WorkshopController@getDetails', array('id' => $workshop->id))) }}
			{{ Form::close() }}
		</div>

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

		{{ Form::open(array('action' => array('ConferenceEditionController@getDetails', 'id' => $workshop->conferenceEdition->id), 'method' => 'GET')) }}				
			<h3>Co-located Conference Edition Information <button type="submit" class="btn btn-xs btn-primary">Details</button></h3>
		{{ Form::close() }}
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
