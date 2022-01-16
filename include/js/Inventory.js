/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var inventoryi18n = '',
	inventoryLinesShown = true,
	defaultProdQty = 1,
	defaultSerQty = 1;

document.addEventListener('DOMContentLoaded', function () {
	ExecuteFunctions('getTranslatedStrings', 'i18nmodule=SalesOrder&tkeys=typetosearch_prodser').then(function (data) {
		inventoryi18n = JSON.parse(data);
	});
	GlobalVariable_getVariable('Inventory_Product_Default_Units', 1, '', gVTUserID).then(function (response) {
		var obj = JSON.parse(response);
		defaultProdQty = obj.Inventory_Product_Default_Units;
	}, function (error) {
		defaultProdQty = 1; // units
	});
	GlobalVariable_getVariable('Inventory_Service_Default_Units', 1, '', gVTUserID).then(function (response) {
		var obj = JSON.parse(response);
		defaultSerQty = obj.Inventory_Service_Default_Units;
	}, function (error) {
		defaultSerQty = 1; // units
	});
	GlobalVariable_getVariable('Inventory_DoNotUseLines', '', gVTModule, gVTUserID).then(function (response) {
		var obj = JSON.parse(response);
		inventoryLinesShown = !obj.Inventory_DoNotUseLines.includes(gVTModule);
	}, function (error) {
		inventoryLinesShown = true;
	});
});

function copyAddressRight(form) {
	if (typeof(form.bill_street) != 'undefined' && typeof(form.ship_street) != 'undefined') {
		form.ship_street.value = form.bill_street.value;
	}

	if (typeof(form.bill_city) != 'undefined' && typeof(form.ship_city) != 'undefined') {
		form.ship_city.value = form.bill_city.value;
	}

	if (typeof(form.bill_state) != 'undefined' && typeof(form.ship_state) != 'undefined') {
		form.ship_state.value = form.bill_state.value;
	}

	if (typeof(form.bill_code) != 'undefined' && typeof(form.ship_code) != 'undefined') {
		form.ship_code.value = form.bill_code.value;
	}

	if (typeof(form.bill_country) != 'undefined' && typeof(form.ship_country) != 'undefined') {
		form.ship_country.value = form.bill_country.value;
	}

	if (typeof(form.bill_pobox) != 'undefined' && typeof(form.ship_pobox) != 'undefined') {
		form.ship_pobox.value = form.bill_pobox.value;
	}

	if (form.ship_countrycode != undefined) {
		[...form.ship_countrycode.options].forEach((option) => {
			option.selected = (option.value == form.bill_countrycode.value);
		});
	}

	return true;
}

function copyAddressLeft(form) {
	if (typeof(form.bill_street) != 'undefined' && typeof(form.ship_street) != 'undefined') {
		form.bill_street.value = form.ship_street.value;
	}

	if (typeof(form.bill_city) != 'undefined' && typeof(form.ship_city) != 'undefined') {
		form.bill_city.value = form.ship_city.value;
	}

	if (typeof(form.bill_state) != 'undefined' && typeof(form.ship_state) != 'undefined') {
		form.bill_state.value = form.ship_state.value;
	}

	if (typeof(form.bill_code) != 'undefined' && typeof(form.ship_code) != 'undefined') {
		form.bill_code.value =	form.ship_code.value;
	}

	if (typeof(form.bill_country) != 'undefined' && typeof(form.ship_country) != 'undefined') {
		form.bill_country.value = form.ship_country.value;
	}

	if (typeof(form.bill_pobox) != 'undefined' && typeof(form.ship_pobox) != 'undefined') {
		form.bill_pobox.value = form.ship_pobox.value;
	}

	if (form.bill_countrycode != undefined) {
		[...form.bill_countrycode.options].forEach((option) => {
			option.selected = (option.value == form.ship_countrycode.value);
		});
	}

	return true;
}

function settotalnoofrows() {
	//set the total number of products
	document.EditView.totalProductCount.value = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
}

function productPickList(currObj, module, row_no) {
	var rowId = row_no;
	var currentRowId = parseInt(currObj.id.match(/([0-9]+)$/)[1]);

	// If we have mismatching rowId and currentRowId, it is due swapping of rows
	if (rowId != currentRowId) {
		rowId = currentRowId;
	}

	var currencyid = document.getElementById('inventory_currency').value;
	var additionalinfo = getInventoryModuleTaxRelatedInformation();
	var popuptype = 'inventory_prod';
	if (module == 'PurchaseOrder' || module == 'Receiptcards') {
		popuptype = 'inventory_prod_po';
		var module_string = '&parent_module=Vendor';
		var parent_id = document.EditView.vendor_id.value;

		if (parent_id != '') {
			window.open('index.php?module=Products&action=Popup&html=Popup_picker&select=enable&form=HelpDeskEditView&popuptype='+popuptype+'&curr_row='+rowId+'&return_module='+module+'&currencyid='+currencyid+'&relmod_id='+parent_id+module_string+additionalinfo, 'productWin', cbPopupWindowSettings);
		} else {
			window.open('index.php?module=Products&action=Popup&html=Popup_picker&select=enable&form=HelpDeskEditView&popuptype='+popuptype+'&curr_row='+rowId+'&return_module='+module+'&currencyid='+currencyid+additionalinfo, 'productWin', cbPopupWindowSettings);
		}
	} else {
		var record_id = '';
		if (document.getElementsByName('account_id').length != 0) {
			record_id= document.EditView.account_id.value;
		}
		if (record_id != '') {
			window.open('index.php?module=Products&action=Popup&html=Popup_picker&select=enable&form=HelpDeskEditView&popuptype='+popuptype+'&curr_row='+rowId+'&relmod_id='+record_id+'&parent_module=Accounts&return_module='+module+'&currencyid='+currencyid+additionalinfo, 'productWin', cbPopupWindowSettings);
		} else {
			window.open('index.php?module=Products&action=Popup&html=Popup_picker&select=enable&form=HelpDeskEditView&popuptype='+popuptype+'&curr_row='+rowId+'&return_module='+module+'&currencyid='+currencyid+additionalinfo, 'productWin', cbPopupWindowSettings);
		}
	}
}

function getInventoryModuleTaxRelatedInformation() {
	var contact_id = '';
	var account_id = '';
	var vendor_id = '';
	var ship_state = '';
	var ship_code = '';
	var ship_country = '';
	if (document.getElementsByName('contact_id').length != 0) {
		contact_id= document.EditView.contact_id.value;
	} else if (document.getElementsByName('ctoid').length != 0) {
		contact_id= document.EditView.ctoid.value;
	}
	if (document.getElementsByName('account_id').length != 0) {
		account_id= document.EditView.account_id.value;
	} else if (document.getElementsByName('accid').length != 0) {
		account_id= document.EditView.accid.value;
	}
	if (document.getElementsByName('vendor_id').length != 0) {
		vendor_id= document.EditView.vendor_id.value;
	}
	if (document.getElementsByName('ship_state').length != 0) {
		ship_state= document.EditView.ship_state.value;
	}
	if (document.getElementsByName('ship_code').length != 0) {
		ship_code= document.EditView.ship_code.value;
	}
	if (document.getElementsByName('ship_country').length != 0) {
		ship_country= document.EditView.ship_country.value;
	}
	var additionalinfo = '&ctoid=' + contact_id;
	additionalinfo = additionalinfo +'&accid=' + account_id;
	additionalinfo = additionalinfo +'&vndid=' + vendor_id;
	additionalinfo = additionalinfo +'&ship_state=' + encodeURIComponent(ship_state);
	additionalinfo = additionalinfo +'&ship_code=' + encodeURIComponent(ship_code);
	additionalinfo = additionalinfo +'&ship_country=' + encodeURIComponent(ship_country);
	var custompopup = ['ctoid', 'accid', 'vndid', 'ship_state', 'ship_code', 'ship_country'];
	additionalinfo += '&cbcustompopupinfo='+custompopup.join(';');
	if (typeof(document.getElementsByName('whid')) != 'undefined' && document.getElementsByName('whid').length != 0) {
		whid= document.EditView.whid.value;
		additionalinfo = additionalinfo +'&whid=' + encodeURIComponent(whid);
	}
	return trim(additionalinfo);
}

function priceBookPickList(currObj, row_no) {
	var currencyid = document.getElementById('inventory_currency').value;
	var productId=getObj('hdnProductId'+row_no).value || -1;
	window.open('index.php?module=PriceBooks&action=Popup&html=Popup_picker&form=EditView&popuptype=inventory_pb&fldname=listPrice'+row_no+'&productid='+productId+'&currencyid='+currencyid, 'priceBookWin', cbPopupWindowSettings);
}

function getProdListBody() {
	var prodListBody;
	if (browser_ie) {
		prodListBody=getObj('productList').children[0].children[0];
	} else if (browser_nn4 || browser_nn6) {
		if (getObj('productList').childNodes.item(0).tagName=='TABLE') {
			prodListBody=getObj('productList').childNodes.item(0).childNodes.item(0);
		} else {
			prodListBody=getObj('productList').childNodes.item(1).childNodes.item(1);
		}
	}
	return prodListBody;
}

function deleteRow(module, i) {
	rowCnt--;

	document.getElementById('row'+i).style.display = 'none';
	var tblrows = document.getElementById('proTab').rows;
	for (var iRow=0; iRow<tblrows.length; iRow++) {
		if (typeof(tblrows[iRow].id)!='undefined' && tblrows[iRow].id=='row'+i) {
			if (typeof(tblrows[iRow+1])!='undefined' && (typeof(tblrows[iRow+1].id)=='undefined' || tblrows[iRow+1].id!='row'+(i+1))) {
				tblrows[iRow+1].style.display = 'none';
				break;
			}
		}
	}
	// Added For product Reordering starts
	var iMax = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	for (var iCount=i; iCount>=1; iCount--) {
		if (document.getElementById('row'+iCount) && document.getElementById('row'+iCount).style.display != 'none') {
			var iPrevRowIndex = iCount;
			break;
		}
	}
	var oPrevRow = '';
	var iPrevCount = iPrevRowIndex;
	var oCurRow = document.getElementById('row'+i);
	var sTemp = oCurRow.cells[0].innerHTML;
	var ibFound = sTemp.indexOf('down_layout.gif');
	var prevLineItemId = document.getElementById('lineitem_id'+iPrevCount) == undefined ? '' : document.getElementById('lineitem_id'+iPrevCount).value;
	if (i != 2 && ibFound == -1 && iPrevCount != 1) {
		oPrevRow = document.getElementById('row'+iPrevCount);
		oPrevRow.cells[0].innerHTML = '<img src="themes/softed/images/delete.gif" border="0" onclick="deleteRow(\''+module+'\','+iPrevCount+')" style="cursor:pointer;" title="'+alert_arr.LBL_DELETE_EMAIL+'"><input id="deleted'+iPrevCount+'" name="deleted'+iPrevCount+'" type="hidden" value="0"><input id="lineitem_id'+iPrevCount+'" name="lineitem_id'+iPrevCount+'" value="'+prevLineItemId+'" type="hidden">&nbsp;<a href="javascript:moveUpDown(\'UP\',\''+module+'\','+iPrevCount+')" title="'+alert_arr.MoveUp+'"><img src="themes/images/up_layout.gif" border="0"></a>';
	} else if (iPrevCount == 1) {
		var iSwapIndex = i;
		for (iCount=i; iCount<=iMax; iCount++) {
			if (document.getElementById('row'+iCount) && document.getElementById('row'+iCount).style.display != 'none') {
				iSwapIndex = iCount;
				break;
			}
		}
		if (iSwapIndex == i) {
			oPrevRow = document.getElementById('row'+iPrevCount);
			oPrevRow.cells[0].innerHTML = '<input type="hidden" id="deleted1" name="deleted1" value="0"><input id="lineitem_id'+iPrevCount+'" name="lineitem_id'+iPrevCount+'" value="'+prevLineItemId+'" type="hidden">&nbsp;';
		}
	}
	// Product reordering addition ends
	document.getElementById('hdnProductId'+i).value = '';
	document.getElementById('deleted'+i).value = 1;
	calcTotal();
}
/*  End */

