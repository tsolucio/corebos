/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
// Show stock or not
var hide_stock = 'no';
ExecuteFunctions('ismoduleactive', 'checkmodule=Products').then(function (response) {
	var obj = JSON.parse(response);
	if (obj.isactive == true) {
		hide_stock = 'no';
	} else {
		hide_stock = 'yes';
	}
}, function (error) {
	hide_stock = 'no';
});

function set_return(issuecards_id, issuecards_name) {
	window.opener.document.EditView.parent_name.value = issuecards_name;
	window.opener.document.EditView.parent_id.value = issuecards_id;
}

function set_return_specific(issuecards_id, issuecards_name) {
	var fldName = getOpenerObj("issuecards_name");
	var fldId = getOpenerObj("issuecards_id");
	fldName.value = issuecards_name;
	fldId.value = issuecards_id;
}

function set_return_shipbilladdress(fromlink, fldname, MODULE, ID) {
	var WindowSettings = "width=680,height=602,resizable=0,scrollbars=0,top=150,left=200";
	if (fldname == 'accid') {
		var baseURL = "index.php?module=Accounts&action=Popup&popuptype=specific_account_address&form=TasksEditView&form_submit=false&fromlink=";
	} else {
		if (fromlink != 'DetailView') {
			var accid = document.EditView.accid.value;
		} else {
			var accid = vtlib_listview.getFieldInfo('mouseArea_accid').recordid;
		}
		if (accid != '') {
			var baseURL = "index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView&parent_module=Accounts&relmod_id="+accid;
		} else {
			var baseURL = "index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView";
		}
	}
	window.open(baseURL, "vtlibui10", WindowSettings);
}
