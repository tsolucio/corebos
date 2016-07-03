/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
document.write("<script type='text/javascript' src='include/js/Inventory.js'></" + "script>");
document.write("<script type='text/javascript' src='include/js/Mail.js'></" + "script>");
document.write("<script type='text/javascript' src='include/js/Merge.js'></" + "script>");
document.write('<div id="setaddressaccountdiv" style="z-index:12;display:none;width:400px;top:30px;left:0;right:0;margin:auto;" class="layerPopup"></div>');

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
	//getOpenerObj used for DetailView
	if (document.getElementById('from_link').value != '') {
		var fldName = window.opener.document.QcEditView.account_name;
		var fldId = window.opener.document.QcEditView.account_id;
	} else {
		var fldName = window.opener.document.EditView.account_name;
		var fldId = window.opener.document.EditView.account_id;
	}
	fldName.value = product_name;
	fldId.value = product_id;
}

function add_data_to_relatedlist(entity_id, recordid) {
	opener.document.location.href = "index.php?module=Emails&action=updateRelations&destination_module=Accounts&entityid=" + entity_id + "&parentid=" + recordid;
}

function set_return_formname_specific(formname, product_id, product_name) {
	window.opener.document.EditView1.account_name.value = product_name;
	window.opener.document.EditView1.account_id.value = product_id;
}

function set_return_address(account_id, account_name, bill_street, ship_street, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country, bill_pobox, ship_pobox) {
	if (document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.account_name.value = account_name;
		window.opener.document.QcEditView.account_id.value = account_id;
	} else {
		window.opener.document.EditView.account_name.value = account_name;
		window.opener.document.EditView.account_id.value = account_id;
	}

	//Ask the user to overwite the address or not - Modified on 06-01-2007
	if (confirm(alert_arr.OVERWRITE_EXISTING_ACCOUNT1 + account_name + alert_arr.OVERWRITE_EXISTING_ACCOUNT2)) {
		//made changes to avoid js error -- ref : hidding fields causes js error(ticket#4017)
		if (document.getElementById('from_link').value != '') {
			if (typeof (window.opener.document.QcEditView.bill_street) != 'undefined')
				window.opener.document.QcEditView.bill_street.value = bill_street;
			if (typeof (window.opener.document.QcEditView.ship_street) != 'undefined')
				window.opener.document.QcEditView.ship_street.value = ship_street;
			if (typeof (window.opener.document.QcEditView.bill_city) != 'undefined')
				window.opener.document.QcEditView.bill_city.value = bill_city;
			if (typeof (window.opener.document.QcEditView.ship_city) != 'undefined')
				window.opener.document.QcEditView.ship_city.value = ship_city;
			if (typeof (window.opener.document.QcEditView.bill_state) != 'undefined')
				window.opener.document.QcEditView.bill_state.value = bill_state;
			if (typeof (window.opener.document.QcEditView.ship_state) != 'undefined')
				window.opener.document.QcEditView.ship_state.value = ship_state;
			if (typeof (window.opener.document.QcEditView.bill_code) != 'undefined')
				window.opener.document.QcEditView.bill_code.value = bill_code;
			if (typeof (window.opener.document.QcEditView.ship_code) != 'undefined')
				window.opener.document.QcEditView.ship_code.value = ship_code;
			if (typeof (window.opener.document.QcEditView.bill_country) != 'undefined')
				window.opener.document.QcEditView.bill_country.value = bill_country;
			if (typeof (window.opener.document.QcEditView.ship_country) != 'undefined')
				window.opener.document.QcEditView.ship_country.value = ship_country;
			if (typeof (window.opener.document.QcEditView.bill_pobox) != 'undefined')
				window.opener.document.QcEditView.bill_pobox.value = bill_pobox;
			if (typeof (window.opener.document.QcEditView.ship_pobox) != 'undefined')
				window.opener.document.QcEditView.ship_pobox.value = ship_pobox;
		} else {
			if (typeof (window.opener.document.EditView.bill_street) != 'undefined')
				window.opener.document.EditView.bill_street.value = bill_street;
			if (typeof (window.opener.document.EditView.ship_street) != 'undefined')
				window.opener.document.EditView.ship_street.value = ship_street;
			if (typeof (window.opener.document.EditView.bill_city) != 'undefined')
				window.opener.document.EditView.bill_city.value = bill_city;
			if (typeof (window.opener.document.EditView.ship_city) != 'undefined')
				window.opener.document.EditView.ship_city.value = ship_city;
			if (typeof (window.opener.document.EditView.bill_state) != 'undefined')
				window.opener.document.EditView.bill_state.value = bill_state;
			if (typeof (window.opener.document.EditView.ship_state) != 'undefined')
				window.opener.document.EditView.ship_state.value = ship_state;
			if (typeof (window.opener.document.EditView.bill_code) != 'undefined')
				window.opener.document.EditView.bill_code.value = bill_code;
			if (typeof (window.opener.document.EditView.ship_code) != 'undefined')
				window.opener.document.EditView.ship_code.value = ship_code;
			if (typeof (window.opener.document.EditView.bill_country) != 'undefined')
				window.opener.document.EditView.bill_country.value = bill_country;
			if (typeof (window.opener.document.EditView.ship_country) != 'undefined')
				window.opener.document.EditView.ship_country.value = ship_country;
			if (typeof (window.opener.document.EditView.bill_pobox) != 'undefined')
				window.opener.document.EditView.bill_pobox.value = bill_pobox;
			if (typeof (window.opener.document.EditView.ship_pobox) != 'undefined')
				window.opener.document.EditView.ship_pobox.value = ship_pobox;
		}
	}
}