// Function to Calcuate the Inventory total including all products
function calcTotal() {
	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	for (var rownum=1; rownum<=max_row_count; rownum++) {
		setDiscount(null, rownum);
	}
	calcGrandTotal();
}

// Function to Calculate the Total for a particular product in an Inventory
function calcProductTotal(rowId) {
	if (document.getElementById('deleted'+rowId) && document.getElementById('deleted'+rowId).value == 0) {
		var chknum = getObj('listPrice'+rowId).value;
		if (chknum.indexOf(',')!=-1 || chknum.indexOf('\'')!=-1) {
			document.getElementById('listPrice'+rowId).value = standarizeFormatCurrencyValue(chknum);
		}
		chknum = getObj('qty'+rowId).value;
		if (chknum.indexOf(',')!=-1 || chknum.indexOf('\'')!=-1) {
			document.getElementById('qty'+rowId).value = standarizeFormatCurrencyValue(chknum);
		}
		var total=getObj('qty'+rowId).value * getObj('listPrice'+rowId).value;
		getObj('productTotal'+rowId).innerHTML=roundValue(total.toString());

		var totalAfterDiscount = total-document.getElementById('discountTotal'+rowId).innerHTML;
		getObj('totalAfterDiscount'+rowId).innerHTML=roundValue(totalAfterDiscount.toString());

		var tax_type = document.getElementById('taxtype').value;
		//if the tax type is individual then add the tax with net price
		var netprice = 0;
		if (tax_type == 'individual') {
			callTaxCalc(rowId);
			netprice = totalAfterDiscount + +document.getElementById('taxTotal'+rowId).innerHTML; // double plus to avoid concatenation
		} else {
			netprice = totalAfterDiscount;
		}
		getObj('netPrice'+rowId).innerHTML=roundValue(netprice.toString());
	}
}

// Function to Calculate the Net and Grand total for all the products together of an Inventory
function calcGrandTotal() {
	var netTotal = 0.0, grandTotal = 0.0;
	var discountTotal_final = 0.0, finalTax = 0.0, sh_amount = 0.0, sh_tax = 0.0, adjustment = 0.0;

	var taxtype = document.getElementById('taxtype').value;

	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	for (var rownum=1; rownum<=max_row_count; rownum++) {
		if (document.getElementById('deleted'+rownum).value == 0) {
			if (document.getElementById('netPrice'+rownum).innerHTML=='') {
				document.getElementById('netPrice'+rownum).innerHTML = 0.0;
			}
			if (!isNaN(document.getElementById('netPrice'+rownum).innerHTML)) {
				netTotal += parseFloat(document.getElementById('netPrice'+rownum).innerHTML);
			}
		}
	}
	netTotal = roundValue(netTotal.toString());
	document.getElementById('netTotal').innerHTML = netTotal;
	document.getElementById('subtotal').value = netTotal;
	setDiscount(this, '_final');
	calcGroupTax();
	//Tax and Adjustment values will be taken when they are valid integer or decimal values
	//if(/^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("txtTax").value))
	//	txtTaxVal = parseFloat(getObj("txtTax").value);
	//if(/^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("txtAdjustment").value))
	//	txtAdjVal = parseFloat(getObj("txtAdjustment").value);

	discountTotal_final = document.getElementById('discountTotal_final').innerHTML;

	//get the final tax based on the group or individual tax selection
	if (taxtype == 'group') {
		finalTax = document.getElementById('tax_final').innerHTML;
	}

	sh_amount = getObj('shipping_handling_charge').value;
	if (document.getElementById('shipping_handling_tax')) {
		sh_tax = parseFloat(document.getElementById('shipping_handling_tax').innerHTML);
	}

	adjustment = getObj('adjustment').value;

	//Add or substract the adjustment based on selection
	var adj_type = document.getElementById('adjustmentType').value;

	grandTotal = parseFloat(netTotal) - parseFloat(discountTotal_final) + parseFloat(finalTax);
	if (sh_amount != '' && sh_amount != 0) {
		grandTotal = grandTotal + parseFloat(sh_amount) + sh_tax;
	}
	if (adjustment != '') {
		if (adjustment.indexOf(',')!=-1 || adjustment.indexOf('\'')!=-1) {
			adjustment = standarizeFormatCurrencyValue(adjustment);
			document.getElementById('adjustment').value = adjustment;
		}
		if (adj_type == '+') {
			grandTotal = grandTotal + parseFloat(adjustment);
		} else {
			grandTotal = grandTotal - parseFloat(adjustment);
		}
	}

	document.getElementById('grandTotal').innerHTML = roundValue(grandTotal.toString());
	document.getElementById('total').value = roundValue(grandTotal.toString());
}

function roundValue(val) {
	// http://www.jacklmoore.com/notes/rounding-in-javascript
	return Number(Math.round(val+'e'+userNumberOfDecimals)+'e-'+userNumberOfDecimals);
}

//This function is used to validate the Inventory modules
function validateInventory(module) {
	return doModuleValidation('', 'EditView', finishValidateInventory);
}

function finishValidateInventory() {
	if (validateInventoryLines(gVTModule)) {
		submitFormForAction('EditView', 'Save');
	} else {
		VtigerJS_DialogBox.unblock();
	}
}

function validateInventoryLines(module) {
	//for products, vendors and pricebook modules we won't validate the product details. here return the control
	if (module == 'Products' || module == 'Vendors' || module == 'PriceBooks' || module == 'Services' || !inventoryLinesShown) {
		return true;
	}

	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;

	if (!FindDuplicate()) {
		return false;
	}

	if (max_row_count == 0) {
		alert(alert_arr.NO_LINE_ITEM_SELECTED);
		return false;
	}

	for (var i=1; i<=max_row_count; i++) {
		//if the row is deleted then avoid validate that row values
		if (document.getElementById('deleted'+i).value == 1) {
			continue;
		}
		if (!emptyCheck('productName'+i, alert_arr.LINE_ITEM, 'text')) {
			return false;
		}
		if (!emptyCheck('qty'+i, 'Qty', 'text')) {
			return false;
		}
		if (!numValidate('qty'+i, 'Qty', 'any', true)) {
			return false;
		}
		if (!emptyCheck('listPrice'+i, alert_arr.LIST_PRICE, 'text')) {
			return false;
		}
		if (!numValidate('listPrice'+i, alert_arr.LIST_PRICE, 'any')) {
			return false;
		}
	}

	//Product - Discount validation - not allow negative values
	if (!validateProductDiscounts()) {
		return false;
	}

	//Final Discount validation - not allow negative values
	discount_checks = document.getElementsByName('discount_final');

	//Percentage selected, so validate the percentage
	if (discount_checks[1].checked) {
		var temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('discount_percentage_final').value);
		if (!temp) {
			alert(alert_arr.VALID_FINAL_PERCENT);
			return false;
		}
	}
	if (discount_checks[2].checked) {
		temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('discount_amount_final').value);
		if (!temp) {
			alert(alert_arr.VALID_FINAL_AMOUNT);
			return false;
		}
	}

	//Shipping & Handling validation - not allow negative values
	temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('shipping_handling_charge').value);
	if (!temp) {
		alert(alert_arr.VALID_SHIPPING_CHARGE);
		return false;
	}

	//Adjustment validation - allow negative values
	temp = /^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('adjustment').value);
	if (!temp) {
		alert(alert_arr.VALID_ADJUSTMENT);
		return false;
	}

	//Group - Tax Validation  - not allow negative values
	//We need to validate group tax only if taxtype is group.
	var taxtype=document.getElementById('taxtype').value;
	if (taxtype=='group') {
		var tax_count=document.getElementById('group_tax_count').value;
		for (i=1; i<=tax_count; i++) {
			temp = /^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('group_tax_percentage'+i).value);
			if (!temp) {
				alert(alert_arr.VALID_TAX_PERCENT);
				return false;
			}
		}
	}

	//Taxes for Shipping and Handling  validation - not allow negative values
	if (document.getElementById('sh_tax_count')) {
		var shtax_count=document.getElementById('sh_tax_count').value;
		for (i=1; i<=shtax_count; i++) {
			temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('sh_tax_percentage'+i).value);
			if (!temp) {
				alert(alert_arr.VALID_SH_TAX);
				return false;
			}
		}
	}
	calcTotal(); /* Product Re-Ordering Feature Code Addition */
	return true;
}

function FindDuplicate() {
	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	var duplicate = false, iposition = '', positions = '', duplicate_products = '';
	var product_id = new Array(max_row_count-1);
	var product_name = new Array(max_row_count-1);
	product_id[1] = getObj('hdnProductId'+1).value;
	product_name[1] = getObj('productName'+1).value;
	for (var i=1; i<=max_row_count; i++) {
		iposition = ''+i;
		for (var j=i+1; j<=max_row_count; j++) {
			if (i == 1) {
				product_id[j] = getObj('hdnProductId'+j).value;
			}
			if (product_id[i] == product_id[j] && product_id[i] != '') {
				if (!duplicate) {
					positions = iposition;
				}
				duplicate = true;
				if (positions.search(j) == -1) {
					positions = positions+' & '+j;
				}

				if (duplicate_products.search(getObj('productName'+j).value) == -1) {
					duplicate_products = duplicate_products+getObj('productName'+j).value+' \n';
				}
			}
		}
	}
	if (duplicate) {
		if (!confirm(alert_arr.SELECTED_MORE_THAN_ONCE+'\n'+duplicate_products+'\n '+alert_arr.WANT_TO_CONTINUE)) {
			return false;
		}
	}
	return true;
}

