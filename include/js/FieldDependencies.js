/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/**
 * Usage:
 *
 * (new FieldDependencies(datasource)).init(document.forms['EditView']); // Default form EditView in case not provided.
 *
 * datasource Format:
 *
 * datasource = {
 * 		"sourcefieldname1" : {
 *
 * 			"sourcevalue1" : {
 * 				"targetfieldname" : ["targetvalue1", "targetvalue2"]
 *	 		},
 * 			"sourcevalue2" : {
 * 				"targetfieldname" : ["targetvalue3", "targetvalue4"]
 * 			},
 *
 * 			"sourcevalue3" : {
 * 				"targetfieldname" : false // This will enable all the values in the target fieldname
 * 			},
 *
 * 			// NOTE: All source values (option) needs to be mapped in the datasource
 *
 * 		},
 *
 * 		"sourcefieldname2" : {
 * 			// ...
 * 		}
 * }
 *
 * NOTE: Event.fire(targetfieldnode, 'dependent:change'); is triggered on the field value changes.
 *
 */

/**
 * Class FieldDependencies
 * @param datasource
 */
function FieldDependencies(datasource) {
	this.baseform = false;
	this.DS = {};
	this.initDS(datasource);
}

/**
 * Initialize the data source
 */
FieldDependencies.prototype.initDS = function (datasource) {
	if (typeof(datasource) != 'undefined') {
		this.DS = datasource;
	}
};

/**
 * Initialize the form fields (setup onchange and dependent:change) listeners
 * and trigger the onchange handler for the loaded select fields.
 *
 * NOTE: Only select fields are supported.
 *
 */
FieldDependencies.prototype.setup = function (sourceform, datasource) {
	var thisContext = this;
	if (typeof(sourceform) == 'undefined') {
		this.baseform = document.forms['EditView'];
	} else {
		this.baseform = sourceform;
		thisContext.actOnDetailViewLoad();
	}

	this.initDS(datasource);

	if (!this.baseform) {
		return;
	}

	var nodelist = document.querySelectorAll('input,select');
	for (var i = 0; i < nodelist.length; i++) {
		// we should use addEventListener here but it doesn't work on the jscalendar element nor on the initial loading of the page
		if (nodelist[i].id.substring(0, 12)=='jscal_field_') {
			nodelist[i].onchange = function (ev) {
				thisContext.actOnSelectChange(ev);
			};
		} else {
			jQuery('#'+nodelist[i].id).bind('change', function (ev) {
				thisContext.actOnSelectChange(ev);
			});
		}
	}
};

/**
 * Initialize the form fields (setup onchange and dependent:change) listeners
 * NOTE: Only select fields are supported.
 */
FieldDependencies.prototype.init = function (sourceform, datasource) {
	this.setup(sourceform, datasource);
	for (var sourcename in this.DS) {
		jQuery('[name="'+sourcename+'"]', this.baseform).trigger('change');
	}
};

/**
 * On Loading of Page handler of detail view.
 */
FieldDependencies.prototype.actOnDetailViewLoad = function () {
	var sourcename = Object.keys(this.DS)[0];
	this.controlActions(sourcename);
};

/**
 * On Change handler for select box.
 */
FieldDependencies.prototype.actOnSelectChange = function (event) {
	var sourcenode = event.target;
	var sourcename = sourcenode.name;
	this.controlActions(sourcename);
};

/**
 * Control all actions performed on both edit and detail views.
 */
