@extends('layouts/main')

@section('head')
		<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>
		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
		{{ HTML::style('stylesheets/bootstrapValidator.min.css') }}

		<script>
			$(document).ready(function() {				
				// enable form validation
				$('#paper-form').bootstrapValidator({
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					live: 'enabled'
				});

				// submission toggle
				$('[name=submissionKind]').change(function() {
					$('.submissionToggle').hide();
					$('#' + $(this).val()).show();
				});

				// initialize submission toggle
				$('.submissionToggle').hide();
				$('#{{ $newSubmission['kind'] }}').show();

				// install conference typeahead
				var conferences = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: {
						url: "{{ URL::action('ConferenceController@getAutocomplete', array('QUERY')) }}",
						replace: function (url, query) {
							// double encode query as it gets decoded -> splitted, which would destroy queries containing '/'
							return url.replace('QUERY', encodeURIComponent(encodeURIComponent(query)));
						}
					}
				});
				conferences.initialize();
				$('#conference_name').typeahead({
					highlight: true
				}, {
					name: 'conferences',
					displayKey: 'name',
					source: conferences.ttAdapter(),
					templates: {
						suggestion: function (obj) {
							if (obj.acronym) {
								return '<i>' + obj.acronym + '</i> - ' + obj.name;
							} else {
								return obj.name;
							}
						}
					}
				}).on('typeahead:selected typeahead:autocompleted', function(event, data) {
					$('#paper-form')
						.data('bootstrapValidator')
						.updateStatus('conference_name', 'NOT_VALIDATED', null)
						.validateField('conference_name');
					conferenceNameChange();
				});
				$('#conference_name').on('input', conferenceNameChange);
				function conferenceNameChange() {
					$.ajax({
						url: "{{ URL::action('ConferenceController@anyEditions') }}",
						data: {'name': $('#conference_name').val()},
						dataType: 'json',
						success: function(data) {
							var select = $('#conference_edition_id');
							var oldVal = select.val();
							var foundOldVal = false;
							select.empty();
							select.append(new Option('', ''));
							for (var i = 0; i < data.length; i++) {
								select.append(new Option(data[i].edition, data[i].id));
								if (data[i].id == oldVal) {
									foundOldVal = true;
								}
							}
							if (foundOldVal) {
								select.val(oldVal);
							}
						}
					});
				}
				conferenceNameChange();

				// install workshop typeahead
				var workshops = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: {
						url: "{{ URL::action('WorkshopController@getAutocomplete', array('QUERY')) }}",
						replace: function (url, query) {
							// double encode query as it gets decoded -> splitted, which would destroy queries containing '/'
							return url.replace('QUERY', encodeURIComponent(encodeURIComponent(query)));
						}
					}
				});
				workshops.initialize();
				$('#workshop_name').typeahead({
					highlight: true
				}, {
					name: 'workshops',
					displayKey: 'name',
					source: workshops.ttAdapter()
				}).on('typeahead:selected typeahead:autocompleted', function(event, data) {
					$('#paper-form')
						.data('bootstrapValidator')
						.updateStatus('workshop_name', 'NOT_VALIDATED', null)
						.validateField('workshop_name');
					workshopNameChange();
				});
				$('#workshop_name').on('input', workshopNameChange);
				function workshopNameChange() {
					$.ajax({
						url: "{{ URL::action('WorkshopController@anyId') }}",
						data: {'name': $('#workshop_name').val()},
						dataType: 'text',
						success: function(data) {
							$('#workshop_id').val(data);
						}
					});
				}
				workshopNameChange();

				// create new conference edition button
				$('#conference_edition_create').click(function() {
					// add return information and current name
					$('<input type="hidden">').attr({
						name: 'conference-edition-create-return-url',
						value: '{{ Request::url() }}'
					}).appendTo('#paper-form');
					$('<input type="hidden">').attr({
						name: 'conference-edition-create-name',
						value: $('#conference_name').val()
					}).appendTo('#paper-form');
					// forget current conference name/editionid
					$('#conference_name').remove();
					$('#conference_edition_id').remove();
					// submit form to alternative target
					$('#paper-form').attr('action', '{{URL::action('ConferenceEditionController@anyEdit')}}');
					$('#paper-form').bootstrapValidator('defaultSubmit');
				});

				// create new workshop button
				$('#workshop_create').click(function() {
					// add return information and current name
					$('<input type="hidden">').attr({
						name: 'workshop-create-return-url',
						value: '{{ Request::url() }}'
					}).appendTo('#paper-form');
					$('<input type="hidden">').attr({
						name: 'workshop-create-name',
						value: $('#workshop_name').val()
					}).appendTo('#paper-form');
					// forget current workshop name/id
					$('#workshop_name').remove();
					$('#workshop_id').remove();
					// submit form to alternative target
					$('#paper-form').attr('action', '{{URL::action('WorkshopController@anyEdit')}}');
					$('#paper-form').bootstrapValidator('defaultSubmit');
				});

				$(".alert").alert();
			});
		</script>
