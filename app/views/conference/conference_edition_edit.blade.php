@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
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
					remote: "{{ URL::to('conferences/autocomplete?q=%QUERY') }}"
				});
				conferences.initialize();
				$('#conference\\[name\\]').typeahead({
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
				});

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

				var datepickersComplete = [$('#abstract_due'), $('#paper_due'), $('#notification_date'), $('#camera_ready_due'), $('#start'), $('#end')];

				// revalidate on date pick
				datepickersComplete.forEach(function (datepicker) {
					datepicker.datepicker().on('change', function(e) {
						var field = $(this).attr('name');
						$('#conference-edition-form')
							.data('bootstrapValidator')
							.updateStatus(field, 'NOT_VALIDATED', null)
							.validateField(field);
					});
				});

				// enable form validation
				$('#conference-edition-form').bootstrapValidator({
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					live: 'enabled'
				});
				
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
   		<h1>Create Conference Edition</h1>
		@if ( $errors->count() > 0 )
			<p>The following errors have occurred:</p>
			<ul>
			@foreach( $errors->all() as $message )
				<li>{{{ $message }}}</li>
			@endforeach
			</ul>
		@endif
		</div>
		{{ Form::model($edition, array('action' => 'ConferenceEditionController@postEdit', 'id' => 'conference-edition-form', 'role' => 'form')) }}
			{{ Form::hidden('id') }}
			<div class="form-group">
				{{ Form::label('conference[name]', 'Conference') }}
				{{ Form::text('conference[name]', null, array('class' => 'form-control', 'placeholder' => 'Conference', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
			<div class="form-group">
				{{ Form::label('location', 'Location') }}
				{{ Form::text('location', null, array('class' => 'form-control', 'placeholder' => 'Location', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
			<div class="form-group">
				{{ Form::label('edition', 'Conference Edition') }}
				{{ Form::text('edition', null, array('class' => 'form-control', 'placeholder' => 'Edition / Year', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
			<div class="form-group single-date">
				{{ Form::label('abstract_due', 'Abstract Submission Deadline') }}
				<div class="input-group date" id="abstract-datepicker">
					{{ Form::text('abstract_due', @date_format(Form::getValueAttribute('abstract_due'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be the first date', 'data-bv-confdate-before' => 'paper_due notification_date camera_ready_due start end')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				{{ Form::label('paper_due', 'Paper Submission Deadline') }}
				<div class="input-group date" id="paper-datepicker">
					{{ Form::text('paper_due', @date_format(Form::getValueAttribute('paper_due'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before everything except the abstract submission deadline', 'data-bv-confdate-before' => 'notification_date camera_ready_due start end', 'data-bv-confdate-after' => 'abstract_due')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				{{ Form::label('notification_date', 'Notification Date') }}
				<div class="input-group date" id="notification-datepicker">
					{{ Form::text('notification_date', @date_format(Form::getValueAttribute('notification_date'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before the camera ready submission deadline and conference, but after the abstract and paper submission deadlines', 'data-bv-confdate-before' => 'camera_ready_due start end', 'data-bv-confdate-after' => 'abstract_due paper_due')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				{{ Form::label('camera_ready_due', 'Camera Ready Submission Deadline') }}
				<div class="input-group date" id="camera-ready-datepicker">
					{{ Form::text('camera_ready_due', @date_format(Form::getValueAttribute('camera_ready_due'), 'M d, Y'), array('class' => 'form-control input-sm', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before the conference, but after the other deadlines and notification', 'data-bv-confdate-before' => 'start end', 'data-bv-confdate-after' => 'abstract_due paper_due notification_date')) }}
					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				</div>
			</div>
			<div class="form-group range-date">
				{{ Form::label('start', 'Date') }}{{ Form::label('end', 'Dummy', array('style' => 'display:none')) }}
				<div class="input-daterange input-group" id="start-end-datepicker">
					{{ Form::text('start', @date_format(Form::getValueAttribute('start'), 'M d, Y'), array('class' => 'input-sm form-control', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be a date before the conference end, but after everything else', 'data-bv-confdate-before' => 'end', 'data-bv-confdate-after' => 'abstract_due paper_due notification_date camera_ready_due')) }}
					<span class="input-group-addon">to</span>
					{{ Form::text('end', @date_format(Form::getValueAttribute('end'), 'M d, Y'), array('class' => 'input-sm form-control', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-confdate' => 'true', 'data-bv-confdate-message' => 'Must be the last date', 'data-bv-confdate-after' => 'abstract_due paper_due notification_date camera_ready_due start')) }}
				</div>
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		{{ Form::close() }}

		</div>
@stop