function fnshow_Hide(Lay) {
	var tagName = document.getElementById(Lay);
	if (tagName.style.display == 'none') {
		tagName.style.display = 'block';
	} else {
		tagName.style.display = 'none';
	}
}

function ValidateTax(txtObj) {
	var temp= /^\d+(\.\d\d*)*$/.test(document.getElementById(txtObj).value);
	if (!temp) {
		alert(alert_arr.ENTER_VALID_TAX);
	}
}

function loadTaxes_Ajax(curr_row) {
	//Retrieve all the tax values for the currently selected product
	var additionalinfo = getInventoryModuleTaxRelatedInformation() + '&invmod=' + gVTModule;
	additionalinfo = additionalinfo + '&invid=' + getObj('record').value;
	additionalinfo = additionalinfo + '&editmode=' + getObj('mode').value;
	var lineItemType = document.getElementById('lineItemType'+curr_row).value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+lineItemType+'&action='+lineItemType+'Ajax&file=InventoryTaxAjax&productid='+document.getElementById('hdnProductId'+curr_row).value+'&curr_row='+curr_row+'&productTotal='+document.getElementById('totalAfterDiscount'+curr_row).innerHTML+additionalinfo
	}).done(function (response) {
		document.getElementById('tax_div'+curr_row).innerHTML=response;
		document.getElementById('taxTotal'+curr_row).innerHTML = getObj('hdnTaxTotal'+curr_row).value;
		calcTotal();
	});
}

function loadGlobalTaxes_Ajax() {
	//Retrieve global tax values for the current configuration
	var additionalinfo = getInventoryModuleTaxRelatedInformation() + '&invmod=' + gVTModule;
	additionalinfo = additionalinfo + '&invid=' + getObj('record').value;
	additionalinfo = additionalinfo + '&editmode=' + getObj('mode').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Products&action=ProductsAjax&file=InventoryGroupTaxAjax'+additionalinfo
	}).done(function (response) {
		document.getElementById('group_tax_div').innerHTML=response;
		calcTotal();
	});
}

// Function to retrieve and update all taxes > recalculates the whole record
function updateAllTaxes() {
	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	for (var i=1; i<=max_row_count; i++) {
		loadTaxes_Ajax(i);
	}
	loadGlobalTaxes_Ajax();
}

function fnAddTaxConfigRow(sh) {
	var table_id = 'add_tax';
	var td_id = 'td_add_tax';
	var label_name = 'addTaxLabel';
	var label_val = 'addTaxValue';
	var add_tax_flag = 'add_tax_type';

	if (sh != '' && sh == 'sh') {
		table_id = 'sh_add_tax';
		td_id = 'td_sh_add_tax';
		label_name = 'sh_addTaxLabel';
		label_val = 'sh_addTaxValue';
		add_tax_flag = 'sh_add_tax_type';
	}
	var tableName = document.getElementById(table_id);
	var row = tableName.insertRow(0);
	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	var col3 = row.insertCell(2);
	var col4 = row.insertCell(3);
	var col5 = row.insertCell(4);

	colone.className = 'slds-form-element';
	coltwo.className = 'slds-form-element';
	col3.className = 'slds-form-element';
	col4.className = 'slds-form-element';
	col5.className = 'slds-form-element';

	colone.innerHTML='<input type=\'text\' id=\''+label_name+'\' name=\''+label_name+'\' value=\''+tax_labelarr.TAX_NAME+'\' class=\'slds-input\' onclick="this.form.'+label_name+'.value=\'\'";/>';
	coltwo.innerHTML='<input type=\'text\' id=\''+label_val+'\' name=\''+label_val+'\' value=\''+tax_labelarr.TAX_VALUE+'\' class=\'slds-input\' onclick="this.form.'+label_val+'.value=\'\'";/>';
	if (sh == '' && sh != 'sh') {
		col3.innerHTML='<input type=\'checkbox\' id=\''+label_name+'retention\' name=\''+label_name+'retention\' class=\'slds-checkbox\' />';
		col4.innerHTML='<input type=\'checkbox\' id=\''+label_name+'default\' name=\''+label_name+'default\' class=\'slds-checkbox\' />';
		col5.innerHTML='<input type=\'checkbox\' id=\''+label_name+'qcreate\' name=\''+label_name+'qcreate\' class=\'slds-checkbox\' />';
	}
	document.getElementById(td_id).innerHTML='<input type=\'submit\' name=\'Save\' value=\' '+tax_labelarr.SAVE_BUTTON+' \' class=\'slds-button slds-button_success save\' onclick="this.form.action.value=\'TaxConfig\'; this.form.'+add_tax_flag+'.value=\'true\'; return validateNewTaxType(\''+label_name+'\',\''+label_val+'\');">&nbsp;<input type=\'submit\' name=\'Cancel\' value=\' '+tax_labelarr.CANCEL_BUTTON+' \' class=\'slds-button slds-button_destructive cancel\' onclick="this.form.action.value=\'TaxConfig\'; this.form.module.value=\'Settings\'; this.form.'+add_tax_flag+'.value=\'false\';">';
}

function validateNewTaxType(fieldname, fieldvalue) {
	if (trim(document.getElementById(fieldname).value)== '') {
		alert(alert_arr.VALID_TAX_NAME);
		return false;
	}
	if (trim(document.getElementById(fieldvalue).value)== '') {
		alert(alert_arr.CORRECT_TAX_VALUE);
		return false;
	} else {
		var temp = /^[-]?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById(fieldvalue).value);
		if (!temp) {
			alert(alert_arr.ENTER_POSITIVE_VALUE);
			return false;
		}
	}
	return true;
}

function validateTaxes(countname) {
	var taxcount = document.getElementById(countname).value+1;

	if (countname == 'tax_count') {
		var taxprefix = 'tax';
		var taxLabelPrefix = 'taxlabel_tax';
	} else {
		taxprefix = 'shtax';
		taxLabelPrefix = 'taxlabel_shtax';
	}

	for (var i=1; i<=taxcount; i++) {
		var taxval = document.getElementById(taxprefix+i).value;
		var taxLabelVal = document.getElementById(taxLabelPrefix+i).value;
		document.getElementById(taxLabelPrefix+i).value = taxLabelVal.replace(/^\s*|\s*$/g, '').replace(/\s+/g, '');

		if (document.getElementById(taxLabelPrefix+i).value.length == 0) {
			alert(alert_arr.LABEL_SHOULDNOT_EMPTY);
			return false;
		}

		//Tax value - numeric validation
		var temp = /^[-]?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(taxval);
		if (!temp) {
			alert('\''+taxval+'\' '+alert_arr.NOT_VALID_ENTRY);
			return false;
		}
	}
	return true;
}