//added to select bill or ship address
function set_return_shipbilladdress(account_id, account_name, bill_street, ship_street, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country, bill_pobox, ship_pobox) {
	jQuery.ajax({
		url : 'index.php?module=Accounts&action=AccountsAjax&file=SelectAccountAddress',
		context : document.body,
		success : function(response) {
			jQuery('#setaddressaccountdiv').html(response);
			jQuery('#setaddressaccountdiv').show();
			fnvshNrm('setaddressaccountdiv');
			jQuery("#account_id").val(account_id);
			jQuery("#account_name").val(account_name);
			jQuery("#bill_street").val(bill_street);
			jQuery("#bill_city").val(bill_city);
			jQuery("#bill_state").val(bill_state);
			jQuery("#bill_code").val(bill_code);
			jQuery("#bill_country").val(bill_country);
			jQuery("#bill_pobox").val(bill_pobox);
			jQuery("#ship_street").val(ship_street);
			jQuery("#ship_city").val(ship_city);
			jQuery("#ship_state").val(ship_state);
			jQuery("#ship_code").val(ship_code);
			jQuery("#ship_country").val(ship_country);
			jQuery("#ship_pobox").val(ship_pobox);
		}
	});
}

function saa_fillinvalues() {
	var account_id = jQuery("#account_id").val();
	var account_name = jQuery("#account_name").val();
	if (typeof (window.opener.document.EditView.account_name) != 'undefined')
		window.opener.document.EditView.account_name.value = account_name;
	if (typeof (window.opener.document.EditView.account_id) != 'undefined')
		window.opener.document.EditView.account_id.value = account_id;
	if (jQuery('#saa_bill').is(':checked'))
		setReturnAddressBill();
	if (jQuery('#saa_ship').is(':checked'))
		setReturnAddressShip();
	window.close();
}

function setReturnAddressBill() {
	var street = jQuery("#bill_street").val();
	var city = jQuery("#bill_city").val();
	var state = jQuery("#bill_state").val();
	var code = jQuery("#bill_code").val();
	var country = jQuery("#bill_country").val();
	var pobox = jQuery("#bill_pobox").val();
	if (typeof (window.opener.document.EditView.bill_street) != 'undefined')
		window.opener.document.EditView.bill_street.value = street;
	if (typeof (window.opener.document.EditView.bill_city) != 'undefined')
		window.opener.document.EditView.bill_city.value = city;
	if (typeof (window.opener.document.EditView.bill_state) != 'undefined')
		window.opener.document.EditView.bill_state.value = state;
	if (typeof (window.opener.document.EditView.bill_code) != 'undefined')
		window.opener.document.EditView.bill_code.value = code;
	if (typeof (window.opener.document.EditView.bill_country) != 'undefined')
		window.opener.document.EditView.bill_country.value = country;
	if (typeof (window.opener.document.EditView.bill_pobox) != 'undefined')
		window.opener.document.EditView.bill_pobox.value = pobox;
}

