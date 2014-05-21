@extends('layouts/main')

@section('head')
		{{ HTML::script('static/javascripts/script.js'); }}
		{{ HTML::style('static/stylesheets/style.css'); }}
@stop

@section('content')
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
				  <li><a href="#">Logout</a></li>
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


		<div class="jumbotron">
		  <h1>Welcome to PhD Paper Tool!</h1>
		  <p><a class="btn btn-primary btn-lg" role="button">Learn more</a></p>
		</div>

	<div id="sticky_navigation">
		<div id="main">
			<div style="width:500px" class="pull-right">        
                <ul >
                    <li><a href="timeline.html">Timeline</a></li>
						<li><a href="overview.html">Overview</a></li>
						<li><a href="data.html">Data Manager</a></li>
						<li class="active"><a href="profile.html">Your Profile</a></li>
                </ul>
            </div>
        </div>
   </div>

	<div id='main'>

		<div class="page-header">
   		<h1>Your Profile</h1>
		</div>

<div class="row">
        <div class="col-xs-8">
            <form role="form" action="" method="POST">
                <div class="form-group">
                    <label>Institution *</label>
                    <input type="text" class="form-control" placeholder="Institution" value="TU Darmstadt">
                </div>

                
                <div class="form-group">
                    <label>Password *</label>
                    <input type="text" class="form-control" placeholder="Password">
                </div>

                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" class="form-control" name="tx_nuacore_piusermanager[first_name]" placeholder="Vorname" value="Hermann">
                </div>

                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" class="form-control" name="tx_nuacore_piusermanager[last_name]" placeholder="Nachname" value="Hanser">
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="text" class="form-control" name="tx_nuacore_piusermanager[email]" placeholder="Email" value="info@world.com">
                </div>

                <div class="row">
                    <div class="col-xs-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked="checked"> Newsletter
                            </label>
                        </div>
                    </div>

                <div class="col-xs-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"> Info
                                </label>
                            </div>
                        </div>
                </div>

                <hr>
                <div class="uiButton">
                    <input type="submit" class="btn btn-primary btn-lg" value="Save">
                </div>
            </form>
        </div>
                <div class="col-xs-4">
            <div class="well">
                <div class="form-group">
                    <label>Username</label>
                    <p>superman01</p>
                </div>

                <div class="form-group">
                    <label>Group</label>
                    <p>ABC</p>
                </div>
                <div class="form-group">
                    <label>Conference</label>
                    <p>Open World 2015</p>
                </div>
                <div class="form-group">
                    <label>Joined date</label>
                    <p>20.03.2014</p>
                </div>

                <div class="form-group">
                    <label>Last login</label>
                    <p>20.03.2014</p>
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
