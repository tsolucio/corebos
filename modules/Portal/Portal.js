/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function fetchAddSite(id) {
	document.getElementById('status').style.display = 'inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Portal&action=PortalAjax&file=Popup&record=' + id
	}).done(function (response) {
		document.getElementById('status').style.display = 'none';
		document.getElementById('editportal_cont').innerHTML = response;
	});
}

function fetchContents(mode) {
	// Reloading the window is better, If not reloaded ... mysitesArray variable needs to be updated
	// using eval method on javascript.
	if (mode == 'data') {
		window.location.href = 'index.php?module=Portal&action=ListView&parenttab=Tools';
		return;
	}
	document.getElementById('status').style.display = 'inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PortalAjax&mode=ajax&module=Portal&file=ListView&datamode=' + mode
	}).done(function (response) {
		document.getElementById('status').style.display = 'none';
		document.getElementById('portalcont').innerHTML = response;
	});
}

function DeleteSite(id) {
	if (confirm(alert_arr.SURE_TO_DELETE)) {
		document.getElementById('status').style.display = 'inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=PortalAjax&mode=ajax&file=Delete&module=Portal&record=' + id
		}).done(function (response) {
			document.getElementById('status').style.display = 'none';
			document.getElementById('portalcont').innerHTML = response;
		});
	}
}

function SaveSite(id) {
	if (document.getElementById('portalurl').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length == 0) {
		alert(alert_arr.SITEURL_CANNOT_BE_EMPTY);
		return false;
	}
	if (document.getElementById('portalname').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length == 0) {
		alert(alert_arr.SITENAME_CANNOT_BE_EMPTY);
		return false;
	}
	jQuery('#orgLay').toggle('puff');
	document.getElementById('status').style.display = 'inline';
	var portalurl = document.getElementById('portalurl').value;
	portalurl = portalurl.replace(/&/g, '#$#$#');
	var portalname = document.getElementById('portalname').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PortalAjax&mode=ajax&file=Save&module=Portal&portalname=' + portalname + '&portalurl=' + portalurl + '&record=' + id
	}).done(function (response) {
		if (response.indexOf(':#:FAILURE') > -1) {
			alert(alert_arr.VALID_DATA);
		} else {
			document.getElementById('portalcont').innerHTML = response;
		}
		document.getElementById('status').style.display = 'none';
	});
}

function setSite(oUrllist) {
	//var url = oUrllist.options[oUrllist.options.selectedIndex].value;
	var id = oUrllist.options[oUrllist.options.selectedIndex].value;
	var url = mysitesArray[id].url;
	document.getElementById('mysites_noload_message').style.display = 'none';

	//many sites do not allow embedded display
	if (mysitesArray[id].embed == 1) {
		document.getElementById('locatesite').src = url;
	} else {
		document.getElementById('locatesite').src = '';
		document.getElementById('mysites_noload_message').style.display = 'block';
		window.open(url);
	}
}

//added as an enhancement to set default value
function defaultMysites(oSelectlist) {
	var id = document.getElementById('urllist').value;
	document.getElementById('status').style.display = 'block';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PortalAjax&mode=ajax&file=Save&module=Portal&check=true&passing_var=' + id
	}).done(function (response) {
		//alert(response);
		document.getElementById('status').style.display = 'none';
	});
}

var oRegex = new Object();
oRegex.UriProtocol = new RegExp('');
oRegex.UriProtocol.compile('^(((ftp|news):\/\/)|mailto:)', 'gi');

oRegex.UrlOnChangeProtocol = new RegExp('');
oRegex.UrlOnChangeProtocol.compile('^(ftp|news)://(?=.)', 'gi');

function OnUrlChange() {
	var sUrl;
	var sProtocol;
	sUrl = document.getElementById('portalurl').value;
	sProtocol = oRegex.UrlOnChangeProtocol.exec(sUrl);
	if (sProtocol) {
		sUrl = sUrl.substr(sProtocol[0].length);
		document.getElementById('portalurl').value = sUrl;
	}
}