function setReturnAddressShip() {
	var street = jQuery("#ship_street").val();
	var city = jQuery("#ship_city").val();
	var state = jQuery("#ship_state").val();
	var code = jQuery("#ship_code").val();
	var country = jQuery("#ship_country").val();
	var pobox = jQuery("#ship_pobox").val();
	if (typeof (window.opener.document.EditView.ship_street) != 'undefined')
		window.opener.document.EditView.ship_street.value = street;
	if (typeof (window.opener.document.EditView.ship_city) != 'undefined')
		window.opener.document.EditView.ship_city.value = city;
	if (typeof (window.opener.document.EditView.ship_state) != 'undefined')
		window.opener.document.EditView.ship_state.value = state;
	if (typeof (window.opener.document.EditView.ship_code) != 'undefined')
		window.opener.document.EditView.ship_code.value = code;
	if (typeof (window.opener.document.EditView.ship_country) != 'undefined')
		window.opener.document.EditView.ship_country.value = country;
	if (typeof (window.opener.document.EditView.ship_pobox) != 'undefined')
		window.opener.document.EditView.ship_pobox.value = pobox;
}

//added to populate address
function set_return_contact_address(account_id, account_name, bill_street, ship_street, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country, bill_pobox, ship_pobox) {
	if (document.getElementById('from_link').value != '') {
		if (typeof (window.opener.document.QcEditView.account_name) != 'undefined')
			window.opener.document.QcEditView.account_name.value = account_name;
		if (typeof (window.opener.document.QcEditView.account_id) != 'undefined')
			window.opener.document.QcEditView.account_id.value = account_id;
	} else {
		if (typeof (window.opener.document.EditView.account_name) != 'undefined')
			window.opener.document.EditView.account_name.value = account_name;
		if (typeof (window.opener.document.EditView.account_id) != 'undefined')
			window.opener.document.EditView.account_id.value = account_id;
		if (confirm(alert_arr.OVERWRITE_EXISTING_ACCOUNT1 + account_name + alert_arr.OVERWRITE_EXISTING_ACCOUNT2)) {
			if (typeof (window.opener.document.EditView.mailingstreet) != 'undefined')
				window.opener.document.EditView.mailingstreet.value = bill_street;
			if (typeof (window.opener.document.EditView.otherstreet) != 'undefined')
				window.opener.document.EditView.otherstreet.value = ship_street;
			if (typeof (window.opener.document.EditView.mailingcity) != 'undefined')
				window.opener.document.EditView.mailingcity.value = bill_city;
			if (typeof (window.opener.document.EditView.othercity) != 'undefined')
				window.opener.document.EditView.othercity.value = ship_city;
			if (typeof (window.opener.document.EditView.mailingstate) != 'undefined')
				window.opener.document.EditView.mailingstate.value = bill_state;
			if (typeof (window.opener.document.EditView.otherstate) != 'undefined')
				window.opener.document.EditView.otherstate.value = ship_state;
			if (typeof (window.opener.document.EditView.mailingzip) != 'undefined')
				window.opener.document.EditView.mailingzip.value = bill_code;
			if (typeof (window.opener.document.EditView.otherzip) != 'undefined')
				window.opener.document.EditView.otherzip.value = ship_code;
			if (typeof (window.opener.document.EditView.mailingcountry) != 'undefined')
				window.opener.document.EditView.mailingcountry.value = bill_country;
			if (typeof (window.opener.document.EditView.othercountry) != 'undefined')
				window.opener.document.EditView.othercountry.value = ship_country;
			if (typeof (window.opener.document.EditView.mailingpobox) != 'undefined')
				window.opener.document.EditView.mailingpobox.value = bill_pobox;
			if (typeof (window.opener.document.EditView.otherpobox) != 'undefined')
				window.opener.document.EditView.otherpobox.value = ship_pobox;
		}
	}
}

//added by rdhital/Raju for emails
function submitform(id) {
	document.massdelete.entityid.value = id;
	document.massdelete.submit();
}

