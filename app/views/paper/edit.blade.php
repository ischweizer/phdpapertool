@extends('layouts/main')

@section('head')
		<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>
		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
		{{ HTML::style('stylesheets/bootstrapValidator.min.css') }}

		<script>
			var onformsubmit = function() {
				// Since only selected properties are sent
				$('#selected_authors option').prop('selected', true);
			}
			
			$(document).ready(function() {
				$('#selected_authors').change(function(){
					$('#side-buttons').removeAttr("disabled");
				});
				
				$('#open_new_author').click(function(){
					$('#authorCreationModal').modal('show');
				});
				
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
					//doesnt work with jquery
					var notifyAuthor = document.getElementById("notifyAuthor").checked ? 1 : 0;
					if (lastname == '' || firstname == '' || email == '') {
						alert("Please fill all fields!");
					} else {
						var data = {
							last_name : lastname,
							first_name : firstname,
							email : email,
							notifyAuthor : notifyAuthor
						};
						
						$.ajax({
							type: "POST",
							url: "{{ URL::action('PaperController@postCreateAuthor') }}",
							data: data,
							success: function(response) {
								if (response.success !== undefined && response.success == 1) {
									$.each(response.authors, function(key, value){
										$('#selected_authors').append(
									        $('<option></option>').val(key).html(value)
									    );
									    $('#authorCreationModal').modal('hide');
									});
									
									$('#last_name').val('');
									$('#first_name').val('');
									$('#email').val('');
								} else {
									alert("An authors with the given email address already exists!");
								}
							}
						});
					}
				});
				
				// install authors typeahead
				var authors = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: {
						url: "{{ URL::action('PaperController@getAutocomplete', array('QUERY')) }}",
						replace: function (url, query) {
							// double encode query as it gets decoded -> splitted, which would destroy queries containing '/'
							return url.replace('QUERY', encodeURIComponent(encodeURIComponent(query)));
						},
						filter: function(list) {
					    	return $.grep(list, function( author ) {
									  return $("#selected_authors option[value='"+author.id+"']").length == 0;;
									});
					    }
					}
				});
				authors.initialize();
				$('#author_list').typeahead({
					highlight: true
				}, {
					name: 'authors',
					displayKey: 'name',
					source: authors.ttAdapter(),
					templates: {
						suggestion: function (obj) {
							return obj.last_name + ' ' + obj.first_name + ' ('+ obj.email + ')';
						}
					}
				}).on('typeahead:selected typeahead:autocompleted', function(event, data) {
					$('#selected_authors').append(
				        $('<option></option>').val(data.id).html(data.last_name + ' ' + data.first_name + ' ('+ data.email + ')')
				    );
					/*$('#paper-form')
						.data('bootstrapValidator')
						.updateStatus('author_list', 'NOT_VALIDATED', null)
						.validateField('author_list');*/
					authorNameChange();
				});
				$('#author_list').on('input', authorNameChange);
				function authorNameChange() {
					
				}
				authorNameChange();
				
				
				$('#remove_author').click(function(){
					var selection = $("#selected_authors").children("option").filter(":selected");
					$.each(selection, function( index, author ) {
						var authorId = author.value;
						if ({{{ Auth::user()->author->id }}} == authorId) {
							alert("You cannot remove yourself!");
						} else {
							$('#authorlist').append(
						        $('<option></option>').val(authorId).html(author.text)
						    );
							$("#selected_authors option[value='"+authorId+"']").remove();
						}
					});
				});
				
				$('#author_up').click(function(){
					$('#selected_authors option:selected').each( function() {
			            var newPos = $('#selected_authors option').index(this) - 1;
			            if (newPos > -1) {
			                $('#selected_authors option').eq(newPos).before("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
			                $(this).remove();
			            }
			        });
				});
				
				$('#author_down').click(function(){
					var countOptions = $('#selected_authors option').size();
			        $('#selected_authors option:selected').each( function() {
			            var newPos = $('#selected_authors option').index(this) + 1;
			            if (newPos < countOptions) {
			                $('#selected_authors option').eq(newPos).after("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
			                $(this).remove();
			            }
			        });
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
			{{ Form::open(array('url' => Input::get('paperBackTarget') ?: Input::old('paperBackTarget'), 'method' => 'GET')) }}
				<h1>@if($model) Edit @else Create @endif Paper <button type="submit" class="btn btn-xs btn-primary">Back</button></h1>
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

		{{ Form::model($model, array('action' => 'PaperController@postEditTarget', 'id' => 'paper-form', 'onsubmit' => 'onformsubmit()')) }}
			{{ Form::hidden('paperBackTarget', Input::get('paperBackTarget')) }}
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
			<div class="form-group">
				<!-- add author -->
				{{ Form::text('authorlist', null, array('placeholder' => 'Author', 'class' => 'form-control', 'id' => 'author_list')) }}<br>
				{{ Form::button('New Author', array('id' => 'open_new_author', 'class' => 'btn btn-sm btn-default')) }}<br><br>
			</div>
			<div class="form-group">
				{{ Form::label('selectedauthors', 'Selected Authors') }}
				<div class="row">
					<div class="col-xs-11">
						{{ Form::select('selectedauthors[]', $selectedauthors, null, array('size' => 10, 'class' => 'form-control', 'id' => 'selected_authors', 'multiple' => true)) }}
					</div>
					<div class="col-xs-1">
						<fieldset id="side-buttons" disabled>
							<div class="btn-group-vertical">
								{{ Form::button('<span class="glyphicon glyphicon-chevron-up"></span>', array('id' => 'author_up', 'class' => 'btn btn-sm btn-default')) }}
								{{ Form::button('<span class="glyphicon glyphicon-chevron-down"></span>', array('id' => 'author_down', 'class' => 'btn btn-sm btn-default')) }}
								{{ Form::button('<span class="glyphicon glyphicon-remove"></span>', array('id' => 'remove_author', 'class' => 'btn btn-sm btn-default')) }}
							</div>
						</fieldset>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				{{ Form::label('abstract', 'Abstract') }}
				{{ Form::textarea('abstract', null, array('placeholder' => 'Abstract', 'class' => 'form-control')) }}
			</div>

			@if ($submission['kind'] != 'none' && $model)
			<div class="form-group">
				{{ Form::label('submissionKind', 'Current Submission Target') }}
				{{ Form::hidden('submissionKind', 'none') }}
				<div class="form-control-static-bordered" style="background-color:#eee">
				@if ($submission['kind'] == 'ConferenceEdition')
					<b>Conference</b> {{{ $submission['conferenceName'] }}}<br>
					<b>Edition</b> {{{ $submission['editionName'] }}}<br>
					{{ HTML::linkAction('ConferenceEditionController@getDetails', 'details', array('id' => $submission['activeDetailID'])) }}
				@elseif ($submission['kind'] == 'Workshop')
					<b>Workshop</b><br>
					{{{ $submission['workshopName'] }}} {{ HTML::linkAction('WorkshopController@getDetails', 'details', array('id' => $submission['activeDetailID'])) }}
				@endif
				</div>
			</div>
			@else
			<div class="form-group">
				{{ Form::label('submissionKind', 'Current Submission Target') }}
				<div class="form-control">
					<label class="radio-inline">
						{{ Form::radio('submissionKind', 'ConferenceEdition', $submission['kind'] == 'ConferenceEdition', array()) }} Conference
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
				</div><div class="form-group col-md-3" style="padding-left:0;padding-right:5px">
					{{ Form::label('conference_edition_id', 'Edition') }}
					{{ Form::select('conference_edition_id', $submission['editionOption'], $submission['activeDetailID'], array('class' => 'form-control', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
				</div><div class="form-group col-md-1" style="padding:1px 0 0 0">
					<label>&nbsp;</label>
					<input id="conference_edition_create" type="button" class="btn btn-sm btn-primary" value="Create New">
				</div></div></div>
			</div>
			<div id="Workshop" class="well submissionToggle">
				<div class="container-fluid"><div class="row"><div class="form-group col-md-11" style="padding-left:0;padding-right:5px">
					{{ Form::hidden('workshop_id', $submission['activeDetailID'], array('id' => 'workshop_id')) }}
					{{ Form::label('workshop_name', 'Workshop') }}
					{{ Form::text('workshop_name', $submission['workshopName'], array('class' => 'form-control', 'placeholder' => 'Workshop', 'required', 'data-bv-notempty-message' => 'May not be empty', 'data-bv-remote' => 'true', 'data-bv-remote-message' => 'Must be an existing workshop', 'data-bv-remote-url' => URL::action('WorkshopController@anyCheck'), 'data-bv-remote-name' => 'name')) }}
				</div><div class="form-group col-md-1" style="padding:1px 0 0 0">
					<label>&nbsp;</label>
					<input id="workshop_create" type="button" class="btn btn-sm btn-primary" value="Create New">
				</div></div></div>
			</div>
			@endif

			{{ Form::submit('Submit', array('class' => 'btn btn-lg btn-primary')) }}
		{{ Form::close() }}
		
		<div class="modal fade" id="authorCreationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Create an Author</h4>
					</div>
					<div class="modal-body">
						
						<div class="row">
							<div class="col-md-6">
								<label for="first_name" class="sr-only">First name</label>
								<input type="text" class="form-control" id="first_name" placeholder="First name">
							</div>
							<div class="col-md-6">
								<label for="last_name" class="sr-only">Last name</label>
								<input type="text" class="form-control" id="last_name" placeholder="Last name">
							</div>
						</div>
						<br>
						<div class="form-group">
							<label for="email" class="sr-only">Group name</label>
							<input type="text" class="form-control" id="email" placeholder="Email">
							
						</div>
						<div class="checkbox">
						    <label>
							<input type="checkbox" id="notifyAuthor"> Notify author via email
						    </label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="new_author">Save author</button>
					</div>
				</div>
			</div>
		</div>
@stop