//Function used to add a new product row in PO, SO, Quotes and Invoice
function fnAddProductRow(module) {
	rowCnt++;

	var tableName = document.getElementById('proTab');
	var prev = tableName.rows.length;
	var pdoRows = document.getElementById('proTab').querySelectorAll('[id^="row"]');
	for (var iCount=pdoRows.length; iCount>=1; iCount--) {
		if (document.getElementById('row'+iCount) && document.getElementById('row'+iCount).style.display != 'none') {
			var iPrevRowId = iCount;
			break;
		}
	}
	var iPrevRowIndex = pdoRows[iPrevRowId-1].rowIndex;
	var count = pdoRows.length+1;
	var row = tableName.insertRow(prev);
	row.id = 'row'+count;
	row.style.verticalAlign = 'top';

	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	var colthree = row.insertCell(2);
	var colfour = row.insertCell(3);
	var colfive = row.insertCell(4);
	var colsix = row.insertCell(5);
	var colseven = row.insertCell(6);

	/* Product Re-Ordering Feature Code Addition Starts */
	var iPrevCount = iPrevRowId;
	var oPrevRow = tableName.rows[iPrevRowIndex];
	/* Product Re-Ordering Feature Code Addition ends */

	//Delete link
	colone.className = 'crmTableRow small';
	colone.innerHTML='<img src="themes/softed/images/delete.gif" border="0" onclick="deleteRow(\''+module+'\','+count+')" style="cursor:pointer;" title="'+alert_arr.LBL_DELETE_EMAIL+'"><input id="deleted'+count+'" name="deleted'+count+'" type="hidden" value="0"><br/><br/>&nbsp;<a href="javascript:moveUpDown(\'UP\',\''+module+'\','+count+')" title="'+alert_arr.MoveUp+'"><img src="themes/images/up_layout.gif" border="0"></a>';
	/* Product Re-Ordering Feature Code Addition Starts */
	var prevLineItemId = document.getElementById('lineitem_id'+iPrevCount) == undefined ? '' : document.getElementById('lineitem_id'+iPrevCount).value;

	if (iPrevCount != 1) {
		oPrevRow.cells[0].innerHTML = '<img src="themes/softed/images/delete.gif" border="0" onclick="deleteRow(\''+module+'\','+iPrevCount+')" style="cursor:pointer;" title="'+alert_arr.LBL_DELETE_EMAIL+'"><input id="deleted'+iPrevCount+'" name="deleted'+iPrevCount+'" type="hidden" value="0"><input id="lineitem_id'+iPrevCount+'" name="lineitem_id'+iPrevCount+'" value="'+prevLineItemId+'" type="hidden"><br/><br/>&nbsp;<a href="javascript:moveUpDown(\'UP\',\''+module+'\','+iPrevCount+')" title="'+alert_arr.MoveUp+'"><img src="themes/images/up_layout.gif" border="0"></a>&nbsp;&nbsp;<a href="javascript:moveUpDown(\'DOWN\',\''+module+'\','+iPrevCount+')" title="'+alert_arr.MoveDown+'"><img src="themes/images/down_layout.gif" border="0"></a><input id=';
	} else {
		oPrevRow.cells[0].innerHTML = '<input id="deleted'+iPrevCount+'" name="deleted'+iPrevCount+'" type="hidden" value="0"><input id="lineitem_id'+iPrevCount+'" name="lineitem_id'+iPrevCount+'" value="'+prevLineItemId+'" type="hidden"><br/><br/><a href="javascript:moveUpDown(\'DOWN\',\''+module+'\','+iPrevCount+')" title="'+alert_arr.MoveDown+'"><img src="themes/images/down_layout.gif" border="0"></a>';
	}
	/* Product Re-Ordering Feature Code Addition ends */

	//Product Name with Popup image to select product
	coltwo.className = 'crmTableRow small';
	coltwo.innerHTML= `<table border="0" cellpadding="1" cellspacing="0" width="100%">
							<tbody>
								<tr>
									<td class="small">
										<div class="slds-combobox_container slds-has-inline-listbox cbds-product-search" style="width:70%;display:inline-block">
											<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click slds-combobox-lookup" aria-expanded="false" aria-haspopup="listbox" role="combobox">
												<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
													<input id="productName${count}"
															name="productName${count}"
															class="slds-input slds-combobox__input cbds-inventoryline__input--name"
															aria-autocomplete="list"
															aria-controls="listbox-unique-id"
															autocomplete="off"
															role="textbox"
															placeholder="${inventoryi18n.typetosearch_prodser}" value=""
															type="text"
															style="box-shadow: none;">
														<span class="slds-icon_container slds-icon-utility-search slds-input__icon slds-input__icon_right">
															<svg class="slds-icon slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
																<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
															</svg>
														</span>
														<div class="slds-input__icon-group slds-input__icon-group_right">
															<div role="status" class="slds-spinner slds-spinner_brand slds-spinner_x-small slds-input__spinner slds-hide">
																<span class="slds-assistive-text">Loading</span>
																<div class="slds-spinner__dot-a"></div>
																<div class="slds-spinner__dot-b"></div>
															</div>
														</div>
												</div>
											</div>
										</div>&nbsp;
										<img id="searchIcon${count}" title="${alert_arr.Products}" src="themes/images/products.gif" style="cursor: pointer;" onclick="productPickList(this,'${module}',${count})" align="absmiddle">
										<input id="hdnProductId${count}" name="hdnProductId${count}" value="" type="hidden">
										<input type="hidden" id="lineItemType${count}" name="lineItemType${count}" value="Products" />
									</td>
								</tr>
								<tr>
									<td class="small">
										<input type="hidden" value="" id="subproduct_ids${count}" name="subproduct_ids${count}" />
										<span id="subprod_names${count}" name="subprod_names${count}" style="color:#C0C0C0;font-style:italic;"></span>
									</td>
								</tr>
								<tr>
									<td class="small" id="setComment${count}">
										<textarea id="comment${count}" name="comment${count}" class=small style="${Inventory_Comment_Style}"></textarea>
										<img src="themes/images/clear_field.gif" onClick="getObj('comment${count}').value=''"; style="cursor:pointer;" />
									</td>
								</tr>
							</tbody>
						</table>`;

	//Additional Information column
	colthree.className = 'crmTableRow small';
	cloneMoreInfoNode(count);

	//QuantityfnAddProductRow
	var temp='';
	colfour.className = 'crmTableRow small';
	temp='<input id="qty'+count+'" name="qty'+count+'" type="text" class="small " style="width:50px" onBlur="settotalnoofrows(); calcTotal(); loadTaxes_Ajax('+count+');';
	if (module == 'Invoice') {
		temp+='stock_alert('+count+');';
	}
	temp+='" onChange="setDiscount(this,'+count+')" value=""/><br><span id="stock_alert'+count+'"></span>';
	colfour.innerHTML=temp;
	//List Price with Discount, Total after Discount and Tax labels
	colfive.className = 'crmTableRow small inv-editview__pricecol';
	colfive.innerHTML = `<table class="slds-table slds-table_cell-buffer"><tbody>
		<tr>
			<td style="padding:5px;">
				<input id="listPrice${count}" name="listPrice${count}" value="0.00" type="text" class="small" style="width:70px" onBlur="calcTotal();setDiscount(this,${count});callTaxCalc(${count}); calcTotal();"${(Inventory_ListPrice_ReadOnly==1 ? ' readonly ' : '')}/>&nbsp;<img src="themes/images/pricebook.gif" onclick="priceBookPickList(this,${count})">
			</td>
		</tr>
		<tr>
			<td style="padding:5px;" nowrap>
				(-)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,'discount_div${count}','discount',${count})" >${product_labelarr.DISCOUNT}</a> : </b>
				<div class="discountUI" id="discount_div${count}">
					<input type="hidden" id="discount_type${count}" name="discount_type${count}" value="">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
					<table class="slds-table slds-table_cell-buffer slds-table_bordered">
					<thead>
						<tr class="slds-line-height_reset">
						<th id="discount_div_title${count}" class="slds-p-left_none" scope="col"></th>
						<th class="cblds-t-align_right slds-p-right_none" scope="col"><img src="themes/images/close.gif" border="0" onClick="fnhide('discount_div${count}')" style="cursor:pointer;"></th>
						</tr>
					</thead>
					<tbody>
					<tr>
						<td class="lineOnTop" style="padding-left: 4px; text-align: left !important;">
							<input type="radio" name="discount${count}" checked onclick="setDiscount(this,${count}); callTaxCalc(${count});calcTotal();">&nbsp; ${product_labelarr.ZERO_DISCOUNT}
						</td>
						<td class="lineOnTop">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left: 4px; text-align: left !important;">
							<input type="radio" name="discount${count}" onclick="setDiscount(this,${count}); callTaxCalc(${count});calcTotal();">&nbsp; % ${product_labelarr.PERCENT_OF_PRICE}
						</td>
						<td style="padding-left: 2px; padding-right: 4px;">
							<input type="text" class="small" size="2" id="discount_percentage${count}" name="discount_percentage${count}" value="0" style="visibility:hidden" onBlur="setDiscount(this,${count}); callTaxCalc(${count});calcTotal();">&nbsp;%
						</td>
					</tr>
					<tr>
						<td nowrap style="padding-left: 4px; text-align: left !important;">
							<input type="radio" name="discount${count}" onclick="setDiscount(this,${count}); callTaxCalc(${count});calcTotal();">&nbsp; ${product_labelarr.DIRECT_PRICE_REDUCTION}
						</td>
						<td style="padding-left: 2px; padding-right: 4px;">
							<input type="text" id="discount_amount${count}" name="discount_amount${count}" size="5" value="0" style="visibility:hidden" onBlur="setDiscount(this,${count}); callTaxCalc(${count});calcTotal();">
						</td>
					</tr>
					</tbody>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td style="padding:5px;" nowrap>
				<b>${product_labelarr.TOTAL_AFTER_DISCOUNT} :</b>
			</td>
		</tr>
		<tr id="individual_tax_row${count}" class="TaxShow">
			<td style="padding:5px;" nowrap>
				(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,'tax_div${count}','tax',${count})" >${product_labelarr.TAX} </a> : </b>
				<div class="discountUI" id="tax_div${count}"></div>
			</td>
		</tr>
		</tbody></table>`;

	//Total and Discount, Total after Discount and Tax details
	colsix.className = 'crmTableRow small inv-editview__totalscol';
	colsix.innerHTML = `<table class="slds-table slds-table_cell-buffer"><tbody>
		<tr><td id="productTotal${count}" style="padding-top:6px;">&nbsp;</td></tr>
		<tr><td id="discountTotal${count}" style="padding-top:6px;">0.00</td></tr>
		<tr><td id="totalAfterDiscount${count}" style="padding-top:6px;">&nbsp;</td></tr>
		<tr><td id="taxTotal${count}" style="padding-top:6px;">0.00</td></tr>
		</tbody></table>`;

	//Net Price
	colseven.className = 'crmTableRow small inv-editview__netpricecol';
	colseven.style.verticalAlign = 'bottom';
	colseven.innerHTML = '<span id="netPrice'+count+'"><b>&nbsp;</b></span>';

	//This is to show or hide the individual or group tax
	decideTaxDiv();
	calcTotal();

	var newProdRow = document.getElementsByClassName('cbds-product-search')[count - 1];
	var ac = new ProductAutocomplete(newProdRow, {}, handleProductAutocompleteSelect);

	return count;
}

function cloneMoreInfoNode(newRowId) {
	var tblBody = document.getElementById('proTab').tBodies[0];
	var moreinfocell1 = tblBody.rows[2].cells[2];
	var moreinfocell = moreinfocell1.cloneNode(true);
	// change IDs
	var domfld = moreinfocell.querySelector('#qtyInStock1');
	if (domfld) {
		domfld.id = 'qtyInStock'+ newRowId;
	}
	for (var i=0; i<moreInfoFields.length; i++) {
		moreinfocell.innerHTML = moreinfocell.innerHTML.replace(new RegExp(moreInfoFields[i]+'1', 'g'), moreInfoFields[i]+ newRowId);
	}
	var pdoRows = document.getElementById('proTab').querySelectorAll('[id^="row"]');
	var tblRowIndex = pdoRows[newRowId-1].rowIndex;
	tblBody.rows[tblRowIndex].cells[2].innerHTML=moreinfocell.innerHTML;
	vtlib_executeJavascriptInElement(moreinfocell);
	// empty fields
	var domflddisp = document.getElementById('qtyInStock'+ newRowId);
	if (domflddisp) {
		domflddisp.innerHTML = '';
	}
	for (i=0; i<moreInfoFields.length; i++) {
		domfld = document.getElementById(moreInfoFields[i]+ newRowId);
		if (domfld) {
			domfld.value = '';
			domflddisp = document.getElementById(moreInfoFields[i]+ newRowId+ '_display');
			if (domflddisp) {
				domflddisp.value = '';
			}
			var domfldhidden = document.getElementById(moreInfoFields[i]+ newRowId+ '_hidden');
			if (domfldhidden) {
				domfldhidden.value = '';
			}
			var domfldpathhidden = document.getElementById(moreInfoFields[i]+ newRowId+ '_path_hidden');
			if (domfldpathhidden) {
				domfldpathhidden.value = '';
			}
		} else {
			var domfld = document.getElementById('jscal_field_'+ moreInfoFields[i]+ newRowId);
			if (domfld) {
				domfld.value = '';
			}
		}
	}
}

function decideTaxDiv() {
	var taxtype = document.getElementById('taxtype').value;
	calcTotal();
	if (taxtype == 'group') {
		//if group tax selected then we have to hide the individual taxes and also calculate the group tax
		hideIndividualTaxes();
		calcGroupTax();
	} else if (taxtype == 'individual') {
		hideGroupTax();
	}
}

function hideIndividualTaxes() {
	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	for (var rownum=1; rownum<=max_row_count; rownum++) {
		document.getElementById('individual_tax_row'+rownum).className = 'TaxHide';
		document.getElementById('taxTotal'+rownum).style.display = 'none';
	}
	document.getElementById('group_tax_row').className = 'TaxShow';
}

function hideGroupTax() {
	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	for (var rownum=1; rownum<=max_row_count; rownum++) {
		document.getElementById('individual_tax_row'+rownum).className = 'TaxShow';
		document.getElementById('taxTotal'+rownum).style.display = 'block';
	}
	document.getElementById('group_tax_row').className = 'TaxHide';
}

