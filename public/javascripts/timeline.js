var Timeline = new function() {
	var _tableUrl, _graphUrl, _groups, _papers, _reviews, _table;

	this.init = function(tableUrl, graphUrl) {
		_tableUrl = tableUrl;
		_graphUrl = graphUrl;
		_table = $('#paper-table').DataTable({
			'stateSave': true,
			'stateDuration': -1,
			'lengthMenu': [5, 10, 25, 50],
			'pageLength': 10,
			'order': [[ 2, 'asc' ]],
			'columnDefs': [
				{ 'targets': [0], 'visible': false, 'searchable': false },
				{ 'targets': [6], 'searchable': false }
			]
		});

		$(window).resize(function() {
			Timeline.draw(TimelineData);
		});

		$('#paper-table').on('draw.dt', this.loadGraph);
	}

	this.loadGraph = function() {
		var papers = _table.rows($('#paper-table tbody tr')).data().map(function(row){return row[0]}).join(',');
		var reviews = (_groups == '');
		if (papers != _papers || reviews != _reviews) {
			_papers = papers;
			_reviews = reviews;

			var reviewParam = '';
			if (_reviews) {
				reviewParam = '&reviews=1';
			}

			$.ajax({
				'global': false,
				'url': _graphUrl + '?paperIds=' + _papers + reviewParam,
				'dataType': 'json',
				'success': function (data) {
					data.items.forEach(function(entry) {
						entry.start = new Date(entry.start);
						entry.end = new Date(entry.end);
					});

					TimelineData = data;
					Timeline.draw(data);
				}
			});
		}
	}

	this.loadTable = function(groups) {
		if (groups != _groups) {
			_groups = groups;
			$.ajax({
				'global': false,
				'url': _tableUrl + '?groupIds=' + groups,
				'dataType': 'json',
				'success': function (data) {
					_table.destroy();
					$('#paper-table tbody').html('');
					$.each(data, function(index, item) {
						$(item).appendTo('#paper-table tbody');
					});
					_table = $('#paper-table').DataTable({
						'stateSave': true,
						'stateDuration': -1,
						'stateLoadParams': function (settings, data) {
							data.iStart = 0; // start at 0 on group change
						},
						'lengthMenu': [5, 10, 25, 50],
						'pageLength': 10,
						'columnDefs': [
							{ 'targets': [0], 'visible': false, 'searchable': false },
							{ 'targets': [2, 3, 4, 5], 'type': 'date', 'data': function (row, type, val, meta) {
								if (typeof type === 'undefined') {
									return '';
								}
								if (type === 'set') {
									if (val === '') {
										val = row[meta.col];
									}
									if (typeof row.sortDate === 'undefined') {
										row.sortDate = new Object();
										row.filterDate = new Object();
									}
									var date = val;
									var dateString = val;
									if (dateString != '') {
										if (dateString.indexOf('<') > -1) {
											dateString = dateString.substring(0, dateString.indexOf('<'));
										}
										date = (dateString == '') ? '' : $.fn.datepicker.DPGlobal.parseDate(dateString, "M dd, yyyy", $.fn.datepicker.defaults.language);
									}
									row.sortDate[meta.col] = date;
									row.filterDate[meta.col] = dateString;
								}
								if (type === 'filter') {
									return row.filterDate[meta.col];
								}
								if (type === 'sort') {
									return row.sortDate[meta.col];
								}
								return row[meta.col];
							}},
							{ 'targets': [6], 'searchable': false }
						]
					});
					_table.draw();
				}
			});
		}
	}

	this.draw = function(data) {
		$('#graph').html('');

		var lanes = data.lanes, 
			items = data.items, 
			now = new Date();

		var margin = {top: 20, right: 0, bottom: 15, left: 160},
			width = $('#main').width() - margin.left - margin.right,
			height = lanes.length * 28 + 10,//* 12 + 70,
			miniHeight = lanes.length * 28,//* 12 + 50,
			mainHeight = height - miniHeight - 50;

		var x = d3.time.scale().clamp(true);
		if(typeof timelineFrom != "undefined" && typeof timelineTo != "undefined"){
			var monthMs = 2628000000;

			x
				.domain([d3.time.day(new Date(now.getTime()+timelineFrom*monthMs)),
					d3.time.day(new Date(now.getTime()+timelineTo*monthMs))])
				.range([0, width]);
		} else {
			x
				.domain([d3.time.sunday(d3.min(items, function(d) { return d.start; })),
					d3.max(items, function(d) { return d.end; })])
				.range([0, width]);
		}

		var visibleItems = items.filter(function (d) { return d.start < x.domain()[1] && d.end > x.domain()[0] });
		var visibleComplexItems = visibleItems.filter(function (d) { return d.hasOwnProperty('complex') && d.complex });

		var ext = d3.extent(lanes, function(d) { return d.id; });
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
			.attr('stroke', function(d) { return d.label === '' ? 'white' : 'lightgray'; });

		mini.append('g').selectAll('.laneText')
			.data(lanes)
			.enter().append('foreignObject')
			.attr("width", "150")
			.attr("height", d3.round(y2(1)))
			.attr('x', -160)
			.attr('y', function(d) { return y2(d.id); })
			.attr('dy', '0.5ex')
			.attr('text-anchor', 'end')
			.append("xhtml:span")
			.attr('class', function(d) { 
				if (d.label.length > 20) {
					return 'laneText long-title';
				} else { 
					return 'laneText';
				}
			})
			.attr('style', 'line-height: ' + d3.round(y2(1)) + 'px;')
			.attr('title', function(d) { return d.label; })
    		.text(function(d) {
				if (d.label.length > 20) {
					return d.label.substr(0, 18) + ' ...';
				} else { 
					return d.label;
				}
			});

		// draw the x axis
		var xDateAxis = d3.svg.axis()
			.scale(x)
			.orient('bottom')
			.ticks(d3.time.mondays, (x.domain()[1] - x.domain()[0]) > 15552e6 ? 2 : 1)
			.tickFormat(d3.time.format('%d'))
			.tickSize(6, 0, 0);

		var xMonthAxis = d3.svg.axis()
			.scale(x)
			.orient('top')
			.ticks(d3.time.months, 1)
			.tickFormat(d3.time.format('%b %Y'))
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
	
		if (x.domain()[0] < now && x.domain()[1] > now) {
			mini.append('line')
				.attr('x1', x(now) + 0.5)
				.attr('y1', 0)
				.attr('x2', x(now) + 0.5)
				.attr('y2', miniHeight)
				.attr('class', 'todayLine');
		}

		mini.append('g').selectAll('miniItems')
			.data(Timeline.getPaths(x, y2, visibleItems))
			.enter().append('path')
			.attr('class', function(d) { return 'miniItem ' + d.class; })
			.attr('d', function(d) { return d.path; });

		var offsetComplexItem = 0.5 * y2(1) - 2.5;
		// for more complex elements generate one rect for each
		mini.append('g').selectAll('complexItems')
			.data(visibleComplexItems)
			.enter().append('rect')
			.attr('x', function(d) { return x(d.start); })
			.attr('y', function(d) { return y2(d.lane) + offsetComplexItem; })
			.attr('width', function(d) { return x(d.end) - x(d.start); })
			.attr('height', function(d) { return 6; })
			.attr('class', function(d) { return 'complexItem ' + d.class; })
			.attr('data-toggle', 'popover')
			.attr('title', function(d) { return d.desc; })
			.attr('data-content', function(d) { return '<a href="' + d.link + '">' + d['link-desc'] + '</a>'; });

		$('.long-title').tooltip({
			animated : 'fade',
			placement : 'top',
			container: '#graph'
		});
		$('[data-toggle=popover]').popover({
			animated : 'fade',
			placement : 'top',
			container: '#graph',
			html: true
		});
	};

	// generates a single path for each item class in the mini display
	// ugly - but draws mini 2x faster than append lines or line generator
	// is there a better way to do a bunch of lines as a single path with d3?
	this.getPaths = function(x, y2, items) {
		var paths = {}, d, offset = 0.5 * y2(1) + 0.5, result = [];
		for (var i = 0; i < items.length; i++) {
			d = items[i];
			if (d.complex)
				continue;

			if (!paths[d.class]) paths[d.class] = '';	

			var temp = x(d.start);
			

			paths[d.class] += ['M',x(d.start),(y2(d.lane) + offset),'H',x(d.end)].join(' ');
			//console.log(paths[d.class]);
		}

		for (var className in paths) {
			//console.log(paths[className]);
			result.push({class: className, path: paths[className]});
		}
		return result;
	};

};
