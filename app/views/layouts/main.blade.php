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
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css" rel="stylesheet">
		<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		{{ HTML::script('javascripts/script.js'); }}
		{{ HTML::style('stylesheets/style.css'); }}
		@yield('head')
	</head>
	<body>
		<div id='main'>
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">PhD Paper Tool</a>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<form class="navbar-form navbar-left" role="search">
						<div class="form-group">
							<input type="text" id='search-bar' class="form-control" placeholder="Search">
						</div>
						<button type="submit" class="btn btn-default">Search</button>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li>{{ HTML::link('logout', 'Logout') }}</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Tools <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#">Action</a></li>
								<li><a href="#">Another action</a></li>
								<li><a href="#">Something else here</a></li>
								<li class="divider"></li>
								<li><a href="#">Separated link</a></li>
							</ul>
						</li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
			</nav>
		</div>
		<div id="sticky_navigation_wrapper">
			<div id="sticky_navigation">
				<div id="main">
					<div style="width:615px" class="pull-right">        
						<ul >
							<li>{{ HTML::link('timeline', 'Timeline') }}</li>
							<li>{{ HTML::link('overview', 'Overview') }}</li>
							<li>{{ HTML::link('paper', 'Paper') }}</li>
							<li>{{ HTML::link('data', 'Data Manager') }}</li>
							<li>{{ HTML::link('profile', 'Your Profile') }}</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		@yield('content')
	</body>
</html>