function setDiscount(currObj, curr_row) {
	var discount_checks = new Array();

	discount_checks = document.getElementsByName('discount'+curr_row);
	calcProductTotal(curr_row);
	if (discount_checks[0].checked) {
		document.getElementById('discount_type'+curr_row).value = 'zero';
		document.getElementById('discount_percentage'+curr_row).style.visibility = 'hidden';
		document.getElementById('discount_amount'+curr_row).style.visibility = 'hidden';
		document.getElementById('discountTotal'+curr_row).innerHTML = 0.00;
	}
	if (discount_checks[1].checked) {
		document.getElementById('discount_type'+curr_row).value = 'percentage';
		document.getElementById('discount_percentage'+curr_row).style.visibility = 'visible';
		document.getElementById('discount_amount'+curr_row).style.visibility = 'hidden';

		var discount_amount = 0.00;
		//This is to calculate the final discount
		if (curr_row == '_final') {
			var discount_percentage_final_value = document.getElementById('discount_percentage'+curr_row).value;
			if (discount_percentage_final_value.indexOf(',')!=-1 || discount_percentage_final_value.indexOf('\'')!=-1) {
				discount_percentage_final_value = standarizeFormatCurrencyValue(discount_percentage_final_value);
				document.getElementById('discount_percentage'+curr_row).value = discount_percentage_final_value;
			}
			if (discount_percentage_final_value == '') {
				discount_percentage_final_value = 0;
			}
			discount_amount = document.getElementById('netTotal').innerHTML*discount_percentage_final_value/100;
		} else {
			// This is to calculate the product discount
			var discount_percentage_value = document.getElementById('discount_percentage'+curr_row).value;
			if (discount_percentage_value.indexOf(',')!=-1 || discount_percentage_value.indexOf('\'')!=-1) {
				discount_percentage_value = standarizeFormatCurrencyValue(discount_percentage_value);
				document.getElementById('discount_percentage'+curr_row).value = discount_percentage_value;
			}
			if (discount_percentage_value == '') {
				discount_percentage_value = 0;
			}
			discount_amount = document.getElementById('qty'+curr_row).value*document.getElementById('listPrice'+curr_row).value*discount_percentage_value/100;
		}
		//Rounded the decimal part of discount amount to two digits
		document.getElementById('discountTotal'+curr_row).innerHTML = roundValue(discount_amount.toString());
	}
	if (discount_checks[2].checked) {
		document.getElementById('discount_type'+curr_row).value = 'amount';
		document.getElementById('discount_percentage'+curr_row).style.visibility = 'hidden';
		document.getElementById('discount_amount'+curr_row).style.visibility = 'visible';
		//Rounded the decimal part of discount amount to two digits
		var discount_amount_value = document.getElementById('discount_amount'+curr_row).value.toString();
		if (discount_amount_value.indexOf(',')!=-1 || discount_amount_value.indexOf('\'')!=-1) {
			discount_amount_value = standarizeFormatCurrencyValue(discount_amount_value);
			document.getElementById('discount_amount'+curr_row).value = discount_amount_value;
		}
		if (discount_amount_value == '') {
			discount_amount_value = 0;
		}
		document.getElementById('discountTotal'+curr_row).innerHTML = roundValue(discount_amount_value);
	}
	// Update product total as discount would have changed.
	if (curr_row != '_final') {
		calcProductTotal(curr_row);
	}
}

//This function is added to call the tax calculation function
function callTaxCalc(curr_row) {
	//when we change discount or list price, we have to calculate the taxes again before calculate the total
	if (getObj('tax_table'+curr_row)) {
		var tax_count = document.getElementById('tax_table'+curr_row).rows.length-1;//subtract the title tr length
		for (var i=0, j=i+1; i<tax_count; i++, j++) {
			var tax_hidden_name = 'hidden_tax'+j+'_percentage'+curr_row;
			var tax_name = document.getElementById(tax_hidden_name).value;
			calcCurrentTax(tax_name, curr_row, i);
		}
	}
}

function calcCurrentTax(tax_name, curr_row, tax_row) {
	//we should calculate the tax amount only for the total After Discount
	var product_total = getObj('totalAfterDiscount'+curr_row).innerHTML;
	if (product_total.substring(0, 3) == 'NaN') {
		product_total = 0;
	}
	var new_tax_percent = document.getElementById(tax_name).value;

	var new_amount_lbl = document.getElementsByName('popup_tax_row'+curr_row);

	//calculate the new tax amount
	var new_tax_amount = product_total*new_tax_percent/100;

	//Rounded the decimal part of tax amount to two digits
	new_tax_amount = roundValue(new_tax_amount.toString());

	//assign the new tax amount in the corresponding text box
	new_amount_lbl[tax_row].value = new_tax_amount;

	var tax_total = 0.00;
	for (var i=0; i<new_amount_lbl.length; i++) {
		tax_total = tax_total + (1*new_amount_lbl[i].value);
	}
	document.getElementById('taxTotal'+curr_row).innerHTML = roundValue(tax_total);
}

function calcGroupTax() {
	var group_tax_count = document.getElementById('group_tax_count').value;

	var netTotal_value = document.getElementById('netTotal').innerHTML;
	if (netTotal_value == '') {
		netTotal_value = 0;
	}

	var discountTotal_final_value = document.getElementById('discountTotal_final').innerHTML;
	if (discountTotal_final_value == '') {
		discountTotal_final_value = 0;
	}

	var net_total_after_discount = netTotal_value-discountTotal_final_value;
	var group_tax_total = 0.00, tax_amount=0.00;

	for (var i=1; i<=group_tax_count; i++) {
		var group_tax_percentage = document.getElementById('group_tax_percentage'+i).value;
		if (group_tax_percentage == '') {
			group_tax_percentage = '0';
		}
		tax_amount = net_total_after_discount*group_tax_percentage/100;
		document.getElementById('group_tax_amount'+i).value = tax_amount;
		group_tax_total = group_tax_total + tax_amount;
	}

	document.getElementById('tax_final').innerHTML = roundValue(group_tax_total);
}

function calcSHTax() {
	if (!document.getElementById('sh_tax_count')) {
		return;
	}
	var sh_tax_count = document.getElementById('sh_tax_count').value;
	var sh_charge = document.getElementById('shipping_handling_charge').value;
	if (sh_charge.indexOf(',')!=-1 || sh_charge.indexOf('\'')!=-1) {
		sh_charge = standarizeFormatCurrencyValue(sh_charge);
		document.getElementById('shipping_handling_charge').value = sh_charge;
	}
	if (sh_charge == '') {
		sh_charge = 0;
	}
	var sh_tax_total = 0.00, tax_amount=0.00;

	for (var i=1; i<=sh_tax_count; i++) {
		var sh_tax_percentage = document.getElementById('sh_tax_percentage'+i).value;
		if (sh_tax_percentage == '') {
			sh_tax_percentage = 0;
		}
		tax_amount = parseFloat(sh_charge) * parseFloat(sh_tax_percentage) / 100;
		//Rounded the decimal part of S&H Tax amount to two digits
		document.getElementById('sh_tax_amount'+i).value = roundValue(tax_amount.toString());
		sh_tax_total = sh_tax_total + tax_amount;
	}

	//Rounded the decimal part of Total S&H Tax amount to two digits
	document.getElementById('shipping_handling_tax').innerHTML = roundValue(sh_tax_total.toString());

	calcTotal();
}

function validateProductDiscounts() {
	var temp = '';
	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	for (var i=1; i<=max_row_count; i++) {
		//if the row is deleted then avoid validate that row values
		if (document.getElementById('deleted'+i).value == 1) {
			continue;
		}

		discount_checks = document.getElementsByName('discount'+i);

		//Percentage selected, so validate the percentage
		if (discount_checks[1].checked) {
			temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('discount_percentage'+i).value);
			if (!temp) {
				alert(alert_arr.VALID_DISCOUNT_PERCENT);
				return false;
			}
		}
		if (discount_checks[2].checked) {
			temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById('discount_amount'+i).value);
			if (!temp) {
				alert(alert_arr.VALID_DISCOUNT_AMOUNT);
				return false;
			}
		}
	}
	return true;
}

function stock_alert(curr_row) {
	var stock=getObj('qtyInStock'+curr_row).innerHTML;
	var qty=getObj('qty'+curr_row).value;
	if (!isNaN(qty)) {
		if (parseFloat(qty) > parseFloat(stock)) {
			getObj('stock_alert'+curr_row).innerHTML='<font color="red" size="1">'+alert_arr.STOCK_IS_NOT_ENOUGH+'</font>';
		} else {
			getObj('stock_alert'+curr_row).innerHTML='';
		}
	} else {
		getObj('stock_alert'+curr_row).innerHTML='<font color="red" size="1">'+alert_arr.INVALID_QTY+'</font>';
	}
}

// Function to Get the price for all the products of an Inventory based on the Currency choosen by the User
function updatePrices() {
	var prev_cur = document.getElementById('prev_selected_currency_id');
	var inventory_currency = document.getElementById('inventory_currency');
	if (confirm(alert_arr.MSG_CHANGE_CURRENCY_REVISE_UNIT_PRICE)) {
		var productsListElem = document.getElementById('proTab');
		if (productsListElem == null) {
			return;
		}

		var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;

		var products_list = '';
		for (var i=1; i<=max_row_count; i++) {
			var productid = document.getElementById('hdnProductId'+i).value;
			if (i != 1) {
				products_list = products_list + '::';
			}
			products_list = products_list + productid;
		}

		if (prev_cur != null && inventory_currency != null) {
			prev_cur.value = inventory_currency.value;
		}
		if (products_list!='') {
			var currency_id = inventory_currency.value;
			//Retrieve all the prices for all the products in currently selected currency
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Products&action=ProductsAjax&file=InventoryPriceAjax&currencyid='+currency_id+'&productsList='+products_list
			}).done(function (response) {
				if (trim(response).indexOf('SUCCESS') == 0) {
					var res = trim(response).split('$');
					updatePriceValues(res[1]);
				} else {
					alert(alert_arr.OPERATION_DENIED);
				}
			});
		}
	} else {
		if (prev_cur != null && inventory_currency != null) {
			inventory_currency.value = prev_cur.value;
		}
	}
}

// Function to Update the price for the products in the Inventory Edit View based on the Currency choosen by the User.
function updatePriceValues(pricesList) {
	if (pricesList == null || pricesList == '') {
		return;
	}
	var prices_list = pricesList.split('::');

	var productsListElem = document.getElementById('proTab');
	if (productsListElem == null) {
		return;
	}

	var max_row_count = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;

	for (var i=1; i<=max_row_count; i++) {
		var list_price_elem = document.getElementById('listPrice'+i);
		var unit_price = prices_list[i-1]; // Price values index starts from 0
		list_price_elem.value = unit_price;

		// Set Direct Discout amount to 0
		var discount_amount = document.getElementById('discount_amount'+i);
		if (discount_amount != null) {
			discount_amount.value = '0';
		}

		calcProductTotal(i);
		setDiscount(list_price_elem, i);
		callTaxCalc(i);
	}
	resetSHandAdjValues();
	calcTotal();
}

