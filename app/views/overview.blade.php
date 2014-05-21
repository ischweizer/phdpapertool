@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/e9421181788/integration/jqueryui/dataTables.jqueryui.js"></script>

		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/e9421181788/integration/jqueryui/dataTables.jqueryui.css">

		<script>
		jQuery(document).ready(function() {
			$(document).ready(function() {
				 $('#example').dataTable();
			});

			$(document).ready(function() {
				 $('#example2').dataTable();
			});
		});
		</script>
@stop

@section('content')

		<div class="jumbotron">
		  <h1>Welcome to PhD Paper Tool!</h1>
		  <p><a class="btn btn-primary btn-lg" role="button">Learn more</a></p>
		</div>

<div id="sticky_navigation">
		<div id="main">
			<div style="width:500px" class="pull-right">        
                <ul >
                    <li><a href="timeline.html">Timeline</a></li>
						<li class="active"><a href="overview.html">Overview</a></li>
						<li><a href="data.html">Data Manager</a></li>
						<li><a href="profile.html">Your Profile</a></li>
                </ul>
            </div>
        </div>
   </div>

		<div id='main'>

			<div class="page-header">
				<h1>Overview</h1>
			</div>

			<h3 class="cat-title">Time</h3>
			<div class="well">
				<table id="example" class="display" cellspacing="0" width="100%">
					<thead>
				      <tr>
				          <th>Date</th>
				          <th>Event</th>
				          <th>Status</th>
				          <th>Description</th>
				      </tr>
				  </thead>
		 
				  <tfoot>
				      <tr>
				          <th>Date</th>
				          <th>Event</th>
				          <th>Status</th>
				          <th>Description</th>
				      </tr>
				  </tfoot>
		 
				  <tbody>
				      <tr>
						      <td>2011/04/25</td>
						      <td>Submit Paper 1</td>
								<td>Closed</td>
								<td></td>
				      </tr>
				      <tr>
						      <td>2011/07/25</td>
						      <td>Review Paper 1</td>
								<td>Closed</td>
								<td>1. Attempt</td>
				      </tr>
				      <tr>
						      <td>2011/08/12</td>
						      <td>Submit Paper 2</td>
								<td>Closed</td>
								<td></td>
				      </tr>
				      <tr>
						      <td>2012/03/29</td>
						      <td>Review Paper 2</td>
								<td>Processing</td>
								<td>1. Attempt</td>
				      </tr>
				      <tr>
						      <td>2012/11/28</td>
						      <td>Upload Paper 1's Camera Ready</td>
								<td>Open</td>
								<td></td>
				      </tr>
				  
					</tbody>
				</table>
			</div>

			<h3 class="cat-title">Paper</h3>
			<div class="well">
				<table id="example2" class="display" cellspacing="0" width="100%">
				  <thead>
				      <tr>
				          <th>Paper</th>
				          <th>Submit</th>
				          <th>Review</th>
				          <th>Camera Ready</th>
							 <th>Complete</th>
				      </tr>
				  </thead>
		 
				  <tfoot>
				      <tr>
				          <th>Paper</th>
				          <th>Submit</th>
				          <th>Review</th>
				          <th>Camera Ready</th>
							<th>Complete</th>
				      </tr>
				  </tfoot>
		 
				  <tbody>
				      <tr>
								<td>Paper 1</td>
						      <td>2011/04/25</td>
								<td>2011/05/25</td>
								<td>2011/06/25</td>
								<td>2011/06/25</td>
				      </tr>
				      <tr>
								<td>Paper 2</td>
						      <td>2011/07/25</td>
								<td>2012/04/25</td>
								<td>2013/04/25</td>
								<td>2011/06/25</td>
				      </tr>
						<tr>
								<td>Paper 3</td>
						      <td>2011/07/25</td>
								<td>2011/04/26</td>
								<td>2011/04/27</td>
								<td>2011/06/25</td>
				      </tr>
						<tr>
								<td>Paper 4</td>
						      <td>2011/07/25</td>
								<td>2011/10/25</td>
								<td>2011/11/25</td>
								<td>2011/06/25</td>
				      </tr>

					</tbody>
				</table>
			</div>

			<hr>
			<div style="text-align:center">
				 <p>Designed and built with all the love in the world by <a href="" target="_blank">TU Darmstadt</a>.</p>
				 <p>Maintained by the <a href="#">core team</a> with the help of <a href="#">our contributors</a>.</p>
				 <p>Code licensed under <a href="https://github.com/twbs/bootstrap/blob/master/LICENSE" target="_blank">MIT</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
			</div>
		</div>
@stop
