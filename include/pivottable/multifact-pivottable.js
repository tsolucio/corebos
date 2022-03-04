// Custom Aggregators

(function () {
	var callWithJQuery;

	callWithJQuery = function (pivotModule) {
		if (typeof exports === "object" && typeof module === "object") {
			return pivotModule(require("jquery"));
		} else if (typeof define === "function" && define.amd) {
			return define(["jquery"], pivotModule);
		} else {
			return pivotModule(jQuery);
		}
	};

	callWithJQuery(function ($) {


		var multifactAggregator = function (aggMap, derivedAggregations) {

			var allAggregators = $.map(aggMap, function (aggregation, key) {
				var agg = $.pivotUtilities.aggregators[aggregation.aggType];
				var _numInputs = agg([])().numInputs || 0;
				return {
					aggregator: agg,
					selfNumInputs: _numInputs,
					name: aggregation.name,
					key: key,
					varName: aggregation.varName,
					hidden: aggregation.hidden
				}
			});

			return function (facts) {
				var aggregations = $.map(allAggregators, function (_agg) {
					return {
						aggregator: _agg.aggregator(aggMap[_agg.key].arguments),
						key: _agg.key,
						name: _agg.name,
						varName: _agg.varName,
						hidden: _agg.hidden
					};

				})


				return function (data, rowKey, colKey) {


					var finalAggregators = $.map(aggregations, function (_agg) {
						return {
							aggregator: _agg.aggregator(data, rowKey, colKey),
							key: _agg.key,
							name: _agg.name,
							varName: _agg.varName,
							hidden: _agg.hidden
						};

					});

					var _finalAggregatorsNameMap = {};
					for (var i = 0, x = finalAggregators.length; i < x; i++) {
						var aggregation = finalAggregators[i];
						_finalAggregatorsNameMap[aggregation.name] = aggregation;

					}

					var _finalDerivedAggregatorsNameMap = {};
					for (var i = 0, x = derivedAggregations.length; i < x; i++) {
						var derivedAggregation = derivedAggregations[i];
						_finalDerivedAggregatorsNameMap[derivedAggregation.name] = derivedAggregation;
					}
					if (!facts && !!data && !!data.valAttrs) {
						facts = data.valAttrs;
					}
					var analytics = {};
					return {
						label: "Facts",

						push: function (record) {

							for (var i = 0, x = finalAggregators.length; i < x; i++) {
								var aggregation = finalAggregators[i];
								aggregation.aggregator.push(record);
							}
						},
						inner : {
								value : function(){
									try{
										//extract which stat is this being called for
										var stat = arguments.callee.caller.arguments[0];
										//Get the aggregator for this stat
										var aggregator = _finalAggregatorsNameMap[stat];

										return aggregator.aggregator.inner.value();
									} catch(e){
										return -100;

									}

								}

							}

						,

						multivalue: function () {

							analytics = {};
							var variables = {};

							var finalAnalytics = {};


							for (var i = 0, x = finalAggregators.length; i < x; i++) {
								var aggregation = finalAggregators[i];
								analytics[aggregation.name] = aggregation.aggregator.value(aggregation.name);
								variables[aggregation.varName] = analytics[aggregation.name];
								if (!aggregation.hidden) {
									finalAnalytics[aggregation.name] = analytics[aggregation.name];
								}

							}
							var _derivedAnalytics = {};
							for (var i = 0, x = derivedAggregations.length; i < x; i++) {
								var derivedAggregation = derivedAggregations[i];
								var derivedVal = 0;
								var expression = 'derivedVal = ' + derivedAggregation.expression;
								eval(expression);
								_derivedAnalytics[derivedAggregation.name] = derivedVal;
							}
							finalAnalytics = $.extend(finalAnalytics, _derivedAnalytics)

							return finalAnalytics;
						},

						// return the first element for unsupported renderers.
						value: function () {
							return 'Multi-Fact-Aggregator does not support single value';
						},
						format: function (x, aggKey) {
							var formatter = null;
							if (!!_finalAggregatorsNameMap[aggKey]) {
								formatter = _finalAggregatorsNameMap[aggKey].aggregator.format;
							} else if (!!_finalDerivedAggregatorsNameMap[aggKey]) {
								var formatterOptions = $.extend({}, _finalDerivedAggregatorsNameMap[aggKey].formatterOptions);

								formatter = $.pivotUtilities.numberFormat(formatterOptions);
							}

							if (!formatter) {
								formatter = $.pivotUtilities.numberFormat();
							}
							return formatter(x);
						}
					};
				};
			};
		}
		$.pivotUtilities.multifactAggregatorGenerator = multifactAggregator;
	});

}).call(this);

