/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
var globaldtlviewspanid = '';
var globaleditareaspanid = '';
var globaltxtboxid = '';
var globalfldtimeformat = '';
var itsonview=false;
var clipcopyclicked=false;
var clipcopyGenDoc=false;
// to retain the old value if we cancel the ajax edit
var globaltempvalue = '';
var globaluitype = '';

document.addEventListener('DOMContentLoaded', function () {
	GlobalVariable_getVariable('GenDoc_CopyLabelToClipboard', 0, '', gVTUserID).then(function (response) {
		var obj = JSON.parse(response);
		clipcopyGenDoc = (obj.GenDoc_CopyLabelToClipboard!='0' && obj.GenDoc_CopyLabelToClipboard!='false');
	}, function (error) {
		clipcopyGenDoc = false;
	});
});

function showHide(showId, hideId) {
	show(showId);
	fnhide(hideId);
}

function hndCancel(valuespanid, textareapanid, fieldlabel) {
	showHide(valuespanid, textareapanid);
	if (globaluitype == '56') {
		if (globaltempvalue == 1) {
			getObj(globaltxtboxid).checked = true;
		} else {
			getObj(globaltxtboxid).checked = false;
		}
	} else if (globaluitype == '50') {
		getObj(globaltxtboxid).value = globaltempvalue;
		getObj('timefmt_' + fieldlabel).innerHTML = (globalfldtimeformat != '24' ? globalfldtimeformat : '');
		getObj('inputtimefmt_' + fieldlabel).value = globalfldtimeformat;
	} else if (globaluitype != '53' && globaluitype != '33' && globaluitype != '3313' && globaluitype != '3314') {
		getObj(globaltxtboxid).value = globaltempvalue;
	}
	globaltempvalue = '';
	itsonview=false;
	return false;
}

function hndCancelOutsideClick() {
	if (itsonview) {
		hndCancel(globaldtlviewspanid, globaleditareaspanid, globalfieldlabel);
	}
	return false;
}

function hndMouseOver(uitype, fieldLabel) {
	var mouseArea='';
	mouseArea='mouseArea_'+ fieldLabel;
	if (itsonview) {
		return;
	}
	show('crmspanid');
	globaluitype = uitype;
	globaldtlviewspanid= 'dtlview_'+ fieldLabel;//valuespanid;
	globaleditareaspanid='editarea_'+ fieldLabel;//textareapanid;
	globalfieldlabel = fieldLabel;
	if (globaluitype == 53) {
		var assigntype = document.getElementsByName('assigntype');
		if (assigntype.length > 0) {
			var assign_type_U = assigntype[0].checked;
			var assign_type_G = false;
			if (assigntype[1]!=undefined) {
				assign_type_G = assigntype[1].checked;
			}
			if (assign_type_U) {
				globaltxtboxid= 'txtbox_U'+fieldLabel;
			} else if (assign_type_G) {
				globaltxtboxid= 'txtbox_G'+fieldLabel;
			}
		} else {
			globaltxtboxid= 'txtbox_U'+fieldLabel;
		}
	} else if (globaluitype == 50) {
		globalfldtimeformat = getObj('inputtimefmt_' + fieldLabel).value;
		globaltxtboxid ='txtbox_' + fieldLabel;
	} else {
		globaltxtboxid='txtbox_'+ fieldLabel;//textboxpanid;
	}
	var divObj = getObj('crmspanid');
	var crmy = findPosY(getObj(mouseArea));
	var crmx = findPosX(getObj(mouseArea));
	divObj.setAttribute('onclick', 'handleEdit();');
	divObj.style.left=(crmx+getObj(mouseArea).offsetWidth -divObj.offsetWidth)+'px';
	divObj.style.top=crmy+'px';
}

function handleCopyClipboard(event) {
	clipcopyclicked = true;
	if (clipcopyGenDoc) {
		let res = globaltxtboxid.substring(7);
		document.getElementById('clipcopylink').dataset.clipboardText = '{' + gVTModule + '.' + res + '}';
	} else {
		if (globaluitype != 53) {
			let temp = getObj(globaltxtboxid).value;
			if (globaluitype == 56) {
				temp = (getObj(globaltxtboxid).checked ? alert_arr.YES : alert_arr.NO);
			} else if (globaluitype == 50) {
				let res = globaltxtboxid.split('_');
				temp = getObj('txtbox_' + res[1]).value + ' ' + getObj('inputtimefmt_' + res[1]).value;
			} else if (globaluitype == 10) {
				let res = globaltxtboxid.substring(7);
				let dispbox = getObj(res + '_display');
				if (dispbox) {
					temp = dispbox.value;
				}
			}
			document.getElementById('clipcopylink').dataset.clipboardText = temp;
		} else {
			let assigne_value = getObj('hdtxt_assigned_user_id').value;
			document.getElementById('clipcopylink').dataset.clipboardText = assigne_value;
		}
	}
	return false;
}

