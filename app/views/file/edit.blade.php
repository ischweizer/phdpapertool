@extends('layouts/main')

@section('head')
		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
		{{ HTML::style('stylesheets/bootstrapValidator.min.css') }}
		
		<style type="text/css">
			.form-control[readonly] {
				background-color:#fff;
			}
		</style>

		<script>
			var onformsubmit = function() {
				
			}
			
			$(document).ready(function() {
				
			});
		</script>
@stop

@section('content')
		<div class="page-header">
			{{ Form::open(array('action' => array('FileController@getEditFile', 'id' => $model->id), 'method' => 'GET')) }}
				<h1>@if($edit) Edit File @else File Details <button type="submit" class="btn btn-xs btn-primary">Edit</button></h1>@endif
			{{ Form::close() }}
		</div>

		@if ( $errors->count() > 0 )
		<div class="alert alert-danger fade in">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<p>The following errors have occurred:</p>
			<ul>
			@foreach( $errors->all() as $message )
				<li>{{{ $message }}}</li>
			@endforeach
			</ul>
		</div>
		@endif

		{{ Form::model($model, array('action' => array('FileController@postEditFile', $model->id), 'id' => 'paper-form', 'onsubmit' => 'onformsubmit()')) }}
			<div class="form-group">
				{{ Form::label('name', 'File Name') }}
				{{ Form::text('name', null, array('placeholder' => 'File Name', 'class' => 'form-control', 'id' => 'filenamefield', 'required', 'data-bv-notempty-message' => 'May not be empty', ($edit)?'':'readonly')) }}
			</div>
			
			<div class="form-group">
				{{ Form::label('comment', 'Comments') }}
				{{ Form::textarea('comment', null, array('placeholder' => 'Comments', 'class' => 'form-control', ($edit)?'':'readonly')) }}
			</div>

			@if($edit)
			{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary')) }}
			@endif
		{{ Form::close() }}
		
@stop