FieldDependencies.prototype.controlActions = function (sourcename) {
	var sourcevalue ='';
	var field, comparator, value, columncondition, fieldName, groupid, conditionCurr, newGroup;
	var i=0;
	var conditions=new Array();
	if (this.DS[sourcename]!==undefined) {
		for (i=0; i<this.DS[sourcename].length; i++) {
			var responsibleConfig=this.DS[sourcename][i];
			conditions=responsibleConfig['conditions']!=='' ?  JSON.parse(responsibleConfig['conditions']) : conditions;
			var conditionResp='';
			var condArray=new Array();
			var condOperatorArray=new Array();
			for (var j=0; j<conditions.length; j++) {
				newGroup=false;
				field=conditions[j]['columnname'];
				comparator=conditions[j]['comparator'];
				value=conditions[j]['value'];
				columncondition=conditions[j]['columncondition'];
				groupid=conditions[j]['groupid'];
				fieldName=field.split(':');
				field=fieldName[1];
				sourcevalue=this.getFieldValue(field);
				switch (comparator) {
				case 'e': conditionResp+= sourcevalue===value; break;
				case 'n': conditionResp+= sourcevalue!==value; break;
				case 's': conditionResp+= sourcevalue.startsWith(value); break;
				case 'Ns': conditionResp+= !sourcevalue.startsWith(value); break;
				case 'ew': conditionResp+= sourcevalue.endsWith(value); break;
				case 'New': conditionResp+= !sourcevalue.endsWith(value); break;
				case 'c': conditionResp+= sourcevalue.indexOf(value)!==-1; break;
				case 'k': conditionResp+= sourcevalue.indexOf(value)===-1; break;
				case 'l': conditionResp+= parseInt(sourcevalue) < parseInt(value); break;
				case 'g': conditionResp+= parseInt(sourcevalue) > parseInt(value); break;
				case 'm': conditionResp+= parseInt(sourcevalue) <= parseInt(value); break;
				case 'h': conditionResp+= parseInt(sourcevalue) >= parseInt(value); break;
				default:
					conditionResp+=false; break;
				}
				if (j<conditions.length - 1 && groupid!=conditions[j+1]['groupid']) {
					condArray.push(conditionResp);
					conditionCurr=conditions[j]['columncondition'].toLowerCase()==='or' ? ' || ' : ' && ';
					condOperatorArray.push(conditionCurr);
					conditionResp='';
					newGroup=true;
				}
				if (columncondition!=='' && !newGroup) {
					columncondition=conditions[j]['columncondition'].toLowerCase()==='or' ? ' || ' : ' && ';
					conditionResp +=' '+columncondition+' ';
				} else if (columncondition=='') {
					condArray.push(conditionResp);
					condOperatorArray.push('');
				}

			}
			conditionResp='';
			for (j=0; j<condArray.length; j++) {
				conditionResp +='('+condArray[j]+')'+condOperatorArray[j];
			}
			if (eval(conditionResp) || conditions.length===0) {
				if (responsibleConfig['actions']['change']!== undefined && responsibleConfig['actions']['change'].length > 0) {
					this.fieldValueChange(responsibleConfig['actions']['change']);
				}
				if (responsibleConfig['actions']['hide'] !== undefined && responsibleConfig['actions']['hide'].length > 0) {
					this.fieldHide(responsibleConfig['actions']['hide']);
				}
				if (responsibleConfig['actions']['readonly'] !== undefined && responsibleConfig['actions']['readonly'].length > 0) {
					this.fieldReadonly(responsibleConfig['actions']['readonly']);
				}
				if (responsibleConfig['actions']['deloptions'] !== undefined && responsibleConfig['actions']['deloptions'].length > 0) {
					this.fieldOptions(sourcename, responsibleConfig['actions']['deloptions'], 'deloptions');
				}
				if (responsibleConfig['actions']['setoptions'] !== undefined && responsibleConfig['actions']['setoptions'].length > 0) {
					this.fieldOptions(sourcename, responsibleConfig['actions']['setoptions'], 'setoptions');
				}
				if (responsibleConfig['actions']['collapse'] !== undefined && responsibleConfig['actions']['collapse'].length > 0) {
					this.blockCollapse(responsibleConfig['actions']['collapse']);
				}
				if (responsibleConfig['actions']['open'] !== undefined && responsibleConfig['actions']['open'].length > 0) {
					this.blockOpen(responsibleConfig['actions']['open']);
				}
				if (responsibleConfig['actions']['disappear'] !== undefined && responsibleConfig['actions']['disappear'].length > 0) {
					this.blockDisappear(responsibleConfig['actions']['disappear']);
				}
				if (responsibleConfig['actions']['appear'] !== undefined && responsibleConfig['actions']['appear'].length > 0) {
					this.blockAppear(responsibleConfig['actions']['appear']);
				}
				if (responsibleConfig['actions']['setclass'] !== undefined && responsibleConfig['actions']['setclass'].length > 0) {
					this.addCSS(responsibleConfig['actions']['setclass']);
				}
				if (responsibleConfig['actions']['function'] !== undefined && responsibleConfig['actions']['function'].length > 0) {
					this.callFunc(sourcename, responsibleConfig['actions']['function']);
				}
			} else {
				if ((responsibleConfig['actions']['setoptions']) !== undefined && responsibleConfig['actions']['setoptions'].length > 0) {
					this.handleEditViewSetOptions(responsibleConfig['actions']['setoptions']);
				}
				if (responsibleConfig['actions']['hide'] !== undefined && responsibleConfig['actions']['hide'].length > 0) {
					this.fieldShow(responsibleConfig['actions']['hide']);
				}
				if (responsibleConfig['actions']['readonly'] !== undefined && responsibleConfig['actions']['readonly'].length > 0) {
					this.fieldEditable(responsibleConfig['actions']['readonly']);
				}
				if (responsibleConfig['actions']['collapse'] !== undefined && responsibleConfig['actions']['collapse'].length > 0) {
					this.blockOpen(responsibleConfig['actions']['collapse']);
				}
				if (responsibleConfig['actions']['disappear'] !== undefined && responsibleConfig['actions']['disappear'].length > 0) {
					this.blockAppear(responsibleConfig['actions']['disappear']);
				}
				if (responsibleConfig['actions']['setclass'] !== undefined && responsibleConfig['actions']['setclass'].length > 0) {
					this.removeCSS(responsibleConfig['actions']['setclass']);
				}
			}
		}
	}
};

