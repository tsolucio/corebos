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

function getListViewEntries_js(module, url) {
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+module+'&action='+module+'Ajax&file=ListView&ajax=true&'+url
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('email_con').innerHTML=response;
		execJS(document.getElementById('email_con'));
	});
}

function massDelete() {
	var delete_selected_row = false;
	//added to fix the issue 4295
	var select_options  =  document.getElementsByName('selected_id');
	var x = select_options.length;

	idstring = '';
	var xx = 0;
	for (var i = 0; i < x; i++) {
		if (select_options[i].checked) {
			idstring = select_options[i].value +';'+idstring;
			if (select_options[i].value == gselectedrowid) {
				gselectedrowid = 0;
				delete_selected_row = true;
			}
			xx++;
		}
	}
	if (xx > 0) {
		document.massdelete.idlist.value=idstring;
	} else {
		alert(alert_arr.SELECT);
		return false;
	}
	if (confirm(alert_arr.DELETE + xx + alert_arr.RECORDS)) {
		getObj('search_text').value = '';
		show('status');
		if (!delete_selected_row) {
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Users&action=massdelete&folderid='+gFolderid+'&return_module=Emails&idlist='+idstring
			}).done(function (response) {
				document.getElementById('status').style.display='none';
				document.getElementById('email_con').innerHTML=response;
				execJS(document.getElementById('email_con'));
			});
		} else {
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Users&action=massdelete&folderid='+gFolderid+'&return_module=Emails&idlist='+idstring
			}).done(function (response) {
				document.getElementById('status').style.display='none';
				document.getElementById('email_con').innerHTML=response;
				execJS(document.getElementById('email_con'));
				document.getElementById('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
				//document.getElementById("subjectsetter").innerHTML='';
			});
		}
	} else {
		return false;
	}
}

function DeleteEmail(id) {
	if (confirm(alert_arr.SURE_TO_DELETE)) {
		getObj('search_text').value = '';
		gselectedrowid = 0;
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Users&action=massdelete&return_module=Emails&folderid='+gFolderid+'&idlist='+id
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('email_con').innerHTML=response;
			execJS(document.getElementById('email_con'));
			document.getElementById('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
			//document.getElementById("subjectsetter").innerHTML='';
		});
	} else {
		return false;
	}
}

function Searchfn() {
	gselectedrowid = 0;
	var osearch_field = document.getElementById('search_field');
	var search_field = osearch_field.options[osearch_field.options.selectedIndex].value;
	var search_text = document.getElementById('search_text').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Emails&action=EmailsAjax&ajax=true&file=ListView&folderid=' + gFolderid + '&search=true&search_field=' + search_field + '&search_text=' + search_text
	}).done(function (response) {
		document.getElementById('email_con').innerHTML = response;
		document.getElementById('status').style.display = 'none';
		document.getElementById('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
		//document.getElementById("subjectsetter").innerHTML = '';
		execJS(document.getElementById('email_con'));
	});
}

function getListViewCount(module, element, parentElement, url) {
	if (module != 'Documents') {
		var elementList = document.getElementsByName(module+'_listViewCountRefreshIcon');
		for (var i=0; i<elementList.length; ++i) {
			elementList[i].style.display = 'none';
		}
	} else {
		element.style.display = 'none';
	}
	var elementList = document.getElementsByName(module+'_listViewCountContainerBusy');
	for (var i=0; i<elementList.length; ++i) {
		elementList[i].style.display = '';
	}
	var element = document.getElementsByName('search_url')[0];
	var searchURL = '';
	if (typeof element !='undefined') {
		searchURL = element.value;
	} else if (typeof document.getElementsByName('search_text')[0] != 'undefined') {
		element = document.getElementsByName('search_text')[0];
		var searchField = document.getElementsByName('search_field')[0];
		if (element.value.length > 0) {
			searchURL = '&query=true&searchtype=BasicSearch&search_field=' + encodeURIComponent(searchField.value)+'&search_text='+encodeURIComponent(element.value);
		}
	} else if (document.getElementById('globalSearchText') != null &&
		typeof document.getElementById('globalSearchText') != 'undefined') {
		var searchText = document.getElementById('globalSearchText').value;
		searchURL = '&query=true&globalSearch=true&globalSearchText='+encodeURIComponent(searchText);
		if (document.getElementById('tagSearchText') != null && typeof document.getElementById('tagSearchText') != 'undefined') {
			var tagSearch = document.getElementById('tagSearchText').value;
			searchURL = '&query=true&globalSearch=true&globalSearchText='+encodeURIComponent(searchText)+'&tagSearchText='+encodeURIComponent(tagSearch);
		}
	}
	if (module != 'Documents') {
		searchURL += (url);
	}
	// Url parameters to carry forward the Alphabetical search in Popups,
	// which is stored in the global variable gPopupAlphaSearchUrl
	if (typeof gPopupAlphaSearchUrl != 'undefined' && gPopupAlphaSearchUrl != '') {
		searchURL += gPopupAlphaSearchUrl;
	}

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+module+'&action='+module+'Ajax&file=ListViewPagging&ajax=true'+searchURL
	}).done(function (response) {
		var elementList = document.getElementsByName(module+'_listViewCountContainerBusy');
		for (var i=0; i<elementList.length; ++i) {
			elementList[i].style.display = 'none';
		}
		elementList = document.getElementsByName(module+'_listViewCountRefreshIcon');
		if (module != 'Documents' && typeof parentElement != 'undefined' && elementList.length !=0) {
			for (i=0; i<=elementList.length;) {
				//No need to increment the count, as the element will be eliminated in the next step.
				elementList[i].parentNode.innerHTML = response;
			}
		} else {
			parentElement.innerHTML = response;
		}
	});
}
function searchDocuments() {
	var emailId = 0;
	window.open('index.php?module=Documents&return_module=Emails&action=Popup&popuptype=detailview&form=EditView&form_submit=false&parenttab=Marketing&srcmodule=Emails&popupmode=ajax&select=1', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
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
		//alert('No recipients were specified');
		alert(no_rcpts_err_msg);
		return false;
	}
	//Changes made to fix tickets #4633, # 5111 to accomodate all possible email formats
	var email_regex = /^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\_\-]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/;
	var arr = new Array();
	if (document.EditView.ccmail != null) {
		if (document.EditView.ccmail.value.length >= 1) {
			var str = document.EditView.ccmail.value;
			arr = str.split(',');
			var tmp;
			for (var i=0; i<=arr.length-1; i++) {
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
			var str = document.EditView.bccmail.value;
			arr = str.split(',');
			var tmp;
			for (var i=0; i<=arr.length-1; i++) {
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
