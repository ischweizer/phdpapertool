@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.2/typeahead.bundle.min.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">

		<script>
			$(document).ready(function() {
				$('#conferences').dataTable({
					"processing": true,
					"serverSide": true,
					"sAjaxSource": "{{ URL::to('conferences/data') }}"
				});

				var conferences = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: "{{ URL::to('conferences/autocomplete?q=%QUERY') }}"
				});
				conferences.initialize();
				$('#remote .typeahead').typeahead({
				}, {
					name: 'conferences',
					displayKey: 'name',
					source: conferences.ttAdapter()
				});
			});
		</script>
		<style type="text/css">
		/* move into CSS file some day... */

.twitter-typeahead {
  width: 100%;
}

#remote input {
  width: 100%;
}

.tt-hint {
  color: #999
}

.tt-dropdown-menu {
  width: 100%;
  margin-top: 8px;
  padding: 6px 0;
  background-color: #fff;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-border-radius: 6px;
     -moz-border-radius: 6px;
          border-radius: 6px;
  -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
     -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
          box-shadow: 0 5px 10px rgba(0,0,0,.2);
}

.tt-suggestion {
  padding: 3px 15px;
}

.tt-suggestion.tt-cursor {
  color: #fff;
  background-color: #0097cf;
}

.tt-suggestion p {
  margin: 0;
}
		</style>
@stop

@section('content')

		<div id='main'>

		<div class="page-header">
   		<h1>Conferences</h1>
		</div>

		<h3 class="cat-title">Auto Complete Selection</h3>
		<div id="remote">
			<input class="form-control typeahead" type="text" placeholder="Conference">
		</div>

		<h3 class="cat-title">Conference Table</h3>
		<table id="conferences" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Acronym</th>
					<th>CORE 2013 Ranking</th>
					<th>Field of Research</th>
				</tr>
			</thead>
	 
			<tfoot>
				<tr>
					<th>Name</th>
					<th>Acronym</th>
					<th>CORE 2013 Ranking</th>
					<th>Field of Research</th>
				 </tr>
			</tfoot>
	 	</table>

		</div>
@stop
