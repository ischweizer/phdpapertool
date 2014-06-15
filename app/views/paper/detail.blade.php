@extends('layouts/main')

@section('head')
		{{ HTML::style('stylesheets/jquery.fileupload.css') }}
		<style type="text/css">
			.form-control[readonly] {
				background-color:#fff;
			}
		</style>
		
		<script>
			$(document).ready(function() {
				$('#open_file_upload').click(function(){
					$('#fileUploadModal').modal('show');
				});
				
				$('#fileupload').fileupload({
			        url: "{{ URL::action('PaperController@postUploadFile', array('id' => $paper->id)) }}",
			        dataType: 'json',
			        type: 'POST',
			        done: function (e, data) {
			        	if (data.result.success == 1) {
				        	$.each(data.files, function (index, file) {
				                $('<p/>').text(file.name).appendTo('#files');
				            });
			        	} else {
				        	alert("File Upload: Some problems occured!");
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
			{{ Form::label('files', 'Files') }}<button type="submit" id="open_file_upload" class="btn btn-xs btn-primary">Upload File</button>
			
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
								{{ Form::open(array('action' => array('PaperController@getDetails', 'id' => $file->id), 'method' => 'GET', 'style' => 'display:inline')) }}
									<button type="submit" class="btn btn-xs btn-primary">Details</button>
								{{ Form::close() }}
								{{ Form::open(array('action' => array('PaperController@anyEdit', 'id' => $file->id), 'style' => 'display:inline')) }}
									<button type="submit" class="btn btn-xs btn-primary">Edit</button>
									{{ Form::hidden('paperBackTarget', URL::action('PaperController@getIndex')) }}
								{{ Form::close() }}
							</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th>Title</th>
						<th>Abstract</th>
						<th>Action</th>
					 </tr>
				</tfoot>
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
						<!--<button type="button" class="btn btn-primary" id="upload_file">Save</button>-->
					</div>
				</div>
			</div>
		</div>
		{{ HTML::script('javascripts/jquery.ui.widget.js') }}
		{{ HTML::script('javascripts/jquery.fileupload.js') }}
@stop
