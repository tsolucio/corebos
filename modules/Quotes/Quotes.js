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
document.addEventListener('DOMContentLoaded', function(event) {
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
});

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
	var fldName = getOpenerObj('quote_name');
	var fldId = getOpenerObj('quote_id');
	fldName.value = product_name;
	fldId.value = product_id;
	window.opener.document.EditView.action.value = 'EditView';
	window.opener.document.EditView.convertmode.value = 'update_quote_val';
	window.opener.document.EditView.submit();
}

function add_data_to_relatedlist(entity_id, recordid) {
	opener.document.location.href='index.php?module=Emails&action=updateRelations&destination_module=Accounts&entityid='+entity_id+'&parentid='+recordid;
}

function set_return_formname_specific(formname, product_id, product_name) {
	window.opener.document.EditView1.quote_name.value = product_name;
	window.opener.document.EditView1.quote_id.value = product_id;
}

function set_return_todo(product_id, product_name) {
	if (document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.task_parent_name.value = product_name;
		window.opener.document.QcEditView.task_parent_id.value = product_id;
	} else {
		window.opener.document.createTodo.task_parent_name.value = product_name;
		window.opener.document.createTodo.task_parent_id.value = product_id;
	}
}

function QuotessetValueFromCapture(recordid, value, target_fieldname) {
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
