@extends('layouts/main')

@section('content')
		<div class="page-header">
			<h1>Submissions</h1>
		</div>

		<table id="submissions" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Paper</th>
					<th colspan="2">Abstract Submission</th>
					<th colspan="2">Paper Submission</th>
					<th colspan="2">Notification Date</th>
					<th colspan="2">Camera Ready Submission</th>
				</tr>
			</thead>
	 		<tbody>
			@foreach ($submissions as $submission)
				<tr>
					<td>{{{ $submission->paper->title }}}</td>
					<td>{{{ $submission->event->abstract_due->format('M d, Y') }}}</td>
					<td>
						<span style="float:right">
						@if($submission->abstract_submitted === null)
							<a href="#"><span class="glyphicon glyphicon-ok" title="success"></span></a>
							<a href="#"><span class="glyphicon glyphicon-remove" title="failure"></span></a>
						@else
							@if ($submission->abstract_submitted)
								<span class="glyphicon glyphicon-ok" title="success"></span>
							@else
								<span class="glyphicon glyphicon-remove" title="failure"></span>
							@endif
						@endif
						</span>
					</td>
					<td>{{{ $submission->event->paper_due->format('M d, Y') }}}</td>
					<td>
						<span style="float:right">
						@if($submission->paper_submitted === null)
							<a href="#"><span class="glyphicon glyphicon-ok" title="success"></span></a>
							<a href="#"><span class="glyphicon glyphicon-remove" title="failure"></span></a>
						@else
							@if ($submission->paper_submitted)
								<span class="glyphicon glyphicon-ok" title="success"></span>
							@else
								<span class="glyphicon glyphicon-remove" title="failure"></span>
							@endif
						@endif
						</span>
					</td>
					<td>{{{ $submission->event->notification_date->format('M d, Y') }}}</td>
					<td>
						<span style="float:right">
						@if($submission->notification_result === null)
							<a href="#"><span class="glyphicon glyphicon-ok" title="success"></span></a>
							<a href="#"><span class="glyphicon glyphicon-remove" title="failure"></span></a>
						@else
							@if ($submission->notification_result)
								<span class="glyphicon glyphicon-ok" title="success"></span>
							@else
								<span class="glyphicon glyphicon-remove" title="failure"></span>
							@endif
						@endif
						</span>
					</td>
					<td>{{{ $submission->event->camera_ready_due->format('M d, Y') }}}</td>
					<td>
						<span style="float:right">
						@if($submission->camera_ready_submitted === null)
							<a href="#"><span class="glyphicon glyphicon-ok" title="success"></span></a>
							<a href="#"><span class="glyphicon glyphicon-remove" title="failure"></span></a>
						@else
							@if ($submission->camera_ready_submitted)
								<span class="glyphicon glyphicon-ok" title="success"></span>
							@else
								<span class="glyphicon glyphicon-remove" title="failure"></span>
							@endif
						@endif
						</span>
					</td>
				</tr>
			@endforeach
			</tbody>
	 	</table>
@stop
