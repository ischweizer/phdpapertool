@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				 $('#papers').dataTable();
			});
		</script>
@stop

@section('content')

	<div id='main'>

		<div class="page-header">
			{{ Form::open(array('action' => 'PaperController@anyEdit')) }}
				<h1>Papers <button type="submit" class="btn btn-xs btn-primary">Create New</button></h1>
				{{ Form::hidden('paperBackTarget', URL::action('PaperController@getIndex')) }}
			{{ Form::close() }}
		</div>

		<h3 class="cat-title">Paper Table</h3>
		<table id="papers" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Title</th>
					<th>Abstract</th>
					<th>Repository</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($papers as $paper)
					<tr>
						<td>{{{ $paper->title }}}</td>
						<td>{{{ Str::limit($paper->abstract, 90) }}}</td>
						<td>{{{ $paper->repository_url }}}</td>
						<td>
							{{ Form::open(array('action' => array('PaperController@anyEdit', 'id' => $paper->id))) }}
								<button type="submit" class="btn btn-xs btn-primary">Edit</button>
								{{ Form::hidden('paperBackTarget', URL::action('PaperController@getIndex')) }}
							{{ Form::close() }}
						</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Title</th>
					<th>Abstract</th>
					<th>Repository</th>
					<th>Action</th>
				 </tr>
			</tfoot>
	 	</table>

	</div>
@stop
