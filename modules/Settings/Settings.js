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
