/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

// ListView inline column search
/* function to clear all the inline search condition fields*/
function clearAllField() {
	doingAdhocColumnSearch = 0;
	document.getElementById('advft_criteria').value = '';
	document.getElementById('advft_criteria_groups').value = '';
	document.basicSearch.searchtype.searchlaunched = '';
	var conditionColumns = getObj('noofsearchfields').value;
	for (var i = 0; i < conditionColumns; i++) {
		var p1 = getObj('fname_'+i);
		if (p1 != null && p1 != undefined) {
			if (getObj('type_'+i).value == 'date' || getObj('type_'+i).value == 'datetime') {
				var searchField = document.getElementById('jscal_field_'+p1.value+'_date1');
			} else {
				var searchField = getObj('tks_'+p1.value);
			}
			if (searchField != null && searchField != undefined) {
				if (getObj('type_'+i).value == 'select' || getObj('type_'+i).value == 'owner' || getObj('type_'+i).value == 'checkbox') {
					clearSelect('tks_'+p1.value);
				} else if (getObj('type_'+i).value == 'date' || getObj('type_'+i).value == 'datetime') {
					document.getElementById('jscal_field_'+p1.value+'_date1').value = '';
					document.getElementById('jscal_field_'+p1.value+'_date2').value = '';
				} else {
					searchField.value = '';
					disableDiv ('div_'+p1.value);
				}
			}
		}
	}
	jQuery.ajax({
		method: 'POST',
		url:'index.php?file=ListView&module='+gVTModule+'&action='+gVTModule+'Ajax&ajax=true'
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		result = response.split('&#&#&#');
		var LVC = document.getElementById('ListViewContents');
		LVC.innerHTML = result[2];
		vtlib_executeJavascriptInElement(LVC);
		if (result[1] != '') {
			alert(result[1]);
		}
	});
}

/* function to clear field*/
function clearField(id) {
	document.getElementById(id).value = '';
}

function clearSelect(id) {
	var selectObj=document.getElementById(id);
	var options=selectObj.options;
	for (var i=0; i<options.length; i++) {
		options[i].selected=false;
	}
}

/* function to copy first date into second date*/
function copyDate(start, end) {
	var startdate = document.getElementById(start);
	document.getElementById(end).value = startdate.value;
}

/* function to enable the condition div*/
function enableDiv(id) {
	var x=document.getElementsByName('layerPopup');
	for (var i=0; i< x.length; i++) {
		if (x[i].id != id && x[i].style.display == 'block') {
			x[i].style.display = 'none';
		}
	}
	document.getElementById(id).style.display = 'block';
}

/* function to disable the condition div*/
function disableDiv(id) {
	document.getElementById(id).style.display = 'none';
}

/* function for reinitializing the seach block by the previous search options*/
function reint_pram(arr) {
	for (var i = 0; i < arr.length; i++) {
		if (arr[i]['type'] == 'text' || arr[i]['type'] == 'number' || arr[i]['type'] == 'currency') {
			getObj(arr[i]['field']).value = arr[i]['value'];
		} else if (arr[i]['type'] == 'select' || arr[i]['type'] == 'owner' || arr[i]['type'] == 'checkbox') {
			jQuery('#'+arr[i]['field']).val(arr[i]['value']);
		} else if (arr[i]['type'] == 'date' || arr[i]['type'] == 'datetime') {
			var t1 = arr[i]['field'].split(',');
			var t2 = arr[i]['value'].split(',');
			getObj(t1[0]).value = t2[0];
			getObj(t1[1]).value = t2[1];
		}
	}
}

/* function for disabling enter key and preventing submit form event*/
function disableEnterKey(e, mod) {
	var key;
	if (window.event) {
		key = window.event.keyCode;
	} else { //IE
		key = e.which;
	} //firefox
	if (key == 13) {
		activateCustomSearch(mod);
	}
	return (key != 13);
}