// Custom Barchart

(function () {
	var callWithJQuery,
		indexOf = [].indexOf || function (item) {
			for (var i = 0, l = this.length; i < l; i++) {
				if (i in this && this[i] === item) return i;
			}
			return -1;
		},
		slice = [].slice,
		bind = function (fn, me) {
			return function () {
				return fn.apply(me, arguments);
			};
		},
		hasProp = {}.hasOwnProperty;

	callWithJQuery = function (pivotModule) {
		if (typeof exports === "object" && typeof module === "object") {
			return pivotModule(require("jquery"));
		} else if (typeof define === "function" && define.amd) {
			return define(["jquery"], pivotModule);
		} else {
			return pivotModule(jQuery);
		}
	};

	callWithJQuery(function ($) {
		$.fn.gtBarchart = function(opts) {
			var barcharter, i, l, numCols, numRows, ref;
			numRows = this.data("numrows");
			numCols = this.data("numcols");

			var _finalAggregatorsNameMap = {};
			var finalAggregators = $.map(opts.aggregations.defaultAggregations, function (aggregation) {
				return aggregation;
			}) || [];
			for (i = 0, x = finalAggregators.length; i < x; i++) {
				var aggregation = finalAggregators[i];
				_finalAggregatorsNameMap[aggregation.name] = aggregation;

			}

			var derivedAggregations = opts.aggregations.derivedAggregations || [];
			var _finalDerivedAggregatorsNameMap = {};
			for (i = 0, x = derivedAggregations.length; i < x; i++) {
				var derivedAggregation = derivedAggregations[i];
				_finalDerivedAggregatorsNameMap[derivedAggregation.name] = derivedAggregation;
			}

			var aggregationMap = $.extend(true, {}, _finalAggregatorsNameMap, _finalDerivedAggregatorsNameMap)


			barcharter = (function(_this) {
				return function(scope) {
					var forEachCell, max, min, range, values, valueSets;
					forEachCell = function(f) {
						return _this.find(scope).each(function() {
							var x, valueAttributeKey;
							x = $(this).data("value");
							valueAttributeKey = $(this).data("value-for");
							if ((x != null) && isFinite(x)) {
								return f(x, $(this), valueAttributeKey);
							}
						});
					};
					values = [];
					valueSets = {};
					forEachCell(function(x, elem, valueAttributeKey) {
						valueSets[valueAttributeKey] = valueSets[valueAttributeKey] || [];
						valueSets[valueAttributeKey].push(x);
						return values.push(x);
					});

					var scalerSet = {};

					Object.keys(valueSets).forEach(function(key){
						if(valueSets.hasOwnProperty(key)){

							max = Math.max.apply(Math, values);
							if (max < 0) {
								max = 0;
							}

							range = max;

							min = Math.min.apply(Math, values);
							if (min < 0) {
								range = max - min;
							}
							scalerSet[key] = (function(){ return function(x) {
								return 100 * x / (1.4 * range);
							}})();
						}
					});


					return forEachCell(function(x, elem, valueAttributeKey) {
						var bBase, bgColor, text, wrapper, scaler;
						scaler = scalerSet[valueAttributeKey];

						var opts = aggregationMap[valueAttributeKey];

						if(opts && opts.renderEnhancement ==='barchart'){
							text = elem.text();
							wrapper = $("<div>").css({
								"position": "relative",
								"width": "120px"
							});
							bgColor = opts.barchartColor || "steelblue";
							bBase = 0;
							if (min < 0) {
								bBase = scaler(-min);
							}
							if (x < 0) {
								bBase += scaler(x);
								bgColor = opts.barchartNegativeColor || "darkred";
								x = -x;
							}
							wrapper.append($("<div>").css({
								"position": "absolute",
								"top": bBase + "%",
								"left": 0,
								//"right": 0,
								"height" : "12px",
								"width": scaler(x) + "%",
								"background-color": bgColor
							}));
							wrapper.append($("<div>").text(text).css({
								"position": "relative",
								"padding-left": "5px",
								"padding-right": "5px"
							}));
							return elem.css({
								"padding": 0,
								"padding-top": "5px",
								"text-align": "right"
							}).html(wrapper);
						} else {
							return elem;
						}

					});
				};
			})(this);
			for (i = l = 0, ref = numRows; 0 <= ref ? l < ref : l > ref; i = 0 <= ref ? ++l : --l) {
				barcharter(".pvtVal.row" + i);
			}
			//barcharter(".pvtTotal.colTotal");
			return this;
		};
	});

}).call(this);

