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
 * On Change handler for select box.
 */
FieldDependencies.prototype.actOnSelectChange = function (event) {
	var sourcenode = event.target;
	var sourcename = sourcenode.name;
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
				sourcevalue=(document.getElementById(field)!=undefined ? document.getElementById(field).value : document.getElementsByName(field).item(0).value);
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
				if (responsibleConfig['actions']['function'] !== undefined && responsibleConfig['actions']['function'].length > 0) {
					this.callFunc(sourcename, responsibleConfig['actions']['function']);
				}
			} else {
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
			}
		}
	}
};

/**
 * Core function to handle the state of field value changes and
 * trigger dependent:change event if (Event.fire API is available - Prototype 1.6)
 */
FieldDependencies.prototype.fieldOptions = function (sourcename, targetFields, type) {
	if (targetFields != null && targetFields != undefined) {
		for (var i=0; i<targetFields.length; i++) {
			var targetname=targetFields[i]['field'];
			var targetElem=document.getElementById(targetname);
			if (targetname != sourcename && targetElem!=undefined) { // avoid loop, target field can not be the same as responsible field
				var targetvalues=targetFields[i]['options'];
				var targetnode = jQuery('[name="'+targetname+'"]', this.baseform);
				var selectedtargetvalue = targetnode.val();

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
					if ( (targetvalues == false || targetvalues.indexOf(targetoption.val()) !== -1) && type==='setoptions') {
						var optionNode = jQuery(document.createElement('option'));
						targetnode.append(optionNode);
						optionNode.text(targetoption.text());
						optionNode.val(targetoption.val());
					} else if ( (targetvalues.indexOf(targetoption.val()) === -1) && type==='deloptions') {
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
			document.getElementsByName(field).item(0).value=value;
		}
	}

};

FieldDependencies.prototype.fieldHide = function (hideFields) {
	var field='';
	for (var i=0; i<hideFields.length; i++) {
		field=hideFields[i]['field'];
		document.getElementById('td_'+field).style.visibility='hidden';
		document.getElementById('td_val_'+field).style.visibility='hidden';
	}
};

FieldDependencies.prototype.fieldShow = function (hideFields) {
	var field='';
	for (var i=0; i<hideFields.length; i++) {
		field=hideFields[i]['field'];
		document.getElementById('td_'+field).style.visibility='visible';
		document.getElementById('td_val_'+field).style.visibility='visible';
	}
};

FieldDependencies.prototype.fieldReadonly = function (readonlyFields) {
	var field='';
	for (var i=0; i<readonlyFields.length; i++) {
		field=readonlyFields[i]['field'];
		document.getElementById(field+'_hidden').innerHTML=document.getElementsByName(field).item(0).value;
		document.getElementById(field+'_hidden').style.display='inline';
		document.getElementsByName(field).item(0).style.display='none';
	}
};

FieldDependencies.prototype.fieldEditable = function (readonlyFields) {
	var field='';
	for (var i=0; i<readonlyFields.length; i++) {
		field=readonlyFields[i]['field'];
		document.getElementsByName(field).item(0).style.display='inline';
		document.getElementById(field+'_hidden').style.display='none';
	}
};

FieldDependencies.prototype.blockCollapse = function (collapseBlocks) {
	var block='', elements;
	for (var i=0; i<collapseBlocks.length; i++) {
		block=collapseBlocks[i]['block'];
		elements=document.getElementsByName('tbl'+block+'Content');
		for (var j=0; j<elements.length; j++) {
			elements[j].style.display='none';
		}
	}
};

FieldDependencies.prototype.blockOpen = function (openBlocks) {
	var block='', elements;
	for (var i=0; i<openBlocks.length; i++) {
		block=openBlocks[i]['block'];
		elements=document.getElementsByName('tbl'+block+'Content');
		for (var j=0; j<elements.length; j++) {
			elements[j].style.display='';
		}
	}
};
FieldDependencies.prototype.blockDisappear = function (disappBlock) {
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

FieldDependencies.prototype.blockAppear = function (appBlock) {
	var block='', elements;
	for (var i=0; i<appBlock.length; i++) {
		block=appBlock[i]['block'];
		elements=document.getElementsByName('tbl'+block+'Content');
		for (var j=0; j<elements.length; j++) {
			elements[j].style.display='';
		}
		document.getElementById('tbl'+block+'Head').style.display='';
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
			//document.getElementsByName(sourcename).item(0).onchange=window[funcName](sourcename,action_field,fldValue,fld.data('initialVal'),parameters);
			window[funcName](sourcename, action_field, fldValue, fld.data('initialVal'), parameters);
		}
		if (typeof(fld.data('initialVal')) == 'undefined') {
			fld.data('initialVal', fldValue);
		}
	}
};
