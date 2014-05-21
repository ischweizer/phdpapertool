@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/e9421181788/integration/jqueryui/dataTables.jqueryui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/e9421181788/integration/jqueryui/dataTables.jqueryui.css">
		{{ HTML::script('static/javascripts/script.js'); }}
		{{ HTML::style('static/stylesheets/style.css'); }}
		<script type="text/javascript" src="http://mbostock.github.com/d3/d3.v2.js"></script>
		<style>
			.chart {
				shape-rendering: crispEdges;
			}

			.mini text {
				font: 9px sans-serif;	
			}

			.main text {
				font: 12px sans-serif;	
			}

			.month text {
				text-anchor: start;
			}

			.todayLine {
				stroke: blue;
				stroke-width: 1.5;
			}

			.axis line, .axis path {
				stroke: black;
			}

			.miniItem {
				stroke-width: 6;	
			}

			.future {
				stroke: gray;
				fill: #ddd;
			}

			.past {
				stroke: green;
				fill: lightgreen;
			}

			.brush .extent {
				stroke: gray;
				fill: blue;
				fill-opacity: .165;
			}
		</style>
		<script>
			jQuery(document).ready(function() {
				$(document).ready(function() {
					 $('#example').dataTable();
				});
			});
		</script>
		<script src="http://bl.ocks.org/bunkat/raw/1962173/b7d7134dbc15912619cc21c7fdc2bb78864a8daa/randomData.js"></script>
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
		               <li class="active"><a href="timeline.html">Timeline</a></li>
							<li><a href="overview.html">Overview</a></li>
							<li><a href="data.html">Data Manager</a></li>
							<li><a href="profile.html">Your Profile</a></li>
		             </ul>
		         </div>
		     </div>
		</div>

		<div id='main'>

		<div class="page-header">
   		<h1>Timeline</h1>
		</div>

<h3 class="cat-title">Interactive Conference Timeline</h3>
<div id="graph">

<script type="text/javascript">

var data = randomData()
  , lanes = data.lanes
  , items = data.items
  , now = new Date();

var margin = {top: 20, right: 15, bottom: 15, left: 60}
  , width = 960 - margin.left - margin.right
  , height = 500 - margin.top - margin.bottom
  , miniHeight = lanes.length * 12 + 50
  , mainHeight = height - miniHeight - 50;

var x = d3.time.scale()
	.domain([d3.time.sunday(d3.min(items, function(d) { return d.start; })),
			 d3.max(items, function(d) { return d.end; })])
	.range([0, width]);
var x1 = d3.time.scale().range([0, width]);

var ext = d3.extent(lanes, function(d) { return d.id; });
var y1 = d3.scale.linear().domain([ext[0], ext[1] + 1]).range([0, mainHeight]);
var y2 = d3.scale.linear().domain([ext[0], ext[1] + 1]).range([0, miniHeight]);

var chart = d3.select('#graph')
	.append('svg:svg')
	.attr('width', width + margin.right + margin.left)
	.attr('height', height + margin.top + margin.bottom)
	.attr('class', 'chart');

chart.append('defs').append('clipPath')
	.attr('id', 'clip')
	.append('rect')
		.attr('width', width)
		.attr('height', mainHeight);

var main = chart.append('g')
	.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')
	.attr('width', width)
	.attr('height', mainHeight)
	.attr('class', 'main');

var mini = chart.append('g')
	.attr('transform', 'translate(' + margin.left + ',' + (mainHeight + 60) + ')')
	.attr('width', width)
	.attr('height', miniHeight)
	.attr('class', 'mini');

// draw the lanes for the main chart
main.append('g').selectAll('.laneLines')
	.data(lanes)
	.enter().append('line')
	.attr('x1', 0)
	.attr('y1', function(d) { return d3.round(y1(d.id)) + 0.5; })
	.attr('x2', width)
	.attr('y2', function(d) { return d3.round(y1(d.id)) + 0.5; })
	.attr('stroke', function(d) { return d.label === '' ? 'white' : 'lightgray' });

main.append('g').selectAll('.laneText')
	.data(lanes)
	.enter().append('text')
	.text(function(d) { return d.label; })
	.attr('x', -10)
	.attr('y', function(d) { return y1(d.id + .5); })
	.attr('dy', '0.5ex')
	.attr('text-anchor', 'end')
	.attr('class', 'laneText');

