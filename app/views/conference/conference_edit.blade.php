@extends('layouts/main')

@section('head')
		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
		{{ HTML::style('stylesheets/bootstrapValidator.min.css') }}

		<script>
			$(document).ready(function() {
				// enable form validation
				$('#conference-form').bootstrapValidator({
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					live: 'enabled'
				});
				$(".alert").alert();
			});
		</script>
@stop

@section('content')
		<div class="page-header">
			{{ Form::open(array('action' => 'ConferenceController@postBack')) }}
				<h1>@if($model) Edit @else Create @endif Conference <button type="submit" class="btn btn-xs btn-primary">Back</button></h1>
				{{ Form::hidden('conferenceBackTarget', Input::get('conferenceBackTarget')) }}
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

		{{ Form::model($model, array('action' => 'ConferenceController@postEditTarget', 'id' => 'conference-form', 'role' => 'form')) }}
			{{ Form::hidden('conferenceBackTarget', Input::get('conferenceBackTarget')) }}
			{{ Form::hidden('id') }}
			<div class="form-group">
				{{ Form::label('name', 'Name') }}
				{{ Form::text('name', $initialName, array('class' => 'form-control', 'placeholder' => 'Name', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
			<div class="form-group">
				{{ Form::label('acronym', 'Acronym') }}
				{{ Form::text('acronym', null, array('class' => 'form-control', 'placeholder' => 'Acronym')) }}
			</div>
			<div class="form-group">
				{{ Form::label('ranking_id', 'Ranking') }}
				{{ Form::select('ranking_id', $rankingOptions, $defaultRanking, array('class' => 'form-control', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
			<div class="form-group">
				{{ Form::label('field_of_research', 'Field of Research Code') }}
				{{ Form::text('field_of_research', null, array('class' => 'form-control', 'placeholder' => 'Field of Research Code')) }}
			</div>
			<button type="submit" class="btn btn-default btn-primary">Submit</button>
		{{ Form::close() }}
@stop
