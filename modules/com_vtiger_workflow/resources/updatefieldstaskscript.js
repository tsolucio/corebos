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

function VTUpdateFieldsTask($, fieldvaluemapping) {
	var vtinst = new VtigerWebservices('webservice.php');
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
			var referenceFields = filter(
				function (e) {
					return e['type']['name']=='reference';
				},
				fields
			);
			var referenceFieldModules = map(
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
					var moduleDescriptions = map(
						function (e) {
							return e[1];
						},
						parameters
					);
					var modules = dict(map(
						function (e) {
							return [e['name'], e];
						},
						moduleDescriptions
					));
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

	function resetFields(opType, fieldName, mappingno, fldrelname, relmodule, fillOptionsValue = true) {
		defaultValue(opType.name)(opType, mappingno);
		var fv = $('#save_fieldvalues_'+mappingno+'_value');
		fv.prop('name', fieldName);
		if (opType['refersTo']!=undefined && fillOptionsValue) {
			var refersize=opType['refersTo'].length;
			var modtypes=new Array();
			var fieldLabels=new Array();
			var k=0;
			var entered=0;
			vtinst.extendSession(handleError(function (result) {
				for (var j=0; j<refersize; j++) {
					var module=opType['refersTo'][j];
					vtinst.describeObject(module, handleError(function (result) {
						k++;
						var parent = result;
						function filteredFields(fields) {
							var filteredfields = filter(
								function (e) {
									return !contains(['autogenerated', 'password'], e.type.name);
								},
								fields
							);
							// Added to filter non-editable fields for update
							return filter(
								function (e) {
									return contains(['1'], e.editable);
								},
								filteredfields
							);
						}
						var moduleFieldTypes = {};
						moduleFieldTypes[result.name] = dict(map(
							function (e) {
								return [e['name'], e['type']];
							},
							filteredFields(parent['fields'])
						));

						function getFieldType(fullFieldName) {
							var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
							if (group==null) {
								var fieldModule = result.name;
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
							return moduleFieldTypes[fieldModule][fieldName];
						}
						modtypes[result.name] = moduleFieldTypes[result.name];
						if (fldrelname!='' && fldrelname!=undefined && fldrelname!=null && modtypes[relmodule]!=undefined && entered==0) {
							entered++;
							defaultValue(getFieldType(fldrelname).name)(getFieldType(fldrelname), mappingno);
						}
						var parentFields = map(function (e) {
							if (refersize>1) {
								return [result.name+'-'+e['name'], result.label+'-'+e['label']];
							} else {
								return [result.name+'-'+e['name'], e['label']];
							}
						}, filteredFields(parent['fields']));
						parentFields.unshift([result.label, '----'+result.label+'----']);
						fieldLabels = dict(parentFields);
						if (k==refersize) {
							fieldLabels['none']='None';
							if ($('#modtypes').val()!='') {
								var modtypes1= JSON.parse($('#modtypes').val());
								var modtypes2=$.extend(modtypes, modtypes1);
							} else {
								modtypes2=modtypes;
							}
							$('#modtypes').val(JSON.stringify(modtypes2));
						}
						var fe = $('#save_fieldvalues10_'+mappingno+'_fieldname');
						if (relmodule=='') {
							$('#save_fieldvalues10_'+mappingno+'_module').val(result.name);
						} else {
							$('#save_fieldvalues10_'+mappingno+'_module').val(relmodule);
						}
						if (k>1) {
							var empt=1;
						} else {
							empt=0;
						}
						fillOptions(fe, fieldLabels, empt);
						$('#save_fieldvalues10_'+mappingno+'_fieldname').show();
						if (fldrelname=='' || fldrelname==undefined || fldrelname==null) {
							$('#save_fieldvalues10_'+mappingno+'_fieldname').val('none');
						} else if (relmodule==result.name) {
							$('#save_fieldvalues10_'+mappingno+'_fieldname').val(relmodule+'-'+fldrelname);
						}
					}));
				}
				var fe = $('#save_fieldvalues10_'+mappingno+'_fieldname');
				fe.bind('change', function () {
					var select = $(this);
					var modtypes=JSON.parse($('#modtypes').val());
					var mappingno = select.prop('id').match(/save_fieldvalues10_(\d+)_fieldname/)[1];
					var modname=$(this).find('option:selected').val().split('-');
					if (modname.length == 1) {
						var fullFieldName = $(this).val();
						var mod=$('#save_fieldvalues10_'+mappingno+'_module').val();
						fieldtype = modtypes[mod];
					} else {
						var fullFieldName = modname[1];
						mod = modname[0];
						$('#save_fieldvalues10_'+mappingno+'_module').val(mod);
					}
					var fieldtype = modtypes[mod];
					if (fullFieldName=='none') {
						fullFieldName=$('#save_fieldvalues_'+mappingno+'_fieldname').val();
					}
					resetFields(fieldtype[fullFieldName], fullFieldName, mappingno, '', '');
					//set property name on hidden field
					var fv = $('#save_fieldvalues_'+mappingno+'_value');
					var fldrelname=$('#save_fieldvalues10_'+mappingno+'_fieldname').val();
					if (fldrelname!='' && fldrelname!=undefined && fldrelname!=null) {
						var frn=fldrelname.split('-');
						fv.prop('name', frn[1]);
					} else {
						fv.prop('name', fullFieldName);
					}
				});
			}));
		} else {
			if (fieldName!='none') {
				defaultValue(opType.name)(opType, mappingno);
				var fieldLabel = jQuery('#save_fieldvalues_'+mappingno+'_fieldname option:selected').html();
				validator.validateFieldData[fieldName] = {
					type: opType.name,
					label: fieldLabel,
					mapno: mappingno
				};
			}
		}
	}

	function defaultValue(fieldType) {

		function forPicklist(opType, mappingno) {
			var value = $('#save_fieldvalues_'+mappingno+'_value');
			var options = implode(
				'',
				map(
					function (e) {
						return '<option value="'+e.value+'">'+e.label+'</option>';
					},
					opType['picklistValues']
				)
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
			multipicklist:forStringField
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
			var successResult = [true];
			var failureResult = [false, 'duplicate_fields_selected_message', []];
			var selectedFieldNames = $('.fieldname');
			var fieldmodule = new Array();
			var exist = 0;
			$.each(selectedFieldNames, function (i, ele) {
				var elId = ele.id.split('_');
				var fieldName = $(ele).val();
				var fldrel = $('#save_fieldvalues_'+elId[2]+'_value').attr('name');
				if (fldrel != null) {
					fieldName = fldrel;
				}
				if (fieldmodule.indexOf(fieldName) >= 0) {
					exist++;
				} else {
					fieldmodule[i] = fieldName;
				}
			});
			if (exist>0) {
				return failureResult;
			}
			return successResult;
		}
	};

	$(document).ready(function () {
		jQuery('#editpopup').draggable({ handle: '#editpopup_draghandle' });
		editpopupobj = fieldExpressionPopup(moduleName, $);
		editpopupobj.setModule(moduleName);
		editpopupobj.close();
		validator.addValidator('validateDuplicateFields', validateDuplicateFields);

		vtinst.extendSession(handleError(function (result) {
			vtinst.describeObject(moduleName, handleError(function (result) {
				var parent = result;
				function filteredFields(fields) {
					var filteredfields = filter(
						function (e) {
							return !contains(['autogenerated', 'password'], e.type.name);
						},
						fields
					);
					// Added to filter non-editable fields for update
					return filter(
						function (e) {
							return contains(['1'], e.editable);
						},
						filteredfields
					);
				}
				var parentFields = map(
					function (e) {
						return [e['name'], e['label']];
					},
					filteredFields(parent['fields'])
				);
				var moduleFieldTypes = {};
				moduleFieldTypes[moduleName] = dict(map(
					function (e) {
						return [e['name'], e['type']];
					},
					filteredFields(parent['fields'])
				));

				function getFieldType(fullFieldName) {
					var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
					if (group==null) {
						var fieldModule = moduleName;
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

						return map(
							function (field) {
								return [name+' : ('+moduleName+') '+field['name'], label+' : ('+moduleName+') '+field['label']];
							},
							filteredFields(modules[moduleName]['fields'])
						);
					}
					return reduceR(concat, map(forModule, referenceField['type']['refersTo']), []);
				}

				var fieldLabels = dict(parentFields);

				function addFieldValueMapping(mappingno) {
					$('#save_fieldvaluemapping').append(`
						<div id="save_fieldvalues_${mappingno}" style="margin-bottom: 5px" class="slds-grid slds-gutters slds-p-horizontal_x-large slds-grid_vertical-align-center">
							<select id="save_fieldvalues_${mappingno}_fieldname" class="fieldname slds-select"></select>
							<select id="save_fieldvalues10_${mappingno}_fieldname" class="fieldname1 slds-select" style="display:none"></select>
							<input type="hidden" id="save_fieldvalues_${mappingno}_value_type" class="type">
							<input type="hidden" id="save_fieldvalues10_${mappingno}_module" class="type1">
							<input type="text" id="save_fieldvalues_${mappingno}_value" class="expressionvalue slds-input fieldborder slds-m-left_x-small" readonly >
							<span id="save_fieldvalues_${mappingno}_remove" class="link remove-link slds-m-left_x-small">
								<svg class="slds-icon slds-icon_small slds-icon-text-light" aria-hidden="true" >
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
								</svg>
								<input type="hidden" id="modtypes">
							</span>
							<input type="hidden" id="modtypes"></span>
						</div>
					`);
					var fe = $('#save_fieldvalues_'+mappingno+'_fieldname');
					var fe1 = $('#save_fieldvalues10_'+mappingno+'_fieldname');
					fillOptions(fe, fieldLabels, 0);

					var fullFieldName = fe.val();
					resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, '', '');

					var re = $('#save_fieldvalues_'+mappingno+'_remove');
					re.bind('click', function () {
						removeFieldValueMapping(mappingno);
					});

					fe.bind('change', function () {
						var select = $(this);
						var mappingno = select.prop('id').match(/save_fieldvalues_(\d+)_fieldname/)[1];
						var fullFieldName = $(this).val();
						resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, '', '');
						//set property name on hidden field
						var fv = $('#save_fieldvalues_'+mappingno+'_value');
						var fldrelname=$('#save_fieldvalues10_'+mappingno+'_fieldname').val();
						if (getFieldType(fullFieldName).name!='reference' && $('#save_fieldvalues10_'+mappingno+'_fieldname')!=undefined) {
							$('#save_fieldvalues10_'+mappingno+'_fieldname').hide();
						}
						if (fldrelname!='' && fldrelname!=undefined && fldrelname!=null) {
							fv.prop('name', fldrelname);
						} else {
							fv.prop('name', fullFieldName);
						}
					});

					fe1.bind('change', function () {
						var select = $(this);
						var mappingno = select.prop('id').match(/save_fieldvalues10_(\d+)_fieldname/)[1];
						var fullFieldName = $(this).val().split('-')[1];
						var moduleName = $(this).val().split('-')[0];
						if (fullFieldName == 'folderid' && moduleName == 'Documents') {
							vtinst.extendSession(handleError(function (result) {
								vtinst.describeObject(moduleName, handleError(function (result) {
									var parent = result;
									function filteredFields(fields) {
										var filteredfields = filter(
											function (e) {
												return !contains(['autogenerated', 'password'], e.type.name);
											},
											fields
										);
										return filter(
											function (e) {
												return contains(['1'], e.editable);
											},
											filteredfields
										);
									}
									var parentFields = map(
										function (e) {
											return [e['name'], e['label']];
										},
										filteredFields(parent['fields'])
									);
									var moduleFieldTypes = {};
									moduleFieldTypes[moduleName] = dict(map(
										function (e) {
											return [e['name'], e['type']];
										},
										filteredFields(parent['fields'])
									));

									function getFieldType(fullFieldName) {
										var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
										if (group==null) {
											var fieldModule = moduleName;
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
												let wsid = plval.value.split('x');
												plval.value = wsid[1];
												return plval;
											});
										}
										return moduleFieldTypes[fieldModule][fieldName];
									}
									resetFields(getFieldType(fullFieldName), fullFieldName, mappingno, '', '', false);
								}));
							}));
						}
					});
				}

				var mappingno=0;
				if (fieldvaluemapping) {
					$.each(fieldvaluemapping, function (i, fieldvaluemap) {
						var fieldname = fieldvaluemap['fieldname'];
						var fldrelname = fieldvaluemap['fldrelname'];
						var module = fieldvaluemap['fldmodule'];
						addFieldValueMapping(mappingno);
						$(format('#save_fieldvalues_%s_fieldname', mappingno)).val(fieldname);
						resetFields(getFieldType(fieldname), fieldname, mappingno, fldrelname, module);
						$(format('#save_fieldvalues_%s_value_type', mappingno)).val(fieldvaluemap['valuetype']);
						if (module!=undefined) {
							$(format('#save_fieldvalues_%s_module', mappingno)).val(module);
						}
						$('#dump').html(fieldvaluemap['value']);
						//set property name on hidden field
						var fv = $('#save_fieldvalues_'+mappingno+'_value');
						if (fldrelname!='' && fldrelname!=undefined && fldrelname!=null) {
							fv.prop('name', fldrelname);
						} else {
							fv.prop('name', fieldname);
						}
						$(format('#save_fieldvalues_%s_value', mappingno)).val(fieldvaluemap['value']);
						mappingno+=1;
					});
				}

				$('#save_fieldvaluemapping_add').bind('click', function () {
					addFieldValueMapping(mappingno++);
				});

				$('#save').bind('click', function () {
					var validateFieldValues = new Array();
					var fieldvaluemapping = [];
					$('#save_fieldvaluemapping').children().each(function (i) {
						var fieldname = $(this).children('.fieldname').val();
						var fldrelname=$(this).children('.fieldname1').val();
						var type = $(this).children('.type').val();
						var value = $(this).children('.expressionvalue').val();
						if (fldrelname!='none' && fldrelname!=undefined && fldrelname!=null) {
							var frn=fldrelname.split('-');
							var fldmodule=$(this).children('.type1').val();
							var fieldvaluemap = {
								fieldname:fieldname,
								fldrelname:frn[1],
								fldmodule:fldmodule,
								valuetype:type,
								value:value
							};
						} else {
							var fieldvaluemap = {
								fieldname:fieldname,
								valuetype:type,
								value:value
							};
						}
						fieldvaluemapping[i]=fieldvaluemap;

						if (type == '' || type == 'rawtext') {
							validateFieldValues.push(fieldname);
						}
					});
					var out = '';
					if (fieldvaluemapping.length!=0) {
						out = JSON.stringify(fieldvaluemapping);
					}
					$('#save_fieldvaluemapping_json').val(out);

					for (var fieldName in validator.validateFieldData) {
						if (validateFieldValues.indexOf(fieldName) < 0) {
							delete validator.validateFieldData[fieldName];
						}
					}
				});

				$('#save_fieldvaluemapping_add-busyicon').hide();
				$('#save_fieldvaluemapping_add').show();
			}));

		}));

	});
}
VTUpdateFieldsTask(jQuery, fieldvaluemapping);
