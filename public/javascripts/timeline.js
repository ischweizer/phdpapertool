var Timeline = new function() {
	this.load = function(url) {
		$.ajax({
			'global': false,
			'url': url,
			'dataType': "json",
			'success': function (data) {
				data.items.forEach(function(entry) {
					entry.start = new Date(entry.start);
					entry.end = new Date(entry.end);
				});
				
				$('#graph').html('');
				Timeline.draw(data);
			}
		});
	}; 
	
	this.draw = function(data) {
		var lanes = data.lanes
		  , items = data.items
		  , now = new Date();

		var margin = {top: 20, right: 15, bottom: 15, left: 160}
		  , width = 960 - margin.left - margin.right
		  , height = lanes.length * 12 + 70
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

		var mini = chart.append('g')
			.attr('transform', 'translate(' + margin.left + ',' + (mainHeight + 60) + ')')
			.attr('width', width)
			.attr('height', miniHeight)
			.attr('class', 'mini');

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
			.enter().append('foreignObject')
			.attr("width", "140")
			.attr("height", "70")
			.html(function(d) { 
				return "<p align='right'>"+d.label+"</p>";
			})
			.attr('x', -160)
			.attr('y', function(d) { return y2(d.id); })
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
	
		mini.append('line')
			.attr('x1', x(now) + 0.5)
			.attr('y1', 0)
			.attr('x2', x(now) + 0.5)
			.attr('y2', miniHeight)
			.attr('class', 'todayLine');

		mini.append('g').selectAll('miniItems')
			.data(Timeline.getPaths(x, y2, items))
			.enter().append('path')
			.attr('class', function(d) { return 'miniItem ' + d.class; })
			.attr('d', function(d) { return d.path; });
	}

	// generates a single path for each item class in the mini display
	// ugly - but draws mini 2x faster than append lines or line generator
	// is there a better way to do a bunch of lines as a single path with d3?
	this.getPaths = function(x, y2, items) {
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
}