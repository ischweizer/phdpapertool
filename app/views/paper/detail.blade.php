@extends('layouts/main')

@section('head')
		<style type="text/css">
			.form-control[readonly] {
				background-color:#fff;
			}
		</style>
@stop

@section('content')
		<div class="page-header">
			{{ Form::open(array('action' => array('PaperController@anyEdit', 'id' => $paper->id))) }}
				<h1>{{{ $paper->title }}} <button type="submit" class="btn btn-xs btn-primary">Edit</button></h1>
				{{ Form::hidden('paperBackTarget', URL::action('PaperController@getDetails', array('id' => $paper->id))) }}
			{{ Form::close() }}
		</div>

		<div class="form-group">
			{{ Form::label('repository_url', 'Repository') }}
			{{ Form::url('repository_url', $paper->repository_url, array('class' => 'form-control', 'readonly')) }}
		</div>

		<div class="form-group">
			{{ Form::label('authors', 'Authors') }}
			{{ Form::select('authors', $selectedauthors, null, array('size' => count($selectedauthors)+1, 'class' => 'form-control', 'readonly')) }}
		</div>

		<div class="form-group">
			{{ Form::label('abstract', 'Abstract') }}
			{{ Form::textarea('abstract', $paper->abstract, array('class' => 'form-control', 'readonly')) }}
		</div>

		<div class="form-group">
			{{ Form::open(array('action' => array('PaperController@anyRetarget', 'id' => $paper->id))) }}
			{{ Form::label('submissionKind', 'Current Submission Target') }} <button type="submit" class="btn btn-xs btn-primary">Change Target</button>
			{{ Form::close() }}
			<div class="form-control-static-bordered">
			@if ($submission['kind'] == 'ConferenceEdition')
				{{ Form::open(array('action' => array('ConferenceEditionController@getDetails', 'id' => $submission['activeDetailID']), 'method' => 'GET')) }}
				<b>Conference</b> {{{ $submission['conferenceName'] }}}<br>
				<b>Edition</b> {{{ $submission['editionName'] }}}<br>
				<button type="submit" class="btn btn-xs btn-primary">Details</button>
				{{ Form::close() }}
			@elseif ($submission['kind'] == 'Workshop')
				{{ Form::open(array('action' => array('WorkshopController@getDetails', 'id' => $submission['activeDetailID']), 'method' => 'GET')) }}
				<b>Workshop</b><br>
				{{{ $submission['workshopName'] }}}
				<button type="submit" class="btn btn-xs btn-primary">Details</button>
				{{ Form::close() }}
			@elseif ($submission['kind'] == 'none')
				<b>none</b>
			@endif
			</div>
		</div>

		{{-- TODO show submission history --}}
		
@stop