// Function to Reset the S&H Charges and Adjustment value with change in Currency
function resetSHandAdjValues() {
	var sh_amount = document.getElementById('shipping_handling_charge');
	if (sh_amount != null) {
		sh_amount.value = '0';
	}

	var sh_amount_tax = document.getElementById('shipping_handling_tax');
	if (sh_amount_tax != null) {
		sh_amount_tax.innerHTML = '0';
	}

	var adjustment = document.getElementById('adjustment');
	if (adjustment != null) {
		adjustment.value = '0';
	}

	var final_discount = document.getElementById('discount_amount_final');
	if (final_discount != null) {
		final_discount.value = '0';
	}
}

/** Function for Product Re-Ordering Feature
 * It will be responsible for moving record up/down, 1 step at a time
 */
function moveUpDown(sType, oModule, iIndex) {
	var aFieldIds = Array('hidtax_row_no', 'productName', 'subproduct_ids', 'hdnProductId', 'comment', 'qty', 'listPrice', 'discount_type', 'discount_percentage', 'discount_amount', 'tax1_percentage', 'hidden_tax1_percentage', 'popup_tax_row', 'tax2_percentage', 'hidden_tax2_percentage', 'lineItemType', 'lineitem_id', 'rel_lineitem_id');
	aFieldIds = aFieldIds.concat(moreInfoFields);
	var aContentIds = Array('qtyInStock', 'netPrice', 'subprod_names');
	var aOnClickHandlerIds = Array('searchIcon');

	var iMax = document.getElementById('proTab').querySelectorAll('[id^="row"]').length;
	var iSwapIndex = 1;
	if (sType == 'UP') {
		for (var iCount=iIndex-1; iCount>=1; iCount--) {
			if (document.getElementById('row'+iCount)) {
				if (document.getElementById('row'+iCount).style.display != 'none' && document.getElementById('deleted'+iCount).value == 0) {
					iSwapIndex = iCount;
					break;
				}
			}
		}
	} else {
		for (iCount=iIndex+1; iCount<=iMax; iCount++) {
			if (document.getElementById('row'+iCount) && document.getElementById('row'+iCount).style.display != 'none' && document.getElementById('deleted'+iCount).value == 0) {
				iSwapIndex = iCount;
				break;
			}
		}
	}
	var iTableRowIndex = document.getElementById('row'+iIndex).rowIndex;
	var iTableRowSwapIndex = document.getElementById('row'+iSwapIndex).rowIndex;
	var oTable = document.getElementById('proTab');
	if (typeof(oTable.rows[iTableRowIndex+1])=='object' && !oTable.rows[iTableRowIndex+1].id.startsWith('row')) {
		var holdRow = oTable.rows[iTableRowIndex+1].innerHTML;
		oTable.rows[iTableRowIndex+1].innerHTML = oTable.rows[iTableRowSwapIndex+1].innerHTML;
		oTable.rows[iTableRowSwapIndex+1].innerHTML = holdRow;
		corebosjshook_InventorymoveUpDown_customrow(oTable, iTableRowIndex+1, iIndex, iTableRowSwapIndex+1, iSwapIndex);
	}
	var iCheckIndex = 0;
	var iSwapCheckIndex = 0;
	var sFormElement = '';
	var domFormElements = document.getElementById('frmEditView').elements;
	for (var j=0; j<=2; j++) {
		if (domFormElements['discount'+iIndex] && domFormElements['discount'+iIndex][j]) {
			sFormElement = domFormElements['discount'+iIndex][j];
			if (sFormElement.checked) {
				iCheckIndex = j;
				break;
			}
		}
	}

	for (j=0; j<=2; j++) {
		if (domFormElements['discount'+iSwapIndex] && domFormElements['discount'+iSwapIndex][j]) {
			sFormElement = domFormElements['discount'+iSwapIndex][j];
			if (sFormElement.checked) {
				iSwapCheckIndex = j;
				break;
			}
		}
	}
	if (domFormElements['discount'+iIndex] && domFormElements['discount'+iIndex][iSwapCheckIndex]) {
		var oElement = domFormElements['discount'+iIndex][iSwapCheckIndex];
		oElement.checked = true;
	}
	if (domFormElements['discount'+iSwapIndex] && domFormElements['discount'+iSwapIndex][iCheckIndex]) {
		var oSwapElement = domFormElements['discount'+iSwapIndex][iCheckIndex];
		oSwapElement.checked = true;
	}

	var sTemp = '';
	var sId = '';
	var sSwapId = '';
	var iMaxElement = aFieldIds.length;
	for (var iCt=0; iCt<iMaxElement; iCt++) {
		sId = aFieldIds[iCt] + iIndex;
		sSwapId = aFieldIds[iCt] + iSwapIndex;
		if (document.getElementById(sId) && document.getElementById(sSwapId)) {
			sTemp = document.getElementById(sId).value;
			document.getElementById(sId).value = document.getElementById(sSwapId).value;
			document.getElementById(sSwapId).value = sTemp;
		}
		sId = 'jscal_field_' + aFieldIds[iCt] + iIndex;
		sSwapId = 'jscal_field_'+ aFieldIds[iCt] + iSwapIndex;
		if (document.getElementById(sId) && document.getElementById(sSwapId)) {
			sTemp = document.getElementById(sId).value;
			document.getElementById(sId).value = document.getElementById(sSwapId).value;
			document.getElementById(sSwapId).value = sTemp;
		}
		sId = aFieldIds[iCt] + iIndex + '_display';
		sSwapId = aFieldIds[iCt] + iSwapIndex + '_display';
		if (document.getElementById(sId) && document.getElementById(sSwapId)) {
			sTemp = document.getElementById(sId).value;
			document.getElementById(sId).value = document.getElementById(sSwapId).value;
			document.getElementById(sSwapId).value = sTemp;
			sId = aFieldIds[iCt] + iIndex + '_type';
			sSwapId = aFieldIds[iCt] + iSwapIndex + '_type';
			sTemp = document.getElementById(sId).value;
			document.getElementById(sId).value = document.getElementById(sSwapId).value;
			document.getElementById(sSwapId).value = sTemp;
		}
	}
	iMaxElement = aContentIds.length;
	for (iCt=0; iCt<iMaxElement; iCt++) {
		sId = aContentIds[iCt] + iIndex;
		sSwapId = aContentIds[iCt] + iSwapIndex;
		if (document.getElementById(sId) && document.getElementById(sSwapId)) {
			sTemp = document.getElementById(sId).innerHTML;
			document.getElementById(sId).innerHTML = document.getElementById(sSwapId).innerHTML;
			document.getElementById(sSwapId).innerHTML = sTemp;
		}
	}
	iMaxElement = aOnClickHandlerIds.length;
	for (iCt=0; iCt<iMaxElement; iCt++) {
		sId = aOnClickHandlerIds[iCt] + iIndex;
		sSwapId = aOnClickHandlerIds[iCt] + iSwapIndex;
		if (document.getElementById(sId) && document.getElementById(sSwapId)) {
			sTemp = document.getElementById(sId).onclick;
			document.getElementById(sId).onclick = document.getElementById(sSwapId).onclick;
			document.getElementById(sSwapId).onclick = sTemp;

			sTemp = document.getElementById(sId).src;
			document.getElementById(sId).src = document.getElementById(sSwapId).src;
			document.getElementById(sSwapId).src = sTemp;

			sTemp = document.getElementById(sId).title;
			document.getElementById(sId).title = document.getElementById(sSwapId).title;
			document.getElementById(sSwapId).title = sTemp;
		}
	}
	settotalnoofrows();
	calcTotal();
	loadTaxes_Ajax(iIndex);
	loadTaxes_Ajax(iSwapIndex);
	callTaxCalc(iIndex);
	callTaxCalc(iSwapIndex);
	setDiscount(this, iIndex);
	setDiscount(this, iSwapIndex);
	calcTotal();
}

