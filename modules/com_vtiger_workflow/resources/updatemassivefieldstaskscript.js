/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
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
function CBMassiveUpdateRelatedTask($, fieldvaluemapping) {
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
			value.replaceWith('<select id="save_fieldvalues_'+mappingno+'_value" class="expressionvalue">'+
				options+'</select>');
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
					var fieldn = fields[j].id+'module';
					var fieldnamemod = $('[id='+fieldn+']')[0].name;
					if (fieldmodule.indexOf(fieldnamemod) >= 0) {
						exist++;
					} else {
						fieldmodule[j] = fieldnamemod;
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
			vtinst.post('getRelatedModulesInfomation', {'module':moduleName}, handleError(function (result) {
				var modarray={};
				var combination;
				for (var prop in result) {
					if (result[prop]['related_module']=='') {
						continue;
					}
					combination=result[prop]['related_module']+'__'+result[prop]['relatedfield'];
					modarray[combination]=result[prop]['labeli18n'];
				}
				function addFieldValueMapping(mappingno) {
					$('#save_fieldvaluemapping').append(
						`<div id="save_fieldvalues_${mappingno}" style="margin-bottom: 5px" class="slds-grid slds-gutters slds-p-horizontal_x-large slds-grid_vertical-align-center">
							<select id="save_fieldvalues_${mappingno}_fieldmodule" class="fieldmodule slds-page-header__meta-text slds-select"></select>
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
					var fe = $('#save_fieldvalues_'+mappingno+'_fieldmodule');
					var i = 1;
					fillOptions(fe, modarray, 0);

					var re = $('#save_fieldvalues_'+mappingno+'_remove');
					re.bind('click', function () {
						removeFieldValueMapping(mappingno);
					});

					fe.bind('change', function () {
						var module=$(this).val().split('__');
						vtinst.describeObject(module[0], handleError(function (result) {
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
							moduleFieldTypes[module[0]] = dict(map(function (e) {
								return [e['name'], e['type']];
							},
							filteredFields(parent['fields'])));

							function getFieldType(fullFieldName) {
								var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
								if (group==null) {
									var fieldModule = module[0];
									var fieldName = fullFieldName;
								} else {
									var fieldModule = group[2];
									var fieldName = group[3];
								}
								if ((moduleFieldTypes[fieldModule]==undefined || moduleFieldTypes[fieldModule][fieldName]==undefined) && fieldName!='none') {
									alert(alert_arr.WF_UPDATE_MAP_ERROR+fieldModule+'.'+fieldName);
									alert(alert_arr.WF_UPDATE_MAP_ERROR_INFO);
									return {name:'string'};
								}
								if (fullFieldName == 'folderid' && moduleFieldTypes[fieldModule][fieldName]['name']=='reference') {
									moduleFieldTypes[fieldModule][fieldName]['name']='picklist';
									moduleFieldTypes[fieldModule][fieldName]['picklistValues']=moduleFieldTypes[fieldModule][fieldName]['picklistValues'].map((plval) => {
										$wsid = plval.value.split('x');
										plval.value = $wsid[1];
										return plval;
									});
								}
								return moduleFieldTypes[fieldModule][fieldName];
							}

							function fieldReferenceNames(referenceField) {
								var name = referenceField['name'];
								var label = referenceField['label'];
								function forModule(moduleName) {
									// If module is not accessible return no field information
									if (!contains(accessibleModules, moduleName)) {
										return [];
									}

									return map(function (field) {
										return [name+' : '+'('+moduleName+') '+field['name'], label+' : '+'('+moduleName+') '+field['label']];
									},
									filteredFields(modules[moduleName]['fields'])
									);
								}
								return reduceR(concat, map(forModule, referenceField['type']['refersTo']), []);
							}

							var fieldLabels = dict(parentFields);
							var fe1 = $('#save_fieldvalues_'+mappingno+'_fieldname');
							fillOptions(fe1, fieldLabels, 0);
							var fullFieldName = fe1.val();
							resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, module[0]+'__'+module[1]);
							fe1.unbind('change');
							fe1.bind('change', function () {
								var select = $(this);
								var mappingno = select.prop('id').match(/save_fieldvalues_(\d+)_fieldname/)[1];
								var fullFieldName = $(this).val();
								resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, module[0]+'__'+module[1]);
							});
						}));
					});
				}
				var mappingno=0;
				if (fieldvaluemapping) {
					$.each(fieldvaluemapping, function (i, fieldvaluemap) {
						var fieldname = fieldvaluemap['fieldname'];
						var fieldmodule = fieldvaluemap['fieldmodule'];
						var module=fieldmodule.split('__');

						vtinst.describeObject(module[0], handleError(function (result) {
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
							moduleFieldTypes[module[0]] = dict(map(function (e) {
								return [e['name'], e['type']];
							},
							filteredFields(parent['fields'])));

							function getFieldType(fullFieldName) {
								var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
								if (group==null) {
									var fieldModule = module[0];
									var fieldName = fullFieldName;
								} else {
									var fieldModule = group[2];
									var fieldName = group[3];
								}
								if ((moduleFieldTypes[fieldModule]==undefined || moduleFieldTypes[fieldModule][fieldName]==undefined) && fieldName!='none') {
									alert(alert_arr.WF_UPDATE_MAP_ERROR+fieldModule+'.'+fieldName);
									alert(alert_arr.WF_UPDATE_MAP_ERROR_INFO);
									return {name:'string'};
								}
								if (fullFieldName == 'folderid' && moduleFieldTypes[fieldModule][fieldName]['name']=='reference') {
									moduleFieldTypes[fieldModule][fieldName]['name']='picklist';
									moduleFieldTypes[fieldModule][fieldName]['picklistValues']=moduleFieldTypes[fieldModule][fieldName]['picklistValues'].map((plval) => {
										$wsid = plval.value.split('x');
										plval.value = $wsid[1];
										return plval;
									});
								}
								return moduleFieldTypes[fieldModule][fieldName];
							}

							function fieldReferenceNames(referenceField) {
								var name = referenceField['name'];
								var label = referenceField['label'];
								function forModule(moduleName) {
									// If module is not accessible return no field information
									if (!contains(accessibleModules, moduleName)) {
										return [];
									}

									return map(function (field) {
										return [name+' : '+'('+moduleName+') '+field['name'], label+' : '+'('+moduleName+') '+field['label']];
									},
									filteredFields(modules[moduleName]['fields'])
									);
								}
								return reduceR(concat, map(forModule, referenceField['type']['refersTo']), []);
							}
							var fieldLabels = dict(parentFields);
							addFieldValueMapping(mappingno);

							var fe1 = $('#save_fieldvalues_'+mappingno+'_fieldname');
							fillOptions(fe1, fieldLabels, 0);
							fe1.bind('change', function () {
								var select = $(this);
								var mappingno = select.prop('id').match(/save_fieldvalues_(\d+)_fieldname/)[1];
								var fullFieldName = $(this).val();
								resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, fieldmodule);
							});
							$(format('#save_fieldvalues_%s_fieldmodule', mappingno)).val(fieldmodule);
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
						var fieldmodule = $(this).children('.fieldmodule').val();
						var fieldname=$(this).children('.fieldname').val();
						var type = $(this).children('.type').val();
						var value = $(this).children('.expressionvalue').val();
						var fieldvaluemap = {
							fieldname:fieldname,
							valuetype:type,
							value:value,
							fieldmodule:fieldmodule
						};
						var module = fieldmodule.split('_');
						if (mod.indexOf(module[0])==-1) {
							mod[ind] = module[0];
							ind++;
						}
						if (mod.length>1) {
							duplicate = 1;
						}
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

		}));

	});
}
CBMassiveUpdateRelatedTask(jQuery, fieldvaluemapping);
