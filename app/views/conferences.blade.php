@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">
		<script>
			jQuery(document).ready(function() {
				$(document).ready(function() {
					$('#conferences').dataTable({
						"processing": true,
						"serverSide": true,
						"sAjaxSource": "{{ URL::to('conferences/data') }}"
					});
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
				</tr>
			</thead>
	 
			<tfoot>
				<tr>
					<th>Name</th>
					<th>Acronym</th>
					<th>CORE 2013 Ranking</th>
					<th>Field of Research</th>
				 </tr>
			</tfoot>
	 	</table>

		</div>
@stop
