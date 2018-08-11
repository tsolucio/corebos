/**
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 */

function showAllRecords() {
	var modname = document.getElementById('relmod').name;
	var idname= document.getElementById('relrecord_id').name;
	var locate = location.href;
	var url_arr = locate.split('?');
	var emp_url = url_arr[1].split('&');
	for (var i=0; i< emp_url.length; i++) {
		if (emp_url[i] != '') {
			var split_value = emp_url[i].split('=');
			if (split_value[0] == modname || split_value[0] == idname ) {
				emp_url[i]='';
			} else if (split_value[0] == 'fromPotential' || split_value[0] == 'acc_id' || emp_url[i] == 'query=true' || emp_url[i] == 'search=true' || split_value[0] == 'searchtype') {
				emp_url[i]='';
			}
		}
	}
	return 'index.php?'+emp_url.join('&');
}

//function added to get all the records when parent record doesn't relate with the selection module records while opening/loading popup.
function redirectWhenNoRelatedRecordsFound() {
	var loadUrl = showAllRecords();
	window.location.href = loadUrl;
}

function add_data_to_relatedlist(entity_id, recordid, mod, popupmode, callback) {
	var return_module = document.getElementById('return_module').value;
	if (mod == 'Documents' && return_module == 'Emails') {
		var attachment = document.getElementById('document_attachment_' + entity_id).value;
		window.opener.addOption(entity_id, attachment);
		if (document.getElementById('closewindow').value=='true') {
			window.close();
		}
		return;
	}
	if (popupmode == 'ajax') {
		VtigerJS_DialogBox.block();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module='+return_module+'&action='+return_module+'Ajax&file=updateRelations&destination_module='+mod+'&entityid='+entity_id+'&parentid='+recordid+'&mode=Ajax'
		}).done(function (response) {
			VtigerJS_DialogBox.unblock();
			var res = JSON.parse(response);
			if (typeof callback == 'function') {
				callback(res);
			}
		});
		return false;
	} else {
		opener.document.location.href='index.php?module='+return_module+'&action=updateRelations&destination_module='+mod+'&entityid='+entity_id+'&parentid='+recordid+'&return_module='+return_module+'&return_action='+gpopupReturnAction;
		if (document.getElementById('closewindow').value=='true') {
			window.close();
		}
	}
}

function set_focus() {
	document.getElementById('search_txt').focus();
}

function callSearch(searchtype) {
	gstart='';
	for (var i=1; i<=26; i++) {
		var data_td_id = 'alpha_'+ eval(i);
		getObj(data_td_id).className = 'searchAlph';
	}
	gPopupAlphaSearchUrl = '';
	var search_fld_val= document.basicSearch.search_field[document.basicSearch.search_field.selectedIndex].value;
	var search_txt_val= encodeURIComponent(document.basicSearch.search_text.value.replace(/\'/, '\\\''));
	var urlstring = '';
	if (searchtype == 'Basic') {
		urlstring = 'search_field='+search_fld_val+'&searchtype=BasicSearch&search_text='+search_txt_val;
	} else if (searchtype == 'Advanced') {
		checkAdvancedFilter();
		var advft_criteria = document.getElementById('advft_criteria').value;
		var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;
		urlstring += '&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups;
		urlstring += '&searchtype=advance&';
	}
	var popuptype = document.getElementById('popup_type').value;
	var module = document.getElementById('module').value;
	var act_tab = document.getElementById('maintab').value;
	urlstring += '&popuptype='+popuptype;
	urlstring += '&maintab='+act_tab;
	urlstring += '&query=true&file=Popup&module=' + module + '&action=' + module + 'Ajax&ajax=true&search=true';
	urlstring += gethiddenelements();
	var record_id = document.basicSearch.record_id.value;

	//support for popupmode and callback
	urlstring += '&popupmode=' + gpopupPopupMode;
	urlstring += '&callback=' + gpopupCallback;

	if (record_id!='') {
		urlstring += '&record_id='+record_id;
	}
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+urlstring
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('ListViewContents').innerHTML= response;
	});
}

function alphabetic(module, url, dataid) {
	gstart='';
	document.basicSearch.search_text.value = '';
	for (var i=1; i<=26; i++) {
		var data_td_id = 'alpha_' + eval(i);
		getObj(data_td_id).className = 'searchAlph';
	}
	getObj(dataid).className = 'searchAlphselected';
	gPopupAlphaSearchUrl = '&'+url;
	var urlstring ='module='+module+'&action='+module+'Ajax&file=Popup&ajax=true&search=true&'+url;
	urlstring +=gethiddenelements();
	var record_id = document.basicSearch.record_id.value;
	if (record_id!='') {
		urlstring += '&record_id='+record_id;
	}
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+ urlstring
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('ListViewContents').innerHTML= response;
	});
}