function InventorySelectAll(mod) {
	if (document.selectall.selected_id != undefined) {
		var x = document.selectall.selected_id.length;
		var y=0;
		var row_id = 0;
		var idstring = '';
		var namestr = '';
		if (x == undefined) {
			if (document.selectall.selected_id.checked) {
				idstring = document.selectall.selected_id.value;
				var c = document.selectall.selected_id.value;
				var prod_array = JSON.parse(document.getElementById('popup_product_'+c).attributes['vt_prod_arr'].nodeValue);
				var prod_id = prod_array['entityid'];
				var prod_name = prod_array['prodname'];
				var unit_price = prod_array['unitprice'];
				var taxstring = prod_array['taxstring'];
				var desc = prod_array['desc'];
				row_id = prod_array['rowid'];
				var dto = prod_array['dto'];
				var subprod_ids = prod_array['subprod_ids'];
				if (mod!='PurchaseOrder') {
					var qtyinstk = prod_array['qtyinstk'];
					set_return_inventory(prod_id, prod_name, unit_price, qtyinstk, taxstring, parseInt(row_id), desc, subprod_ids, dto);
				} else {
					set_return_inventory_po(prod_id, prod_name, unit_price, taxstring, parseInt(row_id), desc, subprod_ids);
				}
				y=1;
			} else {
				alert(alert_arr.SELECT);
				return false;
			}
		} else {
			y=0;
			for (var i = 0; i < x; i++) {
				if (document.selectall.selected_id[i].checked) {
					idstring = document.selectall.selected_id[i].value+';'+idstring;
					var c = document.selectall.selected_id[i].value;
					var prod_array = JSON.parse(document.getElementById('popup_product_'+c).attributes['vt_prod_arr'].nodeValue);
					var prod_id = prod_array['entityid'];
					var prod_name = prod_array['prodname'];
					var unit_price = prod_array['unitprice'];
					var taxstring = prod_array['taxstring'];
					var desc = prod_array['desc'];
					var dto = prod_array['dto'];
					var subprod_ids = prod_array['subprod_ids'];
					if (y>0) {
						row_id = window.opener.fnAddProductRow(mod);
					} else {
						row_id = prod_array['rowid'];
					}
					if (mod!='PurchaseOrder') {
						var qtyinstk = prod_array['qtyinstk'];
						set_return_inventory(prod_id, prod_name, unit_price, qtyinstk, taxstring, parseInt(row_id), desc, subprod_ids, dto);
					} else {
						set_return_inventory_po(prod_id, prod_name, unit_price, taxstring, parseInt(row_id), desc, subprod_ids);
					}
					y=y+1;
					window.opener.document.EditView.elements['qty'+row_id].onblur();
				}
			}
		}
		if (y != 0) {
			document.selectall.idlist.value=idstring;
			return true;
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
	}
}

/****
	* ProductAutocomplete
	* @author: MajorLabel <info@majorlabel.nl>
	* @license VPL
	*/
(function productautocompleteModule(factory) {

	if (typeof define === 'function' && define.amd) {
		define(factory);
	} else if (typeof module != 'undefined' && typeof module.exports != 'undefined') {
		module.exports = factory();
	} else {
		window['ProductAutocomplete'] = factory();
	}

})(function productautocompleteFactory() {

	/**
	 * @class ProductAutocomplete
	 * @param {element}
	 * @param {element}:	Root 'InventoryBlock' Object
	 * @param {function}: 	Callback for custom implementations. Will receive an object with
	 *						the root autocomplete node and all the result data
		* @param {object}		The root inventoryblock object
		*/
	function ProductAutocomplete(el, parent, callback, rootObj) {
		this.el = el;
		this.root = rootObj;
		this.parent = parent;
		this.specialKeys = ['up', 'down', 'esc', 'enter'];
		this.threshold = 3;
		this.input = el.getElementsByTagName('input')[0];
		this.source='index.php?module=Utilities&sourceModule='+gVTModule+'&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getProductServiceAutocomplete&limit=10&term=';
		this.active = false;
		this.resultContainer = null;
		this.resultBox = null;
		this.lookupContainer = this.utils.getFirstClass(el, 'slds-combobox-lookup');
		this.currentResults = [];
		this.callback = typeof callback === 'function' ? callback : false;

		/* Instance listeners */
		window.addEventListener('keyup', this.preventSubmit.bind(this), true);
		this.utils.on(this.input, 'keyup', this.debounce(this.trigger, 420), this);
		this.utils.on(this.input, 'keyup', this.handleImmediateInput, this);
		this.utils.on(this.input, 'blur', this.delayedClear, this);

		GlobalVariable_getVariable('Application_ProductService_Search_Autocomplete_Limit', 10, '', gVTUserID)
			.then((r) => {
				const limit = JSON.parse(r)['Application_ProductService_Search_Autocomplete_Limit'];
				this.source = this.source.replace('limit=10', `limit=${limit}`);
			}).catch((e) => {
				console.error(e);
			});
	}

	ProductAutocomplete.prototype = {
		constructor : ProductAutocomplete,

		trigger: function (e) {
			var isSpecialKey = this.isSpecialKey(e.keyCode);
			var term = this.input.value;
			if (!isSpecialKey && term.length > this.threshold) {
				this.getResults(term);
				this.setSpinner(true);
			} else if (term.length < this.threshold) {
				this.clear();
			}
		},

		isSpecialKey: function (code) {
			if (window.keycodeMap[code] !== undefined) {
				return this.specialKeys.indexOf(window.keycodeMap[code]) == -1 ? false : true;
			} else {
				return false;
			}
		},

		handleImmediateInput: function (e) {
			if (this.isSpecialKey(e.keyCode)) {
				this.handleKeyInput(e);
			}
		},

		debounce: function (func, duration) {
			let timeout;

			return function (...args) {
				const effect = () => {
					timeout = null;
					return func.apply(this, args);
				};
				clearTimeout(timeout);
				timeout = setTimeout(effect, duration);
			};
		},

		setSpinner: function (state) {
			let spinner = this.utils.getFirstClass(this.el, 'slds-spinner');
			if (state) {
				spinner.classList.remove('slds-hide');
			} else {
				spinner.classList.add('slds-hide');
			}
		},

		preventSubmit: function (e) {
			if (e.keyCode == 13 && this.active) {
				e.preventDefault();
				e.stopImmediatePropagation();
				e.stopPropagation();
				this.selectCurrentlyHighlighted();
				return false;
			}
		},

		getResults: function (term) {
			var h = getAccConFieldnames,
				dE = document.EditView,
				accid = h().acc === '' ? 0 : document.EditView[h().acc].value,
				ctoid = h().con === '' ? 0 : document.EditView[h().con].value,
				recid = dE === undefined ? 0 : dE.record.value,
				_this = this,
				r = new XMLHttpRequest();
			var currencyfield = document.getElementById('inventory_currency');
			var currencyid = '';
			if (currencyfield!=undefined) {
				currencyid = currencyfield.value;
			}

			r.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					var res = JSON.parse(this.responseText);
					_this.processResult(res);
				}
			};
			r.open('GET', this.source + this.input.value + '&accid='+accid+ '&ctoid='+ctoid+'&modid='+recid+'&currencyid='+currencyid, true);
			r.send();

			// Helper to keep organized
			function getAccConFieldnames() {
				let fldNames = {'acc': '', 'con': ''};
				if (document.EditView !== undefined) {
					if (document.EditView.account_id !== undefined) {
						fldNames.acc = 'account_id';
					} else if (document.EditView.accid !== undefined) {
						fldNames.acc = 'accid';
					}
					if (document.EditView.contact_id !== undefined) {
						fldNames.con = 'contact_id';
					} else if (document.EditView.ctoid !== undefined) {
						fldNames.acc = 'ctoid';
					}
				}
				return fldNames;
			}
		},

		processResult: function (res) {
			if (res.length > 0) {
				// Build and attach container
				if (!this.active) {
					this.resultBox = this.buildResultBox();
					this.attachResultBox(this.resultBox);
					this.resultContainer = this.buildResultContainer();
					this.resultBox.appendChild(this.resultContainer);
					this.active = true;
					window.preventFormSubmitOnEnter = true;
				}

				// Build results
				this.buildResults(res);
			}
			this.setSpinner(false);
		},

		buildResultBox: function () {
			var div = _createEl('div', '');
			div.setAttribute('role', 'listbox');
			// Only temp until full LDS is implemented
			div.style.position = 'relative';
			// END only temp
			return div;
		},

		buildResultContainer: function () {
			var ul 	= _createEl('ul', 'slds-listbox slds-listbox_vertical slds-dropdown slds-dropdown_fluid');
			ul.setAttribute('role', 'presentation');
			// Only temp until full LDS is implemented
			ul.style.visibility = 1;
			ul.style.opacity = 1;
			ul.style.transform = 'none';
			ul.style.left = 0;
			ul.style.maxWidth = '100%';
			ul.style.width = '100%';
			ul.style.visibility = 'visible';
			// END only temp
			return ul;
		},

		attachResultBox: function (containerDiv) {
			this.lookupContainer.appendChild(containerDiv);
			this.lookupContainer.classList.add('slds-is-open');
			this.lookupContainer.setAttribute('aria-expanded', 'true');
		},

		removeResultBox: function () {
			this.lookupContainer.classList.remove('slds-is-open');
			this.lookupContainer.removeAttribute('aria-expanded', 'true');
			this.lookupContainer.removeChild(this.resultBox);
		},

		buildResults: function (results) {
			// Empty all first
			this.resultContainer.innerHTML = '';
			this.currentResults = [];

			for (var i = 0; i < results.length; i++) {
				this.attachResultToContainer(this.buildResult(results[i]));
			}

			// Pre-select the first result
			this.utils.getFirstClass(this.currentResults[0].node, 'slds-listbox__option').classList.add('slds-has-focus');
			this.currentResults[0].selected = true;
		},

		buildResult: function (result) {
			var media = this.buildResultMedia(result.meta.name, [
				{'label' : result.translations.ven_no, 'value' : result.meta.mfr_no},
				{'label' : result.translations.mfr_no, 'value' : result.meta.ven_no}
			]);

			var li = _createEl('li', 'slds-listbox__item slds-border_bottom');
			li.setAttribute('role', 'presentation');
			li.appendChild(media);
			this.currentResults.push({
				'obj' 		: result,
				'node'		: li,
				'selected'	: false
			});

			this.utils.on(li, 'click', this.click, this);
			this.utils.on(li, 'mouseover', this.onResultHover, this);

			return li;
		},

		buildResultMedia: function (name, lines) {
			var mediaBody = _createEl('div', 'slds-media__body');
			var listboxText = _createEl('span', 'slds-listbox__option-text slds-listbox__option-text_entity slds-text-title_caps cbds-product-search-title', name);
			var listboxMetas = this.buildListboxMetas(lines);

			mediaBody.appendChild(listboxText);
			for (var i = 0; i < listboxMetas.length; i++) {
				if (lines[i].value != '##FIELDDISABLED##') {
					mediaBody.appendChild(listboxMetas[i]);
				}
			}

			var media = _createEl('div', 'slds-media slds-listbox__option slds-listbox__option_entity slds-listbox__option_has-meta');
			media.setAttribute('role', 'option');
			media.appendChild(mediaBody);
			return media;
		},

		buildListboxMetas: function (lines) {
			var returnLines = [];
			for (var i = 0; i < lines.length; i++) {
				returnLines.push(this.buildListboxMeta(lines[i]));
			}
			return returnLines;
		},

		buildListboxMeta: function (line) {
			var grid = _createEl('div', 'slds-grid slds-has-flexi-truncate slds-p-top_xx-small');
			var title = _createEl('div', 'slds-col slds-size_1-of-2 slds-p-left_none slds-text-title slds-truncate', line.label);
			var value = _createEl('div', 'slds-col slds-size_1-of-2 slds-p-left_none', line.value);
			grid.appendChild(title);
			grid.appendChild(value);
			var meta = _createEl('span', 'slds-listbox__option-meta slds-listbox__option-meta_entity');
			meta.appendChild(grid);
			return meta;
		},

		attachResultToContainer: function (resultLi) {
			this.resultContainer.appendChild(resultLi);
		},

		onResultHover : function (e) {
			var result = this.utils.findUp(e.target, '.slds-listbox__item');
			for (var i = 0; i < this.currentResults.length; i++) {
				this.setResultState(i, '');
			}
			this.setResultState(this.getResultIndexByNode(result), 'selected');
		},

		clear: function () {
			if (this.active) {
				this.removeResultBox();
				this.destroyResultListeners();
				this.currentResults = [];
				this.active = false;
				window.preventFormSubmitOnEnter = false;
			}
		},

		delayedClear : function () {
			var _this = this;
			window.setTimeout(
				function () {
					_this.clear();
				},
				150
			);
		},

		destroyResultListeners: function () {
			for (var i = 0; i < this.currentResults.length; i++) {
				this.utils.off(this.currentResults[i].node, 'click', this.click, this);
				this.utils.on(this.currentResults[i].node, 'mouseover', this.onResultHover, this);
			}
		},

		handleKeyInput : function (e) {
			if (this.active) {
				var key = _getKey(e.keyCode);
				switch (key) {
				case 'up':
					this.selectPrev();
					break;
				case 'down':
					this.selectNext();
					break;
				case 'enter':
					this.selectCurrentlyHighlighted();
					break;
				case 'esc':
					this.clear();
					break;
				}
			}
		},

		selectPrev: function () {
			var current = this.getCurrentSelectedResult();
			if (current != 0) {
				this.setResultState(current, '');
				this.setResultState((current - 1), 'selected');
			}
		},

		selectNext: function () {
			var current = this.getCurrentSelectedResult();
			if (current != this.currentResults.length -1) {
				this.setResultState(current, '');
				this.setResultState((current + 1), 'selected');
			}
		},

		setResultState: function (index, state) {
			if (state == 'selected') {
				this.utils.getFirstClass(this.currentResults[index].node, 'slds-listbox__option').classList.add('slds-has-focus');
				this.currentResults[index].selected = true;
			} else {
				this.utils.getFirstClass(this.currentResults[index].node, 'slds-listbox__option').classList.remove('slds-has-focus');
				this.currentResults[index].selected = false;
			}
		},

		getCurrentSelectedResult: function () {
			for (var i = 0; i < this.currentResults.length; i++) {
				if (this.currentResults[i].selected) {
					return i;
				}
			}
		},

		click: function (e) {
			var el = this.utils.findUp(e.target, '.slds-listbox__item'); // Click event could fire on child
			if (el) {
				var result = this.getMatchingResultByNode(el);
				this.select(result);
			}
		},

		getMatchingResultByNode: function (node) {
			for (var i = 0; i < this.currentResults.length; i++) {
				if (node.isSameNode(this.currentResults[i].node)) {
					return this.currentResults[i];
				}
			}
		},

		getResultIndexByNode: function (node) {
			for (var i = 0; i < this.currentResults.length; i++) {
				if (node.isSameNode(this.currentResults[i].node)) {
					return i;
				}
			}
		},

		selectCurrentlyHighlighted() {
			var current = this.getCurrentSelectedResult();
			this.select(this.currentResults[current]);
		},

		select: function (result) {
			this.fillLine(result);
			this.clear(); // Clear autocomplete
		},

		fillLine: function (result) {
			if (!this.callback) {
				var lineNode = this.utils.findUp(result.node, '.' + this.root.lineClass),
					usageunits = this.root.el.getElementsByClassName(this.root.lineClass + '--usageunit');

				this.utils.getFirstClass(lineNode, 'cbds-product-line-image').src = result.obj.meta.image;
				this.parent.setField('listprice', result.obj.pricing.unit_price);
				this.parent.setField('cost_price', result.obj.pricing.unit_cost);
				this.parent.setField('qtyinstock', result.obj.logistics.qtyinstock);
				this.parent.setField('qtyindemand', result.obj.logistics.qtyindemand);

				this.utils.getFirstClass(lineNode, this.root.inputPrefix + '--description').innerHTML = result.obj.meta.comments;
				this.input.value = result.obj.meta.name;

				for (var i = usageunits.length - 1; i >= 0; i--) {
					usageunits[i].innerHTML = result.obj.logistics.usageunit;
				}

				this.parent.productId = result.obj.meta.id;
				this.parent.divisible = result.obj.meta.divisible == 0 ? false : true;

				this.parent.expandExtra();
				this.parent.getProductImage(result.obj.meta.id);
				this.parent.calcLine();

				this.utils.getFirstClass(this.utils.findUp(this.el, '.' + this.root.lineClass), this.root.inputPrefix + '--quantity').focus();
				this.retrieveProductTaxes(result.obj.meta.id);
			} else {
				this.callback({
					'result': result.obj,
					'source': this.el
				});
			}
		},

		retrieveProductTaxes: function (id) {
			fetch(`index.php?module=Products&action=ProductsAjax&file=InventoryTaxAjax&productid=${id}&ctoid=0&accid=0&vndid=0&returnarray=1`)
				.then(r => r.json())
				.then((data) => {
					this.parent.actualizeLineTaxes(data);
				});
		},

		/*
		 * Class utilities
		 */
		utils : {
			/*
			 * Util: 'findUp'
			 * Returns the first element up the DOM that matches the search
			 *
			 * @param: element: 	the node to start from
			 * @param: searchterm: 	Can be a class (prefix with '.'), ID (prefix with '#')
			 *						or an attribute (default when no prefix)
			 */
			findUp : function (element, searchterm) {
				return findUp(element, searchterm);
			},
			/*
			 * Util: 'getFirstClass'
			 * Returns the first element from the root that matches
			 * the classname
			 *
			 * @param: root: 		the node to start from
			 * @param: className: 	The classname to search for
			 */
			getFirstClass: function (root, className) {
				return root.getElementsByClassName(className)[0] != undefined ? root.getElementsByClassName(className)[0] : {};
			},
			/*
			 * Util: 'on'
			 * Adds an event listener
			 *
			 * @param: el: 			The node to attach the listener to
			 * @param: type: 		The type of event
			 * @param: func: 		The function to perform
			 * @param: context: 	The context to bind the listener to
			 */
			on: function (el, type, func, context) {
				try {
					el.addEventListener(type, func.bind(context));
				} catch (e) {
					throw new Error(e + '. Called by ' + this.on.caller);
				}
			},
			/*
			 * Util: 'off'
			 * Removes an event listener
			 *
			 * @param: el: 			The node to remove the listener from
			 * @param: type: 		The type of event
			 * @param: func: 		The function to remove
			 */
			off: function (el, type, func) {
				el.removeEventListener(type, func);
			},
			/*
			 * Util: 'insertAfter'
			 * Inserts a new node after the given
			 *
			 * @param: referenceNode: 	The node to insert after
			 * @param: newNode: 		The node to insert
			 */
			insertAfter: function (referenceNode, newNode) {
				referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
			},
			/*
			 * Util: 'deductPerc'
			 * deducts a percentage from a number
			 *
			 * @param: base: 		The base '100%' number
			 * @param: percentage: 	The percentage to deduct
			 */
			deductPerc: function (base, percentage) {
				return (base * (1 - (percentage / 100)));
			},
			/*
			 * Util: 'getPerc'
			 * Returns a percentage of a base no.
			 *
			 * @param: base: 		The base '100%' number
			 * @param: percentage: 	The percentage to return
			 */
			getPerc: function (base, percentage) {
				return base * (percentage / 100);
			}
		}
	};

	/**
	  * Section with factory tools
	  */
	function _createEl(elType, className, inner) {
		var el = document.createElement(elType);
		if (className.indexOf(' ') == -1 && className != undefined && className != '') {
			el.classList.add(className);
		} else {
			var classes = className.split(' ');
			for (var i = 0; i < classes.length; i++) {
				if (classes[i] != '') {
					el.classList.add(classes[i]);
				}
			}
		}
		if (inner != undefined) {
			el.innerHTML = inner;
		}
		return el;
	}

	function _getKey(code) {
		return window.keycodeMap[code];
	}

	/*
	 * Globals
	 */
	window.keycodeMap = {
		38: 'up',
		40: 'down',
		37: 'left',
		39: 'right',
		27: 'esc',
		9:  'tab',
		13: 'enter'
	};

	/*
	 * Export
	 */
	return ProductAutocomplete;
});