function handleEdit(event) {
	if (clipcopyclicked) {
		return false;
	}
	show(globaleditareaspanid);
	fnhide(globaldtlviewspanid);
	if (((globaluitype == 15 || globaluitype == 16 || globaluitype == 1613 || globaluitype == 1614 || globaluitype == 1615) && globaltempvalue == '') ||
		(globaluitype != 53 && globaluitype != 15 && globaluitype != 16 && globaluitype != 1613 && globaluitype != 1614 && globaluitype != 1615)
	) {
		globaltempvalue = getObj(globaltxtboxid).value;
		if (getObj(globaltxtboxid).type != 'hidden') {
			getObj(globaltxtboxid).focus();
		}
	}
	fnhide('crmspanid');
	itsonview=true;
	if (event) {
		event.stopPropagation();
	}
	return false;
}

// trim both leading and trailing spaces
function trim(str) {
	var s = str.replace(/\s+$/, '');
	s = s.replace(/^\s+/, '');
	return s;
}

var genUiType = '';
var genFldValue = '';

function dtlViewAjaxDirectFieldSave(fieldValue, module, tableName, fieldName, crmId, okmsg) {
	var data = {
		'fldName' : fieldName,
		'fieldValue' : encodeURIComponent(fieldValue)
	};
	var url = 'file=DetailViewAjax&module=' + module + '&action=' + module + 'Ajax&record=' + crmId + '&recordid=' + crmId + '&ajxaction=DETAILVIEW';
	if (module == 'Users') {
		url += '&form_token=' + (document.getElementsByName('form_token')[0].value);
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?' + url,
		data : data
	}).done(function (response) {
		if (response.indexOf(':#:FAILURE')>-1) {
			alert(alert_arr.ERROR_WHILE_EDITING);
		} else if (response.indexOf(':#:ERR')>-1) {
			alert(response.replace(':#:ERR', ''));
			VtigerJS_DialogBox.hidebusy();
		} else if (response.indexOf(':#:SUCCESS')>-1) {
			//For HD & FAQ - comments, we should empty the field value
			if ((module == 'HelpDesk' || module == 'Faq') && fieldName == 'comments') {
				var comments = response.replace(':#:SUCCESS', '');
				if (getObj('comments_div') != null) {
					getObj('comments_div').innerHTML = comments;
				}
				if (getObj(dtlView) != null) {
					getObj(dtlView).innerHTML = '';
				}
				if (getObj('comments') != null) {
					getObj('comments').value = '';
				}
			} else {
				if (okmsg != null && okmsg!= '') {
					alert(okmsg);
				}
			}
			VtigerJS_DialogBox.hidebusy();
		}
	});
}

function dtlViewAjaxSave(fieldLabel, module, uitype, tableName, fieldName, crmId) {
	dtlviewModuleValidation(fieldLabel, module, uitype, tableName, fieldName, crmId);
}

