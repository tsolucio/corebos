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
			if (assigntype[1]!=undefined) {
				var assign_type_G = assigntype[1].checked;
			} else {
				var assign_type_G = false;
			}
			if (assign_type_U == true) {
				globaltxtboxid= 'txtbox_U'+fieldLabel;
			} else if (assign_type_G == true) {
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
	divObj = getObj('crmspanid');
	var crmy = findPosY(getObj(mouseArea));
	var crmx = findPosX(getObj(mouseArea));
	if (document.all) {
		divObj.onclick=handleEdit;
	} else {
		divObj.setAttribute('onclick', 'handleEdit();');
	}
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
	//if (event) event.stopPropagation();
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

//Asha: Function changed to trim both leading and trailing spaces.
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
			alert_str = response.replace(':#:ERR', '');
			alert(alert_str);
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
		if (assign_type_U == true) {
			var txtBox= 'txtbox_U'+fieldLabel;
		} else if (assign_type_G == true) {
			var txtBox= 'txtbox_G'+fieldLabel;
			var group_id = encodeURIComponent(document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text);
			groupurl = '&assigned_group_id='+group_id+'&assigntype=T';
		}
	} else if (uitype == 15 || uitype == 16 || uitype == 1613 || uitype == 1614 || uitype == 1615) {
		var txtBox= 'txtbox_'+ fieldLabel;
		var not_access =document.getElementById(txtBox);
		var pickval = not_access.options[not_access.selectedIndex].value;
		if (pickval == alert_arr.LBL_NOT_ACCESSIBLE) {
			document.getElementById(editArea).style.display='none';
			document.getElementById(dtlView).style.display='block';
			itsonview=false; //to show the edit link again after hiding the editdiv.
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

	var popupTxt= 'popuptxt_'+ fieldLabel;
	var hdTxt = 'hdtxt_'+ fieldLabel;

	VtigerJS_DialogBox.showbusy();
	var isAdmin = document.getElementById('hdtxt_IsAdmin').value;

	//overriden the tagValue based on UI Type for checkbox
	if (uitype == '56') {
		if (document.getElementById(txtBox).checked == true) {
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
		if (document.getElementById(txtBox).checked == true) {
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
				if (fieldName == 'email' && tagValue == '' && port_obj == true) {
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
			alert_str = response.replace(':#:ERR', '');
			alert(alert_str);
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
			}
			if (typeof colorizer_after_change === 'function') {
				colorizer_after_change(fieldName, tagValue);
			}
			VtigerJS_DialogBox.hidebusy();
		}
	});
	tagValue = get_converted_html(tagValue);
	if (uitype == '13') {
		var temp_fieldname = 'internal_mailer_'+fieldName;
		if (document.getElementById(temp_fieldname)) {
			var mail_chk_arr = document.getElementById(temp_fieldname).innerHTML.split('####');
			var fieldId = mail_chk_arr[0];
			var internal_mailer_flag = mail_chk_arr[1];
			if (internal_mailer_flag == 1) {
				var email_link = '<a href="javascript:InternalMailer('+crmId+','+fieldId+',\''+fieldName+'\',\''+module+'\',\'record_id\');" onclick=\'event.stopPropagation();\'>'+tagValue+'&nbsp;</a>';
			} else {
				var email_link = '<a href="mailto:'+ tagValue+'" target="_blank" onclick=\'event.stopPropagation();\'>'+tagValue+'&nbsp;</a>';
			}
		}
		getObj(dtlView).innerHTML = email_link;
		if (fieldName == 'email' || fieldName == 'email1') {
			var priEmail = getObj('pri_email');
			if (priEmail) {
				priEmail.value = tagValue;
			}
		} else {
			var secEmail = getObj('sec_email');
			if (secEmail) {
				secEmail.value = tagValue;
			}
		}
	} else if (uitype == '11') {
		if (typeof(use_asterisk) != 'undefined' && use_asterisk == true) {
			getObj(dtlView).innerHTML = '<a href="javascript:;" onclick="startCall(\''+tagValue+'\',\''+crmId+'\')" onclick=\'event.stopPropagation();\'>'+tagValue+'</a>';
		} else {
			getObj(dtlView).innerHTML = tagValue;
		}
	} else if (uitype == '17') {
		var matchPattern = /^[\w]+:\/\//;
		if (tagValue.match(matchPattern)) {
			getObj(dtlView).innerHTML = '<a href="'+ tagValue+'" target="_blank" onclick=\'event.stopPropagation();\'>'+tagValue+'&nbsp;</a>';
		} else {
			getObj(dtlView).innerHTML = '<a href="http://'+ tagValue+'" target="_blank" onclick=\'event.stopPropagation();\'>'+tagValue+'&nbsp;</a>';
		}
	} else if (uitype == '85') {
		getObj(dtlView).innerHTML = '<a href="skype:'+ tagValue+'?call" onclick=\'event.stopPropagation();\'><img src=\'themes/images/skype.gif\' align=\'absmiddle\'></img>&nbsp;'+tagValue+'&nbsp;</a>';
	} else if (uitype == '53') {
		var hdObj = getObj(hdTxt);
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
		if (isAdmin == '0') {
			getObj(dtlView).innerHTML = hdObj.value;
		} else if (isAdmin == '1' && assign_type_U == true) {
			getObj(dtlView).innerHTML = '<a href="index.php?module=Users&action=DetailView&record='+tagValue+'" onclick=\'event.stopPropagation();\'>'+hdObj.value+'&nbsp;</a>';
		} else if (isAdmin == '1' && assign_type_G == true) {
			getObj(dtlView).innerHTML = '<a href="index.php?module=Settings&action=GroupDetailView&groupId='+tagValue+'" onclick=\'event.stopPropagation();\'>'+hdObj.value+'&nbsp;</a>';
		}
	} else if (uitype == '52' || uitype == '77') {
		if (isAdmin == '1') {
			getObj(dtlView).innerHTML = '<a href="index.php?module=Users&action=DetailView&record='+tagValue+'">'+document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text+'&nbsp;</a>';
		} else {
			getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text;
		}
	} else if (uitype == '56') {
		if (tagValue == '1') {
			getObj(dtlView).innerHTML = alert_arr.YES;
		} else {
			getObj(dtlView).innerHTML = alert_arr.NO;
		}
	} else if (uitype == 117) {
		getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text;
	} else if (uitype == '10') {
		getObj(dtlView).innerHTML = '<a href="index.php?module='+document.getElementById(fieldName+'_type').value+'&action=DetailView&record='+tagValue+'">'+document.getElementById(fieldName+'_display').value+'&nbsp;</a>';
	} else if (getObj(popupTxt)) {
		var popObj = getObj(popupTxt);
		if (uitype == '73' || uitype == '51') {
			getObj(dtlView).innerHTML = '<a href="index.php?module=Accounts&action=DetailView&record='+tagValue+'">'+popObj.value+'&nbsp;</a>';
		} else if (uitype == '57') {
			getObj(dtlView).innerHTML = '<a href="index.php?module=Contacts&action=DetailView&record='+tagValue+'">'+popObj.value+'&nbsp;</a>';
		} else if (uitype == '76') {
			getObj(dtlView).innerHTML = '<a href="index.php?module=Potentials&action=DetailView&record='+tagValue+'">'+popObj.value+'&nbsp;</a>';
		} else if (uitype == '78') {
			getObj(dtlView).innerHTML = '<a href="index.php?module=Quotes&action=DetailView&record='+tagValue+'">'+popObj.value+'&nbsp;</a>';
		} else if (uitype == '80') {
			getObj(dtlView).innerHTML = '<a href="index.php?module=SalesOrder&action=DetailView&record='+tagValue+'">'+popObj.value+'&nbsp;</a>';
		} else if (uitype == '53') {
			var hdObj = getObj(hdTxt);
			if (isAdmin == '0') {
				getObj(dtlView).innerHTML = hdObj.value;
			} else if (isAdmin == '1') {
				getObj(dtlView).innerHTML = '<a href="index.php?module=Users&action=DetailView&record='+tagValue+'">'+hdObj.value+'&nbsp;</a>';
			}
		} else if (uitype == '56') {
			if (tagValue == '1') {
				getObj(dtlView).innerHTML = alert_arr.YES;
			} else {
				getObj(dtlView).innerHTML = '';
			}
		} else {
			getObj(dtlView).innerHTML = popObj.value;
		}
	} else if (uitype == '15' || uitype == '16' || uitype == '31' || uitype == '32') {
		var notaccess =document.getElementById(txtBox);
		tagValue = notaccess.options[notaccess.selectedIndex].text;
		if (tagValue == alert_arr.LBL_NOT_ACCESSIBLE) {
			getObj(dtlView).innerHTML = '<font color=\'red\'>'+get_converted_html(tagValue)+'</font>';
		} else {
			getObj(dtlView).innerHTML = get_converted_html(tagValue);
		}
	} else if (uitype == '33' || uitype == '3313' || uitype == '3314') {
		/* Wordwrap a long list of multi-select combo box items at the item separator string */
		var DETAILVIEW_WORDWRAP_WIDTH = '70'; // must match value in DetailViewUI.tpl.

		var lineLength = 0;
		for (var i=0; i < notaccess_label.length; i++) {
			lineLength += notaccess_label[i].length + 2; // + 2 for item separator string
			/*if(lineLength > DETAILVIEW_WORDWRAP_WIDTH && i > 0) {
				lineLength = notaccess_label[i].length + 2; // reset.
				notaccess_label[i] = '<br/>&nbsp;' + notaccess_label[i]; // prepend newline.
			}*/
			notaccess_label[i] = get_converted_html(notaccess_label[i]);
			// Prevent a browser splitting multiword items:
			//notaccess_label[i] = notaccess_label[i].replace(/ /g, '&nbsp;');
			notaccess_label[i] = notaccess_label[i].replace(alert_arr.LBL_NOT_ACCESSIBLE, '<font color=\'red\'>'+alert_arr.LBL_NOT_ACCESSIBLE+'</font>'); // for Not accessible label.
		}
		/* Join items with item separator string (which must match string in DetailViewUI.tpl, EditViewUtils.php and CRMEntity.php)!! */
		getObj(dtlView).innerHTML = notaccess_label.join(', ');
	} else if (uitype == '19') {
		var desc = trim(document.getElementById(txtBox).value);
		desc = desc.replace(/(^|[\n ])([\w]+?:\/\/.*?[^ \"\n\r\t<]*)/g, '$1<a href="$2" target="_blank">$2</a>');
		desc = desc.replace(/(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:\/[^ \"\t\n\r<]*)?)/g, '$1<a href="http://$2" target="_blank">$2</a>');
		desc = desc.replace(/(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/i, '$1<a href="mailto:$2@$3">$2@$3</a>');
		desc = desc.replace(/,\"|\.\"|\)\"|\)\.\"|\.\)\"/, '"');
		//desc = desc.replace(/[\n\r]/g, "<br>&nbsp;");
		getObj(dtlView).textContent = desc;
	} else if (uitype == '50') {
		let timefmt = tagValue.substring(tagValue.length-2);
		if (timefmt == '24') {
			timefmt = '';
		}
		getObj(dtlView).innerHTML = tagValue.substring(0, tagValue.length-2)+'&nbsp;<font size=1><em>&nbsp;<span id=\'timefmt_'+fieldName+'\'>'+timefmt+'</span></em></font>';
	} else {
		getObj(dtlView).innerHTML = tagValue.replace(/[\n\r]+/g, '<br>&nbsp;');
	}
	showHide(dtlView, editArea);  //show,hide
	itsonview=false;
}

function dtlviewModuleValidation(fieldLabel, module, uitype, tableName, fieldName, crmId) {
	var formName = 'DetailView';
	if (doformValidation('')) { //base function which validates form data
		//Testing if a Validation file exists
		jQuery.ajax({
			url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=ValidationExists&valmodule='+gVTModule+'&crmid='+crmId,
			type:'get'
		}).fail(function () {
			//Validation file does not exist
			dtlViewAjaxFinishSave(fieldLabel, module, uitype, tableName, fieldName, crmId);
		}).done(function (data) {
			//Validation file exists
			if (data == 'yes') {
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
					sentForm[fieldName] = r;
					break;
				case '56':
				case 56:
					if (document.getElementById('txtbox_'+fieldName).checked == true) {
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
					if (assign_type_U == true) {
						var txtBox= 'txtbox_U'+fieldLabel;
						sentForm['assign_type'] = 'U';
					} else if (assign_type_G == true) {
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
					} else { //Error
						alert(msg);
					}
				}).fail(function () {
					//Error while asking file
					VtigerJS_DialogBox.unblock();
					alert('Error with AJAX');
				});
			} else { // no validation we send form
				dtlViewAjaxFinishSave(fieldLabel, module, uitype, tableName, fieldName, crmId);
			}
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
			if (assign_type_U == true) {
				selCombo= 'txtbox_U'+fieldLabel;
			} else if (assign_type_G == true) {
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
	var mouseArea='';
	mouseArea='mouseArea_'+ fieldLabel;
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
	leftpos  = 0;
	toppos = 0;
	var aTag = oBtnObj;
	do {
		leftpos += aTag.offsetLeft;
		toppos += aTag.offsetTop;
	} while (aTag = aTag.offsetParent);
	tagName.style.top= toppos + 20 + 'px';
	tagName.style.left= leftpos - 276 + 'px';
}

function getListOfRecords(obj, sModule, iId, sParentTab) {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Users&action=getListOfRecords&ajax=true&CurModule='+sModule+'&CurRecordId='+iId+'&CurParentTab='+sParentTab,
	}).done(function (response) {
		document.getElementById('lstRecordLayout').innerHTML = response;
		Lay = 'lstRecordLayout';
		var tagName = document.getElementById(Lay);
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
