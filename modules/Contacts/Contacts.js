/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

document.addEventListener('DOMContentLoaded', function () {
	var accdiv = document.createElement('div');
	accdiv.style.zIndex = '12';
	accdiv.style.display = 'none';
	accdiv.style.width = '400px';
	accdiv.style.top = '30px';
	accdiv.style.left = '0';
	accdiv.style.right = '0';
	accdiv.style.margin = 'auto';
	accdiv.id = 'setaddresscontactdiv';
	accdiv.className = 'layerPopup';
	document.body.prepend(accdiv);
}, false);

function copyAddressRight(form) {
	if (typeof(form.otherstreet) != 'undefined' && typeof(form.mailingstreet) != 'undefined') {
		form.otherstreet.value = form.mailingstreet.value;
	}
	if (typeof(form.othercity) != 'undefined' && typeof(form.mailingcity) != 'undefined') {
		form.othercity.value = form.mailingcity.value;
	}
	if (typeof(form.otherstate) != 'undefined' && typeof(form.mailingstate) != 'undefined') {
		form.otherstate.value = form.mailingstate.value;
	}
	if (typeof(form.otherzip) != 'undefined' && typeof(form.mailingzip) != 'undefined') {
		form.otherzip.value = form.mailingzip.value;
	}
	if (typeof(form.othercountry) != 'undefined' && typeof(form.mailingcountry) != 'undefined') {
		form.othercountry.value = form.mailingcountry.value;
	}
	if (typeof(form.otherpobox) != 'undefined' && typeof(form.mailingpobox) != 'undefined') {
		form.otherpobox.value = form.mailingpobox.value;
	}
	return true;
}

function copyAddressLeft(form) {
	if (typeof(form.otherstreet) != 'undefined' && typeof(form.mailingstreet) != 'undefined') {
		form.mailingstreet.value = form.otherstreet.value;
	}
	if (typeof(form.othercity) != 'undefined' && typeof(form.mailingcity) != 'undefined') {
		form.mailingcity.value = form.othercity.value;
	}
	if (typeof(form.otherstate) != 'undefined' && typeof(form.mailingstate) != 'undefined') {
		form.mailingstate.value = form.otherstate.value;
	}
	if (typeof(form.otherzip) != 'undefined' && typeof(form.mailingzip) != 'undefined') {
		form.mailingzip.value =	form.otherzip.value;
	}
	if (typeof(form.othercountry) != 'undefined' && typeof(form.mailingcountry) != 'undefined') {
		form.mailingcountry.value = form.othercountry.value;
	}
	if (typeof(form.otherpobox) != 'undefined' && typeof(form.mailingpobox) != 'undefined') {
		form.mailingpobox.value = form.otherpobox.value;
	}
	return true;
}

function toggleDisplay(id) {
	if (this.document.getElementById(id).style.display=='none') {
		this.document.getElementById(id).style.display='inline';
		this.document.getElementById(id+'link').style.display='none';
	} else {
		this.document.getElementById(id).style.display='none';
		this.document.getElementById(id+'link').style.display='none';
	}
}

function set_return(product_id, product_name) {
	if (document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.parent_name.value = product_name;
		window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
		window.opener.document.EditView.parent_name.value = product_name;
		window.opener.document.EditView.parent_id.value = product_id;
	}
}

function add_data_to_relatedlist_incal(id, name) {
	var idval = window.opener.document.EditView.contactidlist.value;
	var nameval = window.opener.document.EditView.contactlist.value;
	if (idval != '') {
		if (idval.indexOf(id) != -1) {
			window.opener.document.EditView.contactidlist.value = idval;
			window.opener.document.EditView.contactlist.value = nameval;
		} else {
			window.opener.document.EditView.contactidlist.value = idval+';'+id;
			if (name != '') {
				// this has been modified to provide delete option for Contacts in Calendar
				//this function is defined in script.js
				window.opener.addOption(id, name);
			}
		}
	} else {
		window.opener.document.EditView.contactidlist.value = id;
		if (name != '') {
			window.opener.addOption(id, name);
		}
	}
}
function set_return_specific(product_id, product_name) {
	//Used for DetailView, Removed 'EditView' formname hardcoding
	var fldName = getOpenerObj('contact_name');
	var fldId = getOpenerObj('contact_id');
	fldName.value = product_name;
	fldId.value = product_id;
}