/* function to searching the records and loding the ajax result in listviewcontent div*/
function activateCustomSearch(module) {
	var urlstring			= '';
	var groupid				= 1;
	var k					= 0;
	var cond				= '';
	var criteriaConditions	= new Array();
	var iterator			= new Array();
	var conditionColumns	= getObj('noofsearchfields').value;
	var searchblock			= getObj('fcolcolumnIndex');
	var backup_array		= new Array();

	/* get the search field criteria data value for ajax url parameter assign to hidden field*/
	for (var i = 0; i < conditionColumns; i++) {
		var fval = getObj('fvalue_'+i);
		if (fval != null && fval != undefined) {
			for (var j = 0; j < searchblock.length; j++) {
				if (searchblock.options[j].text == fval.value) {
					getObj('customval_'+i).value = searchblock.options[j].value.replace(/\\'/g, '');
					break;
				}
			}
		}
	}

	/* check for the field condition that has been selected by the user for search*/
	for (i = 0, j = 0; i < conditionColumns; i++) {
		var p1 = getObj('fname_'+i);
		if (p1 != null && p1 != undefined) {
			if (getObj('type_'+i).value == 'date' || getObj('type_'+i).value == 'datetime') {
				var searchField = document.getElementById('jscal_field_'+p1.value+'_date1');
			} else {
				var searchField = getObj('tks_'+p1.value);
			}
			if (searchField != null && searchField != undefined && searchField.value != '') {
				iterator[j++] = i;
			}
		}
	}

	if (iterator.length == 0) {
		alert(alert_arr.SELECTCONDITION);
		return false;
	}

	if (iterator.length > 1) {
		cond = 'and';
	}
	for (i = 0; i < iterator.length; i++) {
		if (i == iterator.length - 1) {
			cond = '';
		}

		var p1 = getObj('fname_'+iterator[i]);
		var p2 = getObj('fvalue_'+iterator[i]);
		var p3 = getObj('customval_'+iterator[i]);
		var p4 = getObj('type_'+iterator[i]);

		if (p1 != null && p1 != undefined) {
			var searchField = getObj('tks_'+p1.value);

			if (searchField != null && searchField != undefined && searchField.value != '') {
				if (p4.value == 'text' || p4.value == 'number' || p4.value == 'currency') {
					var p5 = getObj('op_cond_'+iterator[i]).value;
					criteriaConditions[k++] =
					{
						'groupid'			: groupid,
						'columnname'		: p3.value,
						'comparator'		: p5,
						'value'				: searchField.value,
						'columncondition'	: cond
					};
					backup_array.push({ 'field' : 'tks_'+p1.value, 'value' : searchField.value, 'type' : p4.value });
				} else if (p4.value == 'select' || p4.value == 'owner' || p4.value == 'checkbox') {
					var tks_array = '';
					jQuery('select#tks_'+p1.value+' option:selected').map(function () {
						tks_array += (jQuery(this).val())+',';
					});

					var temp_array = tks_array.split(',');
					var comp = 'e';

					if ((temp_array.length) - 1 > 1) {
						var lcon = 'or';
						for (j = 0; j < temp_array.length - 1; j++) {
							if (j == temp_array.length - 2) {
								if (i == iterator.length - 1) {
									lcon = '';
								} else {
									lcon = 'and';
								}
							}
							criteriaConditions[k++] =
							{
								'groupid'			: groupid,
								'columnname'		: p3.value,
								'comparator'		: comp,
								'value'				: temp_array[j],
								'columncondition'	: lcon
							};
						}
						backup_array.push({ 'field' : 'tks_'+p1.value, 'value' : temp_array, 'type' : p4.value });
					} else {
						if (i == (iterator.length) - 1) {
							lcon = '';
						} else {
							lcon = 'and';
						}

						criteriaConditions[k++] =
						{
							'groupid'			: groupid,
							'columnname'		: p3.value,
							'comparator'		: comp,
							'value'				: temp_array[0],
							'columncondition'	: lcon
						};
						backup_array.push({ 'field' : 'tks_'+p1.value, 'value' : temp_array[0], 'type' : p4.value });
					}
				}
			}
			if (p4.value == 'date'	|| p4.value == 'datetime') {
				var d1	=  document.getElementById('jscal_field_'+p1.value+'_date1');
				var d2	=  document.getElementById('jscal_field_'+p1.value+'_date2');

				if (d1 != null && d1 != undefined && d1.value != '' && d2 != null && d2 != undefined && d2.value != '') {
					criteriaConditions[k++] =
					{
						'groupid'			: groupid,
						'columnname'		: p3.value,
						'comparator'		: 'bw',
						'value'				: d1.value+','+d2.value,
						'columncondition'	: cond
					};
					backup_array.push({ 'field' : 'jscal_field_'+p1.value+'_date1,jscal_field_'+p1.value+'_date2', 'value' : d1.value+','+d2.value, 'type' : p4.value });
				} else {
					alert(alert_arr.ERR_INVALID_DATE);
					return false;
				}
			}
		}
	}
	var advft_criteria = JSON.stringify(criteriaConditions);
	var advft_criteria_groups = '[null,{"groupcondition":""}]';
	document.getElementById('advft_criteria').value = advft_criteria;
	document.getElementById('advft_criteria_groups').value = advft_criteria_groups;
	document.basicSearch.searchtype.searchlaunched = 'advance';
	doingAdhocColumnSearch = 1;
	urlstring += '&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups+'&';
	urlstring += 'searchtype=advance&';
	if (iterator.length > 0) {
		var posturl = 'query=true&file=index&module='+module+'&action='+module+'Ajax&ajax=true&search=true';
		document.getElementById('status').style.display = 'inline';
		jQuery.ajax({
			method: 'POST',
			url:'index.php?'+urlstring+posturl,
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			result = response.split('&#&#&#');
			var LVC = document.getElementById('ListViewContents');
			LVC.innerHTML = result[2];
			vtlib_executeJavascriptInElement(LVC);
			if (result[1] != '') {
				alert(result[1]);
			}
		});
	} else {
		alert(alert_arr.SELECTCONDITION);
	}
	return false;
}

// MassEdit Feature
function massedit_togglediv(curTabId, total) {
	for (var i=0; i<total; i++) {
		var tagName = document.getElementById('massedit_div'+i);
		var tagName1 = document.getElementById('tab'+i);
		tagName.style.display = 'none';
		tagName1.className = 'dvtUnSelectedCell';
	}
	tagName = document.getElementById('massedit_div'+curTabId);
	tagName.style.display = 'block';
	tagName1 = document.getElementById('tab'+curTabId);
	tagName1.className = 'dvtSelectedCell';
}

function massedit_initOnChangeHandlers() {
	var form = document.getElementById('massedit_form');
	// Setup change handlers for input boxes
	var inputs = form.getElementsByTagName('input');
	for (var index = 0; index < inputs.length; ++index) {
		var massedit_input = inputs[index];
		// TODO Onchange on readonly and hidden fields are to be handled later.
		massedit_input.onchange = function () {
			var checkbox = document.getElementById(this.name + '_mass_edit_check');
			if (checkbox) {
				checkbox.checked = true;
			}
		};
	}
	// Setup change handlers for select boxes
	var selects = form.getElementsByTagName('select');
	for (index = 0; index < selects.length; ++index) {
		var massedit_select = selects[index];
		massedit_select.onchange = function () {
			var checkbox = document.getElementById(this.name + '_mass_edit_check');
			if (checkbox) {
				checkbox.checked = true;
			}
		};
	}
}

function mass_edit(obj, divid, module, parenttab) {
	var select_options = document.getElementById('allselectedboxes').value;
	var numOfRows = document.getElementById('numOfRows').value;
	var excludedRecords = document.getElementById('excludedRecords').value;
	var count = 0;
	if (select_options=='all') {
		var idstring = select_options;
		var skiprecords = excludedRecords.split(';');
		count = skiprecords.length;
		if (count > 1) {
			count = numOfRows - count + 1;
		} else {
			count = numOfRows;
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
			mass_edit_formload(idstring, module, parenttab);
		}
	} else {
		var x = select_options.split(';');
		count = x.length;
		if (count > 1) {
			idstring = select_options;
			if (count > getMaxMassOperationLimit()) {
				confirm_str = alert_arr.MORE_THAN_500;
				if (confirm(confirm_str)) {
					confirm_status = true;
				} else {
					return false;
				}
			} else {
				confirm_status = true;
			}

			if (confirm_status) {
				mass_edit_formload(idstring, module, parenttab);
			}
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
	}
	fnvshobj(obj, divid);
}

function mass_edit_formload(idstring, module, parenttab) {
	if (typeof (parenttab) == 'undefined') {
		parenttab = '';
	}
	var excludedRecords = document.getElementById('excludedRecords').value;
	var viewid = getviewId();
	document.getElementById('status').style.display = 'inline';
	var urlstring = '';
	var searchtype = document.basicSearch.searchtype.value;
	if (document.basicSearch.searchtype.searchlaunched != undefined && document.basicSearch.searchtype.searchlaunched == 'basic') {
		var search_fld_val = document.getElementById('bas_searchfield').options[document.getElementById('bas_searchfield').selectedIndex].value;
		var search_txt_val = encodeURIComponent(document.basicSearch.search_text.value);
		if (search_txt_val != '') {// if the search fields are not empty
			urlstring = '&query=true&ajax=true&search=true&search_field=' + search_fld_val + '&searchtype=BasicSearch&search_text=' + search_txt_val;
		}
	} else if (document.basicSearch.searchtype.searchlaunched != undefined && document.basicSearch.searchtype.searchlaunched == 'advance' && checkAdvancedFilter()) {
		var advft_criteria = encodeURIComponent(document.getElementById('advft_criteria').value);
		var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;
		urlstring = '&query=true&ajax=true&search=true&advft_criteria=' + advft_criteria + '&advft_criteria_groups=' + advft_criteria_groups + '&searchtype=advance';
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+encodeURIComponent(module)+'&action='+encodeURIComponent(module+'Ajax')+'&parenttab='+encodeURIComponent(parenttab)+'&file=MassEdit&mode=ajax&idstring='+idstring+'&viewname='+viewid+'&excludedRecords='+excludedRecords+urlstring
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		var result = response;
		document.getElementById('massedit_form_div').innerHTML=result;
		document.getElementById('massedit_form')['massedit_recordids'].value = document.getElementById('massedit_form')['idstring'].value;
		document.getElementById('massedit_form')['massedit_module'].value = module;
		vtlib_executeJavascriptInElement(document.getElementById('massedit_form_div'));
	});
}

function mass_edit_fieldchange(selectBox) {
	var oldSelectedIndex = selectBox.oldSelectedIndex;
	var selectedIndex = selectBox.selectedIndex;

	if (document.getElementById('massedit_field' + oldSelectedIndex)) {
		document.getElementById('massedit_field' + oldSelectedIndex).style.display = 'none';
	}
	if (document.getElementById('massedit_field' + selectedIndex)) {
		document.getElementById('massedit_field' + selectedIndex).style.display = 'block';
	}

	selectBox.oldSelectedIndex = selectedIndex;
}

var gstart='';
function massDelete(module) {
	var searchurl = document.getElementById('search_url').value;
	var viewid = getviewId();
	var idstring = '';
	var count = 0;
	var numOfRows = 0;
	if (module != 'Documents' || Document_Folder_View == 0) {
		var select_options = document.getElementById('allselectedboxes').value;
		var excludedRecords = document.getElementById('excludedRecords').value;
		numOfRows = document.getElementById('numOfRows').value;
		if (select_options == 'all') {
			document.getElementById('idlist').value = select_options;
			idstring = select_options;
			var skiprecords = excludedRecords.split(';');
			count = skiprecords.length;
			if (count > 1) {
				count = numOfRows - count + 1;
			} else {
				count = numOfRows;
			}
		} else {
			var x = select_options.split(';');
			count = x.length;
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
	} else {
		select_options = '';
		excludedRecords = '';
		var obj = document.getElementsByName('folderidVal');
		var folderid = '0';
		var activation = 'false';
		if (obj) {
			for (var i = 0; i < obj.length; i++) {
				var id = obj[i].value;
				if (document.getElementById('selectedboxes_selectall' + id).value == 'all') {
					var rows = document.getElementById('numOfRows_selectall' + id).value;
					numOfRows = numOfRows + parseInt(rows);
					excludedRecords = excludedRecords + document.getElementById('excludedRecords_selectall' + id).value;
					folderid = id + ';' + folderid;
					activation = 'true';
				} else {
					select_options = select_options + document.getElementById('selectedboxes_selectall' + id).value;
				}
			}
		}
		x = select_options.split(';');
		count = x.length;
		numOfRows = numOfRows + count - 1;
		if (activation == 'true') {
			document.getElementById('idlist').value = select_options;
			idstring = select_options;
			skiprecords = excludedRecords.split(';');
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
		var alert_str = alert_arr.DELETE + count +alert_arr.RECORDS;

		if (module == 'Accounts') {
			alert_str = alert_arr.DELETE_ACCOUNT +count+alert_arr.RECORDS;
		} else if (module == 'Vendors') {
			alert_str = alert_arr.DELETE_VENDOR+count+alert_arr.RECORDS;
		}

		if (confirm(alert_str)) {
			document.getElementById('status').style.display='inline';
			var url = '&excludedRecords='+excludedRecords;
			if (module=='Documents') {
				url = url+'&folderidstring='+folderid+'&selectallmode='+activation;
			}

			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Users&action=massdelete&return_module='+module+'&'+gstart+'&viewname='+viewid+'&idlist='+idstring+searchurl+url
			}).done(function (response) {
				document.getElementById('status').style.display='none';
				var result = response.split('&#&#&#');
				document.getElementById('ListViewContents').innerHTML= result[2];
				if (result[1] != '') {
					alert(result[1]);
				}
				document.getElementById('basicsearchcolumns').innerHTML = '';
				document.getElementById('allselectedboxes').value='';
				if (document.getElementById('excludedRecords')) {
					document.getElementById('excludedRecords').value='';
				}
			});
		} else {
			return false;
		}
	}
}

function showDefaultCustomView(selectView, module, parenttab) {
	document.getElementById('status').style.display = 'inline';
	var viewName = encodeURIComponent(selectView.options[selectView.options.selectedIndex].value);
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=' + module + '&action=' + module + 'Ajax&file=ListView&ajax=true&start=1&viewname=' + viewName + '&parenttab=' + parenttab
	}).done(function (response) {
		document.getElementById('status').style.display = 'none';
		var result = response.split('&#&#&#');
		document.getElementById('ListViewContents').innerHTML = result[2];
		if (result[1] != '') {
			alert(result[1]);
		}
		document.getElementById('basicsearchcolumns_real').innerHTML = document.getElementById('basicsearchcolumns').innerHTML;
		document.getElementById('basicsearchcolumns').innerHTML = '';
		document.basicSearch.search_text.value = '';
	});
}

