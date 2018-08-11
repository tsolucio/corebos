/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

function callRBSearch(searchtype) {
	for (var i=1; i<=26; i++) {
		var data_td_id = 'alpha_'+ eval(i);
		getObj(data_td_id).className = 'searchAlph';
	}
	gPopupAlphaSearchUrl = '';
	search_fld_val= document.getElementById('bas_searchfield').options[document.getElementById('bas_searchfield').selectedIndex].value;
	search_txt_val=document.basicSearch.search_text.value;
	var urlstring = '';
	if (searchtype == 'Basic') {
		urlstring = 'search_field='+search_fld_val+'&searchtype=BasicSearch&search_text='+search_txt_val+'&';
	}
	var selectedmodule = document.getElementById('select_module').options[document.getElementById('select_module').selectedIndex].value;
	urlstring += 'selected_module='+selectedmodule;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+urlstring +'&query=true&module=RecycleBin&action=RecycleBinAjax&file=index&ajax=true&mode=ajax'
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modules_datas').innerHTML=response;
		document.getElementById('search_ajax').innerHTML = '';
	});
}

function changeModule(pickmodule) {
	document.getElementById('status').style.display='inline';
	var module=pickmodule.options[pickmodule.options.selectedIndex].value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=RecycleBinAjax&module=RecycleBin&mode=ajax&file=ListView&selected_module='+module
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modules_datas').innerHTML=response;
		document.getElementById('searchAcc').innerHTML = document.getElementById('search_ajax').innerHTML;
		document.getElementById('search_ajax').innerHTML = '';
	});
}

function massRestore() {
	var excludedRecords = document.getElementById('excludedRecords').value;
	var select_options  =  document.getElementById('allselectedboxes').value;
	//var searchurl = document.getElementById('search_url').value;
	var numOfRows = document.getElementById('numOfRows').value;
	var idstring = '';
	if (select_options=='all') {
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
		var count=x.length;
		if (count > 1) {
			document.getElementById('idlist').value=select_options;
			idstring = select_options;
		} else {
			alert(mod_alert_arr.SELECT_ATLEAST_ONE_ENTITY);
			return false;
		}
		count = count-1;
	}
	if (count > getMaxMassOperationLimit()) {
		var confirm_str = alert_arr.MORE_THAN_500;
		if (confirm(confirm_str)) {
			var confirm_status = true;
		} else {
			return false;
		}
	} else {
		confirm_status = true;
	}

	if (confirm_status) {
		var selectmodule = document.getElementById('selected_module').value;
		var selectmoduletranslated =  document.getElementById('selected_module_translated').value;
		if (confirm(mod_alert_arr.MSG_RESTORE_CONFIRMATION + ' ' + count + ' ' + selectmoduletranslated + '?')) {
			document.getElementById('status').style.display='inline';
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?action=RecycleBinAjax&module=RecycleBin&mode=ajax&file=Restoration&idlist='+idstring+'&selectmodule='+selectmodule+'&excludedRecords='+excludedRecords
			}).done(function (response) {
				document.getElementById('status').style.display='none';
				document.getElementById('modules_datas').innerHTML=response;
				document.getElementById('search_ajax').innerHTML = '';
			});
		}
	}
}

function restore(entityid, select_module) {
	if (confirm(mod_alert_arr.MSG_RESTORE_CONFIRMATION + ' ' + select_module + '?')) {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=RecycleBinAjax&module=RecycleBin&mode=ajax&file=Restoration&idlist='+entityid+'&selectmodule='+select_module
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('modules_datas').innerHTML=response;
			document.getElementById('search_ajax').innerHTML = '';
		});
	}
}

