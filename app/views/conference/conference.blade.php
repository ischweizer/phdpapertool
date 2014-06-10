@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				$('#conference-editions').dataTable({
				});
			});
		</script>
@stop

@section('content')
		<div class="page-header">
			<h1>{{{ $conference->name }}}</h1>
		</div>
		
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
					<td>{{{ $conference->acronym }}}</td>
					<td>{{{ $conference->ranking->name }}}</td>
					<td>{{{ $conference->field_of_research }}}</td>
				</tr>
			</tbody>
		</table>

		{{ Form::open(array('action' => 'ConferenceEditionController@anyEdit')) }}
			<h3>Conference Editions <button type="submit" class="btn btn-xs btn-primary">Create New</button></h3>
			{{ Form::hidden('conference_id', $conference->id) }}
		{{ Form::close() }}
		<table id="conference-editions" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Location</th>
					<th>Edition</th>
					<th>Date</th>
					<th>Action</th>
				</tr>
			</thead>
	 		<tbody>
			@foreach ($conference->editions as $edition)
				<tr>
					<td>{{{ $edition->location }}}</td>
					<td>{{{ $edition->edition }}}</td>
					<td>{{{ $edition->event->start->format('M d, Y') }}} - {{{ $edition->event->end->format('M d, Y') }}}</td>
					<td>{{ HTML::linkAction('ConferenceEditionController@getDetails', 'details', array('id' => $edition->id)) }} {{ HTML::linkAction('ConferenceEditionController@anyEdit', 'edit', array('id' => $edition->id)) }}</td>
				</tr>
			@endforeach
			</tbody>
	 	</table>
@stop