function handleProductAutocompleteSelect(obj) {
	var no = obj.source.getElementsByClassName('slds-input')[0].id.replace('productName', ''),
		type = obj.result.meta.type,
		searchIcon = document.getElementById('searchIcon' + no),
		qty = obj.result.meta.type == 'Products' ? defaultProdQty : defaultSerQty;

	document.getElementById('productName'+no).value = obj.result.meta.name;
	document.getElementById('comment'+no).innerHTML = obj.result.meta.comments;
	var currency = document.getElementById('inventory_currency').value;
	if (obj.result.pricing.multicurrency[currency] != undefined && gVTModule != 'PurchaseOrder' && gVTModule != 'Receiptcards') {
		if (Object.keys(obj.result.pricing.multicurrency).length == 1 && obj.result.pricing.multicurrency[currency].actual_price != obj.result.pricing.unit_price) {
			ldsPrompt.show(alert_arr['Warning'], alert_arr.ACT_UNIT_PRICE_MISMATCH);
		}
		document.getElementById('listPrice'+no).value = obj.result.pricing.multicurrency[currency].actual_price;
	} else {
		var list_price = obj.result.pricing.unit_price;
		if (gVTModule == 'PurchaseOrder' || gVTModule == 'Receiptcards' ) {
			list_price = obj.result.pricing.unit_cost;
		}
		document.getElementById('listPrice'+no).value = list_price;
	}
	document.getElementById('hdnProductId'+no).value = obj.result.meta.id;
	document.getElementById('lineItemType'+no).value = obj.result.meta.type;
	document.getElementById('qty'+no).value = qty;
	if (gVTModule!='PurchaseOrder' && gVTModule != 'Receiptcards') {
		document.getElementById('qtyInStock'+no).innerHTML = obj.result.logistics.qtyinstock;
	}
	if (obj.result.pricing.discount != undefined && obj.result.pricing.discount != 0) {
		document.EditView.elements['discount'+no][1].checked = true;
		document.EditView.elements['discount_percentage'+no].value = obj.result.pricing.discount;
	} else {
		// zero discount
		document.EditView.elements['discount'+no][0].checked = true;
		document.EditView.elements['discount_percentage'+no].value = 0;
		document.EditView.elements['discount_amount'+no].value = 0;
	}

	// Update the icon
	switch (type) {
	case 'Products':
		searchIcon.src = 'themes/images/products.gif';
		searchIcon.setAttribute('onclick', 'productPickList(this,\''+gVTModule+'\',\''+no+'\')');
		break;
	case 'Services':
		searchIcon.src = 'themes/images/services.gif';
		searchIcon.setAttribute('onclick', 'servicePickList(this,\''+gVTModule+'\',\''+no+'\')');
		break;
	}
	var func = gVTModule + 'setValueFromCapture';
	if (typeof window[func] == 'function') {
		window[func](obj.result.meta.id, obj.result.meta.name, 'productName'+no);
	}
	document.getElementById('qty'+no).focus();
}

// Launch for the existing rows and prevent form submission when an autocomplete is active and open
window.addEventListener('load', function () {
	var rows = document.getElementsByClassName('cbds-product-search');
	for (var i = rows.length - 1; i >= 0; i--) {
		new ProductAutocomplete(rows[i], {}, handleProductAutocompleteSelect);
	}
	if (document.getElementById('frmEditView') !== null) {
		document.getElementById('frmEditView').onkeypress = function (e) {
			if (e.keyCode == 13 && window.preventFormSubmitOnEnter) {
				e.preventDefault();
				return false;
			}
		};
	}
});
