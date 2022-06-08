/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function editbuilderscript($, conditions) {
	var vtinst = new VtigerWebservices('webservice.php');
	var editpopupobj;

	function id(v) {
		return v;
	}

	function map(fn, list) {
		var out = [];
		$.each(list, function (i, v) {
			out[out.length]=fn(v);
		});
		return out;
	}

	function field(name) {
		return function (object) {
			if (typeof(object) != 'undefined') {
				return object[name];
			}
		};
	}

	function zip() {
		var out = [];
		var lengths = map(field('length'), arguments);
		var min = reduceR(
			function (a, b) {
				return a<b?a:b;
			},
			lengths,
			lengths[0]
		);
		for (var i=0; i<min; i++) {
			out[i]=map(field(i), arguments);
		}
		return out;
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

	function diff(reflist, list) {
		var out = [];
		$.each(list, function (i, v) {
			if (contains(reflist, v)) {
				out.push(v);
			}
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

	function concat(lista, listb) {
		return lista.concat(listb);
	}

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

	function mergeObjects(obj1, obj2) {
		var res = {};
		for (k in obj1) {
			res[k] = obj1[k];
		}
		for (var k in obj2) {
			res[k] = obj2[k];
		}
		return res;
	}

	function center(el) {
		el.css({
			position: 'absolute'
		});
		el.width('400px');
		el.height('125px');
		placeAtCenter(el.get(0));
	}

	function PageLoadingPopup() {
		function show() {
			//$('#workflow_loading').css('display', 'block');
			//center($('#workflow_loading'));
		}
		function close() {
			$('#workflow_loading').css('display', 'none');
		}
		return {
			show:show,
			close:close
		};
	}
	var pageLoadingPopup = PageLoadingPopup();

	var operations = function () {
		var op = {
			string:['is', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty', 'exists', 'does not start with', 'does not end with', 'was'],
			number:['equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'has changed', 'exists', 'was'],
			value:['is', 'is not', 'has changed', 'has changed to', 'is empty', 'is not empty', 'exists', 'was', 'has this as nth child'],
			multipicklist:['is', 'is not', 'was'],
			date:['is', 'is not', 'has changed', 'has changed to', 'between', 'before', 'after', 'is today', 'less than days ago', 'more than days ago', 'in less than', 'in more than', 'days ago', 'days later', 'exists', 'was'],
			datetime:['is', 'is not', 'has changed', 'has changed to', 'less than hours before', 'less than hours later', 'more than hours before', 'more than hours later', 'equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'exists', 'was']
		};
		var mapping = [
			['string', ['string', 'text', 'url', 'email', 'phone']],
			['number', ['integer', 'double', 'currency']],
			['value', ['reference', 'picklist', 'multipicklist', 'datetime', 'time', 'date', 'boolean']],
			['multipicklist', ['multipicklist']],
			['date', ['date']],
			['datetime', ['datetime', 'time']]
		];

		var out = {};
		$.each(mapping, function (i, v) {
			var opName = v[0];
			var types = v[1];
			$.each(types, function (i, v) {
				out[v] = op[opName];
			});
		});
		return out;
	}();

	var transOperations = function () {
		var op = {
			string:[alert_arr.LBL_IS, alert_arr.LBL_CONTAINS, alert_arr.LBL_DOES_NOT_CONTAIN, alert_arr.LBL_STARTS_WITH,
				alert_arr.LBL_ENDS_WITH, alert_arr.LBL_HAS_CHANGED, alert_arr.LBL_IS_EMPTY, alert_arr.LBL_IS_NOT_EMPTY, alert_arr.LBL_EXISTS, alert_arr.DOES_NOT_START_WITH,
				alert_arr.DOES_NOT_END_WITH, alert_arr.LBL_WAS],
			number:[alert_arr.LBL_EQUAL_TO, alert_arr.LBL_LESS_THAN, alert_arr.LBL_GREATER_THAN, alert_arr.LBL_DOEST_NOT_EQUAL,
				alert_arr.LBL_LESS_THAN_OR_EQUAL_TO, alert_arr.LBL_GREATER_THAN_OR_EQUAL_TO, alert_arr.LBL_HAS_CHANGED, alert_arr.LBL_EXISTS, alert_arr.LBL_WAS],
			value:[alert_arr.LBL_IS, alert_arr.LBL_IS_NOT, alert_arr.LBL_HAS_CHANGED, alert_arr.LBL_HAS_CHANGED_TO, alert_arr.LBL_IS_EMPTY, alert_arr.LBL_IS_NOT_EMPTY,
				alert_arr.LBL_EXISTS, alert_arr.LBL_WAS],
			reference:[alert_arr.LBL_IS, alert_arr.LBL_IS_NOT, alert_arr.LBL_HAS_CHANGED, alert_arr.LBL_HAS_CHANGED_TO, alert_arr.LBL_IS_EMPTY, alert_arr.LBL_IS_NOT_EMPTY,
				alert_arr.LBL_EXISTS, alert_arr.LBL_WAS, alert_arr.HAS_THIS_AS_NTH_CHILD],
			date:[alert_arr.LBL_IS, alert_arr.LBL_IS_NOT, alert_arr.LBL_HAS_CHANGED, alert_arr.LBL_HAS_CHANGED_TO,
				alert_arr.LBL_BETWEEN, alert_arr.LBL_BEFORE, alert_arr.LBL_AFTER, alert_arr.LBL_IS_TODAY, alert_arr.LBL_LESS_THAN_DAYS_AGO,
				alert_arr.LBL_MORE_THAN_DAYS_AGO, alert_arr.LBL_IN_LESS_THAN, alert_arr.LBL_IN_MORE_THAN, alert_arr.LBL_DAYS_AGO, alert_arr.LBL_DAYS_LATER,
				alert_arr.LBL_EXISTS, alert_arr.LBL_WAS],
			datetime:[alert_arr.LBL_IS, alert_arr.LBL_IS_NOT, alert_arr.LBL_HAS_CHANGED, alert_arr.LBL_HAS_CHANGED_TO,
				alert_arr.LBL_LESS_THAN_HOURS_BEFORE, alert_arr.LBL_LESS_THAN_HOURS_LATER, alert_arr.LBL_MORE_THAN_HOURS_BEFORE, alert_arr.LBL_MORE_THAN_HOURS_LATER, alert_arr.LBL_EQUAL_TO, alert_arr.LBL_LESS_THAN, alert_arr.LBL_GREATER_THAN, alert_arr.LBL_DOEST_NOT_EQUAL,
				alert_arr.LBL_LESS_THAN_OR_EQUAL_TO, alert_arr.LBL_GREATER_THAN_OR_EQUAL_TO, alert_arr.LBL_EXISTS, alert_arr.LBL_WAS]
		};
		var mapping = [
			['string', ['string', 'text', 'url', 'email', 'phone']],
			['number', ['integer', 'double', 'currency']],
			['value', ['picklist', 'multipicklist', 'datetime', 'time', 'date', 'boolean']],
			['reference', ['reference']],
			['date', ['date']],
			['datetime', ['datetime', 'time']]
		];

		var out = {};
		$.each(mapping, function (i, v) {
			var opName = v[0];
			var types = v[1];
			$.each(types, function (i, v) {
				out[v] = op[opName];
			});
		});
		return out;
	}();

	var JoinConditionOptions = {'and': alert_arr.LBL_AND, 'or': alert_arr.LBL_OR};
	var format = fn.format;

	function fillOptions(el, options) {
		el.empty();
		$.each(options, function (k, v) {
			el.append('<option value="'+k+'">'+v+'</option>');
		});
	}

	function resetFields(opType, condno) {
		var ops = $('#save_condition_'+condno+'_operation');
		var selOperations = operations[opType.name];
		var selTransOperations = new Array();
		var selectedOperations = new Array();

		// Remove 'has changed' operation for reference fields
		var fe = $('#save_condition_'+condno+'_fieldname');
		var fullFieldName = fe.val();
		var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
		if (group != null) {
			for (var i=0; i<selOperations.length; ++i) {
				if (selOperations[i] != 'has changed' && selOperations[i] != 'has changed to') {
					selectedOperations.push(selOperations[i]);
					selTransOperations.push(transOperations[opType.name][i]);
				}
			}
		} else {
			selectedOperations = selOperations;
			selTransOperations = transOperations[opType.name];
		}

		var l = dict(zip(selectedOperations, selTransOperations));
		fillOptions(ops, l);
		defaultValue(opType.name)(opType, condno);
	}

	function defaultValue(fieldType) {

		function forPicklist(opType, condno) {
			var value = $('#save_condition_'+condno+'_value');
			var options = implode(
				'',
				map(
					function (e) {
						return '<option value="'+e.value+'">'+e.label+'</option>';
					},
					opType['picklistValues']
				)
			);
			value.replaceWith('<select id="save_condition_'+condno+'_value" class="slds-select ceexpressionvalue" onchange="updateWSSQL();">'+options+'</select>');
			$('#save_condition_'+condno+'_value_type').val('rawtext');
		}
		function forString(opType, condno) {
			var value = $(format('#save_condition_%s_value', condno));
			value.replaceWith(format('<input type="text" id="save_condition_%s_value" value="" class="slds-input ceexpressionvalue" readonly style="border:1px solid #dddbda;width:64%;" onchange="updateWSSQL();" />', condno));
			var fv = $('#save_condition_'+condno+'_value');
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
			string:forString,
			picklist:forString,
			multipicklist:forPicklist
		};
		var ret = functions[fieldType];
		if (ret==null) {
			ret = functions['string'];
		}
		return ret;
	}

	function removeCondition(groupno, condno) {
		$(format('#save_condition_%s', condno)).remove();
		resetJoinCondition(groupno, condno);
	}

	function removeConditionGroup(groupno) {
		$(format('#condition_group_%s', groupno)).remove();
		$(format('#condition_group_%s_joincondition', groupno)).remove();
		resetGroupJoinCondition(groupno);
		let grpbtns = document.querySelectorAll('button[id^="save_conditions_add_"]');
		let totgrp = grpbtns.length;
		Array.from(grpbtns).forEach(x => x.style.visibility='hidden');
		if (totgrp>0) {
			grpbtns[totgrp-1].style.visibility = 'visible';
		} else {
			document.getElementById('startwhennoconditions').style.display = 'flex';
			$('#startwhennoconditionsbutton').unbind('click').bind('click', function () {
				document.getElementById('startwhennoconditions').style.display = 'none';
				addConditionFunction(groupno, condno++);
			});
		}
	}

	function resetJoinCondition(groupno, condno) {
		var lastCondition = $('#save_condition_group_'+groupno+' div:last');
		lastCondition.children('.joincondition').css('visibility', 'hidden');

		var previousLastCondition = lastCondition.prev();
		if (previousLastCondition.length > 0) {
			previousLastCondition.children('.joincondition').css('visibility', 'visible');
		}

		var groupConditions = $(format('#save_condition_group_%s', groupno)).children();
		if (groupConditions.length <= 0) {
			removeConditionGroup(groupno);
		}
		resetGroupJoinCondition(groupno);
		updateWSSQL();
	}

	function resetGroupJoinCondition(groupno) {
		var firstChildNode = $('#save_conditions :first');
		if (firstChildNode.length > 0 && firstChildNode.prop('class').indexOf('condition_group_join_block') >= 0) {
			firstChildNode.remove();
		}
	}

	//Convert user type into reference for consistency in describe objects
	//This is done inplace
	function referencify(desc) {
		var fields = desc['fields'];
		for (var i=0; i<fields.length; i++) {
			var field = fields[i];
			var type = field['type'];
			if (type['name']=='owner') {
				type['realname']='owner';
				type['name']='reference';
				type['refersTo']=['Users'];
			}
		}
		return desc;
	}

	function getDescribeObjects(accessibleModules, moduleName, callback) {
		vtinst.describeObject(moduleName, handleError(function (result) {
			var parent = referencify(result);
			var fields = parent['fields'];
			var referenceFields = filter(
				function (e) {
					return e['type']['name']=='reference';
				},
				fields
			);
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
					}, parameters);
					var modules = dict(map(function (e) {
						return [e['name'], referencify(e)];
					}, moduleDescriptions));
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

	var addConditionFunction = '';
	var parentFields = {};
	var referenceFieldTypes = {};
	var moduleFieldTypes = {};
	var referenceFields = '';
	var fieldLabels = '';
	var groupno=0;
	var condno=0;
	function changeModule(newconditions) {
		if (moduleName=='') {
			return;
		}
		if (!newconditions && typeof newconditions!='undefined') {
			conditions = newconditions;
		}
		pageLoadingPopup.show();
		jQuery('#editpopup').draggable({ handle: '#editpopup_draghandle' });
		editpopupobj = fieldExpressionPopup(moduleName, $);
		editpopupobj.setModule(moduleName);
		editpopupobj.close();

		vtinst.extendSession(handleError(function (result) {
			vtinst.listTypes(handleError(function (accessibleModules) {
				getDescribeObjects(accessibleModules, moduleName, handleError(function (modules) {
					var parent = modules[moduleName];
					function filteredFields(fields) {
						return filter(
							function (e) {
								return !contains(['autogenerated', 'owner', 'password'], e.type.name);
							},
							fields
						);
					}
					parentFields = map(
						function (e) {
							return [e['name'], e['label']];
						},
						filteredFields(parent['fields'])
					);
					referenceFieldTypes = filter(
						function (e) {
							return (e['type']['name']=='reference');
						},
						parent['fields']
					);
					moduleFieldTypes = {};
					$.each(modules, function (k, v) {
						moduleFieldTypes[k] = dict(map(
							function (e) {
								return [e['name'], e['type']];
							},
							filteredFields(v['fields'])
						));
					});

					function getFieldType(fullFieldName) {
						var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
						if (group==null) {
							var fieldModule = moduleName;
							var fieldName = fullFieldName;
						} else {
							var fieldModule = group[2];
							var fieldName = group[3];
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
									return [name+' : ('+moduleName+') '+field['name'], label+' : ('+modules[moduleName]['label']+') '+field['label']];
								},
								filteredFields(modules[moduleName]['fields'])
							);
						}
						return reduceR(concat, map(forModule, referenceField['type']['refersTo']), []);
					}

					referenceFields = reduceR(concat, map(fieldReferenceNames, referenceFieldTypes), []);
					fieldLabels = dict(parentFields.concat(referenceFields));

					function addCondition(groupid, condid) {
						if ($('#save_condition_group_'+groupid).length <= 0) {
							var group_condition_html = '';
							if ($('.condition_group_block').length > 0) {
								group_condition_html = '<div class="condition_group_join_block" id="condition_group_'+groupid+'_joincondition" > \
									<select id="save_condition_group_'+groupid+'_joincondition" class="slds-select cejoincondition" style="width:fit-content;" onchange="updateWSSQL();"></select></div>';
							}
							$('#save_conditions').append(`${group_condition_html}
								<div id="condition_group_${groupid}" class="condition_group_block" >
									<div id="save_condition_group_${groupid}" class="save_condition_group">
									</div>
									<div>
										<button class="slds-button slds-button_neutral" type="button" id="add_group_condition_${groupid}">
											<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
											</svg>
											${alert_arr.LBL_NEW_CONDITION}
										</button>
										<button class="slds-button slds-button_neutral" type="button" id="save_conditions_add_${groupid}">
											<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
											</svg>
											${alert_arr.LBL_NEW_CONDITION_GROUP_BUTTON_LABEL}
										</button>
									</div>
								</div>`
							);
							if ($('.condition_group_block').length > 0) {
								var fjgc = $('#save_condition_group_'+groupid+'_joincondition');
								fillOptions(fjgc, JoinConditionOptions);
							}
							var gpcond = $('#add_group_condition_'+groupid);
							gpcond.bind('click', function () {
								addCondition(groupid, condno++);
							});

							$('#save_conditions_add_'+groupid).bind('click', function () {
								addCondition(groupno, condno++);
							});
							Array.from(document.querySelectorAll('button[id^="save_conditions_add_"]')).forEach(x => x.style.visibility='hidden');
							document.getElementById('save_conditions_add_'+groupid).style.visibility = 'visible';

							var rem_group_img = $('#save_condition_group_'+groupid+'_remove');
							rem_group_img.bind('click', function () {
								removeConditionGroup(groupid);
							});

							// First set groupno to highest groupid
							if (groupno < groupid) {
								groupno = groupid;
							}
							// Once groupno is same as highest groupid, increment it for next group usage
							if (groupid == groupno) {
								groupno += 1;
							}
						}

						$('#save_condition_group_'+groupid).append(
							`<div id="save_condition_${condid}" class='slds-grid slds-gutters_x-small slds-m-bottom_large'>
							<div class="slds-col slds-size_4-of-8">
								<input type="hidden" id="save_condition_${condid}_groupid" class="groupid" value="${groupid}" />
								<select id="save_condition_${condid}_fieldname" class="slds-select cefieldname" onchange="updateWSSQL();"></select>
							</div>
							<div class="slds-col slds-size_1-of-8">
								<select id="save_condition_${condid}_operation" class="slds-select ceoperation" onchange="updateWSSQL();"></select>
							</div>
							<div class="slds-col slds-size_3-of-8">
								<input type="hidden" id="save_condition_${condid}_value_type" class="ceexpressiontype" />
								<input type="text" id="save_condition_${condid}_value" class="slds-input ceexpressionvalue" readonly style="border:1px solid #dddbda;width:64%;" onchange="updateWSSQL();" />
								<select id="save_condition_${condid}_joincondition" class="slds-select cejoincondition" style="width:fit-content;" onchange="updateWSSQL();"></select>
								<span id="save_condition_${condid}_remove">
									<button class="slds-button slds-button_icon slds-button_icon-border-filled ceremovebutton" title="${alert_arr.JSLBL_Delete}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
									</svg>
									<span class="slds-assistive-text">${alert_arr.JSLBL_Delete}</span>
									</button>
								</span>
							</div>
							</div>`
						);
						resetJoinCondition(groupid, condid);

						var fe = $('#save_condition_'+condid+'_fieldname');
						fillOptions(fe, fieldLabels);

						var fjc = $('#save_condition_'+condid+'_joincondition');
						fillOptions(fjc, JoinConditionOptions);

						var fullFieldName = fe.val();

						resetFields(getFieldType(fullFieldName), condid);

						var re = $('#save_condition_'+condid+'_remove');
						re.bind('click', function () {
							removeCondition(groupid, condid);
						});

						fe.bind('change', function () {
							var select = $(this);
							var condNo = select.prop('id').match(/save_condition_(\d+)_fieldname/)[1];
							var fullFieldName = $(this).val();
							resetFields(getFieldType(fullFieldName), condNo);
						});

						var condition = $('#save_condition_'+condid+'_operation');
						condition.bind('change', function () {
							var value = $(this).val();
							if (value == 'is empty' || value == 'is not empty') {
								$('#save_condition_'+condid+'_value').hide();
							} else {
								$('#save_condition_'+condid+'_value').show();
							}
						});
					}
					addConditionFunction = addCondition;
					groupno=0;
					condno=0;
					if (conditions) {
						$.each(conditions, function (i, condition) {
							var fieldname = condition['fieldname'];
							if (fieldname == '') {
								return;
							}
							var groupid = condition['groupid'];
							if (typeof(groupid)=='undefined' || groupid == '') {
								groupid = 0;
							}
							addCondition(groupid, condno);
							$(format('#save_condition_%s_fieldname', condno)).val(fieldname);
							resetFields(getFieldType(fieldname), condno);
							$(format('#save_condition_%s_operation', condno)).val(condition['operation']);
							$('#dump').html(condition['value']);
							var text = $('#dump').text();
							if (condition['operation'] == 'is empty' || condition['operation'] == 'is not empty') {
								$(format('#save_condition_%s_value', condno)).hide();
							}
							$(format('#save_condition_%s_value', condno)).val(text);
							$(format('#save_condition_%s_value_type', condno)).val(condition['valuetype']);
							if (condition['joincondition'] != '') {
								$(format('#save_condition_%s_joincondition', condno)).val(condition['joincondition']);
							}
							if (condition['groupjoin'] != '') {
								$(format('#save_condition_group_%s_joincondition', groupid)).val(condition['groupjoin']);
							}
							condno+=1;
						});
					} else {
						document.getElementById('startwhennoconditions').style.display = 'flex';
						$('#startwhennoconditionsbutton').unbind('click').bind('click', function () {
							document.getElementById('startwhennoconditions').style.display = 'none';
							addCondition(groupno, condno++);
						});
					}

					$('#save_submit').unbind('click').bind('click', function () {
						var conditions = [];
						var i=0;
						$('#save_conditions').children('.condition_group_block').each(function (j, conditiongroupblock) {
							$(conditiongroupblock).children('.save_condition_group').each(function (k, conditiongroup) {
								$(conditiongroup).children().each(function (l) {
									var fieldname = this.querySelector('div > .cefieldname').value;
									var operation = this.querySelector('div > .ceoperation').value;
									var value = this.querySelector('div > .ceexpressionvalue').value;
									var valuetype = this.querySelector('div > .ceexpressiontype').value;
									var joincondition = this.querySelector('div > .cejoincondition').value;
									var groupid = this.querySelector('div > .groupid').value;
									var groupjoin = '';
									if (groupid != '') {
										let scgj = document.getElementById('save_condition_group_'+groupid+'_joincondition');
										if (scgj != null) {
											groupjoin = scgj.value;
										}
									}
									var condition = {
										fieldname:fieldname,
										operation:operation,
										value:value,
										valuetype:valuetype,
										joincondition:joincondition,
										groupid:groupid,
										groupjoin:groupjoin
									};
									conditions[i++]=condition;
								});
							});
						});
						var out = '';
						if (conditions.length!=0) {
							out = JSON.stringify(conditions);
						}
						$('#save_conditions_json').val(out);
						document.forms['EditView'].submit();
					});
					pageLoadingPopup.close();
					this.dispatchEvent(new Event('condition_builder_module_changed'));
					updateWSSQL();
				}));
			}));
		}));
	}

	function getMetaInformation() {
		return {
			'parentFields': parentFields,
			'referenceFieldTypes': referenceFieldTypes,
			'moduleFieldTypes': moduleFieldTypes,
			'referenceFields': referenceFields,
			'fieldLabels': fieldLabels
		};
	}

	$(document).ready(changeModule);
	this.changeModule = changeModule;
	this.getMetaInformation = getMetaInformation;
	this.cbaccess = vtinst;
	return this;
}

