@extends('layouts/main')

@section('head')
		<meta http-equiv="refresh" content="3; URL={{ URL::action($action, array('id' => $id)) }}">
@stop

@section('content')
		<div class="page-header">
   		<h1>{{ $type }} @if($edited) Edited! @else Created! @endif</h1>
		</div>

		You should be forwarded to the details page automatically. If not, click {{ HTML::linkAction($action, 'here', array('id' => $id)) }}.
@stop
