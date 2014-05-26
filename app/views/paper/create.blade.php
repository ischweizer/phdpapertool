@extends('layouts/main')

@section('head')
		<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>

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
			});
			
			var checkform = function(){
				$('#selected_authors option').prop('selected', true);
			}
		</script>
@stop

@section('content')

	<div id='main'>

		<div class="page-header">
   			<h1>Papers</h1>
			{{ HTML::linkAction('PaperController@getIndex', 'Back') }}
		</div>

		<h3 class="cat-title">Create Paper</h3>
		{{ Form::open(array('action' => array('PaperController@postCreate', $paper->id), 'onsubmit' => 'checkform()')) }}
		    <!-- title -->
			{{ Form::label('title', 'Title') }}<br>
			{{ Form::text('title', $paper->title, array('placeholder' => 'Title', 'class' => 'form-control')) }}<br>
			
			<!-- repository -->
			{{ Form::label('repository_url', 'Repository') }}<br>
			{{ Form::text('repository_url', $paper->repository_url, array('placeholder' => 'Repository', 'class' => 'form-control')) }}<br>
			
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
			{{ Form::label('selectedauthors', 'Selected Authors') }}<br>
			{{ Form::select('selectedauthors[]', $selectedauthors, null, array('size' => 10, 'class' => 'form-control', 'id' => 'selected_authors', 'multiple' => true)) }}<br>
			
			<!-- abstract -->
			{{ Form::label('abstract', 'Abstract') }}<br>
			{{ Form::textarea('abstract', $paper->abstract, array('placeholder' => 'Abstract', 'class' => 'form-control')) }}<br>
			
			<!-- submit -->
			{{ Form::submit('Create new paper', array('class' => 'btn btn-lg btn-primary')) }}<br>
		{{ Form::close() }}
	</div>
@stop
