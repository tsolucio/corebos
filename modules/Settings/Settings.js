/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function getData(fieldname, divid){
	$.ajax({
		url: 'index.php?module=Settings&action=SettingsAjax&file=loaddata&fieldname='+fieldname,
		success: function(html) {
			var ajaxDisplay = document.getElementById(divid);
			ajaxDisplay.innerHTML = html;
		}
	});
}

function OnScrollDiv (div) {
	var info = document.getElementById ('info');
	info.innerHTML = 'Horizontal: ' + div.scrollLeft
						+ 'px<br/>Vertical: ' + div.scrollTop + 'px';
}