/**
 * Core function to handle the state of field value changes and
 * trigger dependent:change event if (Event.fire API is available - Prototype 1.6)
 */
FieldDependencies.prototype.fieldOptions = function (sourcename, targetFields, type) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.fieldOptionsEditView(sourcename, targetFields, type);
	} else {
		this.fieldOptionsDetailView(sourcename, targetFields, type);
	}
};

FieldDependencies.prototype.fieldOptionsEditView = function (sourcename, targetFields, type) {
	if (targetFields != null && targetFields != undefined) {
		for (var i=0; i<targetFields.length; i++) {
			var targetname=targetFields[i]['field'];
			var targetElem=document.getElementById(targetname);
			if (targetname != sourcename && targetElem!=undefined) { // avoid loop, target field can not be the same as responsible field
				var targetvalues=targetFields[i]['options'];
				var targetnode = jQuery('[name="'+targetname+'"]', this.baseform);
				var selectedtargetvalue = targetvalues[0];

				// In IE we cannot hide the options!, the only way to achieve this effect is recreating the options list again.
				// To maintain implementation consistency, let us keep copy of options in select node and use it for re-creation
				if (typeof(targetnode.data('allOptions')) == 'undefined') {
					var allOptions = [];
					jQuery('option', targetnode).each(function (index, option) {
						allOptions.push(option);
					});
					targetnode.data('allOptions', allOptions);
				}
				var targetoptions = targetnode.data('allOptions');
				// Remove the existing options nodes from the target selection
				jQuery('option', targetnode).remove();

				for (var index = 0; index < targetoptions.length; ++index) {
					var targetoption = jQuery(targetoptions[index]);
					// Show the option if field mapping matches the option value or there is not field mapping available.
					if ((!targetvalues || targetvalues.indexOf(targetoption.val()) !== -1) && type==='setoptions') {
						var optionNode = jQuery(document.createElement('option'));
						targetnode.append(optionNode);
						optionNode.text(targetoption.text());
						optionNode.val(targetoption.val());
					} else if ((targetvalues.indexOf(targetoption.val()) === -1) && type==='deloptions') {
						var optionNode = jQuery(document.createElement('option'));
						targetnode.append(optionNode);
						optionNode.text(targetoption.text());
						optionNode.val(targetoption.val());
					}
				}
				targetnode.val(selectedtargetvalue);
				targetnode.trigger('change');
			}
		}
	}
};

