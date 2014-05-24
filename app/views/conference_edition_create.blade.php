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
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: "{{ URL::to('conferences/autocomplete?q=%QUERY') }}"
				});
				conferences.initialize();
				$('#conference').typeahead({
					highlight: true
				}, {
					name: 'conferences',
					displayKey: 'name',
					source: conferences.ttAdapter()
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

				var datepickersComplete = [$('#abstract-datepicker-input'), $('#paper-datepicker-input'), $('#notification-datepicker-input'), $('#camera-ready-datepicker-input'), $('#start-datepicker-input'), $('#end-datepicker-input')];

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
		</div>

        <form role="form" method="post" action="conference-editions/create" id="conference-edition-form">
			<div class="form-group">
				<label for="exampleInputEmail1">Conference</label>
				<input type="text" class="form-control typeahead" name="conference" id="conference" placeholder="Conference" required data-bv-notempty-message="May not be empty">
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">Location</label>
				<input type="text" class="form-control" name="location" required data-bv-notempty-message="May not be empty">
			</div>
			<div class="form-group">
				<label for="edition">Conference Edition</label>
				<input type="text" class="form-control" name="edition" placeholder="Edition / Year" required data-bv-notempty-message="May not be empty">
			</div>
			<div class="form-group single-date">
				<label for="abstract-due">Abstract Submission Deadline</label>
				<div class="input-group date" id="abstract-datepicker">
					<input type="text" class="form-control input-sm" id="abstract-datepicker-input" name="abstract-due" required data-bv-notempty-message="May not be empty" data-bv-confdate data-bv-confdate-message="Must be the first date" data-bv-confdate-before="paper-due notification-date camera-ready-due start end">
					<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				<label for="paper-due">Paper Submission Deadline</label>
				<div class="input-group date" id="paper-datepicker">
					<input type="text" class="form-control input-sm" id="paper-datepicker-input" name="paper-due" required data-bv-notempty-message="May not be empty" data-bv-confdate data-bv-confdate-message="Must be a date before everything except the abstract submission deadline" data-bv-confdate-before="notification-date camera-ready-due start end" data-bv-confdate-after="abstract-due">
					<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				<label for="notification-due">Notification Date</label>
				<div class="input-group date" id="notification-datepicker">
					<input type="text" class="form-control input-sm" id="notification-datepicker-input" name="notification-date" required data-bv-notempty-message="May not be empty" data-bv-confdate data-bv-confdate-message="Must be a date before the camera ready submission deadline and conference, but after the abstract and paper submission deadlines" data-bv-confdate-before="camera-ready-due start end" data-bv-confdate-after="abstract-due paper-due">
					<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
				</div>
			</div>
			<div class="form-group single-date">
				<label for="camera-ready-due">Camera Ready Submission Deadline</label>
				<div class="input-group date" id="camera-ready-datepicker">
					<input type="text" class="form-control input-sm" id="camera-ready-datepicker-input" name="camera-ready-due" required data-bv-notempty-message="May not be empty" data-bv-confdate data-bv-confdate-message="Must be a date before the conference, but after the other deadlines and notification" data-bv-confdate-before="start end" data-bv-confdate-after="abstract-due paper-due notification-date">
					<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
				</div>
			</div>
			<div class="form-group range-date">
				<label for="start">Date</label>
				<div class="input-daterange input-group" id="start-end-datepicker">
					<input type="text" class="input-sm form-control" name="start" id="start-datepicker-input" required data-bv-notempty-message="May not be empty" data-bv-confdate data-bv-confdate-message="Must be a date before the conference end, but after everything else" data-bv-confdate-before="end" data-bv-confdate-after="abstract-due paper-due notification-date camera-ready-due">
					<span class="input-group-addon">to</span>
					<input type="text" class="input-sm form-control" name="end" id="end-datepicker-input" required data-bv-notempty-message="May not be empty" data-bv-confdate data-bv-confdate-message="Must be the last date" data-bv-confdate-after="abstract-due paper-due notification-date camera-ready-due start">
				</div>
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		</form>

		</div>
@stop


