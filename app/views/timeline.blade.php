@extends('layouts/main')

@section('head')
		{{ HTML::script('javascripts/jquery.dataTables.min.js')}}
		{{ HTML::script('javascripts/dataTables.bootstrap.js')}}
		{{ HTML::style('stylesheets/dataTables.bootstrap.css'); }}
		{{ HTML::script('javascripts/d3.v3.min.js')}}
		{{ HTML::style('stylesheets/timeline.css'); }}
		{{ HTML::script('javascripts/timeline.js'); }}
			
		{{ HTML::style('javascripts/chosen/chosen.css'); }}
		{{ HTML::script('javascripts/chosen/chosen.jquery.js'); }}
		<script>
			$(document).ready(function() {
				Timeline.load('{{URL::action('TimelineController@getData')}}');
				$('#example').dataTable();
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
					
				 	Timeline.load('{{URL::action('TimelineController@getData')}}?groupids=' + selectedGroups.join( "," ));
				 	$('#selectPapersBtn').dropdown('toggle');
				});
				
				$('#selectAllGroups').change(function() {
					$( "#selectGroups" ).prop('disabled', $(this).prop('checked')).trigger('chosen:updated');
				});
				
				$('.dropdown-menu').click(function(e) {
					e.stopPropagation();
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
   		<h1>Timeline</h1>
		</div>

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

<h3 class="cat-title">Interactive Paper Timeline</h3>
<div id="graph"></div>
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
					  <th>Action</th>
				  </tr>
			  </thead>
	 
			  <tbody>
				@foreach ($papers as $paper)
				<tr>
					<td>{{{ $paper->title }}}</td>
					@if ($paper->activeSubmission)
						@if($paper->activeSubmission->abstract_submitted === null)
							@if($paper->activeSubmission->isAbstractReadyToSet())
								<td align="center" class="info" id="cell_{{ $paper->id }}_abstract">{{{ $paper->activeSubmission->event->abstract_due->format('M d, Y') }}}
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'abstract', 1)"><span class="glyphicon glyphicon-ok"></span></button>
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'abstract', 0)"><span class="glyphicon glyphicon-remove"></span></button>
								</td>
							@elseif ($paper->activeSubmission->event->abstract_due->lte(Carbon::now()))
								<td align="center" class="info" id="cell_{{ $paper->id }}_abstract">{{{ $paper->activeSubmission->event->abstract_due->format('M d, Y') }}}</td>
							@else
								<td align="center" class="warning" id="cell_{{ $paper->id }}_abstract">{{{ $paper->activeSubmission->event->abstract_due->format('M d, Y') }}}</td>
							@endif
						@else
							@if ($paper->activeSubmission->abstract_submitted)
								<td align="center" class="success" id="cell_{{ $paper->id }}_abstract">{{{ $paper->activeSubmission->event->abstract_due->format('M d, Y') }}}</td>
							@else
								<td align="center" class="danger" id="cell_{{ $paper->id }}_abstract">{{{ $paper->activeSubmission->event->abstract_due->format('M d, Y') }}}</td>
							@endif
						@endif
						@if($paper->activeSubmission->paper_submitted === null)
							@if($paper->activeSubmission->isPaperReadyToSet())
								<td align="center" class="info" id="cell_{{ $paper->id }}_paper">{{{ $paper->activeSubmission->event->paper_due->format('M d, Y') }}}
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'paper', 1)"><span class="glyphicon glyphicon-ok"></span></button>
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'paper', 0)"><span class="glyphicon glyphicon-remove"></span></button>
								</td>
							@elseif ($paper->activeSubmission->event->paper_due->lte(Carbon::now()))
								<td align="center" class="info" id="cell_{{ $paper->id }}_paper">{{{ $paper->activeSubmission->event->paper_due->format('M d, Y') }}}</td>
							@else
								<td align="center" class="warning" id="cell_{{ $paper->id }}_paper">{{{ $paper->activeSubmission->event->paper_due->format('M d, Y') }}}</td>
							@endif
						@else
							@if ($paper->activeSubmission->paper_submitted)
								<td align="center" class="success" id="cell_{{ $paper->id }}_paper">{{{ $paper->activeSubmission->event->paper_due->format('M d, Y') }}}</td>
							@else
								<td align="center" class="danger" id="cell_{{ $paper->id }}_paper">{{{ $paper->activeSubmission->event->paper_due->format('M d, Y') }}}</td>
							@endif
						@endif
						@if($paper->activeSubmission->notification_result === null)
							@if($paper->activeSubmission->isNotificationReadyToSet())
								<td align="center" class="info" id="cell_{{ $paper->id }}_notification">{{{ $paper->activeSubmission->event->notification_date->format('M d, Y') }}}
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'notification', 1)"><span class="glyphicon glyphicon-ok"></span></button>
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'notification', 0)"><span class="glyphicon glyphicon-remove"></span></button>
								</td>
							@elseif ($paper->activeSubmission->event->notification_date->lte(Carbon::now()))
								<td align="center" class="info" id="cell_{{ $paper->id }}_notification">{{{ $paper->activeSubmission->event->notification_date->format('M d, Y') }}}</td>
							@else
								<td align="center" class="warning" id="cell_{{ $paper->id }}_notification">{{{ $paper->activeSubmission->event->notification_date->format('M d, Y') }}}</td>
							@endif
						@else
							@if ($paper->activeSubmission->notification_result)
								<td align="center" class="success" id="cell_{{ $paper->id }}_notification">{{{ $paper->activeSubmission->event->notification_date->format('M d, Y') }}}</td>
							@else
								<td align="center" class="danger" id="cell_{{ $paper->id }}_notification">{{{ $paper->activeSubmission->event->notification_date->format('M d, Y') }}}</td>
							@endif
						@endif
						@if($paper->activeSubmission->camera_ready_submitted === null)
							@if($paper->activeSubmission->isCameraReadyReadyToSet())
								<td align="center" class="info" id="cell_{{ $paper->id }}_camera">{{{ $paper->activeSubmission->event->camera_ready_due->format('M d, Y') }}}
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'camera', 1)"><span class="glyphicon glyphicon-ok"></span></button>
									<button type="button" class="btn btn-default btn-xs" onclick="updateSubmission({{ $paper->id }}, 'camera', 0)"><span class="glyphicon glyphicon-remove"></span></button>
								</td>
							@elseif ($paper->activeSubmission->event->camera_ready_due->lte(Carbon::now()))
								<td align="center" class="info" id="cell_{{ $paper->id }}_camera">{{{ $paper->activeSubmission->event->camera_ready_due->format('M d, Y') }}}</td>
							@else
								<td align="center" class="warning" id="cell_{{ $paper->id }}_camera">{{{ $paper->activeSubmission->event->camera_ready_due->format('M d, Y') }}}</td>
							@endif
						@else
							@if ($paper->activeSubmission->camera_ready_submitted)
								<td align="center" class="success" id="cell_{{ $paper->id }}_camera">{{{ $paper->activeSubmission->event->camera_ready_due->format('M d, Y') }}}</td>
							@else
								<td align="center" class="danger" id="cell_{{ $paper->id }}_camera">{{{ $paper->activeSubmission->event->camera_ready_due->format('M d, Y') }}}</td>
							@endif
						@endif
					@else
						<td></td><td></td><td></td><td></td>
					@endif
					<td>
						{{ Form::open(array('action' => array('PaperController@getDetails', 'id' => $paper->id), 'method' => 'GET', 'style' => 'display:inline')) }}
							<button type="submit" class="btn btn-xs btn-primary">Details</button>
						{{ Form::close() }}
						@if (!$paper->activeSubmission)
							{{ Form::open(array('action' => array('PaperController@anyRetarget', 'id' => $paper->id), 'style' => 'display:inline')) }}
							{{ Form::hidden('paperRetargetBackTarget', URL::to('timeline')) }}
							<button type="submit" class="btn btn-xs btn-primary">Set Target</button>
							{{ Form::close() }}
						@endif
					</td>
				</tr>
				@endforeach
				</tbody>
			</table>
@stop
