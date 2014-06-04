@extends('layouts/main')

@section('conf')
	<?php $conf['currentPage'] = 'profile'; ?>
@stop

@section('content')
		<div class="page-header">
   		<h1>Your Profile</h1>
		</div>

		<div class="row">
        <div class="col-xs-8">
        	@if (isset($msg))
        		@if ($msg['success'])
        			<div class="alert alert-success">{{ $msg['content'] }}</div>
        		@else
        			<div class="alert alert-danger">{{ $msg['content'] }}</div>
        		@endif
			@endif
					
            <form role="form" action="profile" method="POST">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" class="form-control" name="password" placeholder="Password">
                </div>

                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ $author['first_name'] }}">
                </div>

                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ $author['last_name'] }}">
                </div>

                <hr>
                <div class="uiButton">
                    <input type="submit" class="btn btn-primary btn-lg" value="Update">
                </div>
            </form>
        </div>
                <div class="col-xs-4">
            <div class="well">
                <div class="form-group">
                    <label>Email</label>
                    <p>{{ $user['email'] }}</p>
                </div>

				@if (isset($group['name']))
					<div class="form-group">
                 	   <label>Group</label>
                  	  <p>{{ $group['name'] }}</p>
                	</div>
                @endif
                
                <div class="form-group">
                    <label>Joined date</label>
                    <p>{{ $user['created_at'] }}</p>
                </div>
            </div>
        </div>
            </div>
@stop
