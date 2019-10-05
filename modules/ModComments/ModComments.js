/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function toggleModule_mod(tabid, action) {
	document.getElementById('status').style.display='block';
	jQuery.ajax({
		method: 'post',
		url: 'index.php?module=ModComments&action=ModCommentsAjax&file=BasicSettings&tabid='+tabid+'&status='+action+'&ajax=true',
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modcommsContents').innerHTML=response;
	});
}
