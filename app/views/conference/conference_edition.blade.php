@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				$('#workshops').dataTable({
				});
			});
		</script>
@stop

@section('content')

		<div id='main'>

		<div class="page-header">
			<h1>{{{ $edition->conference->name }}}<br>Edition {{{ $edition->edition }}}</h1>
			{{ HTML::linkAction('ConferenceEditionController@getEdit', 'edit', array('id' => $edition->id)) }}
		</div>

   		<h3>Conference Edition Information</h3>
		<table class="table" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Location</th>
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
					<td>{{{ $edition->location }}}</td>
					<td>{{{ $edition->event->start->format('M d, Y') }}}</td>
					<td>{{{ $edition->event->end->format('M d, Y') }}}</td>
					<td>{{{ $edition->event->abstract_due->format('M d, Y') }}}</td>
					<td>{{{ $edition->event->paper_due->format('M d, Y') }}}</td>
					<td>{{{ $edition->event->notification_date->format('M d, Y') }}}</td>
					<td>{{{ $edition->event->camera_ready_due->format('M d, Y') }}}</td>
				</tr>
			</tbody>
		</table>

   		<h3>Conference Information</h3>
		{{ HTML::linkAction('ConferenceController@getDetails', 'details', array('id' => $edition->conference->id)) }}
		<table class="table" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Acronym</th>
					<th>CORE 2013 Ranking</th>
					<th>Field of Research</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{{ $edition->conference->acronym }}}</td>
					<td>{{{ $edition->conference->ranking->name }}}</td>
					<td>{{{ $edition->conference->field_of_research }}}</td>
				</tr>
			</tbody>
		</table>

   		<h3>Co-located Workshops</h3>
		<table id="workshops" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Date</th>
					<th>Action</th>
				</tr>
			</thead>
	 		<tbody>
			@foreach ($edition->workshops as $workshop)
				<tr>
					<td>{{{ $workshop->name }}}</td>
					<td>{{{ $workshop->event->start->format('M d, Y') }}} - {{{ $workshop->event->end->format('M d, Y') }}}</td>
					<td>{{ HTML::linkAction('WorkshopController@getDetails', 'details', array('id' => $workshop->id)) }} {{ HTML::linkAction('WorkshopController@getEdit', 'edit', array('id' => $workshop->id)) }}</td>
				</tr>
			@endforeach
			</tbody>
	 	</table>

		</div>
@stop
