@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<style>
			.chart {
				shape-rendering: crispEdges;
			}

			.mini text {
				font: 9px sans-serif;	
			}

			.main text {
				font: 12px sans-serif;	
			}

			.month text {
				text-anchor: start;
			}

			.todayLine {
				stroke: blue;
				stroke-width: 1.5;
			}

			.axis line, .axis path {
				stroke: black;
			}

			.miniItem {
				stroke-width: 6;	
			}

			.future {
				stroke: gray;
				fill: #ddd;
			}

			.past {
				stroke: green;
				fill: lightgreen;
			}

			.brush .extent {
				stroke: gray;
				fill: blue;
				fill-opacity: .165;
			}
		</style>
		<script>
			jQuery(document).ready(function() {
				$(document).ready(function() {
					 $('#example').dataTable();
				});
			});
			
			var dataURL='{{URL::action('TimelineController@getData')}}';
		</script>
@stop

@section('content')
		<div class="page-header">
   		<h1>Timeline</h1>
		</div>

<h3 class="cat-title">Interactive Paper Timeline</h3>
<div id="graph">
	{{ HTML::script('javascripts/timeline.js'); }}
</div>
<p>&nbsp;</p>
<h3 class="cat-title">Papers</h3>
			<table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			  <thead>
				  <tr>
					  <th>Paper</th>
					  <th>Abstract Submission Deadline</th>
					  <th>Paper Submission Deadline</th>
					  <th>Notification Date</th>
					  <th>Camera Ready Submission Deadline</th>
				  </tr>
			  </thead>
	 
			  <tbody>
				@foreach ($papers as $paper)
				<tr>
					<td>{{{ $paper->title }}}</td>
					@if ($paper->activeSubmission)
					<td align='center'>{{{ $paper->activeSubmission->event->abstract_due->format('d.m.Y') }}}</td>
					<td align='center'>{{{ $paper->activeSubmission->event->paper_due->format('d.m.Y') }}}</td>
					<td align='center'>{{{ $paper->activeSubmission->event->notification_date->format('d.m.Y') }}}</td>
					<td align='center'>{{{ $paper->activeSubmission->event->camera_ready_due->format('d.m.Y') }}}</td>
					@else
					<td></td><td></td><td></td><td></td>
					@endif
				</tr>
				@endforeach
				</tbody>
			</table>
@stop
