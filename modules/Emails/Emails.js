/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var gFolderid = 1;
var gselectedrowid = 0;

function searchDocuments() {
	let q = '&query=true&search=true&searchtype=BasicSearch&search_field=filelocationtype&search_text=I';
	window.open(
		'index.php?module=Documents&return_module=Emails&action=Popup&popuptype=detailview&form=EditView&form_submit=false&srcmodule=Emails&popupmode=ajax&select=1'+q,
		'emaildocselect',
		cbPopupWindowSettings);
}

function addOption(id, filename) {
	var table = document.getElementById('attach_cont');
	var attachments = document.getElementsByName('doc_attachments[]');
	if (attachments !== undefined) {
		for (var i = 0; i < attachments.length; i++) {
			if (attachments[i].value == id) {
				return;
			}
		}
	}
	var newRow = '<div>';
	newRow += '<a href=\'javascript:void(0)\' onclick=\'this.parentNode.parentNode.removeChild(this.parentNode);\'><img src=\'' + remove_image_url + '\'></a>';
	newRow += '&nbsp' + filename + '<input type=\'hidden\' name=\'doc_attachments[]\' value=\'' + id + '\'>';
	newRow += '</div>';
	table.innerHTML += newRow;
}

function email_validate(oform, mode) {
	if (trim(mode) == '') {
		return false;
	}
	if (oform.parent_name.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) {
		alert(no_rcpts_err_msg);
		return false;
	}
	//accomodate all possible email formats
	var email_regex = /^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\_\-]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/;
	var arr;
	var tmp;
	var str;
	var i;
	if (document.EditView.ccmail != null) {
		if (document.EditView.ccmail.value.length >= 1) {
			str = document.EditView.ccmail.value;
			arr = str.split(',');
			for (i=0; i<=arr.length-1; i++) {
				tmp = arr[i];
				if (tmp.match('<') && tmp.match('>')) {
					if (!findAngleBracket(arr[i])) {
						alert(cc_err_msg+': '+arr[i]);
						return false;
					}
				} else if (trim(arr[i]) != '' && !(email_regex.test(trim(arr[i])))) {
					alert(cc_err_msg+': '+arr[i]);
					return false;
				}
			}
		}
	}
	if (document.EditView.bccmail != null) {
		if (document.EditView.bccmail.value.length >= 1) {
			str = document.EditView.bccmail.value;
			arr = str.split(',');
			for (i=0; i<=arr.length-1; i++) {
				tmp = arr[i];
				if (tmp.match('<') && tmp.match('>')) {
					if (!findAngleBracket(arr[i])) {
						alert(bcc_err_msg+': '+arr[i]);
						return false;
					}
				} else if (trim(arr[i]) != '' && !(email_regex.test(trim(arr[i])))) {
					alert(bcc_err_msg+': '+arr[i]);
					return false;
				}
			}
		}
	}
	if (oform.subject.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) {
		var email_sub = prompt(alert_arr.ERR_EMAIL_WITH_NO_SUBJECT, alert_arr.EMAIL_WITH_NO_SUBJECT);
		if (email_sub) {
			oform.subject.value = email_sub;
		} else {
			return false;
		}
	}
	if (mode == 'send') {
		server_check();
	} else if (mode == 'save') {
		oform.action.value='Save';
		oform.submit();
	} else {
		return false;
	}
}

//function to extract the mailaddress inside < > symbols
function findAngleBracket(mailadd) {
	var strlen = mailadd.length;
	var gt = 0;
	var lt = 0;
	var ret = '';
	for (var i=0; i<strlen; i++) {
		if (mailadd.charAt(i) == '<' && gt == 0) {
			lt = 1;
		}
		if (mailadd.charAt(i) == '>' && lt == 1) {
			gt = 1;
		}
		if (mailadd.charAt(i) != '<' && lt == 1 && gt == 0) {
			ret = ret + mailadd.charAt(i);
		}
	}
	if (/^[a-z0-9]([a-z0-9_\-\.]*)@([a-z0-9_\-\.]*)(\.[a-z]{2,3}(\.[a-z]{2}){0,2})$/.test(ret)) {
		return true;
	}
	return false;
}

function server_check() {
	var oform = window.document.EditView;
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Emails&action=EmailsAjax&file=Save&ajax=true&server_check=true',
	}).done(function (response) {
		if (response.indexOf('SUCCESS') > -1) {
			oform.send_mail.value='true';
			oform.action.value='Save';
			oform.submit();
		} else {
			if (response.indexOf('FAILURESTORAGE') > -1) {
				if (confirm(conf_srvr_storage_err_msg)) {
					oform.send_mail.value='true';
					oform.action.value='Save';
					oform.submit();
				}
			} else {
				alert(conf_mail_srvr_err_msg);
			}
			return false;
		}
	});
}

function delAttachments(id) {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=delImage&attachmodule=Emails&recordid='+id
	}).done(function (response) {
		jQuery('#row_'+id).fadeOut();
	});
}
