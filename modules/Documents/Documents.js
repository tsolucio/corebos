/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function UpdateAjaxSave(label, fid, fldname, fileOrFolder) {
	var fldVal = document.getElementById('txtbox_' + label).value;
	if (fldVal.replace(/^\s+/g, '').replace(/\s+$/g, '').length == 0) {
		alert(alert_arr.FOLDERNAME_EMPTY);
		return false;
	}
	if (fldVal.replace(/^\s+/g, '').replace(/\s+$/g, '').length >= 21) {
		alert(alert_arr.FOLDER_NAME_TOO_LONG);
		return false;
	}
	if (fldVal.match(/['"\\%+?]/)) {
		alert(alert_arr.NO_SPECIAL_CHARS_DOCS);
		return false;
	}
	if (fileOrFolder == 'file') {
		var url = 'action=DocumentsAjax&mode=ajax&ajax=true&file=Save&module=Documents&fileid=' + fid + '&fldVal=' + fldVal + '&fldname=' + fldname + '&act=ajaxEdit';
	} else {
		var foldername = encodeURIComponent(fldVal);
		foldername = foldername.replace(/^\s+/g, '').replace(/\s+$/g, '');
		foldername = foldername.replace(/&/gi, '*amp*');
		var url = 'action=DocumentsAjax&mode=ajax&ajax=true&file=SaveFolder&module=Documents&record=' + fid + '&foldername=' + fldVal + '&savemode=Save';
	}
	if (fldname == 'status') {
		fldVal = document.getElementById('txtbox_'+label).options[document.getElementById('txtbox_' + label).options.selectedIndex].text;
		gtempselectedIndex = document.getElementById('txtbox_' + label).options.selectedIndex;
	}
	document.getElementById('status').style.display = 'block';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+ url
	}).done(function (response) {
		var item = response;
		document.getElementById('status').style.display = 'none';
		if (item.indexOf('Failure') > -1) {
			var ihtml = '<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td class=small bgcolor=red><font color=white size=2><b>'
				+ alert_arr.LBL_UNABLE_TO_UPDATE + '</b></font></td></tr></table>';
			document.getElementById('lblError').innerHTML = ihtml;
			setTimeout(hidelblError, 3000);
		} else if (item.indexOf('DUPLICATE_FOLDERNAME') > -1) {
			alert(alert_arr.DUPLICATE_FOLDER_NAME);
		} else {
			document.getElementById('dtlview_' + label).innerHTML = fldVal;
			eval('hndCancel(\'dtlview_' + label + '\',\'editarea_' + label + '\',\'' + label + '\')');
			if (fldname == 'status') {
				document.getElementById('txtbox_' + label).selectedIndex = gtempselectedIndex;
			} else {
				document.getElementById('txtbox_' + label).value = fldVal;
			}
			eval(item);
		}
	});
}

function closeFolderCreate() {
	document.getElementById('folder_id').value = '';
	document.getElementById('folder_name').value = '';
	document.getElementById('folder_desc').value = '';
	fninvsh('orgLay');
}

function AddFolder() {
	var fldrname = getObj('folder_name').value;
	var fldrdesc = getObj('folder_desc').value;
	if (fldrname.replace(/^\s+/g, '').replace(/\s+$/g, '').length == 0) {
		alert(alert_arr.FOLDERNAME_EMPTY);
		return false;
	}
	if (fldrname.replace(/^\s+/g, '').replace(/\s+$/g, '').length >= 21) {
		alert(alert_arr.FOLDER_NAME_TOO_LONG);
		return false;
	}
	if (fldrdesc.replace(/^\s+/g, '').replace(/\s+$/g, '').length >= 51) {
		alert(alert_arr.FOLDER_DESCRIPTION_TOO_LONG);
		return false;
	}
	if (fldrname.match(/['"\\%+]/) || fldrdesc.match(/['"\\%+]/)) {
		alert(alert_arr.NO_SPECIAL_CHARS_DOCS + alert_arr.NAME_DESC);
		return false;
	}
	if (fldrname.match(/[?]+$/) || fldrname.match(/[?]+/)) {
		alert(alert_arr.NO_SPECIAL_CHARS_DOCS);
		return false;
	}
	fninvsh('orgLay');
	var foldername = encodeURIComponent(getObj('folder_name').value);
	var folderdesc = encodeURIComponent(getObj('folder_desc').value);
	foldername = foldername.replace(/^\s+/g, '').replace(/\s+$/g, '');
	foldername = foldername.replace(/&/gi, '*amp*');
	folderdesc = folderdesc.replace(/^\s+/g, '').replace(/\s+$/g, '');
	folderdesc = folderdesc.replace(/&/gi, '*amp*');
	getObj('folder_name').value = '';
	getObj('folder_desc').value = '';
	var mode = getObj('fldrsave_mode').value;
	if (mode == 'save') {
		var url = '&savemode=Save&foldername=' + foldername + '&folderdesc=' + folderdesc;
	}
	getObj('fldrsave_mode').value = 'save';
	document.getElementById('status').style.display = 'block';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=DocumentsAjax&mode=ajax&ajax=true&file=SaveFolder&module=Documents' + url
	}).done(function (response) {
		var item = response;
		document.getElementById('status').style.display = 'none';
		if (item.indexOf('Failure') > -1) {
			document.getElementById('lblError').innerHTML = '<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td class=small bgcolor=red><font color=white size=2><b>' + alert_arr.LBL_UNABLE_TO_ADD_FOLDER + '</b></font></td></tr></table>';
			setTimeOutFn();
		} else if (item.indexOf('DUPLICATE_FOLDERNAME') > -1) {
			alert(alert_arr.DUPLICATE_FOLDER_NAME);
		} else {
			getObj('ListViewContents').innerHTML = item.replace('&#&#&#&#&#&#', '');
		}
	});
}