function getListViewEntries_js(module, url) {
	var all_selected = document.getElementById('allselectedboxes').value;
	var excludedRecords = document.getElementById('excludedRecords').value;

	document.getElementById('status').style.display='block';
	var selected_module = document.getElementById('select_module').value;
	var urlstring = '&selected_module=' + selected_module;
	if (document.getElementById('search_url').value!='') {
		urlstring = document.getElementById('search_url').value+'&selected_module='+selected_module;
	}

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=RecycleBin&action=RecycleBinAjax&file=ListView&mode=ajax&ajax=true&'+url+urlstring+'&allselobjs='+all_selected+'&excludedRecords='+excludedRecords
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		if (document.getElementById('modules_datas')) {
			document.getElementById('modules_datas').innerHTML = response;
		}
		if (all_selected == 'all') {
			document.getElementById('linkForSelectAll').style.display='block';
			document.getElementById('selectAllRec').style.display='none';
			document.getElementById('deSelectAllRec').style.display='inline';
			var exculdedArray=excludedRecords.split(';');
			var obj = document.getElementsByName('selected_id');
			if (obj) {
				var viewForSelectLink = showSelectAllLink(obj, exculdedArray);
				document.getElementById('selectCurrentPageRec').checked = viewForSelectLink;
				document.getElementById('allselectedboxes').value='all';
				document.getElementById('excludedRecords').value = document.getElementById('excludedRecords').value+excludedRecords;
			}
		} else {
			document.getElementById('linkForSelectAll').style.display='none';
			update_selected_checkbox();
		}
	});
}

function alphabetic(module, url, dataid) {
	for (var i=1; i<=26; i++) {
		var data_td_id = 'alpha_'+ eval(i);
		getObj(data_td_id).className = 'searchAlph';
	}
	var selectedmodule = document.getElementById('select_module').options[document.getElementById('select_module').selectedIndex].value;
	url += '&selected_module='+selectedmodule;
	getObj(dataid).className = 'searchAlphselected';
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+module+'&action='+module+'Ajax&file=index&mode=ajax&ajax=true&'+url
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modules_datas').innerHTML=response;
		document.getElementById('search_ajax').innerHTML = '';
	});
}

function emptyRecyclebin(id) {
	if (document.getElementById(id)) {
		document.getElementById(id).style.display='none';
	}
	VtigerJS_DialogBox.progress();
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=RecycleBin&action=RecycleBinAjax&file=EmptyRecyclebin&mode=ajax&ajax=true&selected_module=&allrec=1'
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modules_datas').innerHTML= response;
		document.getElementById('searchAcc').innerHTML = document.getElementById('search_ajax').innerHTML;
		document.getElementById('search_ajax').innerHTML = '';
		VtigerJS_DialogBox.hideprogress();
	});
}

function callEmptyRecyclebin(id) {
	var excludedRecords = document.getElementById('excludedRecords').value;
	if (id==''|| id==null) {
		var select_options  =  document.getElementById('allselectedboxes').value;
	} else {
		select_options = id;
	}
	var recbutton  =  document.getElementById('selectCurrentPageRec');
	var allrec;
	//var searchurl = document.getElementById('search_url').value;
	var numOfRows = document.getElementById('numOfRows').value;
	var idstring = '';
	if (recbutton.checked) {
		allrec=1;
	} else {
		allrec=0;
	}

	if (select_options == 'all') {
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
		var count=x.length;
		if (id==''|| id==null) {
			if (count > 1) {
				document.getElementById('idlist').value=select_options;
				idstring = select_options;
			} else {
				allrec=1;
				count = ++numOfRows;
			}
			count = count-1;
		} else {
			document.getElementById('idlist').value=select_options;
			idstring = select_options;
			count=1;
		}
	}
	if (count > getMaxMassOperationLimit()) {
		var confirm_str = alert_arr.MORE_THAN_500;
		if (confirm(confirm_str)) {
			var confirm_status = true;
		} else {
			return false;
		}
	} else {
		confirm_status = true;
	}
	if (confirm_status) {
		var selectmodule = document.getElementById('selected_module').value;
		var selectmoduletranslated =  document.getElementById('selected_module_translated').value;
		if (confirm(mod_alert_arr.MSG_EMPTY_CONFIRMATION + ' ' + count + ' ' + selectmoduletranslated + '?')) {
			document.getElementById('status').style.display='inline';
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?action=RecycleBinAjax&module=RecycleBin&ajax=true&file=EmptyRecyclebin&idlist='+idstring+'&selectmodule='+selectmodule+'&excludedRecords='+excludedRecords+'&allrec='+allrec
			}).done(function (response) {
				document.getElementById('status').style.display='none';
				document.getElementById('modules_datas').innerHTML=response;
				document.getElementById('search_ajax').innerHTML = '';
			});
		}
	}
}
