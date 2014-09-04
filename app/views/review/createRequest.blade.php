@extends('layouts/main')

@section('head')
	<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>
	{{ HTML::script('javascripts/bootstrap-datepicker.min.js') }}
	{{ HTML::style('stylesheets/datepicker3.min.css')}}

	{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
	{{ HTML::style('stylesheets/bootstrapValidator.min.css') }}
	{{ HTML::style('stylesheets/jquery.fileupload.css') }}
	<style type="text/css">
		
	</style>
	<script type="text/javascript">
		
		$(document).ready(function(){

			$.fn.datepicker.defaults.format = "M dd, yyyy";
			$.fn.datepicker.defaults.multidateSeparator = ";";

			$('#deadline-datepicker').datepicker({
				startDate: '+1d'
			});
			
			$('#open_new_author').click(function(){
				$('#authorCreationModal').modal('show');
			});
			
			$('#open_file_upload').click(function(){
				$('#fileUploadModal').modal('show');
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
									$('#selectedAuthors').append(
								        $('<option></option>').val(key).html(value)
								    );
								    $('#authorCreationModal').modal('hide');
								});
								$('#createReviewForm')
									.data('bootstrapValidator')
									.updateStatus('selectedAuthors[]', 'NOT_VALIDATED', null)
									.validateField('selectedAuthors[]');
								
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

			$('#addUser').click(function() {
				var chosenAuthor = $('#authorSelect option:selected').remove();
				$('#selectedAuthors').append(chosenAuthor);
				$('#createReviewForm')
					.data('bootstrapValidator')
					.updateStatus('selectedAuthors[]', 'NOT_VALIDATED', null)
					.validateField('selectedAuthors[]');
			});

			$('#removeUser').click(function(){
				var chosenAuthor = $('#selectedAuthors option:selected').remove();
				$('#authorSelect').append(chosenAuthor);
				$('#createReviewForm')
					.data('bootstrapValidator')
					.updateStatus('selectedAuthors[]', 'NOT_VALIDATED', null)
					.validateField('selectedAuthors[]');
			});

			$('#addFile').click(function() {
				var chosenFile = $('#fileSelect option:selected').remove();
				$('#selectedFiles').append(chosenFile);
				$('#createReviewForm')
					.data('bootstrapValidator')
					.updateStatus('selectedFiles[]', 'NOT_VALIDATED', null)
					.validateField('selectedFiles[]');
			});

			$('#removeFile').click(function(){
				var chosenFile = $('#selectedFiles option:selected').remove();
				$('#fileSelect').append(chosenFile);
				$('#createReviewForm')
					.data('bootstrapValidator')
					.updateStatus('selectedFiles[]', 'NOT_VALIDATED', null)
					.validateField('selectedFiles[]');
			});
			
			$('#fileupload').fileupload({
		        url: "{{ URL::action('FileController@postUploadFile', array('id' => $paper->id)) }}",
		        dataType: 'json',
		        autoUpload: false,
		        type: 'POST',
		        add: function (e, data) {
		        	$.each(data.files, function (index, file) {
		                $('<p/>').text(file.name).appendTo('#files');
		            });
		        	$('#startupload').click(function () {
	                    data.submit();
	                });
		        },
		        done: function (e, data) {
		        	if (data.result.success == 1) {
		        		filesUploaded = true;
			        	$('#uploadstatus').html('Upload finished.');
			        	var files = data.result.files;
			        	$.each( files, function( key, value ) {
						    $('#selectedFiles').append(
						        $('<option></option>').val(key).html(value)
						    );
						});
						$('#fileUploadModal').modal('hide');
						$('#createReviewForm')
							.data('bootstrapValidator')
							.updateStatus('selectedFiles[]', 'NOT_VALIDATED', null)
							.validateField('selectedFiles[]');
		        	} else {
			        	//$('#uploadstatus').html("Some problems occured!");
			        	alert("Some problems occured!");
		        	}
		        },
		        fail : function (e, data) {
			        console.log(JSON.stringify(data.messages));
			        alert(data.messages.uploadedBytes);
		        },
		        progressall: function (e, data) {
		            var progress = parseInt(data.loaded / data.total * 100, 10);
		            $('#progress .progress-bar').css(
		                'width',
		                progress + '%'
		            );
		        }
		    }).prop('disabled', !$.support.fileInput)
		        .parent().addClass($.support.fileInput ? undefined : 'disabled');

			$('#createReviewForm').submit(function(){
				$('#selectedAuthors option').prop('selected', true);
				$('#selectedFiles option').prop('selected', true);
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
								  return $("#selectedAuthors option[value='"+author.id+"']").length == 0;;
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
				$('#selectedAuthors').append(
			        $('<option></option>').val(data.id).html(data.last_name + ' ' + data.first_name + ' ('+ data.email + ')')
			    );
				$('#createReviewForm')
					.data('bootstrapValidator')
					.updateStatus('selectedAuthors[]', 'NOT_VALIDATED', null)
					.validateField('selectedAuthors[]');
				authorNameChange();
			});
			$('#author_list').on('input', authorNameChange);
			function authorNameChange() {
				
			}
			authorNameChange();

			// enable form validation
			$.fn.bootstrapValidator.validators.notemptyselect = {
				html5Attributes: {
						message: 'message'
				},
				validate: function(validator, $field, options) {
					var count = validator.getFieldElements($field.attr('data-bv-field')).find('option').length;
					if (count > 0) {
						return true;
					} else {
						return false;
					}
				}
			};
			$('#createReviewForm').bootstrapValidator({
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				live: 'enabled'
			});

			$('#deadline').on('change', function(e) {
				var field = $(this).attr('name');
				$('#createReviewForm')
					.data('bootstrapValidator')
					.updateStatus(field, 'NOT_VALIDATED', null)
					.validateField(field);
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
				{{ Form::text('deadline', '', array('class' => 'form-control input-sm datepicker', 'required', 'data-bv-notempty-message' => 'May not be empty')) }}
				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('author', 'Author') }}
			{{ Form::text('authorlist', null, array('placeholder' => 'Author', 'class' => 'form-control', 'id' => 'author_list')) }}
			{{ Form::button('New Author', array('id' => 'open_new_author', 'class' => 'btn btn-sm btn-default')) }}
		</div>
		<div class="form-group select-field">
			{{ Form::label('selectedAuthors', 'Selected Reviewers') }}
			<div class="row">
				<div class="col-xs-11">
					{{ Form::select('selectedAuthors[]', array(), null, array('size' => 5, 'class' => 'form-control', 'id' => 'selectedAuthors', 'multiple' => true, 'data-bv-notemptyselect' => 'true', 'data-bv-notemptyselect-message' => 'Must select at least one reviewer.')) }}
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
			{{ Form::select('fileSelect', $fileNames, null, array('class' => 'form-control', 'id' => 'fileSelect')) }}
			{{ Form::button('Select File', array('id' => 'addFile', 'class' => 'btn btn-sm btn-primary')) }}
			{{ Form::button('Upload File', array('id' => 'open_file_upload', 'class' => 'btn btn-sm btn-default')) }}
		</div>
		<div class="form-group select-field">
			{{ Form::label('selectedFiles', 'Selected Files') }}
			<div class="row">
				<div class="col-xs-11">
					{{ Form::select('selectedFiles[]', array(), null, array('size' => 5, 'class' => 'form-control', 'id' => 'selectedFiles', 'multiple' => true, 'data-bv-notemptyselect' => 'true', 'data-bv-notemptyselect-message' => 'Must select at least one file.')) }}
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
	<div class="modal fade" id="fileUploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Upload a File</h4>
				</div>
				<div class="modal-body">
					
					<span class="btn btn-success fileinput-button">
						<i class="glyphicon glyphicon-plus"></i>
						<span>Select files...</span>
						<!-- The file input field used as target for the file upload widget -->
						<input id="fileupload" type="file" name="files[]" multiple>
					</span>
					<br>
					<br>
					<!-- The global progress bar -->
					<div id="progress" class="progress">
						<div class="progress-bar progress-bar-success"></div>
					</div>
					<!-- The container for the uploaded files -->
					<div id="files" class="files"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="startupload">Upload</button>
				</div>
			</div>
		</div>
	</div>
	{{ HTML::script('javascripts/jquery.ui.widget.js') }}
	{{ HTML::script('javascripts/jquery.fileupload.js') }}


@stop