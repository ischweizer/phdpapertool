@extends('layouts/main')

@section('head')
	<script type="text/javascript">
		$(document).ready(function() {
			$('#university_select').change(function(){
				var university_id = $('#university_select option:selected').val();
				$.ajax({
					url: "enroll",
					data: {'university':university_id},
					success: function(data){
						console.log(data);
					}
				});
			});
		});
		
	</script>
@stop

@section('content')

<div class="container">
	<select class="form-control" id="university_select">
		<option value="-1"></option>
		@foreach ($universities as $university)
			<option value="{{{ $university->id }}}">
				{{{ $university->name }}}
			</option>
		@endforeach	
		<option value="other">other</option>
	</select>
</div> 

@stop
