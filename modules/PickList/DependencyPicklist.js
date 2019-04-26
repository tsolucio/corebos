/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var modifiedMappingValues = new Array();

function changeDependencyPicklistModule() {
	document.getElementById('status').style.display='inline';
	var oModulePick = document.getElementById('pickmodule');
	var module=oModulePick.options[oModulePick.selectedIndex].value;

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PickListAjax&module=PickList&directmode=ajax&file=PickListDependencySetup&moduleName='+encodeURIComponent(module)
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('picklist_datas').innerHTML=response;
	});
}

function addNewDependencyPicklist() {
	var selectedModule = document.getElementById('pickmodule').value;
	if (selectedModule == '') {
		alert(alert_arr.ERR_SELECT_MODULE_FOR_DEPENDENCY);
		return false;
	}

	document.getElementById('status').style.display='inline';

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PickListAjax&module=PickList&directmode=ajax&file=PickListDependencySetup&submode=editdependency&moduleName='+encodeURIComponent(selectedModule)
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		modifiedMappingValues = new Array();
		document.getElementById('picklist_datas').innerHTML=response;
	});
}

function editNewDependencyPicklist(module) {
	document.getElementById('status').style.display='inline';

	var sourcePick = document.getElementById('sourcefield');
	var sourceField = sourcePick.options[sourcePick.selectedIndex].value;

	var targetPick = document.getElementById('targetfield');
	var targetField = targetPick.options[targetPick.selectedIndex].value;

	if (sourceField == targetField) {
		document.getElementById('status').style.display='none';
		alert(alert_arr.ERR_SAME_SOURCE_AND_TARGET);
		return false;
	}

	var urlstring = 'moduleName='+encodeURIComponent(module)+'&sourcefield='+sourceField+'&targetfield='+targetField;

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PickListAjax&module=PickList&directmode=ajax&file=PickListDependencySetup&submode=editdependency&'+urlstring
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('picklist_datas').innerHTML=response;
	});
}

function editDependencyPicklist(module, sourceField, targetField) {
	document.getElementById('status').style.display='inline';

	var urlstring = 'moduleName='+encodeURIComponent(module)+'&sourcefield='+sourceField+'&targetfield='+targetField;

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PickListAjax&module=PickList&directmode=ajax&file=PickListDependencySetup&submode=editdependency&'+urlstring
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		modifiedMappingValues = new Array();
		var element = document.getElementById('picklist_datas');
		element.innerHTML=response;
		vtlib_executeJavascriptInElement(element);
	});
}

function deleteDependencyPicklist(module, sourceField, targetField, msg) {
	if (confirm(msg)) {
		document.getElementById('status').style.display='inline';

		var urlstring = 'moduleName='+encodeURIComponent(module)+'&sourcefield='+sourceField+'&targetfield='+targetField;

		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=PickListAjax&module=PickList&directmode=ajax&file=PickListDependencySetup&submode=deletedependency&'+urlstring
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('picklist_datas').innerHTML=response;
		});
	} else {
		return false;
	}
}

function saveDependency(module) {
	document.getElementById('status').style.display='inline';

	var dependencyMapping = serializeData();
	if (dependencyMapping == false) {
		document.getElementById('status').style.display='none';
		return false;
	}
	var data = {
		'moduleName' : module,
		'dependencymapping' : dependencyMapping
	};

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PickListAjax&module=PickList&directmode=ajax&file=PickListDependencySetup&submode=savedependency',
		data : data
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('picklist_datas').innerHTML=response;
	});
}

function serializeData() {
	var sourceField = document.getElementById('sourcefield').options[document.getElementById('sourcefield').selectedIndex].value;
	var targetField = document.getElementById('targetfield').options[document.getElementById('targetfield').selectedIndex].value;
	var maxMappingCount = modifiedMappingValues.length;
	var valueMapping = [];

	for (var i=0; i<maxMappingCount; ++i) {
		var sourceValueId = modifiedMappingValues[i];
		var sourceValue = document.getElementById(sourceValueId).value;
		var mappingIndex = sourceValueId.replace(sourceField, '');
		var node = document.getElementById('valueMapping'+mappingIndex);
		if (node != null && typeof(node) != 'undefined') {
			var targetValueNodes = document.getElementsByName('valueMapping'+mappingIndex);
			var targetValues = [];
			for (var j=0; j<targetValueNodes.length; ++j) {
				var targetValueNode = targetValueNodes[j];
				if (targetValueNode != null && targetValueNode.parentNode != null
					&& jQuery(targetValueNode).parent().hasClass('selectedCell')) {
					targetValues.push(targetValueNode.value);
				}
			}

			if (targetValues.length == 0) {
				alert(alert_arr.ERR_ATLEAST_ONE_VALUE_FOR + ' ' + sourceValue);
				return false;
			}

			valueMapping[i] = {
				'sourcevalue':sourceValue,
				'targetvalues':targetValues
			};
		}
	}

	var formData = {
		'sourcefield': sourceField,
		'targetfield': targetField,
		'valuemapping': valueMapping
	};
	return JSON.stringify(formData);
}

function selectSourceValue(sourceValueIndex) {
	if (sourceValueIndex < 0) {
		return;
	}
	if (document.getElementById('sourceValue'+sourceValueIndex) == null) {
		return;
	}
	document.getElementById('sourceValue'+sourceValueIndex).checked = true;
}

function selectTargetValue(sourceIndex, targetValue) {
	var targetElements = jQuery('input[name=\'valueMapping'+sourceIndex+'\']');

	targetElements.each(function () {
		if (jQuery(this).val() == targetValue) {
			selectCell(jQuery(this).parent());
		}
	});
}

function unselectTargetValue(sourceIndex, targetValue) {
	var targetElements = jQuery(document.getElementsByName('valueMapping'+sourceIndex));
	targetElements.each(function () {
		if (jQuery(this).val() == targetValue) {
			unselectCell(jQuery(this).parent());
		}
	});
}

function selectCell(element) {
	if (element != null) {
		jQuery(element).removeClass('unselectedCell');
		jQuery(element).addClass('selectedCell');
	}
}

function unselectCell(element) {
	if (element != null) {
		jQuery(element).removeClass('selectedCell');
		jQuery(element).addClass('unselectedCell');
	}
}

function loadMappingForSelectedValues() {
	var sourceElements = jQuery('input[name="selectedSourceValues"]:checked');
	var classElements = jQuery('.picklistValueMapping');
	classElements.hide();

	sourceElements.each(function () {
		var selectedElementId = (((jQuery(this).val()).replace(/(\W)/gi, '\\$1')).replace(/\\\s/gi, '.'));
		var selectedElementCells = jQuery('.'+selectedElementId);
		selectedElementCells.show();
	});
}

function handleCellClick(event, element) {
	if (element.tagName == 'TD') {
		if (jQuery(element).hasClass('selectedCell')) {
			unselectCell(element);
		} else {
			selectCell(element);
		}
		var selectedSourceId = (jQuery(element).prop('id')).slice(7);
		if (typeof selectedSourceId != 'undefined' && modifiedMappingValues.indexOf(selectedSourceId) == -1) {
			modifiedMappingValues.push(selectedSourceId);
		}
	}
}

var isMouseDown = false;

function handleCellMouseDown(event, element) {
	isMouseDown = true;
	handleCellClick(event, element);
	return false;
}

function handleCellMouseOver(event, element) {
	if (isMouseDown) {
		handleCellClick(event, element);
	}
}

function handleCellMouseUp(event, element) {
	isMouseDown = false;
}
