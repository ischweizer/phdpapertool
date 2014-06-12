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
			{{ Form::open(array('action' => array('ConferenceController@anyEdit', 'id' => $conference->id))) }}
				<h1>{{{ $conference->name }}} <button type="submit" class="btn btn-xs btn-primary">Edit</button></h1>
				{{ Form::hidden('conferenceBackTarget', URL::action('ConferenceController@getDetails', array('id' => $conference->id))) }}
			{{ Form::close() }}
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
			{{ Form::hidden('conferenceEditionBackTarget', URL::action('ConferenceController@getDetails', array('id' => $conference->id))) }}
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
					<td>
						{{ Form::open(array('action' => array('ConferenceEditionController@getDetails', 'id' => $edition->id), 'method' => 'GET', 'style' => 'display:inline')) }}
							<button type="submit" class="btn btn-xs btn-primary">Details</button>
						{{ Form::close() }}
						{{ Form::open(array('action' => array('ConferenceEditionController@anyEdit', 'id' => $edition->id), 'style' => 'display:inline')) }}
							{{ Form::hidden('conferenceEditionBackTarget', URL::action('ConferenceController@getDetails', array('id' => $conference->id))) }}
							<button type="submit" class="btn btn-xs btn-primary">Edit</button>
						{{ Form::close() }}
					</td>
				</tr>
			@endforeach
			</tbody>
	 	</table>
@stop
