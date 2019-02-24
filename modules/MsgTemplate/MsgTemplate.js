/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function submittemplate(recordid, value, target_fieldname, formname) {
	let idlist = window.opener.document.getElementById('listofids').value;
	window.document.location.href = 'index.php?module=MsgTemplate&action=MsgTemplateAjax&file=TemplateMerge&listofids='+idlist+'&action_id='+recordid;
}

function msgtFillInModuleFields() {
	alert('Fill In fields');
}

function msgtInsertIntoMsg() {
	alert('Insert into msg');
}