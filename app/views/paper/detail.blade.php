@extends('layouts/main')

@section('head')
		
		{{ HTML::script('javascripts/bootstrap-datepicker.min.js') }}
		{{ HTML::style('stylesheets/datepicker3.min.css')}}

		{{ HTML::script('javascripts/bootstrapValidator.min.js') }}
		{{ HTML::style('stylesheets/bootstrapValidator.min.css') }}

		{{ HTML::style('stylesheets/jquery.fileupload.css') }}
		<style type="text/css">
			.form-control[readonly] {
				background-color:#fff;
			}

		</style>
		
		@if ($owner)

			<script type="text/javascript">
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


					
				});
			</script>
		@endif
@stop

@section('content')
		<div class="page-header">
			{{ Form::open(array('action' => array('PaperController@anyEdit', 'id' => $paper->id))) }}
				<h1>{{{ $paper->title }}} 
				@if ($owner)
					<button type="submit" class="btn btn-xs btn-primary">Edit</button>
				@endif
				</h1>
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
			@if(!$paper->activeSubmission || !$paper->activeSubmission->camera_ready_submitted)
				{{ Form::open(array('action' => array('PaperController@anyRetarget', 'id' => $paper->id))) }}
				{{ Form::hidden('paperRetargetBackTarget', URL::action('PaperController@getDetails', array('id' => $paper->id))) }}
				{{ Form::label('submissionKind', 'Current Submission Target') }} 
				@if($owner)
					<button type="submit" class="btn btn-xs btn-primary">Change Target</button>
				@endif
				{{ Form::close() }}
			@else
				{{ Form::label('submissionKind', 'Successfully Finished Submission') }}
			@endif
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

		@if($owner)
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

				</table>
				
			</div>
		
			<div class="form-group">

				
				{{ Form::open(array('action' => array('ReviewController@getCreate', $paper->id), 'method' => 'get')) }}
				{{ Form::label('reviews', 'Review Requests') }}
					{{ Form::submit('Create Review Request', array('class' => 'btn btn-xs btn-primary')) }}
				{{ Form::close() }}
				<ul class="list-group">
					@foreach ($paper->reviewRequests as $reviewRequest)
						<li class="list-group-item">
							{{ Form::label('deadline', 'Deadline: ' ) }}
							{{ @date_format($reviewRequest->deadline, 'M d, Y') }} <br>
							{{ Form::label('files', 'Files: ') }}
								@foreach ($reviewRequest->files as $file)
									<a href="{{ URL::action('FileController@getFile', $file->id) }}" type="submit" class="btn btn-xs btn-default" role="button">{{$file->name}}</a>
								@endforeach
							<br>
							@if ($reviewRequest->message)
								{{ Form::label('message', 'Message') }}
								<pre>{{ $reviewRequest->message }}</pre>
							@endif	
							<table class="table table-bordered">
								<tr>
									<th>Requested reviewer</th>
									<th>Status</th>
								</tr>
								@foreach ($reviewRequest->authors as $author)
									<tr>
										<td>
											{{{ $author->formatName() }}}
										</td>
										<td>
											@if ($author->pivot->answer)
												<?php $review = $requestAnswers[$reviewRequest->id][$author->id]; ?>
												{{ Form::open(array('action' => array('ReviewController@getDetails', 'id' => $review->id), 'method' => 'GET')) }}
													Review recieved <button type="submit" class="btn btn-xs btn-primary">Details</button>
												{{ Form::close() }}
											@elseif (is_null($author->pivot->answer)) 
												No answer recieved yet
											@else
												Review denied
											@endif
										</td>
									</tr>
								@endforeach
							</table>
						</li>
					@endforeach
				</ul>
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

			
			{{ HTML::script('javascripts/jquery.ui.widget.js') }}
			{{ HTML::script('javascripts/jquery.fileupload.js') }}

		@endif

@stop