function getListViewEntries_js(module, url) {
	if (module!='Documents' || Document_Folder_View == 0) {
		var excludedRecords = document.getElementById('excludedRecords').value;
		var all_selected = document.getElementById('allselectedboxes').value;
		var count = document.getElementById('numOfRows').value;
	} else {
		var obj = document.getElementsByName('folderidVal');
		var selected = '';
		var selectedRecords = new Array();
		var excludedRecords = new Array();
		var numOfRows = new Array();
		for (var i=0; i<obj.length; i++) {
			var id = obj[i].value;
			excludedRecords[i] = document.getElementById('excludedRecords_selectall'+id).value;
			selectedRecords[i] = document.getElementById('selectedboxes_selectall'+id).value;
			numOfRows[i] = document.getElementById('numOfRows_selectall'+id).value;
		}
		var urlArray= url.split('&');
		var folderid;
		for (i=0; i<urlArray.length; i++) {
			var getId = urlArray[i].split('=');
			if (getId[0] == 'folderid') {
				folderid = parseInt(getId[1]);
				all_selected = document.getElementById('selectedboxes_selectall'+folderid).value;
			}
		}
	}

	var select_options = document.getElementsByName('selected_id');
	var x = select_options.length;
	var idstring = '';

	for (i = 0; i < x; i++) {
		if (select_options[i].checked) {
			idstring = select_options[i].value +';'+idstring;
		}
	}

	document.getElementById('status').style.display='inline';
	if (typeof document.getElementById('search_url') != 'undefined' && document.getElementById('search_url').value!='') {
		var urlstring = document.getElementById('search_url').value;
	} else {
		urlstring = '';
	}

	gstart = url;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+module+'&action='+module+'Ajax&file=ListView&ajax=true&allselobjs='+all_selected+'&selobjs='+idstring+'&'+url+urlstring
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		var result = response.split('&#&#&#');
		document.getElementById('ListViewContents').innerHTML= result[2];

		if (module == 'Documents' && Document_Folder_View) {
			obj = document.getElementsByName('folderidVal');
			for (var i=0; i<obj.length; i++) {
				var id = obj[i].value;
				document.getElementById('excludedRecords_selectall'+id).value = document.getElementById('excludedRecords_selectall'+id).value + excludedRecords[i];
				document.getElementById('selectedboxes_selectall'+id).value = document.getElementById('selectedboxes_selectall'+id).value + selectedRecords[i];
				document.getElementById('numOfRows_selectall'+id).value = numOfRows[i];
				document.getElementById('count_selectall'+id).innerHTML = numOfRows[i];
				if (selectedRecords[i] == 'all') {
					document.getElementById('linkForSelectAll_selectall'+id).style.display = 'table-cell';
					document.getElementById('selectAllRec_selectall'+id).style.display='none';
					document.getElementById('deSelectAllRec_selectall'+id).style.display='inline';
					var exculdedArray = excludedRecords[i].split(';');
					var selectedobj = document.getElementsByName('selected_id'+id);
					var viewForSelectLink = showSelectAllLink(selectedobj, exculdedArray);
					document.getElementById('currentPageRec_selectall'+id).checked = viewForSelectLink;
				} else {
					if (selectedRecords[i] != '') {
						selected = selectedRecords[i].split(';');
						selected.splice(selected.indexOf(''), 1);
						for (var j=0; j<selected.length; j++) {
							if (document.getElementById(selected[j])) {
								document.getElementById(selected[j]).checked = true;
							}
						}
					}
				}
				default_togglestate('selected_id'+id, 'selectall'+id);
			}
		} else {
			document.getElementById('numOfRows').value = count;
			document.getElementById('count').innerHTML = count;
			if (all_selected == 'all') {
				document.getElementById('linkForSelectAll').style.display = 'table-cell';
				document.getElementById('selectAllRec').style.display = 'none';
				document.getElementById('deSelectAllRec').style.display = 'inline';
				exculdedArray=excludedRecords.split(';');
				obj = document.getElementsByName('selected_id');
				viewForSelectLink = showSelectAllLink(obj, exculdedArray);
				document.getElementById('selectCurrentPageRec').checked = viewForSelectLink;
				document.getElementById('allselectedboxes').value = 'all';
				document.getElementById('excludedRecords').value = document.getElementById('excludedRecords').value+excludedRecords;
			} else {
				document.getElementById('linkForSelectAll').style.display = 'none';
				update_selected_checkbox();
			}
		}
		if (result[1] != '') {
			alert(result[1]);
		}
		document.getElementById('basicsearchcolumns').innerHTML = '';
	});
}
//for multiselect check box in list view:

