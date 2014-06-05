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
	
	@yield('conf')
	<body>
		<div id="top_toolbar">
				<a class="navbar-brand" href="#">PhD Paper Tool</a>
				<form class="navbar-form navbar-left" role="search">
					<div class="form-group">
						<input type="text" id='search-bar' class="form-control" placeholder="Search">
					</div>
					<button type="submit" class="btn btn-default">Search</button>
				</form>
				<div class="header_link">
					@if (Auth::guest())
						{{ HTML::link('', 'Login', array('class' => 'btn btn-info')) }}
					@else		
						{{ HTML::link('logout', 'Logout', array('class' => 'btn btn-warning')) }}
					@endif
				</div>
		</div>
				
		<?php
			if (!isset($conf['currentPage'])) {
				$conf['currentPage'] = '';
			}
		?>		
		<div id="sticky_navigation_wrapper">
			<div id="sticky_navigation">
				<div id="main">
					<div style="width:500px" class="pull-right">        
						<ul >
							<li {{ ($conf['currentPage'] == 'timeline') ? 'class="active"' : '' }}>{{ HTML::link('timeline', 'Timeline') }}</li>
							<li {{ ($conf['currentPage'] == 'paper') ? 'class="active"' : '' }}>{{ HTML::link('paper', 'My Paper') }}</li>
							<li {{ ($conf['currentPage'] == 'data') ? 'class="active"' : '' }}>{{ HTML::link('data', 'My Review') }}</li>
							<li {{ ($conf['currentPage'] == 'profile') ? 'class="active"' : '' }}>{{ HTML::link('profile', 'My Profile') }}</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		@if (Auth::guest())
		<div class="jumbotron">
		  <h1>Welcome to PhD Paper Tool!</h1>
		  <p><a class="btn btn-primary btn-lg" role="button">Learn more</a></p>
		</div>
		@endif


		<div id='main'>
			@yield('content')
			
			<hr>
			<div style="text-align:center">
				<p>Designed and built with all the love in the world by <a href="" target="_blank">TU Darmstadt</a>.</p>
				<p>Maintained by the <a href="#">core team</a> with the help of <a href="#">our contributors</a>.</p>
				<p>Code licensed under <a href="https://github.com/twbs/bootstrap/blob/master/LICENSE" target="_blank">MIT</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
			</div>
		</div>
	</body>
</html>

