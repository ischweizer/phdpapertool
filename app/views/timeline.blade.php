@extends('layouts/main')

@section('head')
		<script src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
		<script src="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.js"></script>

		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/28e7751dbec/integration/bootstrap/3/dataTables.bootstrap.css">
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
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

		<div class="page-header">
   		<h1>Timeline</h1>
		</div>

<h3 class="cat-title">Interactive Paper Timeline</h3>
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
	  , minExtent = brush.extent()[0].getTime()
	  , maxExtent = brush.extent()[1].getTime()
	  , visItems = items.filter(function (d) { return d.start < maxExtent && d.end > minExtent});

	//mini.select('.brush').call(brush.extent([minExtent, maxExtent]));		

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

	mini.select('.brush').call(brush.extent([start,end]));
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
<h3 class="cat-title">Papers</h3>
			<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
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

			<hr>
			<div style="text-align:center">
				 <p>Designed and built with all the love in the world by <a href="" target="_blank">TU Darmstadt</a>.</p>
				 <p>Maintained by the <a href="#">core team</a> with the help of <a href="#">our contributors</a>.</p>
				 <p>Code licensed under <a href="https://github.com/twbs/bootstrap/blob/master/LICENSE" target="_blank">MIT</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
			</div>
		</div>
@stop