FieldDependencies.prototype.handleEditViewSetOptions = function (targetFields) {
	if (targetFields != null && targetFields != undefined) {
		for (var i=0; i<targetFields.length; i++) {
			var targetname=targetFields[i]['field'];
			var targetvalues=targetFields[i]['options'];
			var targetselected = $('#'+targetname).find(":selected").text();
			var targetnode = jQuery('[name="'+targetname+'"]', this.baseform);
			if (targetselected == targetvalues[i]) {
				if (typeof(targetnode.data('allOptions')) == 'undefined') {
					var allOptions = [];
					jQuery('option', targetnode).each(function (index, option) {
						allOptions.push(option);
					});
					targetnode.data('allOptions', allOptions);
				}
				var targetoptions = targetnode.data('allOptions');
				jQuery('option', targetnode).remove();
				for (var index = 0; index < targetoptions.length; ++index) {
					var targetoption = jQuery(targetoptions[index]);
					var optionNode = jQuery(document.createElement('option'));
					targetnode.append(optionNode);
					optionNode.text(targetoption.text());
					optionNode.val(targetoption.val());
				}
				var firstval_as_selected = jQuery(targetoptions[0]);
				targetnode.val(firstval_as_selected.val());
				targetnode.trigger('change');
			}
		}
	}
};

FieldDependencies.prototype.fieldOptionsDetailView = function (sourcename, targetFields, type) {
	if (targetFields != null && targetFields != undefined) {
		for (var i=0; i<targetFields.length; i++) {
			var targetname=targetFields[i]['field'];
			var targetElem=document.getElementById('txtbox_'+targetname);
			if (targetname != sourcename && targetElem!=undefined) { // avoid loop, target field can not be the same as responsible field
				var targetvalues=targetFields[i]['options'];
				var targetnode = jQuery('[name="'+targetname+'"]', this.baseform);
				targetnode.push(targetElem);
				var selectedtargetvalue = targetvalues[0];

				// In IE we cannot hide the options!, the only way to achieve this effect is recreating the options list again.
				// To maintain implementation consistency, let us keep copy of options in select node and use it for re-creation
				if (typeof(targetnode.data('allOptions')) == 'undefined') {
					var allOptions = [];
					jQuery('option', targetnode).each(function (index, option) {
						allOptions.push(option);
					});
					targetnode.data('allOptions', allOptions);
				}
				var targetoptions = targetnode.data('allOptions');
				// Remove the existing options nodes from the target selection
				jQuery('option', targetnode).remove();

				for (var index = 0; index < targetoptions.length; ++index) {
					var targetoption = jQuery(targetoptions[index]);
					// Show the option if field mapping matches the option value or there is not field mapping available.
					if ((!targetvalues || targetvalues.indexOf(targetoption.val()) !== -1) && type==='setoptions') {
						// document.getElementById('dtlview_'+targetname).value = targetoption.val();
						document.getElementById('dtlview_'+targetname).innerText = targetoption.val();
						var optionNode = jQuery(document.createElement('option'));
						targetnode.append(optionNode);
						optionNode.text(targetoption.text());
						optionNode.val(targetoption.val());
					} else if ((targetvalues.indexOf(targetoption.val()) === -1) && type==='deloptions') {
						var optionNode = jQuery(document.createElement('option'));
						targetnode.append(optionNode);
						optionNode.text(targetoption.text());
						optionNode.val(targetoption.val());
					}
				}
				targetnode.val(selectedtargetvalue);
				targetnode.trigger('change');
			}
		}
	}
};

