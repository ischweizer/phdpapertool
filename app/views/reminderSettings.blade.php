@extends('layouts/main')

@section('head')
	<script type="text/javascript">
	    
	</script>
@stop

@section('content')
    
    <div class="page-header">
	<h1>Reminder Settings</h2>
    </div>
    
    @if (isset($saved) && $saved)
	<div class="alert alert-info">Your changes have been saved</div>
    @endif

    <form role="form" action="{{ action('ReminderSettingsController@saveSettings') }}" method="POST">
	@foreach($settings as $tableName => $isChecked)
	    <div class="form-group">
		<div class="checkbox">
		    <label>
			<input type="checkbox" name="{{$tableName}}" {{{$isChecked ? 'checked' : ''}}}> {{str_replace('_', ' ', ucfirst($tableName))}}
		    </label>
		</div>
	    </div>
	@endforeach
	<div class="uiButton">
	    <input type="submit" class="btn btn-primary btn-lg" value="Save">
	</div>
    </form>
@stop