function check_object(sel_id, groupParentElementId) {
	if (document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View) {
		var selected = trim(document.getElementById('selectedboxes_'+groupParentElementId).value);
		var skip = document.getElementById('excludedRecords_'+groupParentElementId).value;
	} else {
		selected = trim(document.getElementById('allselectedboxes').value);
		skip = document.getElementById('excludedRecords').value;
	}
	var select_global = new Array();
	select_global = selected.split(';');
	var box_value = sel_id.checked;
	var id = sel_id.value;
	var duplicate = select_global.indexOf(id);
	var size = select_global.length-1;
	var result = '';
	if (box_value == true) {
		if (document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View && document.getElementById('selectedboxes_'+groupParentElementId).value == 'all') {
			document.getElementById('excludedRecords_'+groupParentElementId).value = skip.replace(skip.match(id+';'), '');
			document.getElementById('selectedboxes_'+groupParentElementId).value = 'all';
		} else if (document.getElementById('allselectedboxes').value == 'all') {
			document.getElementById('excludedRecords').value = skip.replace(skip.match(id+';'), '');
			document.getElementById('allselectedboxes').value = 'all';
		} else {
			if (duplicate == '-1') {
				select_global[size] = id;
			}

			size=select_global.length-1;
			for (i=0; i<=size; i++) {
				if (trim(select_global[i])!='') {
					result=select_global[i]+';'+result;
				}
			}
			//default_togglestate(sel_id.name,groupParentElementId);
			if (document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View) {
				document.getElementById('selectedboxes_'+groupParentElementId).value = result;
			} else {
				document.getElementById('allselectedboxes').value = result;
			}
		}
		default_togglestate(sel_id.name, groupParentElementId);
	} else {
		if (document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View && document.getElementById('selectedboxes_'+groupParentElementId).value == 'all') {
			document.getElementById('excludedRecords_'+groupParentElementId).value = id+';'+skip;
			document.getElementById('selectedboxes_'+groupParentElementId).value = 'all';
		} else if (document.getElementById('allselectedboxes').value == 'all') {
			document.getElementById('excludedRecords').value = id+';'+skip;
			document.getElementById('allselectedboxes').value = 'all';
		} else {
			if (duplicate != '-1') {
				select_global.splice(duplicate, 1);
			}

			size = select_global.length-1;
			var i=0;
			for (i=size; i>=0; i--) {
				if (trim(select_global[i])!='') {
					result = select_global[i]+';'+result;
				}
			}
			default_togglestate(sel_id.name, groupParentElementId);
			if (document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View) {
				document.getElementById('selectedboxes_'+groupParentElementId).value = result;
			} else {
				document.getElementById('allselectedboxes').value = result;
			}
		}
		if (document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View) {
			document.getElementById('currentPageRec_'+groupParentElementId).checked = false;
		} else {
			document.getElementById('selectCurrentPageRec').checked = false;
		}
	}
}

