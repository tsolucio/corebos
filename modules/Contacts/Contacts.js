/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
document.write("<script type='text/javascript' src='include/js/Mail.js'></"+"script>");
document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");
document.write('<div id="setaddresscontactdiv" style="z-index:12;display:none;width:400px;top:30px;left:0;right:0;margin:auto;" class="layerPopup"></div>');

function copyAddressRight(form) {
	if(typeof(form.otherstreet) != 'undefined' && typeof(form.mailingstreet) != 'undefined')
		form.otherstreet.value = form.mailingstreet.value;
	if(typeof(form.othercity) != 'undefined' && typeof(form.mailingcity) != 'undefined')
		form.othercity.value = form.mailingcity.value;
	if(typeof(form.otherstate) != 'undefined' && typeof(form.mailingstate) != 'undefined')
		form.otherstate.value = form.mailingstate.value;
	if(typeof(form.otherzip) != 'undefined' && typeof(form.mailingzip) != 'undefined')
		form.otherzip.value = form.mailingzip.value;
	if(typeof(form.othercountry) != 'undefined' && typeof(form.mailingcountry) != 'undefined')
		form.othercountry.value = form.mailingcountry.value;
	if(typeof(form.otherpobox) != 'undefined' && typeof(form.mailingpobox) != 'undefined')
		form.otherpobox.value = form.mailingpobox.value;
	return true;
}

function copyAddressLeft(form) {
	if(typeof(form.otherstreet) != 'undefined' && typeof(form.mailingstreet) != 'undefined')
		form.mailingstreet.value = form.otherstreet.value;
	if(typeof(form.othercity) != 'undefined' && typeof(form.mailingcity) != 'undefined')
		form.mailingcity.value = form.othercity.value;
	if(typeof(form.otherstate) != 'undefined' && typeof(form.mailingstate) != 'undefined')
		form.mailingstate.value = form.otherstate.value;
	if(typeof(form.otherzip) != 'undefined' && typeof(form.mailingzip) != 'undefined')
		form.mailingzip.value =	form.otherzip.value;
	if(typeof(form.othercountry) != 'undefined' && typeof(form.mailingcountry) != 'undefined')
		form.mailingcountry.value = form.othercountry.value;
	if(typeof(form.otherpobox) != 'undefined' && typeof(form.mailingpobox) != 'undefined')
		form.mailingpobox.value = form.otherpobox.value;
	return true;
}

function toggleDisplay(id){
	if(this.document.getElementById( id).style.display=='none'){
		this.document.getElementById( id).style.display='inline';
		this.document.getElementById(id+"link").style.display='none';
	}else{
		this.document.getElementById(id).style.display='none';
		this.document.getElementById(id+"link").style.display='none';
	}
}

function set_return(product_id, product_name) {
	if(document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.parent_name.value = product_name;
		window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
		window.opener.document.EditView.parent_name.value = product_name;
		window.opener.document.EditView.parent_id.value = product_id;
	}
}

function add_data_to_relatedlist_incal(id,name)
{
	var idval = window.opener.document.EditView.contactidlist.value;
	var nameval = window.opener.document.EditView.contactlist.value;
	if(idval != '') {
		if(idval.indexOf(id) != -1) {
			window.opener.document.EditView.contactidlist.value = idval;
			window.opener.document.EditView.contactlist.value = nameval;
		} else {
			window.opener.document.EditView.contactidlist.value = idval+';'+id;
			if(name != '') {
				// this has been modified to provide delete option for Contacts in Calendar
				//this function is defined in script.js ------- Jeri
				window.opener.addOption(id,name);
			}
		}
	} else {
		window.opener.document.EditView.contactidlist.value = id;
		if(name != '')
		{
			window.opener.addOption(id,name);
		}
	}
}
function set_return_specific(product_id, product_name) {
	//Used for DetailView, Removed 'EditView' formname hardcoding
	var fldName = getOpenerObj("contact_name");
	var fldId = getOpenerObj("contact_id");
	fldName.value = product_name;
	fldId.value = product_id;
}
//only for Todo
function set_return_toDospecific(product_id, product_name) {
	var fldName = getOpenerObj("task_contact_name");
	var fldId = getOpenerObj("task_contact_id");
	fldName.value = product_name;
	fldId.value = product_id;
}

function submitform(id){
	document.massdelete.entityid.value=id;
	document.massdelete.submit();
}

