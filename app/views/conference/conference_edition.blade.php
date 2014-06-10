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
		<div class="page-header">
			{{ Form::open(array('action' => array('ConferenceEditionController@anyEdit', 'id' => $edition->id))) }}
				<h1>{{{ $edition->conference->name }}}<br>Edition {{{ $edition->edition }}} <button type="submit" class="btn btn-xs btn-primary">Edit</button></h1>
				{{ Form::hidden('conferenceEditionBackTarget', URL::action('ConferenceEditionController@getDetails', array('id' => $edition->id))) }}
			{{ Form::close() }}
		</div>

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

		{{ Form::open(array('action' => array('ConferenceController@getDetails', 'id' => $edition->conference->id), 'method' => 'GET')) }}				
			<h3>Conference Information <button type="submit" class="btn btn-xs btn-primary">Details</button></h3>
		{{ Form::close() }}

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

		{{ Form::open(array('action' => 'WorkshopController@anyEdit')) }}
			<h3>Co-located Workshops <button type="submit" class="btn btn-xs btn-primary">Create New</button></h3>
			{{ Form::hidden('conference_edition_id', $edition->id) }}
		{{ Form::close() }}
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
					<td>
						{{ Form::open(array('action' => array('WorkshopController@getDetails', 'id' => $workshop->id), 'method' => 'GET', 'style' => 'display:inline')) }}
							<button type="submit" class="btn btn-xs btn-primary">Details</button>
						{{ Form::close() }}
						{{ Form::open(array('action' => array('WorkshopController@anyEdit', 'id' => $workshop->id), 'style' => 'display:inline')) }}
							{{ Form::hidden('workshopBackTarget', URL::action('ConferenceEditionController@getDetails', array('id' => $edition->id))) }}
							<button type="submit" class="btn btn-xs btn-primary">Edit</button>
						{{ Form::close() }}
					</td>
				</tr>
			@endforeach
			</tbody>
	 	</table>
@stop
