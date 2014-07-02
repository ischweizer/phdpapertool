@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				
			});
		</script>
@stop

@section('content')
		<div class="page-header">
			<h1>
				Events<br>
				{{ Form::open(array('action' => array('ConferenceController@getIndex'), 'method' => 'GET', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">Browse Conferences</button>
				{{ Form::close() }}
				{{ Form::open(array('action' => array('ConferenceController@anyEdit'), 'method' => 'GET', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">Create Conference</button>
				{{ Form::close() }}
				{{ Form::open(array('action' => array('ConferenceEditionController@anyEdit'), 'method' => 'GET', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">Create Conference Edition</button>
				{{ Form::close() }}
				{{ Form::open(array('action' => array('WorkshopController@anyEdit'), 'method' => 'GET', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">Create Workshop</button>
				{{ Form::close() }}
				{{ Form::open(array('action' => array('PaperController@anyEdit'), 'method' => 'GET', 'style' => 'display:inline')) }}
					<button type="submit" class="btn btn-xs btn-primary">Create Paper</button>
				{{ Form::close() }}
			</h1>
		</div>

		{{ Form::label('ceditions', 'Conferences') }}
		<table id="ceditions-table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Acronym</th>
					<th>Edition</th>
					<th>Location</th>
					<th>Abstract Due</th>
					<th>Paper Due</th>
					<th>Notification Date</th>
					<th>Camera Ready Due</th>
					<th>Start</th>
					<th>End</th>
					<th>Action &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>{{-- preserve space for both buttons to fit beside each other --}}
				</tr>
			</thead>
			<tbody>
				@foreach ($conferenceeditions as $ceditionevent)
					<tr>
						<td>{{{ $ceditionevent->detail->conference->acronym }}}</td>
						<td>{{{ $ceditionevent->detail->edition }}}</td>
						<td>{{{ $ceditionevent->detail->location }}}</td>
						<td>{{{ $ceditionevent->abstract_due->format('M d, Y') }}}</td>
						<td>{{{ $ceditionevent->paper_due->format('M d, Y') }}}</td>
						<td>{{{ $ceditionevent->notification_date->format('M d, Y') }}}</td>
						<td>{{{ $ceditionevent->camera_ready_due->format('M d, Y') }}}</td>
						<td>{{{ $ceditionevent->start->format('M d, Y') }}}</td>
						<td>{{{ $ceditionevent->end->format('M d, Y') }}}</td>
						<td>
							{{ Form::open(array('action' => array('ConferenceEditionController@getDetails', 'id' => $ceditionevent->detail->id), 'method' => 'GET', 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">Details</button>
							{{ Form::close() }}
							
							{{ Form::open(array('action' => array('ConferenceEditionController@getNewPaper', 'id' => $ceditionevent->detail->id), 'method' => 'GET', 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">New Paper</button>
							{{ Form::close() }}
						</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Acronym</th>
					<th>Edition</th>
					<th>Location</th>
					<th>Abstract Due</th>
					<th>Paper Due</th>
					<th>Notification Date</th>
					<th>Camera Ready Due</th>
					<th>Start</th>
					<th>End</th>
					<th>Action</th>
				</tr>
			</tfoot>
	 	</table>
	 	
	 	
	 	{{ Form::label('workshops', 'Workshops') }}
	 	<table id="workshop-table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Abstract Due</th>
					<th>Paper Due</th>
					<th>Notification Date</th>
					<th>Camera Ready Due</th>
					<th>Start</th>
					<th>End</th>
					<th>Action &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>{{-- preserve space for both buttons to fit beside each other --}}
				</tr>
			</thead>
			<tbody>
				@foreach ($workshops as $workshopevent)
					<tr>
						<td>{{{ $workshopevent->detail->name }}}</td>
						<td>{{{ $workshopevent->abstract_due->format('M d, Y') }}}</td>
						<td>{{{ $workshopevent->paper_due->format('M d, Y') }}}</td>
						<td>{{{ $workshopevent->notification_date->format('M d, Y') }}}</td>
						<td>{{{ $workshopevent->camera_ready_due->format('M d, Y') }}}</td>
						<td>{{{ $workshopevent->start->format('M d, Y') }}}</td>
						<td>{{{ $workshopevent->end->format('M d, Y') }}}</td>
						<td>
							{{ Form::open(array('action' => array('WorkshopController@getDetails', 'id' => $workshopevent->detail->id), 'method' => 'GET', 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">Details</button>
							{{ Form::close() }}
							
							{{ Form::open(array('action' => array('WorkshopController@getNewPaper', 'id' => $workshopevent->detail->id), 'method' => 'GET', 'style' => 'display:inline')) }}
								<button type="submit" class="btn btn-xs btn-primary">New Paper</button>
							{{ Form::close() }}
						</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Name</th>
					<th>Abstract Due</th>
					<th>Paper Due</th>
					<th>Notification Date</th>
					<th>Camera Ready Due</th>
					<th>Start</th>
					<th>End</th>
					<th>Action</th>
				</tr>
			</tfoot>
	 	</table>
@stop
