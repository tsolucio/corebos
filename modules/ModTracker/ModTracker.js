/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function addSearchModTrackerField(fieldoption, criteriaoption) {
	var tableName = document.getElementById('addModTrackerSrc');
	var prev = tableName.rows.length;
	var count = prev;
	var row = tableName.insertRow(prev);
	if (count%2) {
		row.className = 'dvtCellLabel';
	} else {
		row.className = 'dvtCellInfo';
	}

	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	var colthree = row.insertCell(2);

	colone.innerHTML='<select id=\'modtrac_fields'+count+'\' name=\'modtrac_fields'+count+'\' class=\'detailedViewTextBox\'>'+fieldoption+'</select>';
	coltwo.innerHTML='<select id=\'modtrac_condition'+count+'\' name=\'modtrac_condition'+count+'\' class=\'detailedViewTextBox\'>'+criteriaoption+'</select> ';
	colthree.innerHTML='<input type=\'text\' id=\'modtrac_src_value'+count+'\' name=\'modtrac_src_value'+count+'\' class=\'detailedViewTextBox\'>';
}

function delRow() {
	var tableName = document.getElementById('addModTrackerSrc');
	var prev = tableName.rows.length;
	if (prev > 1) {
		document.getElementById('addModTrackerSrc').deleteRow(prev-1);
	}
}

function searchModTrackerResult() {
	var filterRows = document.getElementById('addModTrackerSrc').rows.length;
	var urlstring = getFilterList();
	var reportname = document.getElementById('reportname').value;
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=ModTracker&action=ModTrackerAjax&ajax=true&file=Report&'+urlstring+'&filtercount='+filterRows+'&reportname='+reportname
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modtrac_reportInnerContents').innerHTML= response;
	});
}

function getListViewModTrackerEntries_js(reportname, url) {
	var filterRows = document.getElementById('addModTrackerSrc').rows.length;
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=ModTracker&action=ModTrackerAjax&ajax=true&file=Report&'+url+'&reportname='+reportname+'&filtercount='+filterRows
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modtrac_reportInnerContents').innerHTML= response;
	});
}

function getFilterList() {
	var filterRows = document.getElementById('addModTrackerSrc').rows.length;
	var urlstring = '';
	if (filterRows == 1) {
		var value = getObj('modtrac_src_value0').value;
		if (!(value == null || value == '')) {
			for (var i=0; i<filterRows; i++) {
				var modtrac_searchfield = getObj('modtrac_fields'+i);
				var modtrac_condition = getObj('modtrac_condition'+i);
				var modtrac_searchfieldvalue = getObj('modtrac_src_value'+i);
				urlstring = urlstring+'field'+i+'='+modtrac_searchfield.value+'&';
				urlstring = urlstring+'condition'+i+'='+modtrac_condition.value.replace(/\\'/g, '')+'&';
				urlstring = urlstring+'value'+i+'='+encodeURIComponent(modtrac_searchfieldvalue.value)+'&';
			}
		}
	}
	return urlstring;
}

function ModTracker_CreatePDF(reportname) {
	var filterRows = document.getElementById('addModTrackerSrc').rows.length;
	var urlstring = getFilterList();
	return 'index.php?module=ModTracker&action=ModTrackerAjax&file=CreateModTrackerPDF&'+urlstring+'&reportname='+reportname+'&mode=createpdf&filtercount='+filterRows;
}

function ModTracker_CreateXL(reportname) {
	var filterRows = document.getElementById('addModTrackerSrc').rows.length;
	var urlstring = getFilterList();
	return 'index.php?module=ModTracker&action=ModTrackerAjax&file=CreateModTrackerXL&'+urlstring+'&reportname='+reportname+'&mode=createxl&filtercount='+filterRows;
}

function toggleModule_mod(tabid, action) {
	document.getElementById('status').style.display='block';
	var data = 'module=ModTracker&action=ModTrackerAjax&file=BasicSettings&tabid='+tabid+'&status='+action+'&ajax=true';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+data
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('modTrackerContents').innerHTML = response;
	});
}
