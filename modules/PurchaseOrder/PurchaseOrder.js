/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function set_return(product_id, product_name) {
	window.opener.document.EditView.parent_name.value = product_name;
	window.opener.document.EditView.parent_id.value = product_id;
}
function set_return_specific(product_id, product_name) {
	//getOpenerObj used for DetailView
	var fldName = getOpenerObj('purchaseorder_name');
	var fldId = getOpenerObj('purchaseorder_id');
	fldName.value = product_name;
	fldId.value = product_id;
}
function set_return_formname_specific(formname, product_id, product_name) {
	window.opener.document.EditView1.purchaseorder_name.value = product_name;
	window.opener.document.EditView1.purchaseorder_id.value = product_id;
}
function set_return_todo(product_id, product_name) {
	window.opener.document.createTodo.task_parent_name.value = product_name;
	window.opener.document.createTodo.task_parent_id.value = product_id;
}
function PurchaseOrdersetValueFromCapture(recordid, value, target_fieldname) {
	if (target_fieldname=='tandc') {
		var url = 'module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getFieldValuesFromRecord&getTheseFields=tandc&getFieldValuesFrom='+recordid;
		jQuery.ajax({
			method: 'GET',
			url: 'index.php?'+url
		}).done(function (response) {
			var str = JSON.parse(response);
			document.EditView.terms_conditions.value = str['tandc'];
		});
	}
}
