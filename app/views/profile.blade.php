@extends('layouts/main')

@section('content')
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
@stop
