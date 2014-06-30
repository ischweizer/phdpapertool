@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				
			});
		</script>
@stop

@section('content')
		<div class="page-header">
			<h1>Events</h1>
		</div>

		<table id="events-table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Acronym</th>
					<th>Edition</th>
					<th>Location</th>
					<th>Abstract Due</th>
					<th>Paper Due</th>
					<th>Notification Date</th>
					<th>Camera Ready Due</th>
					<th>Start</th>
					<th>End</th>
					<th>Action &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>{{-- preserve space for both buttons to fit beside each other --}}
				</tr>
			</thead>
			<tbody>
				@foreach ($eventlist as $event)
					<?php $cEdition = $event->detail->isConferenceEdition() ? $event->detail : $event->detail->conferenceEdition ?>
					<tr>
						<td>{{{ $cEdition->conference->acronym }}}</td>
						<td>{{{ $cEdition->edition }}}</td>
						<td>{{{ $cEdition->location }}}</td>
						<td>{{{ $event->abstract_due->format('M d, Y') }}}</td>
						<td>{{{ $event->paper_due->format('M d, Y') }}}</td>
						<td>{{{ $event->notification_date->format('M d, Y') }}}</td>
						<td>{{{ $event->camera_ready_due->format('M d, Y') }}}</td>
						<td>{{{ $event->start->format('M d, Y') }}}</td>
						<td>{{{ $event->end->format('M d, Y') }}}</td>
						<td>
							{{ Form::open(array('action' => ($event->detail->isConferenceEdition()) ? array('ConferenceEditionController@getDetails', 'id' => $cEdition->id) : array('WorkshopController@getDetails', 'id' => $event->detail->id), 'method' => 'GET', 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">Details</button>
							{{ Form::close() }}
						</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Acronym</th>
					<th>Edition</th>
					<th>Location</th>
					<th>Abstract Due</th>
					<th>Paper Due</th>
					<th>Notification Date</th>
					<th>Camera Ready Due</th>
					<th>Start</th>
					<th>End</th>
					<th>Action</th>
				</tr>
			</tfoot>
	 	</table>
@stop
