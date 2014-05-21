@extends('layouts/main')

@section('head')
	<script type="text/javascript">
		$(document).ready(function() {
			$('#university_select').change(function(){
				var university_id = $(this).val();
				if(university_id == 'empty' || university_id == 'new'){
					$('#department_select').empty().prop('disabled',true);
					$('#lab_select').empty().prop('disabled',true);
					$('#group_select').empty().prop('disabled',true);
					$('#submit').prop('disabled',true);
					if (university_id == 'new') {
						console.log('TODO');
					};
				} else {
					$.ajax({
						url: "enroll",
						data: {'university':university_id},
						success: function(data){
							var box = $('#department_select');
							box.empty();
							box.append(
								new Option('','empty')
							);
							for (var i = 0; i < data.length; i++) {
								box.append(
									new Option(data[i].name,data[i].id)
								);
							};
							box.append(
									new Option('new','new')
								);
							box.prop('disabled',false);
						}
					});
				}
			});

			$('#department_select').change(function(){
				var department_id = $(this).val();
				if(department_id == 'empty' || department_id == 'new'){
					$('#lab_select').empty().prop('disabled',true);
					$('#group_select').empty().prop('disabled',true);
					$('#submit').prop('disabled',true);
					if (department_id == 'new') {
						console.log('TODO');
					};
				} else {
					$.ajax({
						url: "enroll",
						data: {'department':department_id},
						success: function(data){
							var box = $('#lab_select');
							box.empty();
							box.append(
								new Option('','empty')
							);
							for (var i = 0; i < data.length; i++) {
								box.append(
									new Option(data[i].name,data[i].id)
								);
							};
							box.append(
									new Option('new','new')
								);
							box.prop('disabled',false);
						}
					});
				}
			});

			$('#lab_select').change(function(){
				var lab_id = $(this).val();
				if(lab_id == 'empty' || lab_id == 'new'){
					$('#group_select').empty().prop('disabled',true);
					$('#submit').prop('disabled',true);
					if (lab_id == 'new') {
						console.log('TODO');
					};
				} else {
					$.ajax({
						url: "enroll",
						data: {'lab':lab_id},
						success: function(data){
							var box = $('#group_select');
							box.empty();
							box.append(
								new Option('','empty')
							);
							for (var i = 0; i < data.length; i++) {
								box.append(
									new Option(data[i].name,data[i].id)
								);
							};
							box.append(
									new Option('new','new')
								);
							box.prop('disabled',false);
						}
					});
				}
			});

			$('#group_select').change(function(){
				var group_id = $(this).val();
				if(group_id == 'empty' || group_id == 'new'){
					$('#submit').prop('disabled',true);
					if (group_id == 'new') {
						console.log('TODO');
					} 
				} else {
					$('#submit').prop('disabled',false);
				}
			});

			$('#submit').click(function(){	
				var group_id = $('#group_select option:selected').val();
				alert();
				$.ajax({
						url: "enrollInGroup",
						data: {'group':group_id},
						success: function(data){
							alert(data);
							if(data){
								$('#infotext').html('Accepted');
							} else {
								$('#infotext').html('Error');
							}
						}
					});
			});
		});
		
	</script>
@stop

@section('content')

<div class="container">
	<h1>Enroll in a research group</h1>


		<div class="form-group">
			<label for="university_select">University</label>
			<select class="form-control" id="university_select">
				<option value="empty"></option>
				@foreach ($universities as $university)
					<option value="{{{ $university->id }}}">
						{{{ $university->name }}}
					</option>
				@endforeach	
				<option value="new">new</option>
			</select>
		</div>

		<div class="form-group">
			<label for="department_select">Department</label>
			<select class="form-control" id="department_select" disabled>
				
			</select>
		</div>
		<div class="form-group">
			<label for="lab_select">Lab</label>
			<select class="form-control" id="lab_select" disabled>
				
			</select>
		</div>
		<div class="form-group">
			<label for="group_select">Group</label>
			<select class="form-control" id="group_select" disabled>
				
			</select>
		</div>
		<button id="submit" class="btn btn-primary btn-lg" disabled>save</button>
		<span id="infotext"></span>

</div> 

@stop
