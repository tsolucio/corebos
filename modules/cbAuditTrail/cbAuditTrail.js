/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function auditenable() {
	setAuditStatus('enabled');
}

function auditdisable() {
	setAuditStatus('disabled');
}

function setAuditStatus(status) {
	document.getElementById("status").style.display = "block";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=cbAuditTrail&action=cbAuditTrailAjax&file=SaveAuditTrail&ajax=true&audit_trail=' + status,
	}).done(function(response) {
		document.getElementById("status").style.display = "none";
		location.reload(true);
	});
}
