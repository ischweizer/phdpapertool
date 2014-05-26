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
							return '<a href="{{URL::action('ConferenceController@getIndex', array('id' => 'data-id-ph'))}}">details</a>'.replace('data-id-ph', data);
						}
					}]
				});
			});
		</script>
@stop

@section('content')

		<div id='main'>

		<div class="page-header">
   		<h1>Conferences</h1>
		</div>

		<table id="conferences" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Acronym</th>
					<th>CORE 2013 Ranking</th>
					<th>Field of Research</th>
					<th>Action</th>
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

		</div>
@stop
