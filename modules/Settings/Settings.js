/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function getData(fieldname, modulename, divid) {
	jQuery.ajax({
		url: 'index.php?module=Settings&action=SettingsAjax&file=loaddata&fieldname='+fieldname+'&modulename='+modulename,
		success: function (html) {
			document.getElementById(divid).innerHTML = html;
		}
	});
}

function deleteModule(modulename) {
	const url = 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=deleteModule&formodule='+modulename;
	if (confirm(alert_arr.ARE_YOU_SURE)) {
		jQuery.ajax({
			method: 'GET',
			url: url,
		}).done(function (response) {
			const state = JSON.parse(response);
			if (state.success) {
				ldsPrompt.show('Success', state.message, 'success');
				document.getElementById(`module_${modulename}`).remove();
			} else {
				ldsPrompt.show('Error', state.message, 'error');
			}
		});
	}
}

function hideSelect() {
	var oselect_array = document.getElementsByTagName('SELECT');
	for (const oselect of oselect_array) {
		oselect.style.display = 'none';
	}
}

function showSelect() {
	var oselect_array = document.getElementsByTagName('SELECT');
	for (const oselect of oselect_array) {
		oselect.style.display = 'block';
	}
}

function callEditDiv(obj, modulename, mode, id, modulei18n) {
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Settings&action=SettingsAjax&orgajax=true&mode='+mode+'&sharing_module='+modulename+'&shareid='+id,
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('tempdiv').innerHTML=response;
		fnvshobj(obj, 'tempdiv');
		if (mode == 'edit') {
			setTimeout('', 10000);
			var related = document.getElementById('rel_module_lists').value;
			fnwriteRules(modulename, related, modulei18n);
		}
	});
}

function fnwriteRules(module, related, modulei18n) {
	var modulelists = related.split('###');
	var relatedstring ='';
	var relatedtag;
	var relatedselect;
	var modulename;
	for (let i=0; i < modulelists.length-1; i++) {
		modulename = modulelists[i]+'_accessopt';
		relatedtag = document.getElementById(modulename);
		relatedselect = relatedtag.options[relatedtag.selectedIndex].text;
		relatedstring += modulelists[i]+':'+relatedselect+' ';
	}
	var tagName = document.getElementById(module+'_share');
	var tagName2 = document.getElementById(module+'_access');
	var tagName3 = document.getElementById('share_memberType');
	var soucre =  document.getElementById('rules');
	var soucre1 =  document.getElementById('relrules');
	var select1 = tagName.options[tagName.selectedIndex].text;
	var select2 = tagName2.options[tagName2.selectedIndex].text;
	var select3 = tagName3.options[tagName3.selectedIndex].text;

	if (modulei18n == i18nOrgSharing.Accounts) {
		modulei18n = i18nOrgSharing.Accounts + ' & ' + i18nOrgSharing.Contacts;
	}

	soucre.innerHTML = modulei18n + ' ' + i18nOrgSharing.LBL_LIST_OF + ' <b>"' + select1 + '"</b> ' + i18nOrgSharing.LBL_CAN_BE_ACCESSED
		+ ' <b>"' +select2 + '"</b> ' + i18nOrgSharing.LBL_IN_PERMISSION + ' ' + select3;
	soucre1.innerHTML = '<b>'+i18nOrgSharing.LBL_RELATED_MODULE_RIGHTS+'</b> '+relatedstring;
}

function disableStyle(id) {
	document.getElementById('orgSharingform').action.value = 'RecalculateSharingRules';
	document.getElementById('orgSharingform').submit();
	document.getElementById(id).style.display = 'none';
	document.getElementById('divId').style.display = 'block';
}

function freezeBackground() {
	var oFreezeLayer = document.createElement('div');
	oFreezeLayer.id = 'freeze';
	oFreezeLayer.className = 'small veil';
	oFreezeLayer.style.height = document.body.offsetHeight + 'px';
	oFreezeLayer.style.width = '100%';
	document.body.appendChild(oFreezeLayer);
	document.getElementById('confId').style.display = 'block';
	hideSelect();
}