// draw the lanes for the mini chart
mini.append('g').selectAll('.laneLines')
	.data(lanes)
	.enter().append('line')
	.attr('x1', 0)
	.attr('y1', function(d) { return d3.round(y2(d.id)) + 0.5; })
	.attr('x2', width)
	.attr('y2', function(d) { return d3.round(y2(d.id)) + 0.5; })
	.attr('stroke', function(d) { return d.label === '' ? 'white' : 'lightgray' });

mini.append('g').selectAll('.laneText')
	.data(lanes)
	.enter().append('text')
	.text(function(d) { return d.label; })
	.attr('x', -10)
	.attr('y', function(d) { return y2(d.id + .5); })
	.attr('dy', '0.5ex')
	.attr('text-anchor', 'end')
	.attr('class', 'laneText');

// draw the x axis
var xDateAxis = d3.svg.axis()
	.scale(x)
	.orient('bottom')
	.ticks(d3.time.mondays, (x.domain()[1] - x.domain()[0]) > 15552e6 ? 2 : 1)
	.tickFormat(d3.time.format('%d'))
	.tickSize(6, 0, 0);

var x1DateAxis = d3.svg.axis()
	.scale(x1)
	.orient('bottom')
	.ticks(d3.time.days, 1)
	.tickFormat(d3.time.format('%a %d'))
	.tickSize(6, 0, 0);

var xMonthAxis = d3.svg.axis()
	.scale(x)
	.orient('top')
	.ticks(d3.time.months, 1)
	.tickFormat(d3.time.format('%b %Y'))
	.tickSize(15, 0, 0);

var x1MonthAxis = d3.svg.axis()
	.scale(x1)
	.orient('top')
	.ticks(d3.time.mondays, 1)
	.tickFormat(d3.time.format('%b - Week %W'))
	.tickSize(15, 0, 0);

main.append('g')
	.attr('transform', 'translate(0,' + mainHeight + ')')
	.attr('class', 'main axis date')
	.call(x1DateAxis);

main.append('g')
	.attr('transform', 'translate(0,0.5)')
	.attr('class', 'main axis month')
	.call(x1MonthAxis)
	.selectAll('text')
		.attr('dx', 5)
		.attr('dy', 12);

mini.append('g')
	.attr('transform', 'translate(0,' + miniHeight + ')')
	.attr('class', 'axis date')
	.call(xDateAxis);

mini.append('g')
	.attr('transform', 'translate(0,0.5)')
	.attr('class', 'axis month')
	.call(xMonthAxis)
	.selectAll('text')
		.attr('dx', 5)
		.attr('dy', 12);

// draw a line representing today's date
main.append('line')
	.attr('y1', 0)
	.attr('y2', mainHeight)
	.attr('class', 'main todayLine')
	.attr('clip-path', 'url(#clip)');
	
mini.append('line')
	.attr('x1', x(now) + 0.5)
	.attr('y1', 0)
	.attr('x2', x(now) + 0.5)
	.attr('y2', miniHeight)
	.attr('class', 'todayLine');

// draw the items
var itemRects = main.append('g')
	.attr('clip-path', 'url(#clip)');

mini.append('g').selectAll('miniItems')
	.data(getPaths(items))
	.enter().append('path')
	.attr('class', function(d) { return 'miniItem ' + d.class; })
	.attr('d', function(d) { return d.path; });

// invisible hit area to move around the selection window
mini.append('rect')
	.attr('pointer-events', 'painted')
	.attr('width', width)
	.attr('height', miniHeight)
	.attr('visibility', 'hidden')
	.on('mouseup', moveBrush);

// draw the selection area
var brush = d3.svg.brush()
	.x(x)
	.extent([d3.time.monday(now),d3.time.saturday.ceil(now)])
	.on("brush", display);

mini.append('g')
	.attr('class', 'x brush')
	.call(brush)
	.selectAll('rect')
		.attr('y', 1)
		.attr('height', miniHeight - 1);

mini.selectAll('rect.background').remove();
display();