function update_selected_checkbox() {
	var cur = document.getElementById('current_page_boxes').value;
	var tocheck = document.getElementById('allselectedboxes').value;
	var cursplit = new Array();
	cursplit = cur.split(';');

	var selsplit = new Array();
	selsplit = tocheck.split(';');

	//	var n=selsplit.length;
	var selectCurrentPageRecCheckValue = true;
	for (var j=0; j<cursplit.length; j++) {
		if (selsplit.indexOf(cursplit[j])!= '-1') {
			document.getElementById(cursplit[j]).checked = 'true';
		} else {
			selectCurrentPageRecCheckValue = false;
		}
	}
	if (selectCurrentPageRecCheckValue && cursplit.length>0) {
		document.getElementById('selectCurrentPageRec').checked = 'true';
	}
}

//Function to Set the status as Approve/Deny for Public access by Admin
function ChangeCustomViewStatus(viewid, now_status, changed_status, module, parenttab) {
	document.getElementById('status').style.display = 'block';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=CustomView&action=CustomViewAjax&file=ChangeStatus&dmodule=' + module + '&record=' + viewid + '&status=' + changed_status
	}).done(function (response) {
		var responseVal = response;
		if (responseVal.indexOf(':#:FAILURE') > -1) {
			alert('Failed');
		} else if (responseVal.indexOf(':#:SUCCESS') > -1) {
			var customview_ele = document.getElementById('viewname');
			showDefaultCustomView(customview_ele, module, parenttab);
		} else {
			document.getElementById('ListViewContents').innerHTML = responseVal;
		}
		document.getElementById('status').style.display = 'none';
	});
}