function dtlViewAjaxFinishSave(fieldLabel, module, uitype, tableName, fieldName, crmId) {
	VtigerJS_DialogBox.block();
	var dtlView = 'dtlview_'+ fieldLabel;
	var editArea = 'editarea_'+ fieldLabel;
	var groupurl = '';

	if (globaluitype == 53) {
		var assigntype = document.getElementsByName('assigntype');
		if (assigntype.length > 0) {
			var assign_type_U = assigntype[0].checked;
			var assign_type_G = false;
			if (assigntype[1]!=undefined) {
				assign_type_G = assigntype[1].checked;
			}
		} else {
			var assign_type_U = assigntype[0].checked;
		}
		if (assign_type_U) {
			var txtBox= 'txtbox_U'+fieldLabel;
		} else if (assign_type_G) {
			var txtBox= 'txtbox_G'+fieldLabel;
			var group_id = encodeURIComponent(document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text);
			groupurl = '&assigned_group_id='+group_id+'&assigntype=T';
		}
	} else if (uitype == 15 || uitype == 16 || uitype == 1613 || uitype == 1614 || uitype == 1615) {
		var txtBox= 'txtbox_'+ fieldLabel;
		var not_access =document.getElementById(txtBox);
		if (not_access.options[not_access.selectedIndex]==undefined || not_access.options[not_access.selectedIndex].value == alert_arr.LBL_NOT_ACCESSIBLE) {
			document.getElementById(editArea).style.display='none';
			document.getElementById(dtlView).style.display='block';
			itsonview=false; //to show the edit link again after hiding the editdiv.
			alert(alert_arr.ERR_FIELD_SELECTION);
			return false;
		}
	} else if (globaluitype == 33 || globaluitype == 3313 || globaluitype == 3314) {
		var txtBox= 'txtbox_'+ fieldLabel;
		var oMulSelect = document.getElementById(txtBox);
		var r = new Array();
		var notaccess_label = new Array();
		for (var iter=0; iter < oMulSelect.options.length; iter++) {
			if (oMulSelect.options[iter].selected) {
				r[r.length] = oMulSelect.options[iter].value;
				notaccess_label[notaccess_label.length] = oMulSelect.options[iter].text;
			}
		}
	} else {
		var txtBox= 'txtbox_'+ fieldLabel;
	}

	VtigerJS_DialogBox.showbusy();

	//overriden the tagValue based on UI Type for checkbox
	if (uitype == '56') {
		if (document.getElementById(txtBox).checked) {
			if (module == 'Contacts') {
				var obj = getObj('email');
				if ((fieldName == 'portal') && (obj == null || obj.value == '')) {
					tagValue = '0';
					alert(alert_arr.PORTAL_PROVIDE_EMAILID);
					return false;
				} else {
					tagValue = '1';
				}
			} else {
				tagValue = '1';
			}
		} else {
			tagValue = '0';
		}
	} else if (uitype == '156') {
		if (document.getElementById(txtBox).checked) {
			tagValue = 'on';
		} else {
			tagValue = 'off';
		}
	} else if (uitype == '33' || uitype == '3313' || uitype == '3314') {
		tagValue = r.join(' |##| ');
	} else if (uitype == '24' || uitype == '21') {
		tagValue = document.getElementById(txtBox).value.replace(/<br\s*\/>/g, ' ');
	} else if (uitype == '50') {
		tagValue = document.getElementById(txtBox).value + getObj('inputtimefmt_' + fieldLabel).value;
	} else {
		tagValue = trim(document.getElementById(txtBox).value);
		if (module == 'Contacts') {
			if (getObj('portal')) {
				var port_obj = getObj('portal').checked;
				if (fieldName == 'email' && tagValue == '' && port_obj) {
					alert(alert_arr.PORTAL_PROVIDE_EMAILID);
					return false;
				}
			}
		}
	}

	var data = {
		'fldName' : fieldName,
		'fieldValue' : encodeURIComponent(tagValue)
	};
	data = corebosjshook_dtlViewAjaxFinishSave_moredata(data);
	var url = 'file=DetailViewAjax&module=' + module + '&action=' + module + 'Ajax&record=' + crmId + '&recordid=' + crmId + '&ajxaction=DETAILVIEW' + groupurl;
	if (module == 'Users') {
		url += '&form_token=' + (document.getElementsByName('form_token')[0].value);
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?' + url,
		data : data
	}).done(function (response) {
		if (response.indexOf(':#:FAILURE')>-1) {
			alert(alert_arr.ERROR_WHILE_EDITING);
		} else if (response.indexOf(':#:ERR')>-1) {
			alert(response.replace(':#:ERR', ''));
			VtigerJS_DialogBox.hidebusy();
		} else if (response.indexOf(':#:SUCCESS')>-1) {
			var result = response.split(':#:');
			if (result[2] != null) {
				var target = null;
				if (module == 'Users') {
					target = document.getElementsByClassName('user-detailview')[0];
				} else {
					target = document.getElementsByClassName('detailview_wrapper_table')[0];
				}
				target.innerHTML = result[2];
				vtlib_executeJavascriptInElement(target);
			}
			//For HD & FAQ - comments, we should empty the field value
			if ((module == 'HelpDesk' || module == 'Faq') && fieldName == 'comments') {
				var comments = result[3] != null ? result[3] : '';
				if (getObj('comments_div') != null) {
					getObj('comments_div').innerHTML = comments;
				}
				if (getObj(dtlView) != null) {
					getObj(dtlView).innerHTML = '';
				}
				if (getObj('comments') != null) {
					getObj('comments').value = '';
				}
			}
			if (typeof colorizer_after_change === 'function') {
				colorizer_after_change(fieldName, tagValue);
			}
			VtigerJS_DialogBox.hidebusy();
		}
		VtigerJS_DialogBox.unblock();
	});
	itsonview=false;
}