FieldDependencies.prototype.fieldValueChange = function (targetFields) {
	var field, value='';
	for (var i=0; i<targetFields.length; i++) {
		field=targetFields[i]['field'];
		value=targetFields[i]['value'];
		if (document.getElementsByName(field).item(0) !== undefined && document.getElementsByName(field).item(0) !== null) {
			let inputfld = document.getElementsByName(field).item(0);
			if (inputfld.type == 'checkbox') {
				inputfld.checked = !(value=='0' || value=='false' || value=='' || value=='null' || value=='yes');
			} else if (inputfld.type == 'hidden' && document.getElementById(field+'_display')!=null) {
				// reference field
				inputfld.value = value;
				ExecuteFunctions('getEntityName', 'getNameFrom='+value).then(function (data) {
					document.getElementById(field+'_display').value = JSON.parse(data);
				});
			} else {
				inputfld.value = value;
			}
		}
	}
};

FieldDependencies.prototype.getFieldValue = function (field) {
	var fld = document.getElementById(field);
	if (fld==undefined) {
		fld = document.getElementsByName(field).item(0);
	}
	if (fld.type == 'checkbox') {
		return (fld.checked ? '1' : '0');
	} else {
		return fld.value;
	}
};

FieldDependencies.prototype.fieldHide = function (hideFields) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.fieldHideEditView(hideFields);
	} else {
		this.fieldHideDetailView(hideFields);
	}
};

FieldDependencies.prototype.fieldHideEditView = function (hideFields) {
	var field='';
	for (var i=0; i<hideFields.length; i++) {
		field=hideFields[i]['field'];
		document.getElementById('td_'+field).style.visibility='hidden';
		document.getElementById('td_val_'+field).style.visibility='hidden';
	}
};

FieldDependencies.prototype.fieldHideDetailView = function (hideFields) {
	var field='';
	for (var i=0; i<hideFields.length; i++) {
		field=hideFields[i]['field'];
		document.getElementById('mouseArea_'+field).style.visibility='hidden';
		document.getElementById('mouseArea_'+field).previousSibling.previousSibling.style.visibility='hidden';
	}
};

FieldDependencies.prototype.fieldShow = function (hideFields) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.fieldShowEditView(hideFields);
	} else {
		this.fieldShowDetailView(hideFields);
	}
};

FieldDependencies.prototype.fieldShowEditView = function (hideFields) {
	var field='';
	for (var i=0; i<hideFields.length; i++) {
		field=hideFields[i]['field'];
		document.getElementById('td_'+field).style.visibility='visible';
		document.getElementById('td_val_'+field).style.visibility='visible';
	}
};

FieldDependencies.prototype.fieldShowDetailView = function (hideFields) {
	var field='';
	for (var i=0; i<hideFields.length; i++) {
		field=hideFields[i]['field'];
		document.getElementById('mouseArea_'+field).style.visibility='visible';
		document.getElementById('mouseArea_'+field).previousSibling.previousSibling.style.visibility='visible';
	}
};

FieldDependencies.prototype.addCSS = function (setClasses) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.addCSSEditView(setClasses);
	} else {
		this.addCSSDetailView(setClasses);
	}
};

FieldDependencies.prototype.addCSSEditView = function (setClasses) {
	fieldclass=setClasses[setClasses.length - 2]['fieldclass'];
	labelclass=setClasses[setClasses.length - 1]['labelclass'];
	var field='';
	for (var i=0; i<setClasses.length - 2; i++) {
		field=setClasses[i]['field'];
		document.getElementById('td_'+field).classList.add(labelclass);
		document.getElementById('td_val_'+field).classList.add(fieldclass);
	}
};

FieldDependencies.prototype.addCSSDetailView = function (setClasses) {
	fieldclass=setClasses[setClasses.length - 2]['fieldclass'];
	labelclass=setClasses[setClasses.length - 1]['labelclass'];
	var field='';
	for (var i=0; i<setClasses.length - 2; i++) {
		field=setClasses[i]['field'];
		document.getElementById('mouseArea_'+field).classList.add(fieldclass);
		document.getElementById('mouseArea_'+field).previousSibling.previousSibling.classList.add(labelclass);
	}
};

