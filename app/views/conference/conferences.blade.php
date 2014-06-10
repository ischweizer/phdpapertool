@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				$('#conferences').dataTable({
					"processing": true,
					"serverSide": true,
					"sAjaxSource": "{{ URL::action('ConferenceController@getData') }}",
					"columnDefs": [{
						"targets": -1,
						"searchable": false,
						"orderable": false,
						"render": function ( data, type, full, meta ) {
							var actions = '';
							actions += '{{ Form::open(array('action' => array('ConferenceController@getDetails', 'id' => 'data-id-ph'), 'method' => 'GET', 'style' => 'display:inline')) }}'.replace('data-id-ph', data);
							actions += '<button type="submit" class="btn btn-xs btn-primary">Details</button>';
							actions += '{{ Form::close() }} ';
							actions += '{{ Form::open(array('action' => array('ConferenceController@anyEdit', 'id' => 'data-id-ph'), 'style' => 'display:inline')) }}'.replace('data-id-ph', data);
							actions += '{{ Form::hidden('conferenceBackTarget', URL::action('ConferenceController@getIndex')) }}';
							actions += '<button type="submit" class="btn btn-xs btn-primary">Edit</button>';
							actions += '{{ Form::close() }}';
							return actions;
						}
					}]
				});
			});
		</script>
@stop

@section('content')
		<div class="page-header">
			{{ Form::open(array('action' => 'ConferenceController@anyEdit')) }}
				<h1>Conferences <button type="submit" class="btn btn-xs btn-primary">Create New</button></h1>
				{{ Form::hidden('conferenceBackTarget', URL::action('ConferenceController@getIndex')) }}
			{{ Form::close() }}
		</div>

		<table id="conferences" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Acronym</th>
					<th>CORE 2013 Ranking</th>
					<th>Field of Research</th>
					<th>Action &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>{{-- preserve space for both buttons to fit beside each other --}}
				</tr>
			</thead>
	 
			<tfoot>
				<tr>
					<th>Name</th>
					<th>Acronym</th>
					<th>CORE 2013 Ranking</th>
					<th>Field of Research</th>
					<th>Action</th>
				 </tr>
			</tfoot>
	 	</table>
@stop
