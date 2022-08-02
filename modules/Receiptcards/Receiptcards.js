/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var i18nReceiptcards = '';
ExecuteFunctions('getTranslatedStrings','i18nmodule=Receiptcards&tkeys=LBL_RECALCULATE_QUESTION').then(function (data) {
	i18nReceiptcards = JSON.parse(data);
});

function set_return(receiptcards_id, receiptcards_name) {
	window.opener.document.EditView.parent_name.value = receiptcards_name;
	window.opener.document.EditView.parent_id.value = receiptcards_id;
}

function set_return_specific(receiptcards_id, receiptcards_name) {
	var fldName = getOpenerObj('receiptcards_name');
	var fldId = getOpenerObj('receiptcards_id');
	fldName.value = receiptcards_name;
	fldId.value = receiptcards_id;
}

function recalculateStock() {
	recalculate = confirm(i18nReceiptcards.LBL_RECALCULATE_QUESTION);
	if (recalculate) {
		jQuery('#status').show();
		jQuery.ajax({
			method:'POST',
			url: 'index.php?module=Receiptcards&action=ReceiptcardsAjax&file=RecalculateStock'
		}).done(function (response) {
			jQuery('#status').hide();
		});
	}
	window.open(baseURL, "vtlibui10", WindowSettings);
}