@stop

@section('content')
		<div class="page-header">
			{{ Form::open(array('url' => Input::get('paperRetargetBackTarget') ?: Input::old('paperRetargetBackTarget'), 'method' => 'GET')) }}
				<h1>Change Submission Target <button type="submit" class="btn btn-xs btn-primary">Back</button></h1>
			{{ Form::close() }}
		</div>

		<div class="form-group">
			{{ Form::label('title', 'Paper Title') }}
			{{ Form::text('title', $paper->title, array('class' => 'form-control', 'disabled')) }}
		</div>

		<div class="form-group">
			{{ Form::label('currentSubmissionKind', 'Current Submission Target') }}
			<div class="form-control-static-bordered" style="background-color:#eee">
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

		@if ($submission['kind'] != 'none')
		<div class="alert alert-warning">
			<strong>Warning!</strong> The current submission will be closed, successful or not.
		</div>
		@endif
		<br>
		{{ Form::model($paper, array('action' => 'PaperController@postRetargetTarget', 'id' => 'paper-form')) }}
			{{ Form::hidden('id') }}
			{{ Form::hidden('paperRetargetBackTarget', Input::get('paperRetargetBackTarget')) }}
			<div class="form-group">
				{{ Form::label('submissionKind', 'New Submission Target') }}
				<div class="form-control">
					<label class="radio-inline">
						{{ Form::radio('submissionKind', 'ConferenceEdition', $newSubmission['kind'] == 'ConferenceEdition', array()) }} Conference
					</label>
					<label class="radio-inline">
						{{ Form::radio('submissionKind', 'Workshop', $newSubmission['kind'] == 'Workshop', array()) }} Workshop
					</label>
					<label class="radio-inline">
						{{ Form::radio('submissionKind', 'none', $newSubmission['kind'] == 'none', array()) }} none
					</label>
				</div>
			</div>
			<div id="ConferenceEdition" class="well submissionToggle">
				<div class="container-fluid"><div class="row"><div class="form-group col-md-8" style="padding-left:0;padding-right:5px">
					{{ Form::label('conference_name', 'Conference') }}
					{{ Form::text('conference_name', $newSubmission['conferenceName'], array('class' => 'form-control', 'placeholder' => 'Conference', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-remote' => 'true', 'data-bv-remote-message' => 'Must be an existing conference', 'data-bv-remote-url' => URL::action('ConferenceController@anyCheck'), 'data-bv-remote-name' => 'name')) }}
				</div><div class="form-group col-md-3" style="padding-left:0;padding-right:5px">
					{{ Form::label('conference_edition_id', 'Edition') }}
					{{ Form::select('conference_edition_id', $newSubmission['editionOption'], $newSubmission['activeDetailID'], array('class' => 'form-control', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
				</div><div class="form-group col-md-1" style="padding:1px 0 0 0">
					<label>&nbsp;</label>
					<input id="conference_edition_create" type="button" class="btn btn-sm btn-primary" value="Create New">
				</div></div></div>
			</div>
			<div id="Workshop" class="well submissionToggle">
				<div class="container-fluid"><div class="row"><div class="form-group col-md-11" style="padding-left:0;padding-right:5px">
					{{ Form::hidden('workshop_id', $newSubmission['activeDetailID'], array('id' => 'workshop_id')) }}
					{{ Form::label('workshop_name', 'Workshop') }}
					{{ Form::text('workshop_name', $newSubmission['workshopName'], array('class' => 'form-control', 'placeholder' => 'Workshop', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-remote' => 'true', 'data-bv-remote-message' => 'Must be an existing workshop', 'data-bv-remote-url' => URL::action('WorkshopController@anyCheck'), 'data-bv-remote-name' => 'name')) }}
				</div><div class="form-group col-md-1" style="padding:1px 0 0 0">
					<label>&nbsp;</label>
					<input id="workshop_create" type="button" class="btn btn-sm btn-primary" value="Create New">
				</div></div></div>
			</div>

			{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary')) }}
		{{ Form::close() }}
@stop