// Custom Heatmap

(function () {
	var callWithJQuery,
		indexOf = [].indexOf || function (item) {
			for (var i = 0, l = this.length; i < l; i++) {
				if (i in this && this[i] === item) return i;
			}
			return -1;
		},
		slice = [].slice,
		bind = function (fn, me) {
			return function () {
				return fn.apply(me, arguments);
			};
		},
		hasProp = {}.hasOwnProperty;

	callWithJQuery = function (pivotModule) {
		if (typeof exports === "object" && typeof module === "object") {
			return pivotModule(require("jquery"));
		} else if (typeof define === "function" && define.amd) {
			return define(["jquery"], pivotModule);
		} else {
			return pivotModule(jQuery);
		}
	};

	callWithJQuery(function ($) {
		$.fn.gtHeatmap = function (scope, opts) {
			var colorScaleGenerator, heatmapper, i, x, j, l, n, numCols, numRows, ref, ref1, ref2, _finalAggregatorsNameMap, _finalDerivedAggregatorsNameMap;
			if (scope == null) {
				scope = "heatmap";
			}
			numRows = this.data("numrows");
			numCols = this.data("numcols");


			_finalAggregatorsNameMap = {};
			var finalAggregators = $.map(opts.aggregations.defaultAggregations, function (aggregation) {
				return aggregation;
			}) || [];
			for (i = 0, x = finalAggregators.length; i < x; i++) {
				var aggregation = finalAggregators[i];
				_finalAggregatorsNameMap[aggregation.name] = aggregation;

			}

			var derivedAggregations = opts.aggregations.derivedAggregations || [];
			_finalDerivedAggregatorsNameMap = {};
			for (i = 0, x = derivedAggregations.length; i < x; i++) {
				var derivedAggregation = derivedAggregations[i];
				_finalDerivedAggregatorsNameMap[derivedAggregation.name] = derivedAggregation;
			}

			var aggregationMap = $.extend(true, {}, _finalAggregatorsNameMap, _finalDerivedAggregatorsNameMap)


			colorScaleGenerator = opts != null ? (ref = opts.heatmap) != null ? ref.colorScaleGenerator : void 0 : void 0;
			if (colorScaleGenerator == null) {
				colorScaleGenerator = function (values) {
					var max, min;
					min = Math.min.apply(Math, values);
					max = Math.max.apply(Math, values);
					return function (x) {
						var nonRed;
						nonRed = 255 - Math.round(255 * (x - min) / (max - min));
						return "rgb(255," + nonRed + "," + nonRed + ")";
					};
				};
			}
			heatmapper = (function (_this) {
				return function (scope) {
					var colorScale, forEachCell, values, valueSets, colorScaleSets;
					forEachCell = function (f) {
						return _this.find(scope).each(function () {
							var x, valueAttributeKey;
							x = $(this).data("value");
							valueAttributeKey = $(this).data("value-for");
							if ((x != null) && isFinite(x)) {
								return f(x, $(this), valueAttributeKey);
							}
						});
					};
					values = [];
					valueSets = {};
					forEachCell(function (x, elem, valueAttributeKey) {
						valueSets[valueAttributeKey] = valueSets[valueAttributeKey] || [];
						valueSets[valueAttributeKey].push(x);
						return values.push(x);
					});
					colorScaleSets = {};
					Object.keys(valueSets).forEach(function(key){
						if(valueSets.hasOwnProperty(key)){
							colorScaleSets[key] = colorScaleGenerator(valueSets[key], key)
						}
					});

					return forEachCell(function (x, elem, valueAttributeKey) {
						var opts = aggregationMap[valueAttributeKey];
						if(opts && opts.renderEnhancement ==='heatmap'){
							return elem.css("background-color", colorScaleSets[valueAttributeKey](x));
						} else {
							return elem;
						}

					});
				};
			})(this);
			switch (scope) {
				case "heatmap":
					heatmapper(".pvtVal");
					break;
				case "rowheatmap":
					for (i = l = 0, ref1 = numRows; 0 <= ref1 ? l < ref1 : l > ref1; i = 0 <= ref1 ? ++l : --l) {
						heatmapper(".pvtVal.row" + i);
					}
					break;
				case "colheatmap":
					for (j = n = 0, ref2 = numCols; 0 <= ref2 ? n < ref2 : n > ref2; j = 0 <= ref2 ? ++n : --n) {
						heatmapper(".pvtVal.col" + j);
					}
			}
			heatmapper(".pvtTotal.rowTotal");
			heatmapper(".pvtTotal.colTotal");
			return this;
		};
	});

}).call(this);

