/*************************************************************************************************
 * Copyright 2022 Spike. -- This file is a part of Spike coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. Spike. reserves all rights not expressly
 * granted by the License. coreBOS distributed by Spike. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 * Author : Spike
 *************************************************************************************************/

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

function CBAddNotificationTask($) {
	var vtinst = new VtigerWebservices('webservice.php');

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

	$(document).ready(function () {

		vtinst.extendSession(handleError(function (result) {
			let params = '?operation=listtypes&sessionName=' + result.sessionName;
			fetch(vtinst.serviceUrl + params, {
				method: 'get',
			}).then(response => response.json()).then(response => {
				if (response.success) {
					$('#cbmodule').append(
						`<option value="wfmodule">${alert_arr.wfmodule}</option>`
					);
					const mods = response.result.types;
					mods.map(function (notify_module) {
						const mod_information = response.result.information;
						$('#cbmodule').append(
							`<option value="${DOMPurify.sanitize(notify_module)}">${DOMPurify.sanitize(mod_information[notify_module].label)}</option>`
						);
					});
					if (moduleName) {
						$('#cbmodule').val(moduleName);
					}
				}
			});
			params = '?operation=query&query=select%20id,first_name,last_name%20from%20Users;&sessionName=' + result.sessionName;
			fetch(vtinst.serviceUrl + params, {
				method: 'get',
			}).then(response => response.json()).then(response => {
				if (response.success) {
					$('#ownerid').append(
						`<option value="wfuser">${alert_arr.currentuser}</option>`
					);
					const usrs = response.result;
					usrs.map(function (notify_user) {
						$('#ownerid').append(
							`<option value="${DOMPurify.sanitize(notify_user.id)}">${DOMPurify.sanitize(notify_user.first_name)} ${DOMPurify.sanitize(notify_user.last_name)}</option>`
						);
					});
					if (wfUser) {
						$('#ownerid').val(wfUser);
					}
				}
			});
		}));
	});
}
CBAddNotificationTask(jQuery);