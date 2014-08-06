@extends('layouts/main')

@section('head')
		{{ HTML::script('javascripts/jquery.dataTables.min.js') }}
		{{ HTML::script('javascripts/dataTables.bootstrap.js') }}

		{{ HTML::style('stylesheets/dataTables.bootstrap.css') }}

		<script>
			$(document).ready(function() {
				 $('#papers').dataTable();
			});
		</script>
@stop

@section('content')

	<div id='main'>

		<div class="page-header">
			<h1>
				@if($archived) Archive @else Papers @endif 
				{{ Form::open(array('action' => 'PaperController@anyEdit', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">Create New</button>
					{{ Form::hidden('paperBackTarget', URL::action('PaperController@getIndex')) }}
				{{ Form::close() }}
				@if($archived) 
				{{ Form::open(array('action' => array('PaperController@getIndex'), 'method' => 'GET', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">All Papers</button>
				{{ Form::close() }}
				@else 
				{{ Form::open(array('action' => array('PaperController@getArchived'), 'method' => 'GET', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">Archived Papers</button>
				{{ Form::close() }}
				@endif
				
			</h1>
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
						<td>@if ($paper->repository_url) {{ HTML::link($paper->repository_url) }} @endif</td>
						<td>
							{{ Form::open(array('action' => array('PaperController@getDetails', 'id' => $paper->id), 'method' => 'GET', 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">Details</button>
							{{ Form::close() }}
							{{ Form::open(array('action' => array('PaperController@anyEdit', 'id' => $paper->id), 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">Edit</button>
								{{ Form::hidden('paperBackTarget', URL::action('PaperController@getIndex')) }}
							{{ Form::close() }}
							{{ Form::open(array('action' => array('PaperController@postArchivePaper', 'id' => $paper->id), 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">@if($archived) Unarchive @else Archive @endif</button>
								{{ Form::hidden('archivepaper', ($archived) ? 0 : 1 ) }}
								{{ Form::hidden('paperBackTarget', ($archived) ? URL::action('PaperController@getArchived') : URL::action('PaperController@getIndex') ) }}
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
