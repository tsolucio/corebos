/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
function exportLanguageCSV() {
	select_options = document.getElementById('allselectedboxes').value;
	var x = select_options.split(';');
	var count = x.length;
	if (count > 1) {
		idString = select_options;
		gotourl('index.php?module=cbtranslation&action=cbtranslationAjax&file=exportToCSV&allrecords=' + idString);
	} else {
		alert(alert_arr.SELECT);
		return false;
	}
}

function exportLanguageJSON() {
	select_options = document.getElementById('allselectedboxes').value;
	var x = select_options.split(';');
	var count = x.length;
	if (count > 1) {
		idString = select_options;
		gotourl('index.php?module=cbtranslation&action=cbtranslationAjax&file=exportToJSON&allrecords=' + idString);
	} else {
		alert(alert_arr.SELECT);
		return false;
	}
}