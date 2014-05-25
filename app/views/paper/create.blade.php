@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				$('#add_author').click(function(){
					var authorId = $( "#authorlist" ).val();
					var name = $( "#authorlist" ).text();
					
					$('#selected_authors').append(
				        $('<option></option>').val(authorId).html(name)
				    );
				    
				    $("#authorlist option[value='"+authorId+"']").remove();
				});
			});
		</script>
@stop

@section('content')

	<div id='main'>

		<div class="page-header">
   			<h1>Papers</h1>
   			<a href="/paper/index">Back</a>
		</div>

		<h3 class="cat-title">Create Paper</h3>
		{{ Form::open(array('url' => 'paper/create')) }}
		    <!-- title -->
			{{ Form::label('title', 'Title') }}<br>
			{{ Form::text('title', '', array('placeholder' => 'Title', 'class' => 'form-control')) }}<br>
			
			<!-- repository -->
			{{ Form::label('repository_url', 'Repository') }}<br>
			{{ Form::text('repository_url', '', array('placeholder' => 'Repository', 'class' => 'form-control')) }}<br>
			
			<!-- authors -->
			{{ Form::label('authors', 'Authors') }}<br>
			{{ Form::select('authorlist', $authors, null, array('id' => 'authorlist')) }}
			{{ Form::button('Add', array('id' => 'add_author')) }}<br><br>
			{{ Form::select('selectedauthors', [], null, array('size' => 10, 'class' => 'form-control', 'id' => 'selected_authors')) }}<br>
			
			<!-- abstract -->
			{{ Form::label('abstract', 'Abstract') }}<br>
			{{ Form::textarea('abstract', '', array('placeholder' => 'Abstract', 'class' => 'form-control')) }}<br>
			
			{{ Form::submit('Create new paper', array('class' => 'btn btn-lg btn-primary')) }}
		{{ Form::close() }}

	</div>
@stop
