@extends('layouts/main')

@section('head')
		<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>
		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}

		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css">
		{{ HTML::style('stylesheets/bootstrapValidator.min.css'); }}
		

		<script>
			var test = 0;
			$(document).ready(function() {
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
					$('#event-form')
						.data('bootstrapValidator')
						.updateStatus('{{ $conferenceName }}', 'NOT_VALIDATED', null)
						.validateField('{{ $conferenceName }}');
					conferenceNameChange();
				});
				$('#conference_name').on('input', conferenceNameChange);

				// install datepickers
				$.fn.datepicker.defaults.format = "M dd, yyyy";
				$.fn.datepicker.defaults.multidateSeparator = ";";
				$('#start-end-datepicker').datepicker({
				});
				$('#abstract-datepicker').datepicker({
				});
				$('#paper-datepicker').datepicker({
				});
				$('#notification-datepicker').datepicker({
				});
				$('#camera-ready-datepicker').datepicker({
				});

				// for validation update 
				var datepickersComplete = [$('#event\\[abstract_due\\]'), $('#event\\[paper_due\\]'), $('#event\\[notification_date\\]'), $('#event\\[camera_ready_due\\]'), $('#event\\[start\\]'), $('#event\\[end\\]')];

				// revalidate on date pick
				datepickersComplete.forEach(function (datepicker) {
					datepicker.datepicker().on('change', function(e) {
						var field = $(this).attr('name');
						$('#event-form')
							.data('bootstrapValidator')
							.updateStatus(field, 'NOT_VALIDATED', null)
							.validateField(field);
					});
				});

				// enable form validation
				$('#event-form').bootstrapValidator({
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					live: 'enabled'
				});

				function conferenceNameChange() {
				@if ($type == 'Conference Edition')
					$.ajax({
						url: "{{ URL::action('ConferenceController@anyId') }}",
						data: {'name': $('#conference_name').val()},
						dataType: 'text',
						success: function(data) {
							$('#conference_id').val(data);
						}
					});
				@elseif ($type == 'Workshop')
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
				@endif
				}

				// in case an initial value is set
				conferenceNameChange();
			});

			(function($) {
				$.fn.bootstrapValidator.validators.confDate = {
					html5Attributes: {
							message: 'message',
							before: 'before',
							after: 'after'
					},
					validate: function(validator, $field, options) {
						var value = $field.val();
						if (value == '') {
							return true;
						}
						var date = $field.datepicker('getDate');
						if (isNaN(date.getTime())) {
							return false;
						}
						if (!value.match(/[A-Z][a-z]+ \d{2}, \d{4}/)) {
							return false;
						}
						if (options.before) {
							var befores = options.before.split(' ');
							if (!$field.data('propagating')) {
								this.propagate(befores, validator);
							}
							for (var i = 0; i < befores.length; i++) {
								var other = this.getSingleJQueryField(befores[i], validator);
								if (other) {
									var otherDate = other.datepicker('getDate');
									if (!isNaN(otherDate.getTime()) && otherDate < date) {
										return false;
									}
								}
							}
						}
						if (options.after) {
							var afters = options.after.split(' ');
							if (!$field.data('propagating')) {
								this.propagate(afters, validator);
							}
							for (var i = 0; i < afters.length; i++) {
								var other = this.getSingleJQueryField(afters[i], validator);
								if (other) {
									var otherDate = other.datepicker('getDate');
									if (!isNaN(otherDate.getTime()) && otherDate > date) {
										return false;
									}
								}
							}
						}
						return true;
					},
					propagate: function(relatedFields, validator) {
						var self = this;
						// propagate value change to related dates
						relatedFields.forEach(function (field) {
							var other = self.getSingleJQueryField(field, validator);
							if (other && other.data('bv.result.confDate') != 'NOT_VALIDATED') {
								other.data('propagating', 1);
								validator.updateStatus(field, 'NOT_VALIDATED', 'confDate').validateField(field);
								other.removeData('propagating');
							}
						});
					},
					getSingleJQueryField: function(fieldName, validator) {
						var other = validator.getFieldElements(fieldName);
						if (other) {
							return $(other[0]);
						} else {
							return null;
						}
					}
				};
			}(window.jQuery));
		</script>
@stop

