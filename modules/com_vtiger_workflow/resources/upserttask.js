/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
//addition to stringify json function
(function () {
	// Convert array to object
	var convArrToObj = function (array) {
		var thisEleObj = new Object();
		if (typeof array == 'object') {
			for (var i in array) {
				var thisEle = convArrToObj(array[i]);
				thisEleObj[i] = thisEle;
			}
		} else {
			thisEleObj = array;
		}
		return thisEleObj;
	};
	var oldJSONStringify = JSON.stringify;
	JSON.stringify = function (input) {
		if (oldJSONStringify(input) == '[]') {
			return oldJSONStringify(convArrToObj(input));
		} else {
			return oldJSONStringify(input);
		}
	};
})();

function CBUpsertTask($) {
	var vtinst = new VtigerWebservices('webservice.php');
	var desc = null;
	var format = fn.format;

	function errorDialog(message) {
		alert(message);
	}

	function handleError(fn) {
		return function (status, result) {
			if (status) {
				fn(result);
			} else {
				errorDialog('Failure:'+result);
			}
		};
	}

	function implode(sep, arr) {
		var out = '';
		$.each(arr, function (i, v) {
			out+=v;
			if (i<arr.length-1) {
				out+=sep;
			}
		});
		return out;
	}

	function fillOptions(el, options, empt) {
		if (empt==0) {
			el.empty();
		}
		$.each(options, function (k, v) {
			if (v.indexOf('----')>-1) {
				var dis='disabled';
			} else {
				dis='';
			}
			el.append('<option value="'+k+'" '+dis+'>'+v+'</option>');
		});
	}

	function map(fn, list) {
		var out = [];
		$.each(list, function (i, v) {
			out[out.length]=fn(v);
		});
		return out;
	}

	function reduceR(fn, list, start) {
		var acc = start;
		$.each(list, function (i, v) {
			acc = fn(acc, v);
		});
		return acc;
	}

	function dict(list) {
		var out = {};
		$.each(list, function (i, v) {
			out[v[0]] = v[1];
		});
		return out;
	}

	function filter(pred, list) {
		var out = [];
		$.each(list, function (i, v) {
			if (pred(v)) {
				out[out.length]=v;
			}
		});
		return out;
	}

	function contains(list, value) {
		var ans = false;
		$.each(list, function (i, v) {
			if (v==value) {
				ans = true;
				return false;
			}
		});
		return ans;
	}

	function diff(reflist, list) {
		var out = [];
		$.each(list, function (i, v) {
			if (contains(reflist, v)) {
				out.push(v);
			}
		});
		return out;
	}

	function concat(lista, listb) {
		return lista.concat(listb);
	}

	//Get an array containing the the description of a module and all modules
	//refered to by it. This is passed to callback.
	function getDescribeObjects(accessibleModules, moduleName, callback) {
		vtinst.describeObject(moduleName, handleError(function (result) {
			var parent = result;
			var fields = parent['fields'];
			var referenceFields = filter(function (e) {
				return e['type']['name']=='reference';
			},
			fields);
			var referenceFieldModules =
			map(
				function (e) {
					return e['type']['refersTo'];
				},
				referenceFields
			);
			function union(a, b) {
				var newfields = filter(function (e) {
					return !contains(a, e);
				}, b);
				return a.concat(newfields);
			}
			var relatedModules = reduceR(union, referenceFieldModules, [parent['name']]);

			// Remove modules that is no longer accessible
			relatedModules = diff(accessibleModules, relatedModules);

			function executer(parameters) {
				var failures = filter(function (e) {
					return e[0]==false;
				}, parameters);
				if (failures.length!=0) {
					var firstFailure = failures[0];
					callback(false, firstFailure[1]);
				} else {
					var moduleDescriptions = map(function (e) {
						return e[1];
					},
					parameters);
					var modules = dict(map(function (e) {
						return [e['name'], e];
					},
					moduleDescriptions));
					callback(true, modules);
				}
			}
			var p = parallelExecuter(executer, relatedModules.length);
			$.each(relatedModules, function (i, v) {
				p(function (callback) {
					vtinst.describeObject(v, callback);
				});
			});
		}));
	}
}
CBUpsertTask = CBUpsertTask(jQuery);