function dtlviewModuleValidation(fieldLabel, module, uitype, tableName, fieldName, crmId) {
	var formName = 'DetailView';
	if (doformValidation('')) { //base function which validates form data
		// Create object which gets the values of all input, textarea, select and button elements from the form
		// var myFields = document.forms[formName].parentElement.querySelectorAll('input,select,textarea'); // this would send in all elements on screen
		var myFields = document.forms[formName].elements; // elements in form
		var sentForm = new Object();
		for (var f=0; f<myFields.length; f++) {
			sentForm[myFields[f].name] = myFields[f].value;
		}
		// field being edited
		switch (uitype) {
		case '33':
		case 33:
		case '3313':
		case 3313:
		case '3314':
		case 3314:
			var txtBox= 'txtbox_'+ fieldLabel;
			var oMulSelect = document.getElementById(txtBox);
			var r = new Array();
			var notaccess_label = new Array();
			for (var iter=0; iter < oMulSelect.options.length; iter++) {
				if (oMulSelect.options[iter].selected) {
					r[r.length] = oMulSelect.options[iter].value;
					notaccess_label[notaccess_label.length] = oMulSelect.options[iter].text;
				}
			}
			sentForm[fieldName] = r.join(' |##| ');
			break;
		case '56':
		case 56:
			if (document.getElementById('txtbox_'+fieldName).checked) {
				sentForm[fieldName] = 1;
			} else {
				sentForm[fieldName] = 0;
			}
			break;
		case '50':
		case 50:
			sentForm[fieldName] = document.getElementById('txtbox_' + fieldName).value;
			sentForm['timefmt_' + fieldName] = document.getElementById('inputtimefmt_' + fieldName).value;
			break;
		case '53':
		case 53:
			var assigntype = document.getElementsByName('assigntype');
			if (assigntype.length > 0) {
				var assign_type_U = assigntype[0].checked;
				var assign_type_G = false;
				if (assigntype[1]!=undefined) {
					assign_type_G = assigntype[1].checked;
				}
			} else {
				var assign_type_U = assigntype[0].checked;
			}
			if (assign_type_U) {
				var txtBox= 'txtbox_U'+fieldLabel;
				sentForm['assign_type'] = 'U';
			} else if (assign_type_G) {
				var txtBox= 'txtbox_G'+fieldLabel;
				sentForm['assign_type'] = 'T';
			}
			sentForm[fieldName] = document.getElementById(txtBox).value;
			break;
		default:
			sentForm[fieldName] = document.getElementById('txtbox_'+fieldName).value;
			break;
		}
		sentForm['action'] = 'DetailViewEdit';
		sentForm['dtlview_edit_fieldcheck'] = fieldName;
		//JSONize form data
		sentForm = JSON.stringify(sentForm);
		jQuery.ajax({
			type : 'post',
			data : {structure: sentForm},
			url : 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=ValidationLoad&valmodule='+gVTModule
		}).done(function (msg) {
			//Validation file answers
			VtigerJS_DialogBox.unblock();
			if (msg.search('%%%CONFIRM%%%') > -1) { //Allow to use confirm alert
				//message to display
				var display = msg.split('%%%CONFIRM%%%');
				if (confirm(display[1])) { //If you click on OK
					dtlViewAjaxFinishSave(fieldLabel, module, uitype, tableName, fieldName, crmId);
				}
			} else if (msg.search('%%%OK%%%') > -1) { //No error
				dtlViewAjaxFinishSave(fieldLabel, module, uitype, tableName, fieldName, crmId);
			} else if (msg.search('%%%FUNCTION%%%') > -1) { //call user function
				var callfunc = msg.split('%%%FUNCTION%%%');
				var params = '';
				if (callfunc[1].search('%%%PARAMS%%%') > -1) { //function has params string
					var cfp = callfunc[1].split('%%%PARAMS%%%');
					callfunc = cfp[0];
					params = cfp[1];
				} else {
					callfunc = callfunc[1];
				}
				if (typeof window[callfunc] == 'function') {
					if (window[callfunc]('', '', 'Save', dtlViewAjaxFinishSave, params)) {
						dtlViewAjaxFinishSave(fieldLabel, module, uitype, tableName, fieldName, crmId);
					}
				} else {
					dtlViewAjaxFinishSave(fieldLabel, module, uitype, tableName, fieldName, crmId);
				}
			} else { //Error
				alert(msg);
			}
		}).fail(function () {
			//Error while asking file
			VtigerJS_DialogBox.unblock();
			alert('Error with AJAX');
		});
	}
	return false;
}

