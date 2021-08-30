/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

// Get User Default calendar variables
var Calendar_Default_Reminder_Minutes = 0; // false
GlobalVariable_getVariable('Calendar_Default_Reminder_Minutes', 0, 'cbCalendar', gVTUserID).then(function (response) {
	var obj = JSON.parse(response);
	Calendar_Default_Reminder_Minutes = obj.Calendar_Default_Reminder_Minutes;
}, function (error) {
	Calendar_Default_Reminder_Minutes = 0; // false
});

function fnAddITSEvent(obj, CurrObj, start_date, end_date, start_hr, start_min, start_fmt, end_hr, end_min, end_fmt, viewOption, subtab, eventlist) {
	var tagName = document.getElementById(CurrObj);
	var left_Side = findPosX(obj);
	var top_Side = findPosY(obj);
	tagName.style.left= left_Side + 'px';
	tagName.style.top= top_Side + 22+ 'px';
	tagName.style.display = 'block';
	eventlist = eventlist.split(';');
	for (var i=0; i<(eventlist.length-1); i++) {
		document.getElementById('add'+eventlist[i].toLowerCase()).href='javascript:gITSshow(\'addITSEvent\',\''+eventlist[i]+'\',\''+start_date+'\',\''+end_date+'\',\''+start_hr+'\',\''+start_min+'\',\''+start_fmt+'\',\''+end_hr+'\',\''+end_min+'\',\''+end_fmt+'\',\''+viewOption+'\',\''+subtab+'\',\'\');fnRemoveITSEvent();';
	}
}

function fnRemoveITSEvent() {
	document.getElementById('addEventDropDown').style.display = 'none';
}
function fnRemoveITSButton() {
	document.getElementById('addButtonDropDown').style.display = 'none';
}
function fnShowITSEvent() {
	document.getElementById('addEventDropDown').style.display= 'block';
}
function fnShowITSButton() {
	document.getElementById('addButtonDropDown').style.display= 'block';
}

function gITSshow(argg1, type, startdate, enddate, starthr, startmin, startfmt, endhr, endmin, endfmt, viewOption, subtab, skipconvertendtime) {
	let url = 'index.php?action=EditView&module=cbCalendar&Module_Popup_Edit=1';
	let shr = parseInt(starthr, 10);
	let smin = parseInt(startmin, 10);
	smin = smin - (smin%5);
	smin = _2digit(smin);
	if (startfmt=='pm' && shr != 12) {
		shr = shr + 12;
	}
	shr = _2digit(shr);
	let ehr = parseInt(endhr, 10);
	let emin = parseInt(endmin, 10);
	emin = emin - (emin%5);
	if (endfmt=='pm' && ehr != 12) {
		ehr = ehr + 12;
	}
	ehr = _2digit(ehr);
	emin = _2digit(emin);
	url = url + '&dtstart=' + startdate + ' ' + shr + ':' + smin;
	url = url + '&dtend=' + enddate + ' ' + ehr + ':' + emin;
	url = url + '&activitytype=' + encodeURIComponent(type);
	window.open(url, null, cbPopupWindowSettings + ',dependent=yes');
}

function graphicalCalendarRefresh() {
	jQuery('#calendar_div').fullCalendar('refetchEvents');
	VtigerJS_DialogBox.unblock();
}

function getITSCalSettings() {
	var url = getITSCalURL();
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Calendar4You&action=ActivityAjax' + url + '&type=settings&ajax=true',
	}).done(function (response) {
		document.getElementById('calSettings').innerHTML = response;
	});
}

function showEventIcon(icon_id) {
	jQuery('#' + icon_id).show();
}

function hideEventIcon(icon_id) {
	jQuery('#' + icon_id).hide();
}

function getITSCalURL() {
	var user_view_type = jQuery('#user_view_type :selected').val();
	var url = '&user_view_type='+user_view_type;
	var view_val = jQuery('#calendar_div').fullCalendar('getView');
	url += '&view='+view_val.name;
	var cal_date = jQuery('#calendar_div').fullCalendar('getDate');
	var year_val = new Date(cal_date).getFullYear();
	url += '&year='+year_val;
	var month_val = new Date(cal_date).getMonth();
	month_val = (month_val * 1) + 1;
	url += '&month='+month_val;
	var day_val = new Date(cal_date).getDate();
	url += '&day='+day_val;
	return url;
}

function loadITSEventSettings(el, mode, id) {
	document.getElementById('event_setting').innerHTML = '<img src=\'themes/images/vtbusy.gif\'>';
	fnvshobj(el, 'event_setting');
	var url = getITSCalURL();
	url += '&mode=' + mode + '&id=' + id;
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Calendar4You&action=ActivityAjax' + url + '&type=event_settings&ajax=true'
	}).done(function (response) {
		document.getElementById('event_setting').innerHTML = response;
	});
}

function saveITSEventSettings() {
	formSelectColumnString('day_selected_fields', 'selected_day_fields');
	formSelectColumnString('week_selected_fields', 'selected_week_fields');
	formSelectColumnString('month_selected_fields', 'selected_month_fields');
}