function getListViewCount(module, element, parentElement, url) {
	var i=0;
	var elementList = '';
	if (module != 'Documents') {
		elementList = document.getElementsByName(module+'_listViewCountRefreshIcon');
		for (i=0; i<elementList.length; ++i) {
			elementList[i].style.display = 'none';
		}
	} else {
		element.style.display = 'none';
	}
	elementList = document.getElementsByName(module+'_listViewCountContainerBusy');
	for (i=0; i<elementList.length; ++i) {
		elementList[i].style.display = '';
	}
	element = document.getElementsByName('search_url')[0];
	var searchURL = '';
	if (typeof element !='undefined') {
		searchURL = element.value;
	} else if (typeof document.getElementsByName('search_text')[0] != 'undefined') {
		element = document.getElementsByName('search_text')[0];
		var searchField = document.getElementsByName('search_field')[0];
		if (element.value.length > 0) {
			searchURL = '&query=true&searchtype=BasicSearch&search_field='+encodeURIComponent(searchField.value)+'&search_text='+encodeURIComponent(element.value);
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
			for (i=0; i<elementList.length;) {
				//No need to increment the count, as the element will be eliminated in the next step.
				elementList[i].parentNode.innerHTML = response;
			}
		} else {
			parentElement.innerHTML = response;
		}
	});
}

