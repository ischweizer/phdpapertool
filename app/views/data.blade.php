@extends('layouts/main')


@section('content')
		

		<div class="jumbotron">
		  <h1>Welcome to PhD Paper Tool!</h1>
		  <p><a class="btn btn-primary btn-lg" role="button">Learn more</a></p>
		</div>



		<div id='main'>
			<div class="page-header">
				<h1>Data Manager</h1>
			</div>

			<h3 class="cat-title">Uploaded Files</h3>
			<div class="row">
				<div class="col-xs-8">
						   <div class="btn-group pull-right">
						   <button class="btn btn-info"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Edit</button>
					  </div>
					  <div class="well">
						   <div class="row">
						       <div class="col-xs-4"><img width="200px" class="media-object" alt="Bild" style="cursor: pointer;" src="http://unseenflirtspoetry.files.wordpress.com/2012/05/homer-excited.png"></div>
						       <div class="col-xs-8">
						           <p><strong>Title:</strong><br>J. Cappel</p>
						           <p><strong>Description:</strong><br>This is my picture</p>
						       </div>
						   </div>
					  </div>
						   <div class="btn-group pull-right">
						   <button class="btn btn-info"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Edit</button>
					  </div>
					  <div class="well">
						   <div class="row">
						       <div class="col-xs-4"><img width="200px" class="media-object" alt="Bild" style="cursor: pointer;" src="http://unseenflirtspoetry.files.wordpress.com/2012/05/homer-excited.png"></div>
						       <div class="col-xs-8">
						           <p><strong>Title:</strong><br>Author</p>
						           <p><strong>Description:</strong><br>Identity Management</p>
						       </div>
						   </div>
					  </div>
						   <div class="btn-group pull-right">
						   <button class="btn btn-info"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Edit</button>
					  </div>
					  <div class="well">
						   <div class="row">
						       <div class="col-xs-4"><img width="200px" class="media-object" alt="Bild" style="cursor: pointer;" src="http://unseenflirtspoetry.files.wordpress.com/2012/05/homer-excited.png"></div>
						       <div class="col-xs-8">
						           <p><strong>Title:</strong><br>Test</p>
						           <p><strong>Description:</strong><br>123</p>
						       </div>
						   </div>
					  </div>  
				</div>
			</div>

			<button class="btn btn-success" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Upload file
			</button>

			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
				 <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					  <h4 class="modal-title" id="myModalLabel">Modal title</h4>
					</div>
					<div class="modal-body">
					  <div class="form-group">
						                       <label>Title *</label>
						                       <input type="text" class="form-control">
						                   </div>
						                   <div class="form-group">
						                       <label>Description *</label>
						                       <input type="text" class="form-control">
						                   </div>
						                   <div id="steckbrief-filebrowser" class="form-group">
						                       <label>File *</label>
						                       <input type="file" class="form-control"/>
		
						                   </div>
					</div>
					<div class="modal-footer">
					  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					  <button type="button" class="btn btn-primary">Upload</button>
					</div>
				 </div>
			  </div>
			</div>

			<hr>
			<div style="text-align:center">
				 <p>Designed and built with all the love in the world by <a href="" target="_blank">TU Darmstadt</a>.</p>
				 <p>Maintained by the <a href="#">core team</a> with the help of <a href="#">our contributors</a>.</p>
				 <p>Code licensed under <a href="https://github.com/twbs/bootstrap/blob/master/LICENSE" target="_blank">MIT</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
			</div>
		</div>
@stop