function DeleteFolderCheck(folderId) {
	gtempfolderId = folderId;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Documents&action=DocumentsAjax&mode=ajax&ajax=true&file=DeleteFolder&deletechk=true&folderid=' + folderId
	}).done(function (response) {
		var item = response;
		if (item.indexOf('NOT_PERMITTED') > -1) {
			alert(alert_arr.NOT_PERMITTED);
			return false;
		} else if (item.indexOf('FAILURE') > -1) {
			alert(alert_arr.LBL_FOLDER_SHOULD_BE_EMPTY);
		} else {
			if (confirm(alert_arr.LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE_FOLDER)) {
				DeleteFolder(gtempfolderId);
			}
		}
	});
}

function DeleteFolder(folderId) {
	document.getElementById('status').style.display = 'block';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Documents&action=DocumentsAjax&mode=ajax&ajax=true&file=DeleteFolder&folderid=' + folderId
	}).done(function (response) {
		var item = response;
		document.getElementById('status').style.display = 'none';
		if (item.indexOf('FAILURE') > -1) {
			alert(alert_arr.LBL_ERROR_WHILE_DELETING_FOLDER);
		} else {
			document.getElementById('ListViewContents').innerHTML = item.replace('&#&#&#&#&#&#', '');
		}
	});
}

function MoveFile(id, foldername) {
	fninvsh('movefolderlist');
	var searchurl = document.getElementById('search_url').value;
	var viewid = getviewId();
	var idstring = '';
	var select_options = '';
	var excludedRecords = '';
	var obj = document.getElementsByName('folderidVal');
	var folderid = '0';
	var numOfRows = 0;
	var activation = 'false';
	if (obj && Document_Folder_View) {
		for (var i = 0; i < obj.length; i++) {
			var folid = obj[i].value;
			if (document.getElementById('selectedboxes_selectall' + folid).value == 'all') {
				excludedRecords = excludedRecords + document.getElementById('excludedRecords_selectall' + folid).value;
				var rows = document.getElementById('numOfRows_selectall' + folid).value;
				numOfRows = numOfRows + parseInt(rows);
				folderid = folid + ';' + folderid;
				activation = 'true';
			} else {
				select_options = select_options + document.getElementById('selectedboxes_selectall' + folid).value;
			}
		}
	} else {
		select_options = document.getElementById('allselectedboxes').value;
		numOfRows = document.getElementById('numOfRows').value;
		excludedRecords = document.getElementById('excludedRecords').value;
		if (select_options=='all') {
			document.getElementById('idlist').value = select_options;
			idstring = select_options;
			var skiprecords = excludedRecords.split(';');
			var count = skiprecords.length;
			if (count > 1) {
				count = numOfRows - count + 1;
			} else {
				count = numOfRows;
			}
		} else {
			var x = select_options.split(';');
			var count = x.length;
			if (count > 1) {
				document.getElementById('idlist').value = select_options;
				idstring = select_options;
			} else {
				alert(alert_arr.SELECT);
				return false;
			}
			// we have to decrese the count value by 1 because when we split with semicolon we will get one extra count
			count = count - 1;
		}
	}
	var x = select_options.split(';');
	var count = x.length;
	numOfRows = numOfRows + count - 1;
	if (activation == 'true' || select_options == 'all') {
		if (select_options == '') {
			select_options = 'all';
		}
		document.getElementById('idlist').value = select_options;
		idstring = select_options;
		var skiprecords = excludedRecords.split(';');
		var excount = skiprecords.length;
		if (excount > 1) {
			count = numOfRows - excount + 1;
		} else {
			count = numOfRows;
		}
	} else {
		if (count > 1) {
			document.getElementById('idlist').value = select_options;
			idstring = select_options;
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
		//we have to decrese the count value by 1 because when we split with semicolon we will get one extra count
		count = count - 1;
	}

	if (idstring != '') {
		var confirm_status = false;
		if (count > getMaxMassOperationLimit()) {
			var confirm_str = alert_arr.MORE_THAN_500;
			if (confirm(confirm_str)) {
				confirm_status = true;
			} else {
				return false;
			}
		} else {
			confirm_status = true;
		}

		if (confirm_status) {
			if (confirm(alert_arr.LBL_ARE_YOU_SURE_TO_MOVE_TO + foldername + alert_arr.LBL_FOLDER)) {
				var url = '&viewname=' + viewid + searchurl + '&excludedRecords=' + excludedRecords + '&folderidstring=' + folderid + '&selectallmode=' + activation;
				document.getElementById('status').style.display = 'block';
				jQuery.ajax({
					method: 'POST',
					url: 'index.php?action=DocumentsAjax&file=MoveFile&from_folderid=0&module=Documents&folderid=' + id + '&idlist=' + idstring + url
				}).done(function (response) {
					var item = response;
					document.getElementById('status').style.display = 'none';
					if (item.indexOf('NOT_PERMITTED') > -1) {
						document.getElementById('lblError').innerHTML = '<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td class=small bgcolor=red><font color=white size=2><b>' + alert_arr.NOT_PERMITTED + '</b></font></td></tr></table>';
						setTimeout(hidelblError, 3000);
					} else {
						getObj('ListViewContents').innerHTML = item.replace('&#&#&#&#&#&#', '');
					}
				});
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		alert(alert_arr.LBL_SELECT_ONE_FILE);
		return false;
	}
}

function dldCntIncrease(fileid) {
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=DocumentsAjax&mode=ajax&ajax=true&file=SaveFile&module=Documents&file_id=' + fileid + '&act=updateDldCnt'
	}).done(function (response) {
	});
}

function massDownload() {
	var arrayobj = {};
	var obj = document.getElementsByName('folderidVal');
	if (obj) {
		for (var i = 0; i < obj.length; i++) {
			var id = obj[i].value;
			var values = document.getElementById('selectedboxes_selectall' + id).value;
			if (values) {
				arrayobj[id] = values;
			}
		}
	}
	var count  = Object.keys(arrayobj).length;
	var array_val = JSON.stringify(arrayobj);
	if (count !== 0) {
		window.location.href = 'index.php?action=DocumentsAjax&mode=ajax&ajax=true&file=SaveFile&module=Documents&file_id=' + array_val + '&act=massDldCnt';
	} else {
		alert(alert_arr.SELECT);
		return false;
	}
}

function checkFileIntegrityDetailView(noteid) {
	document.getElementById('vtbusy_integrity_info').style.display = '';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Documents&action=DocumentsAjax&mode=ajax&ajax=true&file=SaveFile&act=checkFileIntegrityDetailView&noteid=' + noteid
	}).done(function (response) {
		var item = response;
		if (item.indexOf('file_available') > -1) {
			document.getElementById('vtbusy_integrity_info').style.display = 'none';
			document.getElementById('integrity_result').innerHTML = '<br><br>&nbsp;&nbsp;&nbsp;<font style=color:green>' + alert_arr.LBL_FILE_CAN_BE_DOWNLOAD + '</font>';
			document.getElementById('integrity_result').style.display = '';
			setTimeout(hideresult, 4000);
		} else if (item.indexOf('file_not_available') > -1) {
			document.getElementById('vtbusy_integrity_info').style.display = 'none';
			document.getElementById('integrity_result').innerHTML = '<br><br>&nbsp;&nbsp;&nbsp;<font style=color:red>' + alert_arr.LBL_DOCUMENT_NOT_AVAILABLE + '</font>';
			document.getElementById('integrity_result').style.display = '';
			setTimeout(hideresult, 6000);
		} else if (item.indexOf('lost_integrity') > -1) {
			document.getElementById('vtbusy_integrity_info').style.display = 'none';
			document.getElementById('integrity_result').innerHTML = '<br><br>&nbsp;&nbsp;&nbsp;<font style=color:red>' + alert_arr.LBL_DOCUMENT_LOST_INTEGRITY + '</font>';
			document.getElementById('integrity_result').style.display = '';
			setTimeout(hideresult, 6000);
		}
	});
}

function hideresult() {
	document.getElementById('integrity_result').style.display = 'none';
}

// Send file as an attachment in an email
function sendfile_email() {
	OpenCompose(document.getElementById('dldfilename').value, 'Documents');
}
