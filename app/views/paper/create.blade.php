@extends('layouts/main')

@section('head')
		<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>
		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
		{{ HTML::style('stylesheets/bootstrapValidator.min.css'); }}

		<script>
			$(document).ready(function() {
				$('#add_author').click(function(){
					var selection = $("#authorlist").children("option").filter(":selected");
					var authorId = selection.val();
					var name = selection.text();
					
					$('#selected_authors').append(
				        $('<option></option>').val(authorId).html(name)
				    );
				    
				    $("#authorlist option[value='"+authorId+"']").remove();
				});
				
				$('#new_author').click(function(){
					var lastname = $('#last_name').val();
					var firstname = $('#first_name').val();
					var email = $('#email').val();
					if (lastname == '' || firstname == '' || email == '') {
						alert("Please fill all fields!");
					} else {
						var data = {
							last_name : lastname,
							first_name : firstname,
							email : email
						};
						
						$.ajax({
							type: "POST",
							url: "{{ URL::action('PaperController@postCreateAuthor') }}",
							data: data,
							success: function(response) {
								$.each(response, function(key, value){
									$('#selected_authors').append(
								        $('<option></option>').val(key).html(value)
								    );
								});
							}
						});
					}
				});
				
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
				$('#{{ $submission['kind'] }}').show();

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
			});
		</script>
@stop

@section('content')
		<div class="page-header">
   			<h1>@if($model) Edit @else Create @endif Paper</h1>
			@if ( $errors->count() > 0 )
			<p>The following errors have occurred:</p>
			<ul>
			@foreach( $errors->all() as $message )
				<li>{{{ $message }}}</li>
			@endforeach
			</ul>
		@endif
		</div>

		{{ Form::model($model, array('action' => 'PaperController@postEdit', 'id' => 'paper-form')) }}
			{{ Form::hidden('id') }}
			<div class="form-group">
				{{ Form::label('title', 'Title') }}
				{{ Form::text('title', null, array('placeholder' => 'Title', 'class' => 'form-control', 'id' => 'titlefield', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
			</div>
			<div class="form-group">
				{{ Form::label('repository_url', 'Repository') }}
				{{ Form::url('repository_url', null, array('placeholder' => 'Repository', 'class' => 'form-control', 'data-bv-uri-message' => 'Has to be a valid URI')) }}
			</div>
			
			<!-- authors -->
			{{ Form::label('authors', 'Authors') }}<br>
			<div class="row">
				<!-- add author -->
				<div class="col-md-6">
					{{ Form::label('select', 'Select Author') }}<br>
					{{ Form::select('authorlist', $authors, null, array('id' => 'authorlist')) }}<br><br>
					{{ Form::button('Add', array('id' => 'add_author', 'class' => 'btn btn-sm btn-primary')) }}<br><br>
				</div>
				<!-- create author -->
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-6">
							{{ Form::label('lastname', 'Last name') }}<br>
							{{ Form::text('lastname', '', array('placeholder' => 'Lastname', 'class' => 'form-control', 'id' => 'last_name')) }}
						</div>
						<div class="col-md-6">
							{{ Form::label('firstname', 'First name') }}<br>
							{{ Form::text('firstname', '', array('placeholder' => 'First name', 'class' => 'form-control', 'id' => 'first_name')) }}
						</div>
					</div>
					<br>
					{{ Form::label('email', 'Email') }}<br>
					{{ Form::text('email', '', array('placeholder' => 'Email', 'class' => 'form-control', 'id' => 'email')) }}<br>
					{{ Form::button('Add', array('id' => 'new_author', 'class' => 'btn btn-sm btn-primary')) }}<br><br>
				</div>
			</div>
			<div class="form-group">
				{{ Form::label('selectedauthors', 'Selected Authors') }}
				{{ Form::select('selectedauthors[]', $selectedauthors, null, array('size' => 10, 'class' => 'form-control', 'id' => 'selected_authors', 'multiple' => true)) }}
			</div>
			
			<div class="form-group">
				{{ Form::label('abstract', 'Abstract') }}
				{{ Form::textarea('abstract', null, array('placeholder' => 'Abstract', 'class' => 'form-control')) }}
			</div>

			<div class="form-group">
				{{ Form::label('submissionKind', 'Submission') }}
				<div class="form-control">
					<label class="radio-inline">
						{{ Form::radio('submissionKind', 'ConferenceEdition', $submission['kind'] == 'ConferenceEdition', array()) }} Conference Edition
					</label>
					<label class="radio-inline">
						{{ Form::radio('submissionKind', 'Workshop', $submission['kind'] == 'Workshop', array()) }} Workshop
					</label>
					<label class="radio-inline">
						{{ Form::radio('submissionKind', 'none', $submission['kind'] == 'none', array()) }} none
					</label>
				</div>
			</div>
			<div id="ConferenceEdition" class="well submissionToggle">
				<div class="container-fluid"><div class="row"><div class="form-group col-md-8" style="padding-left:0;padding-right:5px">
					{{ Form::label('conference_name', 'Conference') }}
					{{ Form::text('conference_name', $submission['conferenceName'], array('class' => 'form-control', 'placeholder' => 'Conference', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-remote' => 'true', 'data-bv-remote-message' => 'Must be an existing conference', 'data-bv-remote-url' => URL::action('ConferenceController@anyCheck'), 'data-bv-remote-name' => 'name')) }}
				</div><div class="form-group col-md-4" style="padding:0">
					{{ Form::label('conference_edition_id', 'Edition') }}
					{{ Form::select('conference_edition_id', $submission['editionOption'], $submission['activeDetailID'], array('class' => 'form-control', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
				</div></div></div>
			</div>
			<div id="Workshop" class="well submissionToggle">
				<div class="form-group">
					{{ Form::hidden('workshop_id', $submission['activeDetailID'], array('id' => 'workshop_id')) }}
					{{ Form::label('workshop_name', 'Workshop') }}
					{{ Form::text('workshop_name', $submission['workshopName'], array('class' => 'form-control', 'placeholder' => 'Workshop', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-remote' => 'true', 'data-bv-remote-message' => 'Must be an existing workshop', 'data-bv-remote-url' => URL::action('WorkshopController@anyCheck'), 'data-bv-remote-name' => 'name')) }}
				</div>
			</div>

			{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary')) }}
		{{ Form::close() }}
@stop
