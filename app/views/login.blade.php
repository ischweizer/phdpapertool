@extends('layouts/main')

@section('content')
		<div class="page-header">
   		<h1>Account Login</h1>
		</div>

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
