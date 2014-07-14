@extends('layouts/main')

@section('head')

	{{ HTML::script('javascripts/bootstrap-datepicker.min.js') }}
	{{ HTML::style('stylesheets/datepicker3.min.css')}}

	{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
	{{ HTML::style('stylesheets/bootstrapValidator.min.css') }}
	<script type="text/javascript">
		
		$(document).ready(function(){

			$.fn.datepicker.defaults.format = "M dd, yyyy";
			$.fn.datepicker.defaults.multidateSeparator = ";";

			$('#deadline').datepicker({
				startDate: '+1d'
			}).on('change', function(e) {
				var field = $(this).attr('name');
				$('#createReviewForm')
					.data('bootstrapValidator')
					.updateStatus(field, 'NOT_VALIDATED', null)
					.validateField(field);
			});

			$('#createReviewForm').bootstrapValidator({
				excluded: [':disabled'],
				message: 'This value is not valid',
				live: 'enabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
			});

			$('#addUser').click(function() {
				var chosenAuthor = $('#authorSelect option:selected').remove();
				$('#selectedAuthors').append(chosenAuthor);
			});

			$('#removeUser').click(function(){
				var chosenAuthor = $('#selectedAuthors option:selected').remove();
				$('#authorSelect').append(chosenAuthor);
			});

			$('#addFile').click(function() {
				var chosenFile = $('#fileSelect option:selected').remove();
				$('#selectedFiles').append(chosenFile);
			});

			$('#removeFile').click(function(){
				var chosenFile = $('#selectedFiles option:selected').remove();
				$('#fileSelect').append(chosenFile);
			});

			$('#createReviewForm').submit(function(){
				$('#selectedAuthors option').prop('selected', true);
				$('#selectedFiles option').prop('selected', true);
			});

		});


	</script>

@stop

@section('content')

		

	{{ Form::open(array('action' => 'ReviewController@postCreateReviewRequest', 'id' => 'createReviewForm')) }}

		<h1>Review Request for Paper: {{{ $paper->title }}}</h1>

		<div class="form-group single-date">
			{{ Form::label('deadline', 'Review Deadline') }}
			<div class="input-group date" id="deadline-datepicker">
				{{ Form::text('deadline', '', array('class' => 'form-control input-sm datepicker', 'required')) }}
				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('author', 'Author') }}
			<div class="input-group"> 
				{{ Form::select('authorSelect', $authorNames, null, array('class' => 'form-control', 'id' => 'authorSelect')) }}
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" id="addUser"><span class="glyphicon glyphicon-plus"></span></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('selectedUser', 'Selected User') }}
			<div class="row">
				<div class="col-xs-11">
					{{ Form::select('selectedAuthors[]', array(), null, array('size' => 5, 'class' => 'form-control', 'id' => 'selectedAuthors', 'multiple' => true)) }}
				</div>
				<div class="col-xs-1">
						<div class="btn-group-vertical" >
							{{ Form::button('<span class="glyphicon glyphicon-remove"></span>', array('id' => 'removeUser', 'class' => 'btn btn-sm btn-default')) }}
						</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('files', 'Files') }}
			<div class="input-group"> 
				{{ Form::select('fileSelect', $fileNames, null, array('class' => 'form-control', 'id' => 'fileSelect')) }}
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" id="addFile"><span class="glyphicon glyphicon-plus"></span></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('selectedFiles', 'Selected Files') }}
			<div class="row">
				<div class="col-xs-11">
					{{ Form::select('selectedFiles[]', array(), null, array('size' => 5, 'class' => 'form-control', 'id' => 'selectedFiles', 'multiple' => true)) }}
				</div>
				<div class="col-xs-1">
						<div class="btn-group-vertical" >
							{{ Form::button('<span class="glyphicon glyphicon-remove"></span>', array('id' => 'removeFile', 'class' => 'btn btn-sm btn-default')) }}
						</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('message', 'Message') }}
			{{ Form::textarea('message', '', array('class' => 'form-control')) }}
		</div>
		{{ Form::hidden('paperId', $paper->id) }}


		{{ Form::submit('Save', array('class' => 'btn btn-primary', 'id' => 'createReviewModalSave')) }}

	{{ Form::close() }}




@stop