function SaveTag(tagfield, crmId, module) {
	var tagValue = document.getElementById(tagfield).value;
	tagValue = encodeURIComponent(tagValue);
	VtigerJS_DialogBox.showbusy();
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?file=TagCloud&module=' + module + '&action=' + module + 'Ajax&recordid=' + crmId + '&ajxaction=SAVETAG&tagfields=' +tagValue
	}).done(function (response) {
		if (response.indexOf(':#:FAILURE') > -1) {
			alert(alert_arr.VALID_DATA);
		} else {
			getObj('tagfields').innerHTML = response;
			document.getElementById(tagfield).value = '';
		}
		VtigerJS_DialogBox.hidebusy();
	});
}

function setSelectValue(fieldLabel) {
	var selCombo= '';
	if (globaluitype == 53) {
		var assigntype = document.getElementsByName('assigntype');
		if (assigntype.length > 0) {
			var assign_type_U = assigntype[0].checked;
			var assign_type_G = false;
			if (assigntype[1]!=undefined) {
				assign_type_G = assigntype[1].checked;
			}
			if (assign_type_U) {
				selCombo= 'txtbox_U'+fieldLabel;
			} else if (assign_type_G) {
				selCombo= 'txtbox_G'+fieldLabel;
			}
		} else {
			selCombo= 'txtbox_U'+fieldLabel;
		}
	} else {
		selCombo= 'txtbox_'+fieldLabel;
	}
	var hdTxtBox = 'hdtxt_'+fieldLabel;
	var oHdTxtBox = document.getElementById(hdTxtBox);
	var oSelCombo = document.getElementById(selCombo);
	oHdTxtBox.value = oSelCombo.options[oSelCombo.selectedIndex].text;
}

//Added to ajax edit the folder name in Documents Module
function hndMouseClick(fieldLabel) {
	if (itsonview) {
		return;
	}
	globaldtlviewspanid= 'dtlview_'+ fieldLabel;//valuespanid;
	globaleditareaspanid='editarea_'+ fieldLabel;//textareapanid;
	globalfieldlabel = fieldLabel;
	globaltxtboxid='txtbox_'+ fieldLabel;//textboxpanid;
	document.getElementById(globaltxtboxid).value = document.getElementById(globaldtlviewspanid).innerHTML;
	handleEdit();
	jQuery('#'+globaltxtboxid).select();
}

function setCoOrdinate(elemId) {
	var oBtnObj = document.getElementById(elemId);
	var tagName = document.getElementById('lstRecordLayout');
	var leftpos = 0;
	var toppos = 0;
	var aTag = oBtnObj;
	do {
		leftpos += aTag.offsetLeft;
		toppos += aTag.offsetTop;
	} while (aTag = aTag.offsetParent);
	tagName.style.top= toppos + 20 + 'px';
	tagName.style.left= leftpos - 276 + 'px';
}

function getListOfRecords(obj, sModule, iId) {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Users&action=getListOfRecords&ajax=true&CurModule='+sModule+'&CurRecordId='+iId,
	}).done(function (response) {
		document.getElementById('lstRecordLayout').innerHTML = response;
		var tagName = document.getElementById('lstRecordLayout');
		var leftSide = findPosX(obj);
		var topSide = findPosY(obj);
		var maxW = tagName.style.width;
		var widthM = maxW.substring(0, maxW.length-2);
		var getVal = parseInt(leftSide) + parseInt(widthM);
		if (getVal > document.body.clientWidth) {
			leftSide = parseInt(leftSide) - parseInt(widthM);
			tagName.style.left = leftSide + 230 + 'px';
			tagName.style.top = topSide + 20 + 'px';
		} else {
			tagName.style.left = leftSide + 230 + 'px';
		}
		setCoOrdinate(obj.id);
		tagName.style.display = 'block';
		tagName.style.visibility = 'visible';
	});
}
