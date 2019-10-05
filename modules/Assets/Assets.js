/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function set_return(product_id, product_name) {
	if (document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.parent_name.value = product_name;
		window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
		window.opener.document.EditView.parent_name.value = product_name;
		window.opener.document.EditView.parent_id.value = product_id;
	}
}
function set_return_specific(product_id, product_name) {
	if (document.getElementById('from_link').value != '') {
		var fldName = window.opener.document.QcEditView.product_name;
		var fldId = window.opener.document.QcEditView.product_id;
	} else if (typeof(window.opener.document.DetailView) != 'undefined') {
		var fldName = window.opener.document.DetailView.product_name;
		var fldId = window.opener.document.DetailView.product_id;
	} else {
		var fldName = window.opener.document.EditView.product_name;
		var fldId = window.opener.document.EditView.product_id;
	}
	fldName.value = product_name;
	fldId.value = product_id;
}

function set_return_formname_specific(formname, product_id, product_name) {
	window.opener.document.EditView1.product_name.value = product_name;
	window.opener.document.EditView1.product_id.value = product_id;
}

function set_return_product(product_id, product_name) {
	if (document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.parent_name.value = product_name;
		window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
		window.opener.document.EditView.product_name.value = product_name;
		window.opener.document.EditView.product_id.value = product_id;
	}
}

function AssetssetValueFromCapture(recordid, value, target_fieldname) {
	if (target_fieldname=='invoiceid') {
		var url = 'module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getFieldValuesFromRecord';
		url = url + '&getTheseFields=account_id,Accounts.accountname&getFieldValuesFrom='+recordid;
		jQuery.ajax({
			method: 'GET',
			url: 'index.php?'+url
		}).done(function (response) {
			var str = JSON.parse(response);
			if (document.EditView) {
				if (document.EditView.account) {
					document.EditView.account.value = str['account_id'];
				}
				if (document.EditView.account_display) {
					document.EditView.account_display.value = str['Accounts.accountname'];
				}
			}
		});
	}
}
