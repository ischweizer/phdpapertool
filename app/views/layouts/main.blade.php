<!DOCTYPE html>
<html>
	<head>
		<title>
			@section('title') 
				PhD Paper Tool
			@show
		</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
		<link type="text/stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
		<link type="text/stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css" rel="stylesheet">
		<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		@yield('head')
	</head>
    <body>
        @yield('content')
    </body>
</html>

