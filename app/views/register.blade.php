@extends('layouts/main')

@section('content')
		<div class="page-header">
   		<h1>Account Registration</h1>
		</div>

		<div class="row">
			<div class="col-xs-8">
				@if ($mode == 'register')
					<form role="form" action="register" method="POST">
						<div class="form-group">
							<label>Email *</label>
							<input type="email" class="form-control" name="email" placeholder="Email">
						</div>

						<div class="form-group">
							<label>First Name *</label>
							<input type="name" class="form-control" name="firstName" placeholder="First Name">
						</div>

						<div class="form-group">
							<label>Last Name *</label>
							<input type="name" class="form-control" name="lastName" placeholder="Last Name">
						</div>

						<div class="form-group">
							<label>Password *</label>
							<input type="password" class="form-control" name="password" placeholder="Password">
						</div>

						<div class="form-group">
							<label>Repeat Password *</label>
							<input type="password" class="form-control" name="repeatPassword" placeholder="Repeat Password">
						</div>

						<hr>
						<div class="uiButton">
							<input type="submit" class="btn btn-primary btn-lg" value="Register">
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
					
				@endif
			</div>    
        </div>
@stop
