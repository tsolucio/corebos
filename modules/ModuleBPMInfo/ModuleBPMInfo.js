/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function bpmsaveinfo(module, return_id, mode, saveinfo) {
	let sinfo = JSON.parse(decodeURIComponent(saveinfo));
	sinfo.tostate = decodeURIComponent(sinfo.tostate.replace(/\+/g, ' '));
	if (sinfo.editmode) {
		document.getElementById(sinfo.fieldName).value = sinfo.tostate;
	} else {
		var txtBox = 'txtbox_'+sinfo.fieldName;
		document.getElementById(txtBox).value = sinfo.tostate;
		document.getElementById('cbcustominfo2').value = sinfo.pflowid;
		dtlViewAjaxSave(sinfo.fieldName, sinfo.bpmmodule, sinfo.uitype, '', sinfo.fieldName, sinfo.bpmrecord);
	}
}