function VT_disableFormSubmit(evt) {
	evt = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if ((evt.keyCode == 13) && (node.type=='text')) {
		node.onchange();
		return false;
	}
	return true;
}

var statusPopupTimer = null;
function closeStatusPopup(elementid) {
	statusPopupTimer = setTimeout('document.getElementById(\'' + elementid + '\').style.display = \'none\';', 50);
}

function updateCampaignRelationStatus(relatedmodule, campaignid, crmid, campaignrelstatusid, campaignrelstatus) {
	VtigerJS_DialogBox.showbusy();
	document.getElementById('campaignstatus_popup_' + crmid).style.display = 'none';
	var data = 'action=updateRelationsAjax&module=Campaigns&relatedmodule=' + relatedmodule + '&campaignid=' + campaignid + '&crmid=' + crmid + '&campaignrelstatusid=' + campaignrelstatusid;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+data
	}).done(function (response) {
		if (response.indexOf(':#:FAILURE')>-1) {
			alert(alert_arr.ERROR_WHILE_EDITING);
		} else if (response.indexOf(':#:SUCCESS')>-1) {
			document.getElementById('campaignstatus_' + crmid).innerHTML = campaignrelstatus;
			VtigerJS_DialogBox.hidebusy();
		}
	});
}

function loadCvList(type, id) {
	var element = type+'_cv_list';
	var value = document.getElementById(element).value;
	var filter = document.getElementById(element)[document.getElementById(element).selectedIndex].value;
	if (filter=='None') {
		return false;
	}
	if (value != '') {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Campaigns&action=CampaignsAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+value
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			var element = document.getElementById('RLContents');
			element.innerHTML = response;
			vtlib_executeJavascriptInElement(element);
		});
	}
}

function emptyCvList(type, id) {
	if (confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)) {
		document.getElementById('status').style.display='inline';
		var relidsselected = get_cookie(type+'_all');
		if (relidsselected == '' || document.getElementById('Campaigns_'+type+'_selectallActivate').value == 'true') {
			var idlist = 'All';
		} else {
			var idlist = relidsselected;
		}
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Campaigns&action=CampaignsAjax&file=updateRelations&ajax=true&parentid='+id+'&destination_module='+type+'&mode=delete',
			data : {
				'idlist' : idlist
			}
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			var element = document.getElementById('RLContents');
			element.innerHTML = response;
			vtlib_executeJavascriptInElement(element);
			set_cookie(type+'_all', '');
		});
	}
}