function searchMapLocation(addressType) {
	var mapParameter = '';
	if (addressType == 'Main') {
		if (fieldname.indexOf('mailingstreet') > -1) {
			if (document.getElementById('dtlview_mailingstreet')) {
				mapParameter = document.getElementById('dtlview_mailingstreet').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('mailingcity') > -1) {
			if (document.getElementById('dtlview_mailingcity')) {
				mapParameter = mapParameter + document.getElementById('dtlview_mailingcity').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('mailingstate') > -1) {
			if (document.getElementById('dtlview_mailingstate')) {
				mapParameter = mapParameter + document.getElementById('dtlview_mailingstate').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('mailingcountry') > -1) {
			if (document.getElementById('dtlview_mailingcountry')) {
				mapParameter = mapParameter + document.getElementById('dtlview_mailingcountry').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('mailingzip') > -1) {
			if (document.getElementById('dtlview_mailingzip')) {
				mapParameter = mapParameter + document.getElementById('dtlview_mailingzip').innerHTML;
			}
		}
	} else if (addressType == 'Other') {
		if (fieldname.indexOf('otherstreet') > -1) {
			if (document.getElementById('dtlview_otherstreet')) {
				mapParameter = document.getElementById('dtlview_otherstreet').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('othercity') > -1) {
			if (document.getElementById('dtlview_othercity')) {
				mapParameter = mapParameter + document.getElementById('dtlview_othercity').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('otherstate') > -1) {
			if (document.getElementById('dtlview_otherstate')) {
				mapParameter = mapParameter + document.getElementById('dtlview_otherstate').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('othercountry') > -1) {
			if (document.getElementById('dtlview_othercountry')) {
				mapParameter = mapParameter + document.getElementById('dtlview_othercountry').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('otherzip') > -1) {
			if (document.getElementById('dtlview_otherzip')) {
				mapParameter = mapParameter + document.getElementById('dtlview_otherzip').innerHTML;
			}
		}
	}
	mapParameter = removeHTMLFormatting(mapParameter);
	window.open('http://maps.google.com/maps?q='+mapParameter, 'goolemap', 'height=450,width=700,resizable=no,titlebar,location,top=200,left=250');
}

function set_return_contact_address(contact_id, contact_name, mailingstreet, otherstreet, mailingcity, othercity, mailingstate, otherstate, mailingcode, othercode, mailingcountry, othercountry, mailingpobox, otherpobox, formName) {
	if (formName == null || formName == '') {
		formName = 'EditView';
	} else {
		// In case formName is specified but does not exists then revert to EditView form
		if (window.opener.document.forms[formName] == null) {
			formName = 'EditView';
		}
	}
	var form = window.opener.document.forms[formName];
	form.contact_name.value = contact_name;
	form.contact_id.value = contact_id;
	if (typeof(form.bill_street) != 'undefined') {
		if (confirm(alert_arr.OVERWRITE_EXISTING_CONTACT1+contact_name+alert_arr.OVERWRITE_EXISTING_CONTACT2)) {
			//made changes to avoid js error -- ref : hidding fields causes js error(ticket#4017)
			if (typeof(form.bill_street) != 'undefined') {
				form.bill_street.value = mailingstreet;
			}
			if (typeof(form.ship_street) != 'undefined') {
				form.ship_street.value = otherstreet;
			}
			if (typeof(form.bill_city) != 'undefined') {
				form.bill_city.value = mailingcity;
			}
			if (typeof(form.ship_city) != 'undefined') {
				form.ship_city.value = othercity;
			}
			if (typeof(form.bill_state) != 'undefined') {
				form.bill_state.value = mailingstate;
			}
			if (typeof(form.ship_state) != 'undefined') {
				form.ship_state.value = otherstate;
			}
			if (typeof(form.bill_code) != 'undefined') {
				form.bill_code.value = mailingcode;
			}
			if (typeof(form.ship_code) != 'undefined') {
				form.ship_code.value = othercode;
			}
			if (typeof(form.bill_country) != 'undefined') {
				form.bill_country.value = mailingcountry;
			}
			if (typeof(form.ship_country) != 'undefined') {
				form.ship_country.value = othercountry;
			}
			if (typeof(form.bill_pobox) != 'undefined') {
				form.bill_pobox.value = mailingpobox;
			}
			if (typeof(form.ship_pobox) != 'undefined') {
				form.ship_pobox.value = otherpobox;
			}
		}
	}
}

function set_return_address(contact_id, contact_name, mailingstreet, otherstreet, mailingcity, othercity, mailingstate, otherstate, mailingzip, otherzip, mailingcountry, othercountry, mailingpobox, otherpobox) {
	jQuery.ajax({
		url: 'index.php?module=Contacts&action=ContactsAjax&file=SelectContactAddress',
		context: document.body
	}).done(function (response) {
		jQuery('#setaddresscontactdiv').html(response);
		jQuery('#setaddresscontactdiv').show();
		fnvshNrm('setaddresscontactdiv');
		jQuery('#contact_id').val(contact_id);
		jQuery('#contact_name').val(contact_name);
		jQuery('#mailingstreet').val(mailingstreet);
		jQuery('#mailingcity').val(mailingcity);
		jQuery('#mailingstate').val(mailingstate);
		jQuery('#mailingzip').val(mailingzip);
		jQuery('#mailingcountry').val(mailingcountry);
		jQuery('#mailingpobox').val(mailingpobox);
		jQuery('#otherstreet').val(otherstreet);
		jQuery('#othercity').val(othercity);
		jQuery('#otherstate').val(otherstate);
		jQuery('#otherzip').val(otherzip);
		jQuery('#othercountry').val(othercountry);
		jQuery('#otherpobox').val(otherpobox);
	});
}

function sca_fillinvalues() {
	var contact_id = jQuery('#contact_id').val();
	var contact_name = jQuery('#contact_name').val();
	if (window.opener.gVTModule != 'Issuecards') {
		if (typeof(window.opener.document.EditView.contact_name) != 'undefined') {
			window.opener.document.EditView.contact_name.value = contact_name;
		}
		if (typeof(window.opener.document.EditView.contact_id) != 'undefined') {
			window.opener.document.EditView.contact_id.value = contact_id;
		}
	} else {
		if (typeof(window.opener.document.EditView.ctoid_display) != 'undefined') {
			window.opener.document.EditView.ctoid_display.value = contact_name;
		}
		if (typeof(window.opener.document.EditView.ctoid) != 'undefined') {
			window.opener.document.EditView.ctoid.value = contact_id;
		}
	}
	if (jQuery('#sca_bill').is(':checked')) {
		setReturnAddressBill();
	}
	if (jQuery('#sca_ship').is(':checked')) {
		setReturnAddressShip();
	}
	window.close();
}

function setReturnAddressBill() {
	var street = jQuery('#mailingstreet').val();
	var city = jQuery('#mailingcity').val();
	var state = jQuery('#mailingstate').val();
	var code = jQuery('#mailingzip').val();
	var country = jQuery('#mailingcountry').val();
	var pobox = jQuery('#mailingpobox').val();
	if (window.opener.gVTModule != 'Issuecards') {
		if (typeof(window.opener.document.EditView.bill_street) != 'undefined') {
			window.opener.document.EditView.bill_street.value = street;
		}
		if (typeof(window.opener.document.EditView.bill_city) != 'undefined') {
			window.opener.document.EditView.bill_city.value = city;
		}
		if (typeof(window.opener.document.EditView.bill_state) != 'undefined') {
			window.opener.document.EditView.bill_state.value = state;
		}
		if (typeof(window.opener.document.EditView.bill_code) != 'undefined') {
			window.opener.document.EditView.bill_code.value = code;
		}
		if (typeof(window.opener.document.EditView.bill_country) != 'undefined') {
			window.opener.document.EditView.bill_country.value = country;
		}
		if (typeof(window.opener.document.EditView.bill_pobox) != 'undefined') {
			window.opener.document.EditView.bill_pobox.value = pobox;
		}
	} else {
		if (typeof (window.opener.document.EditView.calle) != 'undefined') {
			window.opener.document.EditView.calle.value = street;
		}
		if (typeof (window.opener.document.EditView.poblacion) != 'undefined') {
			window.opener.document.EditView.poblacion.value = city;
		}
		if (typeof (window.opener.document.EditView.provincia) != 'undefined') {
			window.opener.document.EditView.provincia.value = state;
		}
		if (typeof (window.opener.document.EditView.cpostal) != 'undefined') {
			window.opener.document.EditView.cpostal.value = code;
		}
		if (typeof (window.opener.document.EditView.pais) != 'undefined') {
			window.opener.document.EditView.pais.value = country;
		}
	}
}

function setReturnAddressShip() {
	var street = jQuery('#otherstreet').val();
	var city = jQuery('#othercity').val();
	var state = jQuery('#otherstate').val();
	var code = jQuery('#otherzip').val();
	var country = jQuery('#othercountry').val();
	var pobox = jQuery('#otherpobox').val();
	if (window.opener.gVTModule != 'Issuecards') {
		if (typeof(window.opener.document.EditView.ship_street) != 'undefined') {
			window.opener.document.EditView.ship_street.value = street;
		}
		if (typeof(window.opener.document.EditView.ship_city) != 'undefined') {
			window.opener.document.EditView.ship_city.value = city;
		}
		if (typeof(window.opener.document.EditView.ship_state) != 'undefined') {
			window.opener.document.EditView.ship_state.value = state;
		}
		if (typeof(window.opener.document.EditView.ship_code) != 'undefined') {
			window.opener.document.EditView.ship_code.value = code;
		}
		if (typeof(window.opener.document.EditView.ship_country) != 'undefined') {
			window.opener.document.EditView.ship_country.value = country;
		}
		if (typeof(window.opener.document.EditView.ship_pobox) != 'undefined') {
			window.opener.document.EditView.ship_pobox.value = pobox;
		}
	} else {
		if (typeof (window.opener.document.EditView.calle) != 'undefined') {
			window.opener.document.EditView.calle.value = street;
		}
		if (typeof (window.opener.document.EditView.poblacion) != 'undefined') {
			window.opener.document.EditView.poblacion.value = city;
		}
		if (typeof (window.opener.document.EditView.provincia) != 'undefined') {
			window.opener.document.EditView.provincia.value = state;
		}
		if (typeof (window.opener.document.EditView.cpostal) != 'undefined') {
			window.opener.document.EditView.cpostal.value = code;
		}
		if (typeof (window.opener.document.EditView.pais) != 'undefined') {
			window.opener.document.EditView.pais.value = country;
		}
	}
}

function googleSynch(module, oButton) {
	fnvshobj(oButton, 'GoogleContacts');
}

function googleContactsSynch(module, oButton, type) {
	var url='index.php?module='+module+'&action='+module+'Ajax&file=List&operation=sync&sourcemodule=Contacts';
	var opts = 'menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes';

	if (type==='signin') {
		fninvsh('GoogleContacts');
		openPopUp('GoogleContacts', oButton, url, 'createemailWin', 830, 662, opts);
	} else {
		document.getElementById('synchronize').disabled=true;
		document.getElementById('synchronizespan').innerHTML='Synchronizing...';
		document.getElementById('syncimage').style.display='block';
		jQuery.ajax({
			method: 'POST',
			url: url
		}).done(function (response) {
			fninvsh('GoogleContacts');
			document.getElementById('GoogleContactsSettings').innerHTML=response;
			fnvshobj(oButton, 'GoogleContactsSettings');
			document.getElementById('synchronize').disabled=false;
			document.getElementById('synchronizespan').innerHTML='Sync';
			document.getElementById('syncimage').style.display='none';
		});
	}
}

function googleContactsSettings(module, oButton) {
	fninvsh('GoogleContacts');
	var url='index.php?module='+module+'&action='+module+'Ajax&file=GSyncSettings&operation=getconfiggsyncsettings&sourcemodule=Contacts';
	jQuery.ajax({
		method: 'POST',
		url: url
	}).done(function (response) {
		document.getElementById('GoogleContactsSettings').innerHTML=response;
		fnvshobj(oButton, 'GoogleContactsSettings');
	});
}

function saveSettings() {
	return doSaveSettings();
}

function doSaveSettings() {
	var container = jQuery('.googleSettings');
	var form = container.find('form[name="contactsyncsettings"]');
	var fieldMapping = packFieldmappingsForSubmit(container);
	form.find('#user_field_mapping').val(fieldMapping);
	//    var serializedFormData = JSON.stringify(form);
	//    var form = document.forms['contactsyncsettings'];
	//    form.submit();
	//    jQuery.ajax({
	//            type : 'post',
	//            data :  serializedFormData,
	//            url : "index.php?module=Contacts&action=ContactsAjax&file=GSaveSyncSettings"
	//    }).done(function(msg) {
	//        alert('sdfsfdsf');
	//    });
	return true;
}

function packFieldmappingsForSubmit(container) {
	var rows = container.find('div#googlesyncfieldmapping').find('table > tbody > tr');
	var mapping = {};
	jQuery.each(rows, function (index, row) {
		var tr = jQuery(row);
		var vtiger_field_name = tr.find('.vtiger_field_name').not('.select2-container').val();
		var google_field_name = tr.find('.google_field_name').val();
		var googleTypeElement = tr.find('.google-type').not('.select2-container');
		var google_field_type = '';
		var google_custom_label = '';
		if (googleTypeElement.length) {
			google_field_type = googleTypeElement.val();
			var customLabelElement = tr.find('.google-custom-label');
			if (google_field_type == 'custom' && customLabelElement.length) {
				google_custom_label = customLabelElement.val();
			}
		}
		var map = {};
		map['vtiger_field_name'] = vtiger_field_name;
		map['google_field_name'] = google_field_name;
		map['google_field_type'] = google_field_type;
		map['google_custom_label'] = google_custom_label;
		mapping[index] = map;
	});
	return JSON.stringify(mapping);
}

function googleContactsLogOut(module) {
	jQuery.ajax({
		type : 'post',
		url : 'index.php?module='+module+'&action='+module+'Ajax&file=List&operation=removeSync&sourcemodule=Contacts'
	}).done(function (msg) {
		window.location.reload();
	});
}
