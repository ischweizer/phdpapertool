@extends('layouts/main')

@section('content')
		<div id="phd-carousel" class="carousel slide" data-ride="carousel" style="width:1140px; margin: 0 auto;">
			<!-- Indicators -->
			<ol class="carousel-indicators">
				<li data-target="#phd-carousel" data-slide-to="0" class="active"></li>
				<li data-target="#phd-carousel" data-slide-to="1"></li>
			</ol>

			<!-- Wrapper for slides -->
			  <div class="carousel-inner">
				<div class="item active">
				  <img src="images/carousel/timeline.png">
				  <div class="carousel-caption">
					<p>Get a quick overview over your papers.</p>
				  </div>
				</div>
				<div class="item">
				  <img src="images/carousel/admin.png">
				  <div class="carousel-caption">
					<p>Manage your lab (and possibly view all papers of your lab).</p>
				  </div>
				</div>
			  </div>

			<!-- Controls -->
			<a class="left carousel-control" href="#phd-carousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left"></span>
			</a>
			<a class="right carousel-control" href="#phd-carousel" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right"></span>
			</a>
		</div>
		
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<div class="jumbotron" style="text-align:center; margin-bottom: 0px;padding-top: 15px;padding-bottom: 15px;">
		  <h1>Welcome to PhD Paper Tool!</h1>
		</div>

		<p></p>
		<div class="panel panel-success">
		  <div class="panel-heading">Account Login</div>
			  <div class="panel-body">	  
				<div class="row">
					<div class="col-xs-8">
					
				@if ($mode == 'login')					
					@if (isset($msg) && !$msg['success'])
						<div class="alert alert-danger">{{ $msg['content'] }}</div>
					@endif
					
					<form role="form" action="login" method="POST">
						<div class="form-group">
							<label>Email *</label>
							<input type="email" class="form-control" name="email" placeholder="Email" value="{{ isset($input['email']) ? $input['email'] : '' }}">
						</div>

						<div class="form-group">
							<label>Password *</label>
							<input type="password" class="form-control" name="password" placeholder="Password">
						</div>

						<div class="checkbox">
							<label>
								<input type="checkbox" value="remember-me" name="isRembered" {{ isset($input['isRembered']) ? 'checked' : '' }}> Remember me
							</label>
						</div>

						<div class="uiButton">
							<input type="submit" class="btn btn-primary btn-lg" value="Login">
							{{ HTML::link('registration', 'Registration', array('class' => 'btn btn-default btn-lg')) }}
						</div>
					</form>
				@else
    				<div class="alert 
					@if ($msg['success'])
						alert-success
					@else
						alert-danger
					@endif
					">{{ $msg['content'] }}</div>	
					<br>
					<p class="text-center">{{ HTML::link('', 'Login again', array('class' => 'btn btn-info')) }}</p>
				@endif	
				</div>    
        		</div>
			</div>    
        </div>
@stop
