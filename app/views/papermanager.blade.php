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
			<h1>Paper Mananger</h1>
		</div>
		@if ($mode == 'userlist')
			<h3 class="cat-title">Users</h3>
			<table id="papers" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Email</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($users as $user)
						@if ($user->group_confirmed)
							<tr>
								<td>{{{ $user->id }}}</td>
								<td>{{{ $user->email }}}</td>
								<td>{{{ $user->author->first_name }}}</td>
								<td>{{{ $user->author->last_name }}}</td>
								<td>
									{{ HTML::linkAction('PaperManagerController@getPapers', 'List papers', array('id' => $user->id), array('class' => 'btn btn-xs btn-primary')) }}
								</td>
							</tr>
						@endif
					@endforeach
				</tbody>
			</table>
		@elseif ($mode == 'paperlist')
			<a href="{{ URL::action('PaperManagerController@getIndex') }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;&nbsp;List papers</a>
			<h3 class="cat-title">Papers</h3>
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
								{{ Form::open(array('action' => array('PaperController@getDetails', 'id' => $paper->id), 'method' => 'GET', 'style' => 'display:inline')) }}
									<button type="submit" class="btn btn-xs btn-primary">Details</button>
								{{ Form::close() }}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		@elseif ($mode == 'unauthorized')
			<div class="alert alert-danger">You don't have permission to access this page.</div>
		@endif
	</div>
@stop
