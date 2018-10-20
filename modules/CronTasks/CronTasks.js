/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
************************************************************************************/

function fetchSaveCron(id) {
	var status = document.getElementById('cron_status').value;
	var timeValue;
	var dailyTime = document.getElementById('CronDay').value;
	var time = document.getElementById('cron_time').value;
	if (time == 'daily') {
		timeValue = 24;
	} else {
		timeValue = document.getElementById('CronTime').value;
	}
	var min_freq =parseInt(document.getElementById('min_freq').value, 10);
	if (!numValidate('CronTime', '', 'any', true)) {
		return false;
	}
	if ((timeValue % 1) !=0) {
		alert(alert_arr.INTEGERVALS);
		return false;
	}
	if (((dailyTime.search(/^\d{2}:\d{2}$/) == -1) || (dailyTime.substr(0, 2) < 0 || dailyTime.substr(0, 2) > 24) || (dailyTime.substr(3, 2) < 0 || dailyTime.substr(3, 2) > 59)) && time == 'daily') {
		alert(alert_arr.ERR_INVALID_TIME);
		return false;
	}
	if ((timeValue < min_freq && time == 'min') || timeValue <= 0 || timeValue == '' ) {
		alert(document.getElementById('desc').value);
	} else {
		document.getElementById('editdiv').style.display='none';
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=CronTasksAjax&module=CronTasks&file=SaveCron&record='+id+'&status='+status+'&timevalue='+timeValue+'&time='+time+'&dailytime='+dailyTime
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('notifycontents').innerHTML=response;
		});
	}
}
function change_input_time() {
	var time = document.getElementById('cron_time').value;
	if (time == 'daily') {
		document.getElementById('CronTime').style.display='none';
		document.getElementById('CronDay').style.display='inline';
	} else {
		document.getElementById('CronTime').style.display='inline';
		document.getElementById('CronDay').style.display='none';
	}
}
function fetchEditCron(id) {
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=CronTasksAjax&module=CronTasks&file=EditCron&record='+id
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('editdiv').innerHTML=response;
	});
}
function move_module(tabid, move) {
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=CronTasks&action=CronTasksAjax&file=CronSequence&parenttab=Settings&record='+tabid+'&move='+move
	}).done(function (response) {
		document.getElementById('notifycontents').innerHTML=response;
	});
}