FieldDependencies.prototype.removeCSS = function (setClasses) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.removeCSSEditView(setClasses);
	} else {
		this.removeCSSDetailView(setClasses);
	}
};

FieldDependencies.prototype.removeCSSEditView = function (setClasses) {
	fieldclass=setClasses[setClasses.length - 2]['fieldclass'];
	labelclass=setClasses[setClasses.length - 1]['labelclass'];
	var field='';
	for (var i=0; i<setClasses.length - 2; i++) {
		field=setClasses[i]['field'];
		document.getElementById('td_'+field).classList.remove(labelclass);
		document.getElementById('td_val_'+field).classList.remove(fieldclass);
	}
};

FieldDependencies.prototype.removeCSSDetailView = function (setClasses) {
	fieldclass=setClasses[setClasses.length - 2]['fieldclass'];
	labelclass=setClasses[setClasses.length - 1]['labelclass'];
	var field='';
	for (var i=0; i<setClasses.length - 2; i++) {
		field=setClasses[i]['field'];
		document.getElementById('mouseArea_'+field).classList.remove(fieldclass);
		document.getElementById('mouseArea_'+field).previousSibling.previousSibling.classList.remove(labelclass);
	}
};

FieldDependencies.prototype.fieldReadonly = function (readonlyFields) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.fieldReadonlyEditView(readonlyFields);
	} else {
		this.fieldReadonlyDetailView(readonlyFields);
	}
};

FieldDependencies.prototype.fieldReadonlyEditView = function (readonlyFields) {
	var field='';
	for (var i=0; i<readonlyFields.length; i++) {
		field=readonlyFields[i]['field'];
		document.getElementById(field+'_hidden').innerHTML=document.getElementsByName(field).item(0).value;
		document.getElementById(field+'_hidden').style.display='inline';
		document.getElementsByName(field).item(0).style.display='none';
	}
};

FieldDependencies.prototype.fieldReadonlyDetailView = function (readonlyFields) {
	var field='';
	for (var i=0; i<readonlyFields.length; i++) {
		field=readonlyFields[i]['field'];
		document.getElementById('dtlview_'+field).innerHTML=document.getElementsByName(field).item(0).value;
		document.getElementById('dtlview_'+field).style.display='inline';
		document.getElementsByName(field).item(0).style.display='none';
	}
};

FieldDependencies.prototype.fieldEditable = function (readonlyFields) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.fieldEditableEditView(readonlyFields);
	} else {
		this.fieldEditableDetailView(readonlyFields);
	}
};

FieldDependencies.prototype.fieldEditableEditView = function (readonlyFields) {
	var field='';
	for (var i=0; i<readonlyFields.length; i++) {
		field=readonlyFields[i]['field'];
		document.getElementsByName(field).item(0).style.display='inline';
		document.getElementById(field+'_hidden').style.display='none';
	}
};

FieldDependencies.prototype.fieldEditableDetailView = function (readonlyFields) {
	var field='';
	for (var i=0; i<readonlyFields.length; i++) {
		field=readonlyFields[i]['field'];
		document.getElementsByName(field).item(0).style.display='inline';
		document.getElementById('dtlview_'+field).style.display='none';
	}
};

FieldDependencies.prototype.blockCollapse = function (collapseBlocks) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.blockCollapseEditView(collapseBlocks);
	} else {
		this.blockCollapseDetailView(collapseBlocks);
	}
};

FieldDependencies.prototype.blockCollapseEditView = function (collapseBlocks) {
	var block='', elements;
	for (var i=0; i<collapseBlocks.length; i++) {
		block=collapseBlocks[i]['block'];
		elements=document.getElementsByName('tbl'+block+'Content');
		for (var j=0; j<elements.length; j++) {
			elements[j].style.display='none';
		}
	}
};