@section('content')

		<div id='main'>

		<div class="page-header">
			<h1>@if($model) Edit @else Create @endif {{ $type }}</h1>
		@if ( $errors->count() > 0 )
			<p>The following errors have occurred:</p>
			<ul>
			@foreach( $errors->all() as $message )
				<li>{{{ $message }}}</li>
			@endforeach
			</ul>
		@endif
		</div>
		{{ Form::model($model, array('action' => $action, 'id' => 'event-form', 'role' => 'form')) }}
			{{ Form::hidden('id') }}
		@if ($type == 'Conference Edition')
			<div class="form-group">
				{{ Form::label($conferenceName, 'Conference') }}
		@elseif ($type == 'Workshop')
			<div class="container-fluid"><div class="row"><div class="form-group col-md-8" style="padding-left:0;padding-right:5px">
				{{ Form::label($conferenceName, 'Co-located Conference') }}
		@endif
				{{ Form::text($conferenceName, null, array('id' => 'conference_name', 'class' => 'form-control', 'placeholder' => 'Conference', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-remote' => 'true', 'data-bv-remote-message' => 'Must be an existing conference', 'data-bv-remote-url' => URL::action('ConferenceController@anyCheck'), 'data-bv-remote-name' => 'name')) }}
		
		@if ($type == 'Conference Edition')
			</div>
		@elseif ($type == 'Workshop')
			</div><div class="form-group col-md-4" style="padding:0">
				{{ Form::label('conference_edition_id', 'Edition') }}
				{{ Form::select('conference_edition_id', $editionOption, null, array('class' => 'form-control', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div></div></div>
		@endif
		@if ($type == 'Conference Edition')
			{{ Form::hidden('conference_id', null, array('id' => 'conference_id')) }}
			<div class="form-group">
				{{ Form::label('location', 'Location') }}
				{{ Form::text('location', null, array('class' => 'form-control', 'placeholder' => 'Location', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
			<div class="form-group">
				{{ Form::label('edition', 'Conference Edition') }}
				{{ Form::text('edition', null, array('class' => 'form-control', 'placeholder' => 'Edition / Year', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
		@elseif ($type == 'Workshop')
			<div class="form-group">
				{{ Form::label('name', 'Workshop Name') }}
				{{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
		@endif
			<div class="form-group single-date">
				{{ Form::label('event[abstract_due]', 'Abstract Submission Deadline') }}
				<div class="input-group date" id="abstract-datepicker">
					{{ Form::text('event[abstract_due]', @date_format(Form::getValueAttribute('event[abstract_due]'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be the first date', 'data-bv-confdate-before' => 'event[paper_due] event[notification_date] event[camera_ready_due] event[start] event[end]')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				{{ Form::label('event[paper_due]', 'Paper Submission Deadline') }}
				<div class="input-group date" id="paper-datepicker">
					{{ Form::text('event[paper_due]', @date_format(Form::getValueAttribute('event[paper_due]'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before everything except the abstract submission deadline', 'data-bv-confdate-before' => 'event[notification_date] event[camera_ready_due] event[start] event[end]', 'data-bv-confdate-after' => 'event[abstract_due]')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				{{ Form::label('event[notification_date]', 'Notification Date') }}
				<div class="input-group date" id="notification-datepicker">
					{{ Form::text('event[notification_date]', @date_format(Form::getValueAttribute('event[notification_date]'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before the camera ready submission deadline and conference, but after the abstract and paper submission deadlines', 'data-bv-confdate-before' => 'event[camera_ready_due] event[start] event[end]', 'data-bv-confdate-after' => 'event[abstract_due] event[paper_due]')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				{{ Form::label('event[camera_ready_due]', 'Camera Ready Submission Deadline') }}
				<div class="input-group date" id="camera-ready-datepicker">
					{{ Form::text('event[camera_ready_due]', @date_format(Form::getValueAttribute('event[camera_ready_due]'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before the conference, but after the other deadlines and notification', 'data-bv-confdate-before' => 'event[start] event[end]', 'data-bv-confdate-after' => 'event[abstract_due] event[paper_due] event[notification_date]')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group range-date">
				{{ Form::label('event[start]', 'Date') }}{{ Form::label('event[end]', 'End Date', array('class' => 'sr-only')) }}
				<div class="input-group input-daterange" id="start-end-datepicker">
					{{ Form::text('event[start]', @date_format(Form::getValueAttribute('event[start]'), 'M d, Y'), array('class' => 'input-sm form-control', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before the conference end, but after everything else', 'data-bv-confdate-before' => 'event[end]', 'data-bv-confdate-after' => 'event[abstract_due] event[paper_due] event[notification_date] event[camera_ready_due]')) }}
					<span class="input-group-addon">to</span>
					{{ Form::text('event[end]', @date_format(Form::getValueAttribute('event[end]'), 'M d, Y'), array('class' => 'input-sm form-control', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be the last date', 'data-bv-confdate-after' => 'event[abstract_due] event[paper_due] event[notification_date] event[camera_ready_due] event[start]')) }}
				</div>
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		{{ Form::close() }}

		</div>
@stop