function gethiddenelements() {
	gstart='';
	var urlstring='';
	if (getObj('select_enable').value != '') {
		urlstring +='&select=enable';
	}
	if (document.getElementById('curr_row').value != '') {
		urlstring +='&curr_row='+document.getElementById('curr_row').value;
	}
	if (getObj('fldname_pb').value != '') {
		urlstring +='&fldname='+getObj('fldname_pb').value;
	}
	if (getObj('productid_pb').value != '') {
		urlstring +='&productid='+getObj('productid_pb').value;
	}
	if (getObj('recordid').value != '') {
		urlstring +='&recordid='+getObj('recordid').value;
	}
	if (getObj('relmod').value != '') {
		urlstring +='&'+getObj('relmod').name+'='+getObj('relmod').value;
	}
	if (getObj('relrecord_id').value != '') {
		urlstring +='&'+getObj('relrecord_id').name+'='+getObj('relrecord_id').value;
	}
	// vtlib customization: For uitype 10 popup during paging
	if (document.getElementById('popupform')) {
		urlstring +='&form='+encodeURIComponent(getObj('popupform').value);
	}
	if (document.getElementById('forfield')) {
		urlstring +='&forfield='+encodeURIComponent(getObj('forfield').value);
	}
	if (document.getElementById('srcmodule')) {
		urlstring +='&srcmodule='+encodeURIComponent(getObj('srcmodule').value);
	}
	if (document.getElementById('forrecord')) {
		urlstring +='&forrecord='+encodeURIComponent(getObj('forrecord').value);
	}
	if (document.getElementById('currencyid') != null && document.getElementById('currencyid').value != '') {
		urlstring +='&currencyid='+document.getElementById('currencyid').value;
	}
	if (document.getElementById('cbcustompopupinfo') != null && document.getElementById('cbcustompopupinfo').value != '') {
		var cbcustompopupinfo = document.getElementById('cbcustompopupinfo').value;
		let arr = cbcustompopupinfo.split(';');
		for (const value of arr) {
			if (document.getElementById(value) != null && document.getElementById(value).value != '') {
				urlstring +='&'+value+'='+document.getElementById(value).value;
			}
		}
	}
	var return_module = document.getElementById('return_module').value;
	if (return_module != '') {
		urlstring += '&return_module='+return_module;
	}
	return urlstring;
}

function getListViewEntries_js(module, url) {
	gstart='&'+url;

	var popuptype = document.getElementById('popup_type').value;
	var urlstring ='module='+module+'&action='+module+'Ajax&file=Popup&ajax=true&'+url;
	urlstring +=gethiddenelements();
	var record_id = document.basicSearch.record_id.value;
	if (record_id !='') {
		urlstring += '&record_id='+record_id;
	}
	var searchtype = document.basicSearch.searchtype.value;
	if (searchtype == 'BasicSearch') {
		var search_fld_val = document.basicSearch.search_field[document.basicSearch.search_field.selectedIndex].value;
		var search_txt_val = document.basicSearch.search_text.value;
		if (search_txt_val != '') {
			urlstring += '&search=true&query=true&search_field='+search_fld_val+'&searchtype=BasicSearch&search_text=' + search_txt_val;
		}
	} else if (searchtype == 'advance') {
		checkAdvancedFilter();
		var advft_criteria = document.getElementById('advft_criteria').value;
		var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;
		urlstring += '&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups;
		urlstring += '&searchtype=advance&query=true&search=true';
	}

	if (gPopupAlphaSearchUrl != '') {
		urlstring += gPopupAlphaSearchUrl;
	} else {
		urlstring += '&popuptype='+popuptype;
	}
	urlstring += (gsorder !='') ? gsorder : '';
	var return_module = document.getElementById('return_module').value;
	if (module == 'Documents' && return_module == 'MailManager') {
		urlstring += '&callback=MailManager.add_data_to_relatedlist';
		urlstring += '&popupmode=ajax';
		urlstring += '&srcmodule=MailManager';
	}

	document.getElementById('status').style.display = '';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+ urlstring
	}).done(function (response) {
		document.getElementById('ListViewContents').innerHTML= response;
		document.getElementById('status').style.display = 'none';
	});
}

function getListViewSorted_js(module, url) {
	gsorder=url;
	var urlstring ='module='+module+'&action='+module+'Ajax&file=Popup&ajax=true'+url;
	urlstring +=gethiddenelements();
	var record_id = document.basicSearch.record_id.value;
	if (record_id!='') {
		urlstring += '&record_id='+record_id;
	}
	urlstring += (gstart !='') ? gstart : '';
	document.getElementById('status').style.display = '';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+ urlstring
	}).done(function (response) {
		document.getElementById('ListViewContents').innerHTML= response;
		document.getElementById('status').style.display = 'none';
	});
}

function QCreatePop(module, urlpop) {
	if (module != 'none') {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module='+module+'&action='+module+'Ajax&file=QuickCreate&from=popup&pop='+urlpop
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('qcformpop').style.display='inline';
			document.getElementById('qcformpop').innerHTML = response;
			// Evaluate all the script tags in the response text.
			var scriptTags = document.getElementById('qcformpop').getElementsByTagName('script');
			for (var i = 0; i< scriptTags.length; i++) {
				var scriptTag = scriptTags[i];
				eval(scriptTag.innerHTML);
			}
		});
	} else {
		hide('qcformpop');
	}
}