// Custom Renderer

(function () {
	var callWithJQuery,
		indexOf = [].indexOf || function (item) {
			for (var i = 0, l = this.length; i < l; i++) {
				if (i in this && this[i] === item) return i;
			}
			return -1;
		},
		slice = [].slice,
		bind = function (fn, me) {
			return function () {
				return fn.apply(me, arguments);
			};
		},
		hasProp = {}.hasOwnProperty;
	;

	callWithJQuery = function (pivotModule) {
		if (typeof exports === "object" && typeof module === "object") {
			return pivotModule(require("jquery"));
		} else if (typeof define === "function" && define.amd) {
			return define(["jquery"], pivotModule);
		} else {
			return pivotModule(jQuery);
		}
	};

	var pivotTableRenderer = function (pivotData, opts) {
		var aggregator, c, colAttrs, colKey, colKeys, defaults, getClickHandler, getMouseEnterHandler, getMouseLeaveHandler, getMouseMoveHandler, i, j, r, result,
			rowAttrs, rowKey, rowKeys, spanSize, tbody, td, th, thead, totalAggregator, tr, txt, val, x,
			valueAttrs;
		defaults = {
			table: {
				clickCallback: null,
				mouseEnterCallback: null,
				mouseLeaveCallback: null,
				mouseMoveCallback: null,
				rowTotals: true,
				colTotals: true
			},
			localeStrings: {
				totals: "Totals"
			}
		};
		opts = $.extend(true, {}, defaults, opts);
		colAttrs = pivotData.colAttrs;
		rowAttrs = pivotData.rowAttrs;
		//now valueAttrs will come from analytics keys
		valueAttrs = [];//pivotData.valAttrs;
		rowKeys = pivotData.getRowKeys();
		colKeys = pivotData.getColKeys();
		var aggregationMap = {};
		if(opts.table.aggregationConfig){

			Object.keys(opts.table.aggregationConfig).forEach(function(key){
				if(opts.table.aggregationConfig.hasOwnProperty(key)){
					var conf = opts.table.aggregationConfig[key];
					aggregationMap[conf.name] = conf;
				}
			});

		}
		//First get to know what does the value list look like?
		var aggregator = pivotData.getAggregator([], [])
		var multipleValues = aggregator.multivalue();
		Object.keys(multipleValues).forEach(function(key){
			if(multipleValues.hasOwnProperty(key)){
				valueAttrs.push(key);
			}
		});
		var enableDebug = !!window.enablePivotDebug;
		if (opts.table.clickCallback) {
			getClickHandler = function (value, rowValues, colValues) {
				var attr, filters, i;
				filters = {};
				for (i in colAttrs) {
					if (!hasProp.call(colAttrs, i)) continue;
					attr = colAttrs[i];
					if (colValues[i] != null) {
						filters[attr] = colValues[i];
					}
				}
				for (i in rowAttrs) {
					if (!hasProp.call(rowAttrs, i)) continue;
					attr = rowAttrs[i];
					if (rowValues[i] != null) {
						filters[attr] = rowValues[i];
					}
				}
				return function (e) {
					return opts.table.clickCallback(e, value, filters, pivotData);
				};
			};
		}

		if (opts.table.mouseEnterCallback) {
			getMouseEnterHandler = function (value, rowValues, colValues) {
				var attr, filters, i;
				filters = {};
				for (i in colAttrs) {
					if (!hasProp.call(colAttrs, i)) continue;
					attr = colAttrs[i];
					if (colValues[i] != null) {
						filters[attr] = colValues[i];
					}
				}
				for (i in rowAttrs) {
					if (!hasProp.call(rowAttrs, i)) continue;
					attr = rowAttrs[i];
					if (rowValues[i] != null) {
						filters[attr] = rowValues[i];
					}
				}
				return function (e) {
					return opts.table.mouseEnterCallback(e, value, filters, pivotData);
				};
			};
		}

		if (opts.table.mouseLeaveCallback) {
			getMouseLeaveHandler= function (value, rowValues, colValues) {
				var attr, filters, i;
				filters = {};
				for (i in colAttrs) {
					if (!hasProp.call(colAttrs, i)) continue;
					attr = colAttrs[i];
					if (colValues[i] != null) {
						filters[attr] = colValues[i];
					}
				}
				for (i in rowAttrs) {
					if (!hasProp.call(rowAttrs, i)) continue;
					attr = rowAttrs[i];
					if (rowValues[i] != null) {
						filters[attr] = rowValues[i];
					}
				}
				return function (e) {
					return opts.table.mouseLeaveCallback(e, value, filters, pivotData);
				};
			};
		}

		if (opts.table.mouseMoveCallback) {
			getMouseMoveHandler= function (value, rowValues, colValues) {
				var attr, filters, i;
				filters = {};
				for (i in colAttrs) {
					if (!hasProp.call(colAttrs, i)) continue;
					attr = colAttrs[i];
					if (colValues[i] != null) {
						filters[attr] = colValues[i];
					}
				}
				for (i in rowAttrs) {
					if (!hasProp.call(rowAttrs, i)) continue;
					attr = rowAttrs[i];
					if (rowValues[i] != null) {
						filters[attr] = rowValues[i];
					}
				}
				return function (e) {
					return opts.table.mouseMoveCallback(e, value, filters, pivotData);
				};
			};
		}
		result = document.createElement("table");
		result.className = "pvtTable";

		/**
		 * Function to get the span
		 * @param arr
		 * @param i
		 * @param j
		 * @returns {number}
		 */
		spanSize = function (arr, i, j) {
			var l, len, n, noDraw, ref, ref1, stop, x;
			if (i !== 0) {
				noDraw = true;
				for (x = l = 0, ref = j; 0 <= ref ? l <= ref : l >= ref; x = 0 <= ref ? ++l : --l) {
					if (arr[i - 1][x] !== arr[i][x]) {
						noDraw = false;
					}
				}
				if (noDraw) {
					return -1;
				}
			}
			len = 0;
			while (i + len < arr.length) {
				stop = false;
				for (x = n = 0, ref1 = j; 0 <= ref1 ? n <= ref1 : n >= ref1; x = 0 <= ref1 ? ++n : --n) {
					if (arr[i][x] !== arr[i + len][x]) {
						stop = true;
					}
				}
				if (stop) {
					break;
				}
				len++;
			}
			return len;
		};

		thead = document.createElement("thead");

		function renderHeaderForTotal() {
			if (parseInt(j) === 0 && opts.table.rowTotals) {
				th = document.createElement("th");
				th.className = "pvtTotalLabel pvtRowTotalLabel";
				th.innerHTML = opts.localeStrings.totals;

				var numHiddenMeasures = 0;
				Object.keys(aggregationMap).forEach(function(key){
					if(aggregationMap[key].hideInTotals){
						numHiddenMeasures ++;
					}
				})

				th.setAttribute("colspan", valueAttrs.length-numHiddenMeasures);
				//Added 1 for value headers
				th.setAttribute("rowspan", 2 + colAttrs.length + (rowAttrs.length === 0 ? 0 : 1));
				tr.appendChild(th);
			}
		}

		function renderHeadersForValues() {
			for (i in colKeys) {
				if (!hasProp.call(colKeys, i)) continue;
				colKey = colKeys[i];
				x = spanSize(colKeys, parseInt(i), parseInt(j));

				if (x !== -1) {
					th = document.createElement("th");
					th.className = "pvtColLabel";

					//adjust colspan according to multiple variables
					x = x * valueAttrs.length;


					th.textContent = colKey[j];
					th.setAttribute("colspan", x);
					if (parseInt(j) === colAttrs.length - 1 && rowAttrs.length !== 0) {
						th.setAttribute("rowspan", 2);
					}
					tr.appendChild(th);
				}
			}
		}

		for (j in colAttrs) {
			if (!hasProp.call(colAttrs, j)) continue;
			c = colAttrs[j];
			tr = document.createElement("tr");
			if (parseInt(j) === 0 && rowAttrs.length !== 0) {
				th = document.createElement("th");
				th.setAttribute("colspan", rowAttrs.length);
				th.setAttribute("rowspan", colAttrs.length);
				tr.appendChild(th);
			}
			th = document.createElement("th");
			th.className = "pvtAxisLabel";
			th.textContent = c;
			tr.appendChild(th);

			if(opts.table.prependRowTotals){
				renderHeaderForTotal();
				renderHeadersForValues();
			} else {
				renderHeadersForValues();
				renderHeaderForTotal();
			}


			thead.appendChild(tr);
		}
		if (rowAttrs.length !== 0) {
			tr = document.createElement("tr");
			for (i in rowAttrs) {
				if (!hasProp.call(rowAttrs, i)) continue;
				r = rowAttrs[i];
				th = document.createElement("th");
				th.className = "pvtAxisLabel";
				th.textContent = r;
				tr.appendChild(th);
			}
			th = document.createElement("th");
			if (colAttrs.length === 0) {
				th.className = "pvtTotalLabel pvtRowTotalLabel";
				th.setAttribute("colspan", valueAttrs.length);
				th.innerHTML = opts.localeStrings.totals;
			}
			tr.appendChild(th);
			thead.appendChild(tr);
		}
		result.appendChild(thead);
		tbody = document.createElement("tbody");


		/**
		 *
		 *
		 *
		 * Following Part Adds the headers for multiple measures
		 *
		 *
		 *
		 *
		 */
		//Setting up the value headers



		tr = document.createElement("tr");

		th = document.createElement("th");
		th.textContent = '';
		//Add 1 to end if there are no cols
		th.setAttribute("colspan", rowAttrs.length+(colKeys.length > 0 ? 1:0));
		//th.setAttribute("rowspan", colAttrs.length);
		tr.appendChild(th);

		/**
		 * Add the headers for multiple measures here
		 */
		function renderMeasureHeadersForValues() {
			//Add the headers for multiple measures here
			for (var k = 0, x = colKeys.length; k < x; k++) {
				var colKey = colKeys[k];
				if (!!colKey) {
					var idx = 0;
					for (var _key in valueAttrs) {
						th = document.createElement("th");
						th.textContent = valueAttrs[idx++];
						tr.appendChild(th);
					}
				}
			}
		}

		/**
		 *  For the Totals header to have the headers of multiple measures
		 */
		function renderMeasuredHeadersForTotals() {
			if (opts.table.rowTotals) {
				for (var l = 0, x = valueAttrs.length; l < x; l++) {
					var valAttr = valueAttrs[l];
					if (aggregationMap[valAttr] && aggregationMap[valAttr].hideInTotals) {
						continue;
					}
					th = document.createElement("th");
					th.textContent = valAttr;
					tr.appendChild(th);

				}
			}
		}

		if(opts.table.prependRowTotals){
			renderMeasuredHeadersForTotals();
			renderMeasureHeadersForValues();
		} else {
			renderMeasureHeadersForValues();
			renderMeasuredHeadersForTotals();
		}



		tbody.appendChild(tr);

		function renderValueCells() {
			for (j in colKeys) {
				if (!hasProp.call(colKeys, j)) continue;
				colKey = colKeys[j];
				aggregator = pivotData.getAggregator(rowKey, colKey);
				val = aggregator.value();

				/**
				 * In this section we are adding values to these cells
				 */

				if (!!aggregator.multivalue) {
					var stats = aggregator.multivalue(rowKey, colKey);
					var idx = 0;
					for (var stat in stats) {
						val = stats[stat]
						td = document.createElement("td");
						td.className = "pvtVal row" + i + " col" + j + " stat" + idx;
						//td.textContent = stat + ' : ' + aggregator.format(val);
						//td.textContent = aggregator.format(val);
						var valueSpan = $('<span>');
						if (enableDebug) {
							valueSpan.append($('<span>').html(stat).addClass('small text-grey'));
							valueSpan.append($('<br>'));
						}

						valueSpan.append($('<span>').html(aggregator.format(val, stat)));

						td.append(valueSpan[0]);
						td.setAttribute("data-value", val);
						td.setAttribute("data-row", i);
						td.setAttribute("data-stat-index", idx);
						td.setAttribute("data-col", j);
						td.setAttribute("data-value-for", stat);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, rowKey, colKey);
						}

						if (getMouseEnterHandler != null) {
							td.onmouseenter = getMouseEnterHandler(val, rowKey, colKey);
						}

						if (getMouseLeaveHandler != null) {
							td.onmouseleave = getMouseLeaveHandler(val, rowKey, colKey);

						}

						if (getMouseMoveHandler != null) {
							td.onmouseleave = getMouseMoveHandler(val, rowKey, colKey);

						}
						tr.appendChild(td);
						idx++;
					}


				} else {

					for (var k = 0, x = valueAttrs.length; k < x; k++) {
						var valueAttr = valueAttrs[k];

						val = aggregator.value();
						td = document.createElement("td");
						td.className = "pvtVal row" + i + " col" + j;
						td.textContent = aggregator.format(val);
						td.setAttribute("data-value", val);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, rowKey, colKey);
						}
						tr.appendChild(td);

					}


				}


			}
			return {stats, idx, valueSpan, k, x};
		}

		function renderRowTotalCells() {
			if (opts.table.rowTotals || colAttrs.length === 0) {
				totalAggregator = pivotData.getAggregator(rowKey, []);
				val = totalAggregator.value();
				if (!!totalAggregator.multivalue) {
					var stats = totalAggregator.multivalue();
					for (var stat in stats) {
						val = stats[stat];

						if(aggregationMap[stat] && aggregationMap[stat].hideInTotals){
							continue;
						}

						td = document.createElement("td");
						td.className = "pvtTotal rowTotal";
						//td.textContent = totalAggregator.format(val, k);

						var valueSpan = $('<span>');
						if (enableDebug) {
							valueSpan.append($('<span>').html(stat).addClass('small text-grey'));
							valueSpan.append($('<br>'));
						}
						valueSpan.append($('<span>').html(totalAggregator.format(val, stat)));

						td.append(valueSpan[0]);

						td.setAttribute("data-value", val);
						td.setAttribute("data-value-for", stat);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, rowKey, []);
						}

						td.setAttribute("data-for", "row" + i);
						tr.appendChild(td);


					}

				} else {

					for (var k = 0, x = valueAttrs.length; k < x; k++) {
						var valueAttr = valueAttrs[k];

						val = totalAggregator.value();
						td = document.createElement("td");
						td.className = "pvtTotal rowTotal";
						td.textContent = totalAggregator.format(val);
						td.setAttribute("data-value", val);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, rowKey, []);
						}
						td.setAttribute("data-for", "row" + i);
						tr.appendChild(td);

					}


				}


			}
			return {stats, valueSpan, k, x};
		}

		/**
		 * For each row in data-table
		 */
		for (i in rowKeys) {

			//Omit the proto props
			if (!hasProp.call(rowKeys, i)) continue;

			rowKey = rowKeys[i];

			/**
			 * Create a tr (row) element for each rowKey
			 * @type {HTMLTableRowElement}
			 */
			tr = document.createElement("tr");
			for (j in rowKey) {
				if (!hasProp.call(rowKey, j)) continue;
				txt = rowKey[j];

				//Get the rowspan for this label
				x = spanSize(rowKeys, parseInt(i), parseInt(j));

				if (x !== -1) {
					th = document.createElement("th");
					th.className = "pvtRowLabel";
					th.textContent = txt;
					th.setAttribute("rowspan", x);
					if (parseInt(j) === rowAttrs.length - 1 && colAttrs.length !== 0) {
						th.setAttribute("colspan", 2);
					}
					tr.appendChild(th);
				}
			}

			if(opts.table.prependRowTotals){
				var {stats, valueSpan, k, x} = renderRowTotalCells();
				var {stats, idx, valueSpan, k, x} = renderValueCells();
			} else {
				var {stats, idx, valueSpan, k, x} = renderValueCells();
				var {stats, valueSpan, k, x} = renderRowTotalCells();
			}

			tbody.appendChild(tr);
		}

		function renderColumnTotals() {
			for (j in colKeys) {
				if (!hasProp.call(colKeys, j)) continue;
				colKey = colKeys[j];
				totalAggregator = pivotData.getAggregator([], colKey);
				val = totalAggregator.value();


				if (!!totalAggregator.multivalue) {
					var stats = totalAggregator.multivalue();
					for (var stat in stats) {
						val = stats[stat];

						td = document.createElement("td");
						td.className = "pvtTotal colTotal";
						//td.textContent = totalAggregator.format(val);

						var valueSpan = $('<span>');
						if (enableDebug) {
							valueSpan.append($('<span>').html(stat).addClass('small text-grey'));
							valueSpan.append($('<br>'));
						}
						valueSpan.append($('<span>').html(totalAggregator.format(val, stat)));

						td.append(valueSpan[0]);

						td.setAttribute("data-value", val);
						td.setAttribute("data-value-for", stat);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, [], colKey);
						}
						td.setAttribute("data-for", "col" + j);
						tr.appendChild(td);


					}

				} else {

					for (var k = 0, x = valueAttrs.length; k < x; k++) {
						var valueAttr = valueAttrs[k];

						val = totalAggregator.value();
						td = document.createElement("td");
						td.className = "pvtTotal colTotal";
						td.textContent = totalAggregator.format(val);
						td.setAttribute("data-value", val);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, [], colKey);
						}
						td.setAttribute("data-for", "col" + j);
						tr.appendChild(td);

					}


				}


			}
		}

		function renderGrandTotals() {
			if (opts.table.rowTotals || colAttrs.length === 0) {
				totalAggregator = pivotData.getAggregator([], []);
				val = totalAggregator.value();


				if (!!totalAggregator.multivalue) {
					var stats = totalAggregator.multivalue();
					for (var stat in stats) {

						if(aggregationMap[stat] && aggregationMap[stat].hideInTotals){
							continue;
						}

						val = stats[stat];

						td = document.createElement("td");
						td.className = "pvtGrandTotal";
						td.textContent = totalAggregator.format(val, stat);
						td.setAttribute("data-value", val);
						td.setAttribute("data-value-for", stat);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, [], []);
						}
						tr.appendChild(td);

					}

				} else {

					for (var k = 0, x = valueAttrs.length; k < x; k++) {
						var valueAttr = valueAttrs[k];

						val = totalAggregator.value();
						td = document.createElement("td");
						td.className = "pvtGrandTotal";
						td.textContent = totalAggregator.format(val);
						td.setAttribute("data-value", val);
						if (getClickHandler != null) {
							td.onclick = getClickHandler(val, [], []);
						}
						tr.appendChild(td);

					}


				}


			}
		}

		if (opts.table.colTotals || rowAttrs.length === 0) {
			tr = document.createElement("tr");
			if (opts.table.colTotals || rowAttrs.length === 0) {
				th = document.createElement("th");
				th.className = "pvtTotalLabel pvtColTotalLabel";
				th.innerHTML = opts.localeStrings.totals;
				th.setAttribute("colspan", rowAttrs.length + (colAttrs.length === 0 ? 0 : 1));
				tr.appendChild(th);
			}

			if(opts.table.prependRowTotals){
				renderGrandTotals();
				renderColumnTotals();
			} else {
				renderColumnTotals();
				renderGrandTotals();
			}


			tbody.appendChild(tr);
		}
		result.appendChild(tbody);
		result.setAttribute("data-numrows", rowKeys.length);
		result.setAttribute("data-numcols", colKeys.length);
		return result;
	};

	callWithJQuery(function ($) {
		return $.pivotUtilities.gtRenderers = {
			"GT Table": function (pivotData, opts) {
				return pivotTableRenderer(pivotData, opts)
			},
			"GT Table Heatmap": function (pivotData, opts) {
				return $(pivotTableRenderer(pivotData, opts)).heatmap("heatmap", opts);
			},
			"GT Table Heatmap and Barchart": function (pivotData, opts) {
				return $($(pivotTableRenderer(pivotData, opts)).heatmap("heatmap", opts)).barchart(opts);
			},
			"GT Table Row Heatmap": function (pivotData, opts) {
				return $(pivotTableRenderer(pivotData, opts)).heatmap("rowheatmap", opts);
			},
			"GT Table Col Heatmap": function (pivotData, opts) {
				return $(pivotTableRenderer(pivotData, opts)).heatmap("colheatmap", opts);
			}
		};
	});

}).call(this);
