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

function CBUpsertTask($, fieldvaluemapping) {
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

	function removeFieldValueMapping(mappingno) {
		$(format('#save_fieldvalues_%s', mappingno)).remove();
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
					return !e[0];
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

	function editFieldExpression(fieldValueNode, fieldType) {
		editpopupobj.edit(fieldValueNode.prop('id'), fieldValueNode.val(), fieldType);
	}

	function resetFields(opType, fieldName, mappingno, fieldmodule) {
		defaultValue(opType.name)(opType, mappingno);
		var fv = $('#save_fieldvalues_'+mappingno+'_value');
		fv.prop('name', fieldName);
		var fv1 = $('#save_fieldvalues_'+mappingno+'_valuemodule');
		fv1.prop('name', fieldmodule);
		var fieldLabel = jQuery('#save_fieldvalues_'+mappingno+'_fieldmodule option:selected').html();
		validator.validateFieldData[fieldName] = {
			type: opType.name,
			label: fieldLabel,
			mapno: mappingno
		};
	}

	function defaultValue(fieldType) {

		function forPicklist(opType, mappingno) {
			var value = $('#save_fieldvalues_'+mappingno+'_value');
			var options = implode('',
				map(function (e) {
					return '<option value="'+e.value+'">'+e.label+'</option>';
				},
				opType['picklistValues'])
			);
			value.replaceWith('<select id="save_fieldvalues_'+mappingno+'_value" class="expressionvalue">'+options+'</select>');
			$('#save_fieldvalues_'+mappingno+'_value_type').val('rawtext');
		}
		function forStringField(opType, mappingno) {
			var value = $(format('#save_fieldvalues_%s_value', mappingno));
			var fv = $(format('#save_fieldvalues_%s_value', mappingno));
			fv.bind('focus', function () {
				editFieldExpression($(this), opType);
			});
			fv.bind('click', function () {
				editFieldExpression($(this), opType);
			});
			fv.bind('keypress', function () {
				editFieldExpression($(this), opType);
			});
		}
		var functions = {
			string:forStringField,
			picklist:forStringField,
			multipicklist:forPicklist
		};
		var ret = functions[fieldType];
		if (ret==null) {
			ret = functions['string'];
		}
		return ret;
	}

	var validateDuplicateFields = {
		init: function () {
		},
		validator: function () {
			jQuery('#duplicate_fields_selected_message').css('display', 'none');
			var result;
			var successResult = [true];
			var failureResult = [false, 'duplicate_fields_selected_message', []];
			var selectedFieldNames = $('.fieldname');
			result = successResult;
			$.each(selectedFieldNames, function (i, ele) {
				var fieldName = $(ele).val();
				var fields = $('[name='+fieldName+']');
				var fieldmodule = new Array();
				var exist = 0;
				for (var j=0; j<fields.length; j++) {
					var fieldn = $('[id=save_fieldvalues_'+j+'_fieldname]').val();
					if (fieldmodule.indexOf(fieldn) >= 0) {
						exist++;
					} else {
						fieldmodule[j] = fieldn;
					}
				}
				if (fields.length > 1 && exist>0) {
					result = failureResult;
				}
			});
			return result;
		}
	};

	$(document).ready(function () {

		jQuery('#editpopup').draggable({ handle: '#editpopup_draghandle' });
		editpopupobj = fieldExpressionPopup(moduleName, $);
		editpopupobj.setModule(moduleName);
		editpopupobj.close();
		validator.addValidator('validateDuplicateFields', validateDuplicateFields);
		vtinst.extendSession(handleError(function (result) {
			let params = '?operation=listtypes&sessionName=' + result.sessionName;
			fetch(vtinst.serviceUrl + params, {
				method: 'get',
			}).then(response => response.json()).then(response => {
				if (response.success) {
					const mods = response.result.types;
					mods.map(function (upsert_module) {
						const mod_information = response.result.information;
						$('#upsert_module').append(
							`<option value="${DOMPurify.sanitize(upsert_module)}">${DOMPurify.sanitize(mod_information[upsert_module].label)}</option>`
						);
					});
					$('#upsert_module').bind('change', function () {
						document.getElementById('save_fieldvaluemapping').innerHTML = '';
						fieldvaluemapping = null;
					});
					if (fieldvaluemapping) {
						$('#upsert_module').val(fieldvaluemapping[0].fieldmodule);
					}
				}
			});

			function addFieldValueMapping(mappingno, mode = 'create') {
				const selected_upsert_module = document.getElementById('upsert_module').value;
				vtinst.describeObject(selected_upsert_module, handleError(function (result) {
					var parent = result;
					function filteredFields(fields) {
						var filteredfields = filter(
							function (e) {
								return !contains(['autogenerated', 'multipicklist', 'password'], e.type.name);
							}, fields
						);
						// Added to filter non-editable fields for update
						return filter(
							function (e) {
								return contains(['1'], e.editable);
							}, filteredfields
						);
					}
					var parentFields = map(function (e) {
						return [e['name'], e['label']];
					}, filteredFields(parent['fields']));
					var moduleFieldTypes = {};
					moduleFieldTypes[selected_upsert_module] = dict(map(function (e) {
						return [e['name'], e['type']];
					},
					filteredFields(parent['fields'])));
					function getFieldType(fullFieldName) {
						var fieldModule = selected_upsert_module;
						var fieldName = fullFieldName;
						var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
						if (group!=null) {
							fieldModule = group[2];
							fieldName = group[3];
						}
						if ((moduleFieldTypes[fieldModule]==undefined || moduleFieldTypes[fieldModule][fieldName]==undefined) && fieldName!='none') {
							alert(alert_arr.WF_UPDATE_MAP_ERROR+fieldModule+'.'+fieldName);
							alert(alert_arr.WF_UPDATE_MAP_ERROR_INFO);
							return {name:'string'};
						}
						if (fullFieldName == 'folderid' && moduleFieldTypes[fieldModule][fieldName]['name']=='reference') {
							moduleFieldTypes[fieldModule][fieldName]['name']='picklist';
							moduleFieldTypes[fieldModule][fieldName]['picklistValues']=moduleFieldTypes[fieldModule][fieldName]['picklistValues'].map((plval) => {
								let wsid = plval.value.split('x');
								plval.value = wsid[1];
								return plval;
							});
						}
						return moduleFieldTypes[fieldModule][fieldName];
					}

					var fieldLabels = dict(parentFields);
					var fe1 = $('#save_fieldvalues_'+mappingno+'_fieldname');
					if (mode == 'create') {
						fillOptions(fe1, fieldLabels, 0);
					}
					var fullFieldName = fe1.val();
					resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, selected_upsert_module);
					fe1.unbind('change');
					fe1.bind('change', function () {
						var select = $(this);
						var mappingno = select.prop('id').match(/save_fieldvalues_(\d+)_fieldname/)[1];
						var fullFieldName = $(this).val();
						resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, selected_upsert_module);
					});
				}));
				$('#save_fieldvaluemapping').append(
					`<div id="save_fieldvalues_${mappingno}" style="margin-bottom: 5px" class="slds-grid slds-gutters slds-p-horizontal_x-large slds-grid_vertical-align-center">
						<select id="save_fieldvalues_${mappingno}_fieldname" class="fieldname slds-page-header__meta-text slds-select slds-m-left_x-small"></select>
						<input type="hidden" id="save_fieldvalues_${mappingno}_value_type" class="type">
						<input type="hidden" id="save_fieldvalues_${mappingno}_valuemodule" class="fieldborder" readonly >
						<input type="text" id="save_fieldvalues_${mappingno}_value" class="expressionvalue slds-input fieldborder slds-m-left_x-small" readonly >
						<span id="save_fieldvalues_${mappingno}_remove" class="link remove-link slds-m-left_x-small">
							<svg class="slds-icon slds-icon_small slds-icon-text-light" aria-hidden="true" >
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
							</svg>
							<input type="hidden" id="modtypes">
						</span>
					</div>`
				);
				var re = $('#save_fieldvalues_'+mappingno+'_remove');
				re.bind('click', function () {
					removeFieldValueMapping(mappingno);
				});
			}
			var mappingno=0;
			if (fieldvaluemapping) {
				$.each(fieldvaluemapping, function (i, fieldvaluemap) {
					var fieldname = fieldvaluemap['fieldname'];
					var fieldmodule = fieldvaluemap['fieldmodule'];
					var module = fieldmodule;
					vtinst.describeObject(module, handleError(function (result) {
						var parent = result;
						function filteredFields(fields) {
							var filteredfields = filter(
								function (e) {
									return !contains(['autogenerated', 'multipicklist', 'password'], e.type.name);
								}, fields
							);
							// Added to filter non-editable fields for update
							return filter(
								function (e) {
									return contains(['1'], e.editable);
								}, filteredfields
							);
						}
						var parentFields = map(function (e) {
							return [e['name'], e['label']];
						}, filteredFields(parent['fields']));
						var moduleFieldTypes = {};
						moduleFieldTypes[module] = dict(map(function (e) {
							return [e['name'], e['type']];
						},
						filteredFields(parent['fields'])));

						function getFieldType(fullFieldName) {
							var fieldModule = module;
							var fieldName = fullFieldName;
							var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
							if (group!=null) {
								fieldModule = group[2];
								fieldName = group[3];
							}
							if ((moduleFieldTypes[fieldModule]==undefined || moduleFieldTypes[fieldModule][fieldName]==undefined) && fieldName!='none') {
								alert(alert_arr.WF_UPDATE_MAP_ERROR+fieldModule+'.'+fieldName);
								alert(alert_arr.WF_UPDATE_MAP_ERROR_INFO);
								return {name:'string'};
							}
							if (fullFieldName == 'folderid' && moduleFieldTypes[fieldModule][fieldName]['name']=='reference') {
								moduleFieldTypes[fieldModule][fieldName]['name']='picklist';
								moduleFieldTypes[fieldModule][fieldName]['picklistValues']=moduleFieldTypes[fieldModule][fieldName]['picklistValues'].map((plval) => {
									let wsid = plval.value.split('x');
									plval.value = wsid[1];
									return plval;
								});
							}
							return moduleFieldTypes[fieldModule][fieldName];
						}

						var fieldLabels = dict(parentFields);
						addFieldValueMapping(mappingno, 'edit');

						var fe1 = $('#save_fieldvalues_'+mappingno+'_fieldname');
						fillOptions(fe1, fieldLabels, 0);
						fe1.bind('change', function () {
							var select = $(this);
							var mappingno = select.prop('id').match(/save_fieldvalues_(\d+)_fieldname/)[1];
							var fullFieldName = $(this).val();
							resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, fieldmodule);
						});
						$(format('#save_fieldvalues_%s_fieldname', mappingno)).val(fieldname);
						resetFields(getFieldType(fieldname), fieldname, mappingno, fieldmodule);
						$(format('#save_fieldvalues_%s_value_type', mappingno)).val(fieldvaluemap['valuetype']);
						$('#dump').html(fieldvaluemap['value']);
						//set property name on hidden field
						var fv = $('#save_fieldvalues_'+mappingno+'_value');
						fv.prop('name', fieldname);
						$(format('#save_fieldvalues_%s_value', mappingno)).val(fieldvaluemap['value']);
						var fv1 = $('#save_fieldvalues_'+mappingno+'_valuemodule');
						fv1.prop('name', fieldmodule);
						mappingno+=1;
					}));
				});
			}

			$('#save_fieldvaluemapping_add').bind('click', function () {
				addFieldValueMapping(mappingno++);
				return false;
			});

			$('#save').bind('click', function () {
				var validateFieldValues = new Array();
				var fieldvaluemapping = [];
				var mod = [];
				var ind = 0;
				var duplicate = 0;
				$('#save_fieldvaluemapping').children().each(function (i) {
					var fieldmodule = $('#upsert_module').val();
					var fieldname=$(this).children('.fieldname').val();
					var type = $(this).children('.type').val();
					var value = $(this).children('.expressionvalue').val();
					var fieldvaluemap = {
						fieldname:fieldname,
						valuetype:type,
						value:value,
						fieldmodule:fieldmodule
					};
					fieldvaluemapping[i]=fieldvaluemap;

					if (type == '' || type == 'rawtext') {
						validateFieldValues.push(fieldname);
					}
				});
				if (duplicate == 0) {
					if (fieldvaluemapping.length==0) {
						var out = '';
					} else {
						out = JSON.stringify(fieldvaluemapping);
					}
					$('#save_fieldvaluemapping_json').val(out);

					for (var fieldName in validator.validateFieldData) {
						if (validateFieldValues.indexOf(fieldName) < 0) {
							delete validator.validateFieldData[fieldName];
						}
					}
				} else {
					alert(alert_arr.duplicatednotallowed);
					return false;
				}
			});

			$('#save_fieldvaluemapping_add-busyicon').hide();
			$('#save_fieldvaluemapping_add').show();
		}));

	});
}
CBUpsertTask(jQuery, fieldvaluemapping);