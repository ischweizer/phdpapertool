@extends('layouts/main')

@section('head')
	<script type="text/javascript">

	var lab_id,group_id;

		@if (Auth::user()->hasGroup())
			group_id = {{{ Auth::user()->group_id }}};
			lab_id = {{{ Auth::user()->group->lab_id }}};
		@endif

		var labName;
		var groupName;

		function createNewLab(){
					$.ajax({
						url: "create",
						data: {'groupName':groupName, "labName":labName},
						success: function(data){
							if(data != "true")
								alert(data);
							location.reload();						
						}
						//TODO no success
					});
		}

		function createNewGroup(){
				$.ajax({
					url: "create",
					data: {'groupName':groupName, "labId":lab_id},
					success: function(data){
						if(data != "true")
							alert(data);
						location.reload();						
					}
					//TODO no success
				});
		}

		$(document).ready(function() {

			$('#lab_select').change(function(){

				$('#submit').prop('disabled',true);
				var box = $('#group_select')
					.empty()
					.prop('disabled',true);
				lab_id = undefined;
				group_id = undefined;

				if ($(this).val() == 'new') {
					$('#labCreationModal').modal('show');
				} else if($(this).val() == ''){

				} else {
					lab_id = parseInt($(this).val());
					$.ajax({
						url: "enroll",
						data: {'lab':lab_id},
						success: function(data){
							box.append(
								new Option('','empty')
							);
							for (var i = 0; i < data.length; i++) {
								box.append(new Option(data[i].name,data[i].id));
							};
							box.append(new Option('new','new'));
							box.prop('disabled',false);
							
						}
						//TODO no success
					});
				}
			});

			$('#group_select').change(function(){

				$('#submit').prop('disabled',true);
				group_id = undefined;
					
				if ($(this).val() == 'new') {
					$('#groupCreationModal').modal('show');
				} else if($(this).val() == ''){

				} else {
					group_id = parseInt($(this).val());
					$('#submit').prop('disabled',false);
				}
			});

			$('#submit').click(function(){	
				//var tried_group_id = parseInt($('#group_select option:selected').val());
				$.ajax({
						url: "enrollInGroup",
						data: {'group':group_id},
						success: function(data){
							if(data){
								$('#infotext')
									.html('Your request was accepted and forwarded to the group leader')
									.addClass('alert-success')
									.show();
							} else {
								$('#infotext')
									.html('Error')
									.addClass('alert-danger')
									.show();
							}
						}
						//TODO no success
					});
			});
			
			$('#labCreationSave').click(function(){
				labName = $('#lab_name').val();
				if(labName) {
					$('#labCreationModal').modal('hide');
					$('#groupCreationModal').modal('show');
				}
			});

			$('#groupCreationSave').click(function(){
				groupName = $('#group_name').val();
					if(groupName){
						$('#groupCreationModal').modal('hide');
						if(labName) {
							createNewLab();
						} else {
							createNewGroup();
						}

					}
			});

		});
		
	</script>
@stop

@section('content')

<div class="container">
	<h1>Enroll in a research group</h1>

	@if (Auth::user()->isAdmin())
		<div class="alert alert-info">You are a group or lab leader</div>
	@elseif (Auth::user()->hasPendingCreation()) 
		<div class="alert alert-info">You have a pending group or lab creation</div>
	@else

		@if ($groupAccepted)
			<div class="alert alert-success">You have successfully enrolled in this Group</div>
		@elseif (Auth::user()->hasGroup())
			<div class="alert alert-info">Your group request is pending</div>
		@endif

		@if (Auth::user()->hasGroup())
			<div class="form-group">
				<label for="lab_select">Lab</label>
				<select class="form-control" id="lab_select">
					<option></option>
					@foreach ($labs as $lab)
						@if (Auth::user()->group->lab_id == $lab->id)
							<option value="{{{ $lab->id }}}" selected>
								{{{ $lab->name }}}
							</option>
						@else
							<option value="{{{ $lab->id }}}">
								{{{ $lab->name }}}
							</option>
						@endif
					@endforeach
					<option value="new">new</option>
				</select>
			</div>
			<div class="form-group">
				<label for="group_select">Group</label>
				<select class="form-control" id="group_select">
						@foreach ($labGroups as $group)
							@if (Auth::user()->group_id == $group->id)
								<option value="{{{ $group->id }}}" selected>
									{{{ $group->name }}}
								</option>
							@else
								<option value="{{{ $group->id }}}">
									{{{ $group->name }}}
								</option>
							@endif
						@endforeach
					<option val="new">new</option>
				</select>
			</div>
		@else
			<div class="form-group">
				<label for="lab_select">Lab</label>
				<select class="form-control" id="lab_select">
					<option></option>
					@foreach ($labs as $lab)
						<option value="{{{ $lab-> id}}}">
							{{{ $lab->name }}}
						</option>
					@endforeach
					<option value="new">new</option>
				</select>
			</div>
			<div class="form-group">
				<label for="group_select">Group</label>
				<select class="form-control" id="group_select" disabled>
				</select>
			</div>

		@endif

		<div class="modal fade" id="labCreationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Create a Lab</h4>
		      </div>
		      <div class="modal-body">
		       <div class="form-group">
			    <label for="lab_name" class="sr-only">Lab name</label>
			    <div class="col-sm-10">
			      <input type="text" class="form-control" id="lab_name" placeholder="Lab name">
			    </div>
			  </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" id="labCreationSave">Save changes</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="groupCreationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Create a Group</h4>
		      </div>
		      <div class="modal-body">
		        <div class="form-group">
			    <label for="group_name" class="sr-only">Group name</label>
			    <div class="col-sm-10">
			      <input type="text" class="form-control" id="group_name" placeholder="Group name">
			    </div>
			    </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" id="groupCreationSave">Save changes</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div id="infotext" class="alert" style="display:none"></div>
		<button id="submit" class="btn btn-primary btn-lg" disabled>request group access</button>

	@endif

</div> 

@stop
