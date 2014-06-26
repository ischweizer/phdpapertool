@extends('layouts/main')

@section('head')
		
		<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css">

		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
		{{ HTML::style('stylesheets/bootstrapValidator.min.css'); }}

		{{ HTML::style('stylesheets/jquery.fileupload.css') }}
		<style type="text/css">
			.form-control[readonly] {
				background-color:#fff;
			}
			.datepicker{z-index:1151 !important;}
		</style>
		
		<script>
			var filesUploaded = false;
			
			$(document).ready(function() {
				$('#open_file_upload').click(function(){
					$('#fileUploadModal').modal('show');
				});
				
				$('#fileUploadModal').on('hidden.bs.modal', function () {
				    if(filesUploaded) 
				    	location.reload();
				})
				
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
		                    //data.context = $('<p/>').text('Uploading...').replaceAll($(this));
		                    data.submit();
		                });
			        },
			        done: function (e, data) {
			        	if (data.result.success == 1) {
			        		filesUploaded = true;
				        	$('#uploadstatus').html('Upload finished.');
			        	} else {
				        	$('#uploadstatus').html("Some problems occured!");
			        	}
			        },
			        fail : function (e, data) {
				        console.log("Failed");
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


				$.fn.datepicker.defaults.format = "M dd, yyyy";
				$.fn.datepicker.defaults.multidateSeparator = ";";

				$('#deadline').datepicker({
					startDate: '+1d'
				}).on('change', function(e) {
					var field = $(this).attr('name');
					$('#createReviewForm')
						.data('bootstrapValidator')
						.updateStatus(field, 'NOT_VALIDATED', null)
						.validateField(field);
				});


				$('#createReviewForm').bootstrapValidator({
					excluded: [':disabled'],
					message: 'This value is not valid',
					live: 'enabled',
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
				});

				$('#openCreateReview').click(function() {
					$('#createReviewModal').modal('show');
				});

				$('#addUser').click(function() {
					var chosenUser = $('#userSelect option:selected').remove();
					$('#selectedUsers').append(chosenUser);
				});

				$('#removeUser').click(function(){
					var chosenUser = $('#selectedUsers option:selected').remove();
					$('#userSelect').append(chosenUser);
				});

				$('#addFile').click(function() {
					var chosenFile = $('#fileSelect option:selected').remove();
					$('#selectedFiles').append(chosenFile);
				});

				$('#removeFile').click(function(){
					var chosenFile = $('#selectedFiles option:selected').remove();
					$('#fileSelect').append(chosenFile);
				});

				$('#createReviewForm').submit(function(){
					$('#selectedUsers option').prop('selected', true);
					$('#selectedFiles option').prop('selected', true);
				});
			});
		</script>
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
		
		<div class="form-group">

			{{ Form::label('reviews', 'Review Requests') }}
			{{ Form::button('Create Review Request', array('class' => 'btn btn-xs btn-primary', 'id' => 'openCreateReview')) }}

			<ul class="list-group">
				@foreach ($reviews as $review)
					<li class="list-group-item">
						{{ Form::label('deadline', 'Deadline: ' ) }}
						{{ @date_format($review->deadline, 'M d, Y') }} <br>
						{{ Form::label('files', 'Files: ') }}
							@foreach ($review->files as $file)
								@if ($file->author_id == Auth::user()->author->id)
									{{ Form::button($file->name, array('class' => 'btn btn-default btn-xs')) }}
								@endif
							@endforeach
						<br>
						{{ Form::label('message', 'Message') }}
						{{ $review->message }}
						<ul class="list-group">
							@foreach ($review->users as $user)
								<li class="list-group-item">
									{{{ $user->formatName() }}}
									@foreach ($review->files as $file)
										@if ($file->author_id == $user->author->id)
											{{ Form::button($file->name, array('class' => 'btn btn-default btn-xs')) }}
										@endif
									@endforeach
								</li>
							@endforeach
						</ul>
					</li>
				@endforeach
			</ul>
		</div>

		<div class="form-group">
			{{ Form::label('files', 'Files') }}
			<button type="submit" id="open_file_upload" class="btn btn-xs btn-primary">Upload File</button>
			
			<table id="file_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Name</th>
						<th>Comment</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($files as $file)
						<tr>
							<td>{{{ $file->name }}}</td>
							<td>{{{ Str::limit($file->comment, 90) }}}</td>
							<td>
								{{ Form::open(array('action' => array('FileController@getFileDetails', 'id' => $file->id), 'method' => 'GET', 'style' => 'display:inline')) }}
									<button type="submit" class="btn btn-xs btn-primary">Details</button>
								{{ Form::close() }}
								{{ Form::open(array('action' => array('FileController@getEditFile', 'id' => $file->id), 'method' => 'GET', 'style' => 'display:inline')) }}
									<button type="submit" class="btn btn-xs btn-primary">Edit</button>
								{{ Form::close() }}
								<a href="{{ URL::action('FileController@getFile', $file->id) }}" type="submit" class="btn btn-xs btn-primary" role="button">Download</a>
							</td>
						</tr>
					@endforeach
				</tbody>
				{{--
				<tfoot>
					<tr>
						<th>Title</th>
						<th>Abstract</th>
						<th>Action</th>
					 </tr>
				</tfoot>
				--}}
			</table>
			
		</div>

		{{-- TODO show submission history --}}
		
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

		<div class="modal fade" id="createReviewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					{{ Form::open(array('action' => 'PaperController@postCreateReviewRequest', 'id' => 'createReviewForm')) }}
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Request a Review</h4>
					</div>
					<div class="modal-body">
						<div class="form-group single-date">
							{{ Form::label('deadline', 'Review Deadline') }}
							<div class="input-group date" id="deadline-datepicker">
								{{ Form::text('deadline', '', array('class' => 'form-control input-sm datepicker', 'required')) }}
								<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('user', 'User') }}
							<div class="input-group"> 
								{{ Form::select('userSelect', $userNames, null, array('class' => 'form-control', 'id' => 'userSelect')) }}
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" id="addUser"><span class="glyphicon glyphicon-plus"></span></button>
								</span>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('selectedUser', 'Selected User') }}
							<div class="row">
								<div class="col-xs-11">
									{{ Form::select('selectedUsers[]', array(), null, array('size' => 5, 'class' => 'form-control', 'id' => 'selectedUsers', 'multiple' => true)) }}
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
							<div class="input-group"> 
								{{ Form::select('fileSelect', $fileNames, null, array('class' => 'form-control', 'id' => 'fileSelect')) }}
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" id="addFile"><span class="glyphicon glyphicon-plus"></span></button>
								</span>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('selectedFiles', 'Selected Files') }}
							<div class="row">
								<div class="col-xs-11">
									{{ Form::select('selectedFiles[]', array(), null, array('size' => 5, 'class' => 'form-control', 'id' => 'selectedFiles', 'multiple' => true)) }}
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
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						{{ Form::submit('Save', array('class' => 'btn btn-primary', 'id' => 'createReviewModalSave')) }}
					</div>
					{{ Form::close() }}
				</div>
			</div>
		</div>
		{{ HTML::script('javascripts/jquery.ui.widget.js') }}
		{{ HTML::script('javascripts/jquery.fileupload.js') }}

@stop
