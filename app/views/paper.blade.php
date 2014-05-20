<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Laravel PHP Framework</title>
</head>
<body>
	{{ Form::open(array('url' => 'paper/create')) }}
	    <!-- title -->
		{{ Form::label('title', 'Title') }}<br>
		{{ Form::text('title') }}<br>
		
		<!-- abstract -->
		{{ Form::label('abstract', 'Abstract') }}<br>
		{{ Form::textarea('abstract') }}<br>
		
		{{ Form::submit('Create new paper') }}
	{{ Form::close() }}
</body>
</html>
