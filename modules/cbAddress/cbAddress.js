/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 ************************************************************************************/

//function for the popup capture hook
function cbAddressCapture(recordid, value, target_fieldname) {
	vtlib_setvalue_from_popup(recordid, value, target_fieldname);
	var module = document.getElementById('srcmodule').value;
	switch (module) {
	case 'Accounts':
	case 'SalesOrder':
	case 'Quotes':
	case 'PurchaseOrder':
	case 'Invoice':
		InventorysetValueFromCapture(recordid, target_fieldname);
		break;
	case 'Contacts':
		ContactSetValueFromCapture(recordid, target_fieldname);
		break;
	}
}

function cbAddressOpenCapture(fromlink, fldname, MODULE, ID) {
	var WindowSettings = 'width=680,height=602,resizable=0,scrollbars=0,top=150,left=200';
	var baseURL = 'index.php?module=cbAddress&action=Popup&html=Popup_picker&form=vtlibPopupView&forfield='+fldname+'&srcmodule='+MODULE;
	if (MODULE != 'PurchaseOrder') {
		var accountid = document.getElementsByName('account_id')[0].value;
	}
	if (MODULE != 'Accounts') {
		var contactid = document.getElementsByName('contact_id')[0].value;
	}
	switch (MODULE) {
	case 'Accounts':
		window.open(baseURL+'&forrecord='+ID+'&acc_id='+ID+'&cbcustompopupinfo=acc_id', 'vtlibui10', WindowSettings);
		break;
	case 'Contacts':
		window.open(baseURL+'&forrecord='+ID+'&acc_id='+accountid+'&cont_id='+contactid+'&cbcustompopupinfo=acc_id;cont_id', 'vtlibui10', WindowSettings);
		break;
	case 'SalesOrder':
	case 'Invoice':
		window.open(baseURL+'&cont_id='+contactid+'&acc_id='+accountid+'&relmod_id='+accountid+'&cbcustompopupinfo=acc_id;cont_id;relmod_id', 'vtlibui10', WindowSettings);
		break;
	case 'Quotes':
		window.open(baseURL+'&forrecord='+ID+'&acc_id='+accountid+'&cont_id='+contactid+'&relmod_id='+accountid+'&cbcustompopupinfo=acc_id;cont_id;relmod_id', 'vtlibui10', WindowSettings);
		break;
	case 'PurchaseOrder':
		window.open(baseURL+'&forrecord='+ID+'&cont_id='+contactid+'&relmod_id='+contactid+'&cbcustompopupinfo=cont_id;relmod_id', 'vtlibui10', WindowSettings);
		break;
	}
}

function ContactSetValueFromCapture(recordid, target_fieldname) {
	var url = 'module=cbAddress&action=cbAddressAjax&ajax=true&file=getAddressInfo&record='+recordid;
	jQuery.ajax({
		method: 'GET',
		url: 'index.php?'+url
	}).done(function (response) {
		var res = JSON.parse(response);
		if (target_fieldname == 'linktoaddressbilling') {
			window.opener.document.EditView.mailingstreet.value = res.street;
			window.opener.document.EditView.mailingpobox.value = res.pobox;
			window.opener.document.EditView.mailingcity.value = res.city;
			window.opener.document.EditView.mailingstate.value = res.state;
			window.opener.document.EditView.mailingzip.value = res.postalcode;
			window.opener.document.EditView.mailingcountry.value = res.country;
		}
		if (target_fieldname == 'linktoaddressshipping') {
			window.opener.document.EditView.otherstreet.value = res.street;
			window.opener.document.EditView.otherpobox.value = res.pobox;
			window.opener.document.EditView.othercity.value = res.city;
			window.opener.document.EditView.otherstate.value = res.state;
			window.opener.document.EditView.otherzip.value = res.postalcode;
			window.opener.document.EditView.othercountry.value = res.country;
		}
		window.close();
	});
}

function InventorysetValueFromCapture(recordid, target_fieldname) {
	var url = 'module=cbAddress&action=cbAddressAjax&ajax=true&file=getAddressInfo&record='+recordid;
	jQuery.ajax({
		method: 'GET',
		url: 'index.php?'+url
	}).done(function (response) {
		var res = JSON.parse(response);
		if (target_fieldname == 'linktoaddressbilling') {
			window.opener.document.EditView.bill_street.value = res.street;
			window.opener.document.EditView.bill_pobox.value = res.pobox;
			window.opener.document.EditView.bill_city.value = res.city;
			window.opener.document.EditView.bill_state.value = res.state;
			window.opener.document.EditView.bill_code.value = res.postalcode;
			window.opener.document.EditView.bill_country.value = res.country;
		}
		if (target_fieldname == 'linktoaddressshipping') {
			window.opener.document.EditView.ship_street.value = res.street;
			window.opener.document.EditView.ship_pobox.value = res.pobox;
			window.opener.document.EditView.ship_city.value = res.city;
			window.opener.document.EditView.ship_state.value = res.state;
			window.opener.document.EditView.ship_code.value = res.postalcode;
			window.opener.document.EditView.ship_country.value = res.country;
		}
		window.close();
	});
}
