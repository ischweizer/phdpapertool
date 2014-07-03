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
				Timeline.load('{{URL::action('TimelineController@getData')}}', '', 'title', 'asc');
				
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
					
				 	Timeline.reloadGroups(selectedGroups.join( "," ));
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
					  <th name='title'>Paper</th>
					  <th name='abstract_due'>Abstract Submission Deadline</th>
					  <th name='paper_due'>Paper Submission Deadline</th>
					  <th name='notification_due'>Notification Date</th>
					  <th name='camera_ready_due'>Camera Ready Submission Deadline</th>
					  <th>Action</th>
				  </tr>
			  </thead>
	 
			  <tbody></tbody>
			</table>
@stop