function mailer_export() {
	var module = document.getElementById('curmodule').value;
	gotourl('index.php?module='+module+'&action=MailerExport&from='+module+'&step=ask');
	return false;
}

function checkgroup() {
	if (document.getElementById('group_checkbox').checked) {
		document.change_ownerform_name.lead_group_owner.style.display = 'block';
		document.change_ownerform_name.lead_owner.style.display = 'none';
	} else {
		document.change_ownerform_name.lead_owner.style.display = 'block';
		document.change_ownerform_name.lead_group_owner.style.display = 'none';
	}
}

function callSearch(searchtype) {
	for (var i = 1; i <= 26; i++) {
		var data_td_id = 'alpha_' + eval(i);
		getObj(data_td_id).className = 'searchAlph';
	}
	gPopupAlphaSearchUrl = '';
	var search_fld_val = document.getElementById('bas_searchfield').options[document.getElementById('bas_searchfield').selectedIndex].value;
	var search_txt_val = encodeURIComponent(document.basicSearch.search_text.value);
	var urlstring = '';
	if (searchtype == 'Basic') {
		var p_tab = document.getElementsByName('parenttab');
		urlstring = 'search_field=' + search_fld_val + '&searchtype=BasicSearch&search_text=' + search_txt_val + '&';
		urlstring = urlstring + 'parenttab=' + p_tab[0].value + '&';
	} else if (searchtype == 'Advanced') {
		checkAdvancedFilter();
		var advft_criteria = encodeURIComponent(document.getElementById('advft_criteria').value);
		var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;
		urlstring += '&advft_criteria=' + advft_criteria + '&advft_criteria_groups=' + advft_criteria_groups + '&';
		urlstring += 'searchtype=advance&';
	}
	document.getElementById('status').style.display = 'inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?' + urlstring + 'query=true&file=index&module=' + gVTModule + '&action=' + gVTModule + 'Ajax&ajax=true&search=true'
	}).done(function (response) {
		document.getElementById('status').style.display = 'none';
		var result = response.split('&#&#&#');
		var LVC = document.getElementById('ListViewContents');
		LVC.innerHTML = result[2];
		vtlib_executeJavascriptInElement(LVC);
		if (result[1] != '') {
			alert(result[1]);
		}
		document.getElementById('basicsearchcolumns').innerHTML = '';
	});
	return false;
}

function alphabetic(module, url, dataid) {
	for (var i = 1; i <= 26; i++) {
		var data_td_id = 'alpha_' + eval(i);
		getObj(data_td_id).className = 'searchAlph';
	}
	getObj(dataid).className = 'searchAlphselected';
	document.getElementById('status').style.display = 'inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=' + module + '&action=' + module + 'Ajax&file=index&ajax=true&search=true&' + url
	}).done(function (response) {
		document.getElementById('status').style.display = 'none';
		var result = response.split('&#&#&#');
		var LVC = document.getElementById('ListViewContents');
		LVC.innerHTML = result[2];
		vtlib_executeJavascriptInElement(LVC);
		if (result[1] != '') {
			alert(result[1]);
		}
		document.getElementById('basicsearchcolumns').innerHTML = '';
	});
}

function PositionDialogToCenter(ID) {
	var vpx, vpy;
	if (self.innerHeight) {// Mozilla, FF, Safari and Opera
		vpx = self.innerWidth;
		vpy = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) {//IE
		vpx = document.documentElement.clientWidth;
		vpy = document.documentElement.clientHeight;
	} else if (document.body) {// IE
		vpx = document.body.clientWidth;
		vpy = document.body.clientHeight;
	}

	//Calculate the length from top, left
	dialogTop = (vpy / 2 - 280 / 2) + document.documentElement.scrollTop;
	dialogLeft = (vpx / 2 - 280 / 2);

	//Position the Dialog to center
	document.getElementById(ID).style.top = dialogTop + 'px';
	document.getElementById(ID).style.left = dialogLeft + 'px';
	document.getElementById(ID).style.display = 'block';
}

function removeDiv(ID) {
	var node2Rmv = getObj(ID);
	if (node2Rmv) {
		node2Rmv.parentNode.removeChild(node2Rmv);
	}
}