function changeInstallType(type) {
	document.getElementById('next_button').disabled = false;
	document.getElementById('next_button').style.display = 'inline';
	if (type == 'express') {
		var bad_files_count = document.getElementById('bad_files').value;
		if (bad_files_count != '0') {
			document.getElementById('next_button').disabled = true;
			document.getElementById('next_button').style.display = 'none';
		}
		document.getElementById('total_steps').innerHTML = '4';
	} else if (type == 'custom') {
		document.getElementById('total_steps').innerHTML = '5';
	}
}

function showGoogleSyncAccDiv(value) {
	if (value != '') {
		fnShowDrop('google_sync_acc_div');
	} else {
		fnHideDrop('google_sync_acc_div');
	}
}

function controlGoogleSync() {
	if (document.getElementById('google_apikey')) {
		var google_login_val = document.getElementById('google_login').value;
		var google_apikey_val = document.getElementById('google_apikey').value;
		var google_clientid_val = document.getElementById('google_clientid').value;
		var google_keyfile_val = document.getElementById('google_keyfile').value;
		var google_refresh = document.getElementById('google_refresh').value;
		if (document.getElementById('googleinsert').checked) {
			var googleinsert =1;
			document.getElementById('googleinsert').value=1;
		} else {
			googleinsert=0;
			document.getElementById('googleinsert').value=0;
		}
		if (google_login_val != '' && google_apikey_val != '' && google_clientid_val != '' && google_keyfile_val != '') {
			fnShowDrop('google_sync_verifying');
			fnHideDrop('google_sync_text');
			jQuery.ajax({
				method:'POST',
				url:'index.php?module=Calendar4You&action=Calendar4YouAjax&file=GoogleSync4YouControl&ajax=true&login=' + google_login_val + '&apikey=' + google_apikey_val + '&keyfile=' + google_keyfile_val + '&clientid=' + google_clientid_val + '&refresh_token=' + google_refresh + '&googleinsert=' + googleinsert
			}).done(function (response) {
				if (google_refresh == '') {
					document.getElementById('google_sync_text').style.color = '#000000';
					document.forms['SharingForm'].submit();
				} else {
					var result = JSON.parse(response);
					document.getElementById('google_sync_text').innerHTML = result['text'];
					fnHideDrop('google_sync_verifying');
					fnShowDrop('google_sync_text');
					if (result['status'] != 'ok') {
						document.getElementById('google_sync_text').style.color = 'red';
						return false;
					} else {
						document.getElementById('google_sync_text').style.color = '#000000';
						document.forms['SharingForm'].submit();
					}
				}
			});
		} else {
			document.forms['SharingForm'].submit();
		}
	} else {
		document.forms['SharingForm'].submit();
	}
}

function changeGoogleAccount() {
	fnShowDrop('google_account_change_div');
	fnHideDrop('google_account_info_div');
	document.getElementById('update_google_account').value = '1';
}

function cleartokens(uid) {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Calendar4You&action=Calendar4YouAjax&file=cleartokens&uid='+uid,
	}).done(function (response) {
		window.location.reload();
	});
}

function insertIntoCRM(userid, eventid, eventtype, geventid, start_date, end_date, start_hr, start_min, start_fmt, end_hr, end_min, end_fmt) {
	var el_div = '';
	if (eventtype == 'todo') {
		el_div = 'createTodo';
	} else {
		el_div = 'addITSEvent';
	}
	gITSshow(el_div, eventtype, start_date, end_date, start_hr, start_min, start_fmt, end_hr, end_min, end_fmt, 'hourview', '', 'skip');
	var title_val = document.getElementById('google_info_'+eventid+'_title').innerHTML;
	var desc_val = document.getElementById('google_info_'+eventid+'_desc').innerHTML;
	var location_val = document.getElementById('google_info_'+eventid+'_location').innerHTML;
	if (eventtype == 'todo') {
		document.createTodo.task_subject.value = title_val;
		document.createTodo.task_description.value = desc_val;
		document.createTodo.geventid.value = geventid;
		document.createTodo.gevent_type.value = eventtype;
		document.createTodo.gevent_userid.value = userid;
	} else {
		document.EditView.subject.value = title_val;
		document.EditView.description.value = desc_val;
		document.EditView.location.value = location_val;
		document.EditView.geventid.value = geventid;
		document.EditView.gevent_type.value = eventtype;
		document.EditView.gevent_userid.value = userid;
	}
}

function exportCalendar() {
	if (document.getElementsByName('exportCalendar')[0].value == 'iCal') {
		var filename = document.getElementById('ics_filename').value;
		VtigerJS_DialogBox.block();
		var url = 'index.php?module=cbCalendar&action=iCalExport&filename='+filename;
		location.href = url;
		VtigerJS_DialogBox.unblock();
		ghide('CalExport');
	}
}

function importCalendar() {
	var file = document.getElementById('ics_file').value;
	if (file != '') {
		if (file.indexOf('.ics') != (file.length - 4)) {
			alert(alert_arr.PLS_SELECT_VALID_FILE+'.ics');
		} else {
			document.ical_import.action.value='iCalImport';
			document.ical_import.module.value='cbCalendar';
			document.ical_import.submit();
		}
	}
}