function display () {

	var rects, labels
	  , minExtent = d3.time.day(brush.extent()[0])
	  , maxExtent = d3.time.day(brush.extent()[1])
	  , visItems = items.filter(function (d) { return d.start < maxExtent && d.end > minExtent});

	mini.select('.brush').call(brush.extent([minExtent, maxExtent]));		

	x1.domain([minExtent, maxExtent]);

	if ((maxExtent - minExtent) > 1468800000) {
		x1DateAxis.ticks(d3.time.mondays, 1).tickFormat(d3.time.format('%a %d'))
		x1MonthAxis.ticks(d3.time.mondays, 1).tickFormat(d3.time.format('%b - Week %W'))		
	}
	else if ((maxExtent - minExtent) > 172800000) {
		x1DateAxis.ticks(d3.time.days, 1).tickFormat(d3.time.format('%a %d'))
		x1MonthAxis.ticks(d3.time.mondays, 1).tickFormat(d3.time.format('%b - Week %W'))
	}
	else {
		x1DateAxis.ticks(d3.time.hours, 4).tickFormat(d3.time.format('%I %p'))
		x1MonthAxis.ticks(d3.time.days, 1).tickFormat(d3.time.format('%b %e'))
	}


	//x1Offset.range([0, x1(d3.time.day.ceil(now) - x1(d3.time.day.floor(now)))]);

	// shift the today line
	main.select('.main.todayLine')
		.attr('x1', x1(now) + 0.5)
		.attr('x2', x1(now) + 0.5);

	// update the axis
	main.select('.main.axis.date').call(x1DateAxis);
	main.select('.main.axis.month').call(x1MonthAxis)
		.selectAll('text')
			.attr('dx', 5)
			.attr('dy', 12);

	// upate the item rects
	rects = itemRects.selectAll('rect')
		.data(visItems, function (d) { return d.id; })
		.attr('x', function(d) { return x1(d.start); })
		.attr('width', function(d) { return x1(d.end) - x1(d.start); });

	rects.enter().append('rect')
		.attr('x', function(d) { return x1(d.start); })
		.attr('y', function(d) { return y1(d.lane) + .1 * y1(1) + 0.5; })
		.attr('width', function(d) { return x1(d.end) - x1(d.start); })
		.attr('height', function(d) { return .8 * y1(1); })
		.attr('class', function(d) { return 'mainItem ' + d.class; });

	rects.exit().remove();

	// update the item labels
	labels = itemRects.selectAll('text')
		.data(visItems, function (d) { return d.id; })
		.attr('x', function(d) { return x1(Math.max(d.start, minExtent)) + 2; });
				
	labels.enter().append('text')
		.text(function (d) { return 'Item\n\n\n\n Id: ' + d.id; })
		.attr('x', function(d) { return x1(Math.max(d.start, minExtent)) + 2; })
		.attr('y', function(d) { return y1(d.lane) + .4 * y1(1) + 0.5; })
		.attr('text-anchor', 'start')
		.attr('class', 'itemLabel');

	labels.exit().remove();
}

function moveBrush () {
	var origin = d3.mouse(this)
	  , point = x.invert(origin[0])
	  , halfExtent = (brush.extent()[1].getTime() - brush.extent()[0].getTime()) / 2
	  , start = new Date(point.getTime() - halfExtent)
	  , end = new Date(point.getTime() + halfExtent);

	brush.extent([start,end]);
	display();
}

// generates a single path for each item class in the mini display
// ugly - but draws mini 2x faster than append lines or line generator
// is there a better way to do a bunch of lines as a single path with d3?
function getPaths(items) {
	var paths = {}, d, offset = .5 * y2(1) + 0.5, result = [];
	for (var i = 0; i < items.length; i++) {
		d = items[i];
		if (!paths[d.class]) paths[d.class] = '';	
		paths[d.class] += ['M',x(d.start),(y2(d.lane) + offset),'H',x(d.end)].join(' ');
	}

	for (var className in paths) {
		result.push({class: className, path: paths[className]});
	}

	return result;
}

</script>
</div>
<p>&nbsp;</p>
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

			<hr>
			<div style="text-align:center">
				 <p>Designed and built with all the love in the world by <a href="" target="_blank">TU Darmstadt</a>.</p>
				 <p>Maintained by the <a href="#">core team</a> with the help of <a href="#">our contributors</a>.</p>
				 <p>Code licensed under <a href="https://github.com/twbs/bootstrap/blob/master/LICENSE" target="_blank">MIT</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
			</div>
		</div>
@stop