function searchMapLocation(addressType) {
	var mapParameter = '';
	if (addressType == 'Main') {
		if (fieldname.indexOf('bill_street') > -1) {
			if (document.getElementById("dtlview_bill_street"))
				mapParameter = document.getElementById("dtlview_bill_street").innerHTML + ' ';
		}
		if (fieldname.indexOf('bill_city') > -1) {
			if (document.getElementById("dtlview_bill_city"))
				mapParameter = mapParameter + document.getElementById("dtlview_bill_city").innerHTML + ' ';
		}
		if (fieldname.indexOf('bill_state') > -1) {
			if (document.getElementById("dtlview_bill_state"))
				mapParameter = mapParameter + document.getElementById("dtlview_bill_state").innerHTML + ' ';
		}
		if (fieldname.indexOf('bill_country') > -1) {
			if (document.getElementById("dtlview_bill_country"))
				mapParameter = mapParameter + document.getElementById("dtlview_bill_country").innerHTML + ' ';
		}
		if (fieldname.indexOf('bill_code') > -1) {
			if (document.getElementById("dtlview_bill_code"))
				mapParameter = mapParameter + document.getElementById("dtlview_bill_code").innerHTML + ' ';
		}
	} else if (addressType == 'Other') {
		if (fieldname.indexOf('ship_street') > -1) {
			if (document.getElementById("dtlview_ship_street"))
				mapParameter = document.getElementById("dtlview_ship_street").innerHTML + ' ';
		}
		if (fieldname.indexOf('ship_city') > -1) {
			if (document.getElementById("dtlview_ship_city"))
				mapParameter = mapParameter + document.getElementById("dtlview_ship_city").innerHTML + ' ';
		}
		if (fieldname.indexOf('ship_state') > -1) {
			if (document.getElementById("dtlview_ship_state"))
				mapParameter = mapParameter + document.getElementById("dtlview_ship_state").innerHTML + ' ';
		}
		if (fieldname.indexOf('ship_country') > -1) {
			if (document.getElementById("dtlview_ship_country"))
				mapParameter = mapParameter + document.getElementById("dtlview_ship_country").innerHTML + ' ';
		}
		if (fieldname.indexOf('ship_code') > -1) {
			if (document.getElementById("dtlview_ship_code"))
				mapParameter = mapParameter + document.getElementById("dtlview_ship_code").innerHTML + ' ';
		}
	}
	mapParameter = removeHTMLFormatting(mapParameter);
	window.open('http://maps.google.com/maps?q=' + mapParameter, 'goolemap', 'height=450,width=700,resizable=no,titlebar,location,top=200,left=250');
}

//javascript function will open new window to display traffic details for particular url using alexa.com
function getRelatedLink() {
	var param = '';
	param = getObj("website").value;
	window.open('http://www.alexa.com/data/details/traffic_details?q=&url=' + param, 'relatedlink', 'height=400,width=700,resizable=no,titlebar,location,top=250,left=250');
}

/*
 * javascript function to populate fieldvalue in account editview
 * @param id1 :: div tag ID
 * @param id2 :: div tag ID
 */
function populateData(id1, id2) {
	document.EditView.description.value = document.getElementById('summary').innerHTML;
	document.EditView.employees.value = getObj('emp').value;
	document.EditView.website.value = getObj('site').value;
	document.EditView.phone.value = getObj('Phone').value;
	document.EditView.fax.value = getObj('Fax').value;
	document.EditView.bill_street.value = getObj('address').value;
	showhide(id1, id2);
}

/*
 * javascript function to show/hide the div tag
 * @param argg1 :: div tag ID
 * @param argg2 :: div tag ID
 */
function showhide(argg1, argg2) {
	var x = document.getElementById(argg1).style;
	var y = document.getElementById(argg2).style;
	if (y.display == "none") {
		y.display = "block";
		x.display = "none";
	}
}

ScrollEffect = function() {
};
ScrollEffect.lengthcount = 202;
ScrollEffect.closelimit = 0;
ScrollEffect.limit = 0;

function just() {
	ig = getObj("company");
	if (ScrollEffect.lengthcount > ScrollEffect.closelimit) {
		closet();
		return;
	}
	ig.style.display = "block";
	ig.style.height = ScrollEffect.lengthcount + 'px';
	ScrollEffect.lengthcount = ScrollEffect.lengthcount + 10;
	if (ScrollEffect.lengthcount < ScrollEffect.limit) {
		setTimeout("just()", 25);
	} else {
		getObj("innerLayer").style.display = "block";
		return;
	}
}

function closet() {
	ig = getObj("company");
	getObj("innerLayer").style.display = "none";
	ScrollEffect.lengthcount = ScrollEffect.lengthcount - 10;
	ig.style.height = ScrollEffect.lengthcount + 'px';
	if (ScrollEffect.lengthcount < 20) {
		ig.style.display = "none";
		return;
	} else {
		setTimeout("closet()", 25);
	}
}

function tickerFillIn(obj) {
	var tagName = document.getElementById(obj);
	document.EditView.description.value = document.getElementById('summary').innerHTML;
	document.EditView.employees.value = getObj('emp').value;
	document.EditView.website.value = getObj('site').value;
	document.EditView.phone.value = getObj('Phone').value;
	document.EditView.fax.value = getObj('Fax').value;
	document.EditView.bill_street.value = getObj('address').value;
	if (tagName.style.display == 'none')
		tagName.style.display = 'block';
	else
		tagName.style.display = 'none';
}

function set_return_todo(product_id, product_name) {
	window.opener.document.createTodo.task_parent_name.value = product_name;
	window.opener.document.createTodo.task_parent_id.value = product_id;
}