function searchMapLocation(addressType)
{
	var mapParameter = '';
	if (addressType == 'Main') {
		if(fieldname.indexOf('mailingstreet') > -1)
		{
			if(document.getElementById("dtlview_mailingstreet"))
				mapParameter = document.getElementById("dtlview_mailingstreet").innerHTML+' ';
		}
		if(fieldname.indexOf('mailingcity') > -1)
		{
			if(document.getElementById("dtlview_mailingcity"))
				mapParameter = mapParameter + document.getElementById("dtlview_mailingcity").innerHTML+' ';
		}
		if(fieldname.indexOf('mailingstate') > -1)
		{
			if(document.getElementById("dtlview_mailingstate"))
				mapParameter = mapParameter + document.getElementById("dtlview_mailingstate").innerHTML+' ';
		}
		if(fieldname.indexOf('mailingcountry') > -1)
		{
			if(document.getElementById("dtlview_mailingcountry"))
				mapParameter = mapParameter + document.getElementById("dtlview_mailingcountry").innerHTML+' ';
		}
		if(fieldname.indexOf('mailingzip') > -1)
		{
			if(document.getElementById("dtlview_mailingzip"))
				mapParameter = mapParameter + document.getElementById("dtlview_mailingzip").innerHTML;
		}
	} else if (addressType == 'Other') {
		if(fieldname.indexOf('otherstreet') > -1)
		{
			if(document.getElementById("dtlview_otherstreet"))
				mapParameter = document.getElementById("dtlview_otherstreet").innerHTML+' ';
		}
		if(fieldname.indexOf('othercity') > -1)
		{
			if(document.getElementById("dtlview_othercity"))
				mapParameter = mapParameter + document.getElementById("dtlview_othercity").innerHTML+' ';
		}
		if(fieldname.indexOf('otherstate') > -1)
		{
			if(document.getElementById("dtlview_otherstate"))
				mapParameter = mapParameter + document.getElementById("dtlview_otherstate").innerHTML+' ';
		}
		if(fieldname.indexOf('othercountry') > -1)
		{
			if(document.getElementById("dtlview_othercountry"))
				mapParameter = mapParameter + document.getElementById("dtlview_othercountry").innerHTML+' ';
		}
		if(fieldname.indexOf('otherzip') > -1)
		{
			if(document.getElementById("dtlview_otherzip"))
				mapParameter = mapParameter + document.getElementById("dtlview_otherzip").innerHTML;
		}
	}
	mapParameter = removeHTMLFormatting(mapParameter);
	window.open('http://maps.google.com/maps?q='+mapParameter,'goolemap','height=450,width=700,resizable=no,titlebar,location,top=200,left=250');
}

function set_return_contact_address(contact_id,contact_name, mailingstreet, otherstreet, mailingcity, othercity, mailingstate, otherstate, mailingcode, othercode, mailingcountry, othercountry,mailingpobox,otherpobox,formName) {
	if (formName == null || formName == '') {
		formName = 'EditView';
	} else {
		// In case formName is specified but does not exists then revert to EditView form
		if(window.opener.document.forms[formName] == null) formName = 'EditView';
	}
	var form = window.opener.document.forms[formName];
	form.contact_name.value = contact_name;
	form.contact_id.value = contact_id;
	if(typeof(form.bill_street) != 'undefined')
	if(confirm(alert_arr.OVERWRITE_EXISTING_CONTACT1+contact_name+alert_arr.OVERWRITE_EXISTING_CONTACT2))
	{
		//made changes to avoid js error -- ref : hidding fields causes js error(ticket#4017)
		if(typeof(form.bill_street) != 'undefined')
			form.bill_street.value = mailingstreet;
		if(typeof(form.ship_street) != 'undefined')
			form.ship_street.value = otherstreet;
		if(typeof(form.bill_city) != 'undefined')
			form.bill_city.value = mailingcity;
		if(typeof(form.ship_city) != 'undefined')
			form.ship_city.value = othercity;
		if(typeof(form.bill_state) != 'undefined')
			form.bill_state.value = mailingstate;
		if(typeof(form.ship_state) != 'undefined')
			form.ship_state.value = otherstate;
		if(typeof(form.bill_code) != 'undefined')
			form.bill_code.value = mailingcode;
		if(typeof(form.ship_code) != 'undefined')
			form.ship_code.value = othercode;
		if(typeof(form.bill_country) != 'undefined')
			form.bill_country.value = mailingcountry;
		if(typeof(form.ship_country) != 'undefined')
			form.ship_country.value = othercountry;
		if(typeof(form.bill_pobox) != 'undefined')
			form.bill_pobox.value = mailingpobox;
		if(typeof(form.ship_pobox) != 'undefined')
			form.ship_pobox.value = otherpobox;
	}
}

