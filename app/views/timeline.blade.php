@extends('layouts/main')

@section('head')
	{{ HTML::script('javascripts/bootstrap-datepicker.min.js') }} {{-- for parsing the dates --}}
	{{ HTML::script('javascripts/jquery.dataTables.min.js')}}
	{{ HTML::script('javascripts/dataTables.bootstrap.js')}}
	{{ HTML::style('stylesheets/dataTables.bootstrap.css'); }}
	{{ HTML::script('javascripts/d3.v3.min.js')}}
	{{ HTML::style('stylesheets/timeline.css'); }}
	{{ HTML::script('javascripts/timeline.js'); }}
		
	{{ HTML::style('javascripts/chosen/chosen.css'); }}
	{{ HTML::script('javascripts/chosen/chosen.jquery.js'); }}
	<script>

		var timelineFrom = -3;
		var timelineTo = 3;
		var zoomSize = 1.5;
		var stepSize = 0.25;
		var TimelineData;

		$(document).ready(function() {
			Timeline.init('{{URL::action('TimelineController@getTableData')}}', '{{URL::action('TimelineController@getGraphData')}}');
			Timeline.loadTable('');
			
			$( "#selectGroups" ).chosen();
			$(".dropdown-menu .chosen-container").width('200px');
			$(".dropdown-menu .chosen-container .search-field input").width('100px');
			
			$('#applyGroupBtn').click(function() {
				var selectedGroups = $( "#selectGroups" ).val() || [];
				if ($('#selectAllGroups').prop('checked')) {
					selectedGroups = [];
					$("#selectGroups option").each(function() {
						selectedGroups.push($(this).val());
					});
				}
				
			 	Timeline.loadTable(selectedGroups.join( "," ));
			 	$('#selectPapersBtn').dropdown('toggle');
			});
			
			$('#selectAllGroups').change(function() {
				$( "#selectGroups" ).prop('disabled', $(this).prop('checked')).trigger('chosen:updated');
			});
			
			$('.dropdown-menu').click(function(e) {
				e.stopPropagation();
  			});

			$('#timelineMinus').click(function () {
				var dist = Math.abs(timelineTo-timelineFrom);

				var mult = zoomSize;

				var maxDist = 16;

				if(dist >= maxDist/mult){
					mult = maxDist/dist;
					$(this).attr("disabled", true);
				}

				timelineFrom *= mult;
				timelineTo *= mult;
				
				$('#graph').html('');
				Timeline.draw(TimelineData);
			});

			$('#timelinePlus').click(function () {

				

				timelineFrom /= zoomSize;
				timelineTo /= zoomSize;

				$('#timelineMinus').attr('disabled', false);

				$('#graph').html('');
				Timeline.draw(TimelineData);
			});

			$('#timelineBack').click(function () {
				var dist = Math.abs(timelineTo-timelineFrom);
				timelineFrom -= dist*stepSize;
				timelineTo -= dist*stepSize;
				
				$('#graph').html('');
				Timeline.draw(TimelineData);
			});

			$('#timelineForward').click(function () {
				var dist = Math.abs(timelineTo-timelineFrom);
				timelineFrom += dist*stepSize;
				timelineTo += dist*stepSize;
				
				$('#graph').html('');
				Timeline.draw(TimelineData);
			});

		});

		function updateSubmission(paperId, field, success) {
			$.ajax({
				url: "{{ URL::action('PaperController@getUpdateSubmission', array('PAPERID', 'FIELD', 'SUCCESS')) }}".replace('PAPERID', paperId).replace('FIELD', field).replace('SUCCESS', success),
				dataType: 'json',
				success: function(data) {
					if (!data.success) {
						alert(data.error);
					} else {
						var $tableCell = $('#cell_' + paperId + '_' + field);
						$tableCell.removeClass('info');
						if (success) {
							$tableCell.addClass('success');
							var next = '';
							if (field == 'abstract')
								next = 'paper';
							else if (field == 'paper')
								next = 'notification';
							else if (field == 'notification')
								next = 'camera';
							var $nextTableCell = $('#cell_' + paperId + '_' + next);
							if ($nextTableCell.hasClass('info')) {
								$nextTableCell.append(' <button type="button" class="btn btn-default btn-xs" onclick="updateSubmission(' + paperId + ', \'' + next + '\', 1)"><span class="glyphicon glyphicon-ok"></span></button>' +
													  ' <button type="button" class="btn btn-default btn-xs" onclick="updateSubmission(' + paperId + ', \'' + next + '\', 0)"><span class="glyphicon glyphicon-remove"></span></button>');
							}
						} else {
							$tableCell.addClass('danger');
						}
						$tableCell.find('button').remove();
					}
				}
			});
		}
	</script>
@stop

@section('content')
	<div class="page-header">
		@if (count($groups) > 0)
			<div id="paperDropdown" class="dropdown pull-right">
				<button id="selectPapersBtn" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Select Papers</button>
				<div class="dropdown-menu panel-body" role="menu" aria-labelledby="selectPapersBtn">
					<select class="form-control" id="selectGroups" data-placeholder="Select groups" multiple>
						@foreach ($groups as $group)
							<option value="{{{ $group->id }}}">{{{ $group->name }}}</option>
						@endforeach
					</select>
					<div class="checkbox">
						<label>
							<input id="selectAllGroups" type="checkbox"> Select all
						</label>
					</div>
					<hr>
					<button id="applyGroupBtn" class="btn btn-primary" style="width: 100%">Apply</button>
				</div>
			</div>
		@endif
		<h1>Timeline</h1>
	</div>
	<div id="graph"></div>
	<div style="float:right">
		<ul class="list-inline" style="display:inline-block">
			<li><span class="paper">&#9608; </span>Paper Submission Stage</li>
			<li><span class="noti">&#9608; </span>Notification Stage</li>
			<li><span class="cam">&#9608; </span>Camera Ready Submission Stage</li>
			<li><span class="con">&#9608; </span>Conference</li>
			<li><span class="workshop">&#9608; </span>Workshop</li>
		</ul>

		<div class="btn-group" style="display:inline-block">
			<button type="button" class="btn btn-default" id="timelineMinus"><span class="glyphicon glyphicon-minus"></span></button>
			<button type="button" class="btn btn-default" id="timelinePlus"><span class="glyphicon glyphicon-plus"></span></button>
			<button type="button" class="btn btn-default" id="timelineBack"><span class="glyphicon glyphicon-chevron-left"></span></button>
			<button type="button" class="btn btn-default" id="timelineForward"><span class="glyphicon glyphicon-chevron-right"></span></button>
		</div>

	</div>

	<p>&nbsp;</p>
	<h3 class="cat-title">Papers</h3>
	<table id="paper-table" class="table table-bordered table-hover" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th name='id'>Id</th>
				<th name='title'>Paper</th>
				<th name='abstract_due'>Abstract Submission Deadline</th>
				<th name='paper_due'>Paper Submission Deadline</th>
				<th name='notification_date'>Notification Date</th>
				<th name='camera_ready_due'>Camera Ready Submission Deadline</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
@stop