FieldDependencies.prototype.blockCollapseDetailView = function (collapseBlocks) {
	var block='', elements;
	for (var i=0; i<collapseBlocks.length; i++) {
		block=collapseBlocks[i]['block'];
		elements=document.getElementById('tbl'+block);
		elements.style.display='none';
	}
};

FieldDependencies.prototype.blockOpen = function (openBlocks) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.blockOpenEditView(openBlocks);
	} else {
		this.blockOpenDetailView(openBlocks);
	}
};

FieldDependencies.prototype.blockOpenEditView = function (openBlocks) {
	var block='', elements;
	for (var i=0; i<openBlocks.length; i++) {
		block=openBlocks[i]['block'];
		elements=document.getElementsByName('tbl'+block+'Content');
		for (var j=0; j<elements.length; j++) {
			elements[j].style.display='';
		}
	}
};

FieldDependencies.prototype.blockOpenDetailView = function (openBlocks) {
	var block='', elements;
	for (var i=0; i<openBlocks.length; i++) {
		block=openBlocks[i]['block'];
		elements=document.getElementById('tbl'+block);
		elements.style.display='';
	}
};

FieldDependencies.prototype.blockDisappear = function (disappBlock) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.blockDisappearEditView(disappBlock);
	} else {
		this.blockDisappearDetailView(disappBlock);
	}
};

FieldDependencies.prototype.blockDisappearEditView = function (disappBlock) {
	var block='', elements;
	for (var i=0; i<disappBlock.length; i++) {
		block=disappBlock[i]['block'];
		elements=document.getElementsByName('tbl'+block+'Content');
		for (var j=0; j<elements.length; j++) {
			elements[j].style.display='none';
		}
		document.getElementById('tbl'+block+'Head').style.display='none';
	}
};

FieldDependencies.prototype.blockDisappearDetailView = function (disappBlock) {
	var block='', elements;
	for (var i=0; i<disappBlock.length; i++) {
		block=disappBlock[i]['block'];
		elements=document.getElementById('tbl'+block);
		elements.style.display='none';
		document.getElementById('tbl'+block).previousSibling.previousSibling.style.display='none';
	}
};

FieldDependencies.prototype.blockAppear = function (appBlock) {
	if (document.forms['EditView'] != undefined && document.forms['DetailView'] == undefined) {
		this.blockAppearEditView(appBlock);
	} else {
		this.blockAppearDetailView(appBlock);
	}
};

FieldDependencies.prototype.blockAppearEditView = function (appBlock) {
	var block='', elements;
	for (var i=0; i<appBlock.length; i++) {
		block=appBlock[i]['block'];
		elements=document.getElementsByName('tbl'+block+'Content');
		for (var j=0; j<elements.length; j++) {
			elements[j].style.display='';
		}
		document.getElementById('tbl'+block).style.display='';
	}
};

FieldDependencies.prototype.blockAppearDetailView = function (appBlock) {
	var block='', elements;
	for (var i=0; i<appBlock.length; i++) {
		block=appBlock[i]['block'];
		elements=document.getElementById('tbl'+block);
		document.getElementById('tbl'+block).style.display='';
		elements.previousSibling.previousSibling.style.display='';
	}
};

FieldDependencies.prototype.callFunc = function (sourcename, allParam) {
	for (var i=0; i<allParam.length; i++) {
		var funcName=allParam[i]['value'];
		var action_field=allParam[i]['field'];
		var parameters=allParam[i]['params'];
		var fldValue=document.getElementsByName(sourcename).item(0).value;
		var fld = jQuery('[name="'+sourcename+'"]', this.baseform);
		//check if the function is already declared
		//make sure it is not going to be called the first time the page is loaded
		if (window[funcName]!==undefined && typeof(fld.data('initialVal')) !== 'undefined') {
			window[funcName](sourcename, action_field, fldValue, fld.data('initialVal'), parameters);
		}
		if (typeof(fld.data('initialVal')) == 'undefined') {
			fld.data('initialVal', fldValue);
		}
	}
};