function set_return_address(contact_id,contact_name, mailingstreet, otherstreet, mailingcity, othercity, mailingstate, otherstate, mailingzip, otherzip, mailingcountry, othercountry,mailingpobox,otherpobox) {
	jQuery.ajax({
		url: 'index.php?module=Contacts&action=ContactsAjax&file=SelectContactAddress',
		context: document.body,
		success: function(response) {
			jQuery('#setaddresscontactdiv').html(response);
			jQuery('#setaddresscontactdiv').show();
			fnvshNrm('setaddresscontactdiv');
			jQuery("#contact_id").val(contact_id);
			jQuery("#contact_name").val(contact_name);
			jQuery("#mailingstreet").val(mailingstreet);
			jQuery("#mailingcity").val(mailingcity);
			jQuery("#mailingstate").val(mailingstate);
			jQuery("#mailingzip").val(mailingzip);
			jQuery("#mailingcountry").val(mailingcountry);
			jQuery("#mailingpobox").val(mailingpobox);
			jQuery("#otherstreet").val(otherstreet);
			jQuery("#othercity").val(othercity);
			jQuery("#otherstate").val(otherstate);
			jQuery("#otherzip").val(otherzip);
			jQuery("#othercountry").val(othercountry);
			jQuery("#otherpobox").val(otherpobox);
		}
	});
}

function sca_fillinvalues() {
	var contact_id = jQuery("#contact_id").val();
	var contact_name = jQuery("#contact_name").val();
	if(typeof(window.opener.document.EditView.contact_name) != 'undefined')
		window.opener.document.EditView.contact_name.value = contact_name;
	if(typeof(window.opener.document.EditView.contact_id) != 'undefined')
		window.opener.document.EditView.contact_id.value = contact_id;
	if (jQuery('#sca_bill').is(':checked')) setReturnAddressBill();
	if (jQuery('#sca_ship').is(':checked')) setReturnAddressShip();
	window.close();
}

function setReturnAddressBill() {
	var street = jQuery("#mailingstreet").val();
	var city = jQuery("#mailingcity").val();
	var state = jQuery("#mailingstate").val();
	var code = jQuery("#mailingzip").val();
	var country = jQuery("#mailingcountry").val();
	var pobox = jQuery("#mailingpobox").val();
	if(typeof(window.opener.document.EditView.bill_street) != 'undefined')
		window.opener.document.EditView.bill_street.value = street;
	if(typeof(window.opener.document.EditView.bill_city) != 'undefined')
		window.opener.document.EditView.bill_city.value = city;
	if(typeof(window.opener.document.EditView.bill_state) != 'undefined')
		window.opener.document.EditView.bill_state.value = state;
	if(typeof(window.opener.document.EditView.bill_code) != 'undefined')
		window.opener.document.EditView.bill_code.value = code;
	if(typeof(window.opener.document.EditView.bill_country) != 'undefined')
		window.opener.document.EditView.bill_country.value = country;
	if(typeof(window.opener.document.EditView.bill_pobox) != 'undefined')
		window.opener.document.EditView.bill_pobox.value = pobox;
}

function setReturnAddressShip() {
	var street = jQuery("#otherstreet").val();
	var city = jQuery("#othercity").val();
	var state = jQuery("#otherstate").val();
	var code = jQuery("#otherzip").val();
	var country = jQuery("#othercountry").val();
	var pobox = jQuery("#otherpobox").val();
	if(typeof(window.opener.document.EditView.ship_street) != 'undefined')
		window.opener.document.EditView.ship_street.value = street;
	if(typeof(window.opener.document.EditView.ship_city) != 'undefined')
		window.opener.document.EditView.ship_city.value = city;
	if(typeof(window.opener.document.EditView.ship_state) != 'undefined')
		window.opener.document.EditView.ship_state.value = state;
	if(typeof(window.opener.document.EditView.ship_code) != 'undefined')
		window.opener.document.EditView.ship_code.value = code;
	if(typeof(window.opener.document.EditView.ship_country) != 'undefined')
		window.opener.document.EditView.ship_country.value = country;
	if(typeof(window.opener.document.EditView.ship_pobox) != 'undefined')
		window.opener.document.EditView.ship_pobox.value = pobox;
}
