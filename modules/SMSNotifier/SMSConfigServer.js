/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

function _SMSCongiServerShowReqParams(selectBox) {
	var providers = selectBox.options;
	for (var index = 0; index < providers.length; ++index) {
		var provideropt = providers[index];

		if (document.getElementById('paramrows_' + provideropt.value)) {
			if (provideropt.selected) {
				document.getElementById('paramrows_' + provideropt.value).style.display = "block";
			} else {
				document.getElementById('paramrows_' + provideropt.value).style.display = "none";
			}
		}
	}
	var selectedIndex = selectBox.selectedIndex-1;
	document.getElementById('_smshelpinfospan').innerHTML = '<a href="' + __smsHelpInfo[selectedIndex].url + '" target="_blank">' + __smsHelpInfo[selectedIndex].label + '</a>';
}

function _SMSConfigServerSaveForm(form) {

	if (form.smsserver_provider.value == '') {
		form.smsserver_provider.style.background = '#FFF4BF';
		return false;
	} else {
		form.smsserver_provider.style.background = '#FFFFFF';
	}

	if (form.smsserver_username.value == '') {
		form.smsserver_username.className = 'detailedViewTextBoxOn';
		form.smsserver_username.focus();
		return false;
	}

	if (form.smsserver_password.value == '') {
		form.smsserver_password.className = 'detailedViewTextBoxOn';
		form.smsserver_password.focus();
		return false;
	}

	document.getElementById("editdiv").style.display = "none";
	var frmvalues = jQuery(form).serialize();

	document.getElementById("status").style.display = "inline";
	jQuery.ajax({
		method:"POST",
		url:'index.php?action=SMSNotifierAjax&module=SMSNotifier&file=SMSConfigServer&mode=Save&' + frmvalues
	}).done(function(response) {
		document.getElementById("status").style.display = "none";
		document.getElementById("_smsservers_").innerHTML = response;
	});
}

function _SMSConfigServerDelete(id) {
	document.getElementById("editdiv").style.display = "none";
	document.getElementById("status").style.display = "inline";
	jQuery.ajax({
		method:"POST",
		url:'index.php?action=SMSNotifierAjax&module=SMSNotifier&file=SMSConfigServer&ajax=true&mode=Delete&record=' + id
	}).done(function(response) {
		document.getElementById("status").style.display = "none";
		document.getElementById("_smsservers_").innerHTML = response;
	});
}

function _SMSConfigServerFetchEdit(id) {
	document.getElementById("status").style.display = "inline";
	jQuery.ajax({
		method:"POST",
		url:'index.php?action=SMSNotifierAjax&module=SMSNotifier&file=SMSConfigServer&ajax=true&mode=Edit&record=' + id
	}).done(function(response) {
		document.getElementById("status").style.display = "none";
		document.getElementById("editdiv").innerHTML = response;
		vtlib_executeJavascriptInElement(document.getElementById("editdiv"));
		document.getElementById("editdiv").style.display = "block";
	});
}
