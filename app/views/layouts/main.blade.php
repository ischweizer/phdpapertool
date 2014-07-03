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
		{{ HTML::script('javascripts/jquery-2.1.1.min.js'); }}
		{{ HTML::style('stylesheets/bootstrap.min.css'); }}
		{{ HTML::style('stylesheets/bootstrap-theme.min.css'); }}
		{{ HTML::script('javascripts/bootstrap.min.js'); }}
		{{ HTML::script('javascripts/script.js'); }}
		{{ HTML::style('stylesheets/style.css'); }}
		@yield('head')
	</head>

	<body>
		<div id="top_toolbar">
				<a class="navbar-brand" href="/">PhD Paper Tool</a>
				{{--<form class="navbar-form navbar-left" role="search">
					<div class="form-group">
						<input type="text" id='search-bar' class="form-control" placeholder="Search">
					</div>
					<button type="submit" class="btn btn-default">Search</button>
				</form>--}}
				<div class="header_link">
					@if (Auth::guest())
						{{ HTML::link('', 'Login', array('class' => 'btn btn-info')) }}
					@else		
						{{{ Auth::user()->email }}}
						{{ HTML::link('logout', 'Logout', array('class' => 'btn btn-warning')) }}
					@endif
				</div>
		</div>

		@if (Auth::check())
		<div id="sticky_navigation_wrapper">
			<div id="sticky_navigation">
				<div class="container">      
					<ul class="pull-right nav nav-pills">
						<li {{ (Route::current()->uri() == 'timeline') ? 'class="active"' : '' }}>{{ HTML::link('timeline', 'Timeline') }}</li>
						<li {{ (Route::current()->uri() == 'paper') ? 'class="active"' : '' }}>{{ HTML::link('paper', 'My Paper') }}</li>
						{{--<li {{ (Route::current()->uri() == 'data') ? 'class="active"' : '' }}>{{ HTML::link('data', 'My Review') }}</li>--}}
						<li {{ (Route::current()->uri() == 'profile') ? 'class="active"' : '' }}>{{ HTML::link('profile', 'My Profile') }}</li>
						<li {{ (Route::current()->uri() == 'conferences') ? 'class="active"' : '' }}>{{ HTML::link('event', 'Conferences') }}</li>
						@if (Auth::user()->isAdmin())
							<li {{(Route::current()->uri() == 'handle') ? 'class="active"' : '' }}>{{ HTML::link('handle', 'Admin')}}</li>
						@endif
					</ul>
				</div>
			</div>
		</div>
		@else
		<div class="jumbotron">
		  <h1>Welcome to PhD Paper Tool!</h1>
		  <p><a class="btn btn-primary btn-lg" role="button">Learn more</a></p>
		</div>
		@endif


		<div id='main' class="container">
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

