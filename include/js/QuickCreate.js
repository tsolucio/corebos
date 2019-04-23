/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function qcemptyCheck(fldName, fldLabel, fldType) {
	var currObj=window.document.QcEditView[fldName];
	if (fldType=='text') {
		if (currObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) {
			alert(fldLabel+alert_arr.CANNOT_BE_EMPTY);
			currObj.focus();
			return false;
		} else {
			return true;
		}
	} else {
		if (trim(currObj.value) == '') {
			alert(fldLabel+alert_arr.CANNOT_BE_NONE);
			return false;
		} else {
			return true;
		}
	}
}

function qcdateValidate(fldName, fldLabel, type) {
	if (patternValidateObject(window.document.QcEditView[fldName], fldLabel, 'DATE')==false) {
		return false;
	}
	return dateValidateObject(window.document.QcEditView[fldName], fldLabel, type);
}

function qcdateComparison(fldName1, fldLabel1, fldName2, fldLabel2, type) {
	return dateComparisonObject(window.document.QcEditView[fldName1], fldLabel1, window.document.QcEditView[fldName2], fldLabel2, type);
}

function qcintValidate(fldName, fldLabel) {
	var val=window.document.QcEditView[fldName].value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	if (isNaN(val) || (val.indexOf('.')!=-1 && fldName != 'potential_amount')) {
		alert(alert_arr.INVALID+fldLabel);
		window.document.QcEditView[fldName].focus();
		return false;
	} else if ((fldName != 'employees' || fldName != 'noofemployees') && (val < -2147483648 || val > 2147483647)) {
		alert(fldLabel +alert_arr.OUT_OF_RANGE);
		return false;
	} else if ((fldName == 'employees' || fldName == 'noofemployees') && (val < 0 || val > 2147483647)) {
		alert(fldLabel +alert_arr.OUT_OF_RANGE);
		return false;
	} else {
		return true;
	}
}

function qcnumConstComp(fldName, fldLabel, type, constval) {
	return numConstCompObject(window.document.QcEditView[fldName], fldLabel, type, constval);
}

function qcdateTimeValidate(dateFldName, timeFldName, fldLabel, type) {
	return dateTimeValidateObject(window.document.QcEditView[dateFldName], window.document.QcEditView[timeFldName], fldLabel, type);
}

function QCreate(qcoptions) {
	var module = qcoptions.options[qcoptions.options.selectedIndex].value;
	if (module != 'none') {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method:'POST',
			url:'index.php?module='+module+'&action='+module+'Ajax&file=QuickCreate'
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('qcform').style.display='inline';
			document.getElementById('qcform').innerHTML = response;
			jQuery('#qcform').draggable();
			// Evaluate all the script tags in the response text.
			var scriptTags = document.getElementById('qcform').getElementsByTagName('script');
			for (var i = 0; i< scriptTags.length; i++) {
				var scriptTag = scriptTags[i];
				eval(scriptTag.innerHTML);
			}
			posLay(qcoptions, 'qcform');
		});
	} else {
		hide('qcform');
	}
}
