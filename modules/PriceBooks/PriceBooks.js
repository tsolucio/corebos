/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function set_return_specific(vendor_id, vendor_name) {
	//getOpenerObj used for DetailView
	var fldName = getOpenerObj('vendor_name');
	var fldId = getOpenerObj('vendor_id');
	fldName.value = vendor_name;
	fldId.value = vendor_id;
}

function set_return_inventory_pb(listprice, fldname) {
	window.opener.document.EditView.elements[fldname].value = listprice;
	window.opener.document.EditView.elements[fldname].focus();
}

