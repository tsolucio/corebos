/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//// Get User Default calendar variables
var calendar_call_default_duration = 5; // minutes
GlobalVariable_getVariable('Calendar_call_default_duration', 5, 'Calendar', gVTUserID).then(function (response) {
	var obj = JSON.parse(response);
	calendar_call_default_duration = obj.Calendar_call_default_duration;
}, function (error) {
	calendar_call_default_duration = 5; // minutes
});
var calendar_other_default_duration = 1; // hours
GlobalVariable_getVariable('Calendar_other_default_duration', 1, 'Calendar', gVTUserID).then(function (response) {
	var obj = JSON.parse(response);
	calendar_other_default_duration = obj.Calendar_other_default_duration;
}, function (error) {
	calendar_other_default_duration = 1; // hours
});

function changeEndtime_StartTime() {
	if (dateTimeFieldComparison('dtstart', 'SStart', 'dtend', 'EEnd', 'LE', false)) {
		return true; // nothing to do
	}
	let type = document.getElementById('activitytype').value;
	let datetimestart = getObj('dtstart');
	let dtarray = datetimestart.value.split(' ');
	let datestart = dtarray[0];
	let timestart = dtarray[1];
	let dateval1 = datestart.replace(/^\s+/g, '').replace(/\s+$/g, '');
	let dateelements1 = splitDateVal(dateval1);
	let dd1 = parseInt(dateelements1[0], 10);
	let mm1 = dateelements1[1];
	let yyyy1 = dateelements1[2];
	let date1 = new Date();
	//date1.setDate(dd1+1);
	date1.setYear(yyyy1);
	date1.setMonth(mm1 - 1, dd1 + 1);
	let tempdate = getdispDate(date1);
	let date = datestart;
	let timearray = timestart.split(':');
	let hour = parseInt(timearray[0], 10);
	let min = parseInt(timearray[1], 10);
	let fmt = document.EditView.inputtimefmt_dtstart.value;
	if (type != 'Call') {
		var day_change_hour = 12 - +calendar_other_default_duration;
		if (fmt == 'PM') {
			if (hour >= day_change_hour) {
				date = tempdate;
				hour = hour - +day_change_hour;
				//min = min;
				fmt = 'AM';
			} else if (hour == 12) {
				hour = 1;
				//min = min;
				fmt = 'PM';
			} else {
				hour = +hour + +calendar_other_default_duration;
			}
			hour = _2digit(hour);
			min = _2digit(min);
			setCalendarDateFields(date, hour, min, fmt);
		} else if (fmt == 'AM') {
			if (hour >= day_change_hour) {
				hour = hour - +day_change_hour;
				//min = min;
				fmt = 'PM';
			} else if (hour == 12) {
				hour = 1;
				//min = min;
				fmt = 'AM';
			} else {
				hour = +hour + +calendar_other_default_duration;
			}
			hour = _2digit(hour);
			min = _2digit(min);
			setCalendarDateFields(date, hour, min, fmt);
		} else {
			hour = +hour + +calendar_other_default_duration;
			if (hour >= 24) {
				hour = 0;
				date = tempdate;
			}
			hour = _2digit(hour);
			min = _2digit(min);
			setCalendarDateFields(date, hour, min, fmt);
		}
	}
	if (type == 'Call') {
		var hour_change_minute = 60 - +calendar_call_default_duration;
		if (fmt == 'PM') {
			if (hour == 11 && min == hour_change_minute) {
				hour = 12;
				min = 0;
				fmt = 'AM';
				date = tempdate;
			} else if (hour == 12 && min == hour_change_minute) {
				hour = 1;
				min = 0;
				fmt = 'PM';
			} else {
				if (min >= hour_change_minute) {
					min = min - hour_change_minute;
					hour = hour + 1;
				} else {
					min = +min + +calendar_call_default_duration;
				}
			}
			hour = _2digit(hour);
			min = _2digit(min);
			setCalendarDateFields(date, hour, min, fmt);
		} else if (fmt == 'AM') {
			if (hour == 11 && min == hour_change_minute) {
				hour = 12;
				min = 0;
				fmt = 'PM';
			} else if (hour == 12 && min == hour_change_minute) {
				hour = 1;
				min = 0;
				fmt = 'AM';
			} else {
				if (min >= hour_change_minute) {
					min = min - hour_change_minute;
					hour = hour + 1;
				} else {
					min = +min + +calendar_call_default_duration;
				}
			}
			hour = _2digit(hour);
			min = _2digit(min);
			setCalendarDateFields(date, hour, min, fmt);
		} else {
			if (min >= hour_change_minute) {
				min = min - hour_change_minute;
				hour = hour + 1;
			} else {
				min = +min + +calendar_call_default_duration;
			}
			if (hour == 24) {
				hour = 0;
				date = tempdate;
			}
			hour = _2digit(hour);
			min = _2digit(min);
			setCalendarDateFields(date, hour, min, fmt);
		}
	}
}

function getdispDate(tempDate) {
	var dd = _2digit(parseInt(tempDate.getDate(), 10));
	var mm = _2digit(parseInt(tempDate.getMonth(), 10)+1);
	var yy = tempDate.getFullYear();
	if (userDateFormat == 'dd-mm-yyyy') {
		return dd+'-'+mm+'-'+yy;
	} else if (userDateFormat == 'mm-dd-yyyy') {
		return mm+'-'+dd+'-'+yy;
	} else {
		return yy+'-'+mm+'-'+dd;
	}
}

function setCalendarDateFields(date, hour, min, fmt) {
	document.EditView.dtend.value = date + ' ' + hour + ':' + min;
	document.EditView.calendar_repeat_limit_date.value = date;
	document.EditView.inputtimefmt_dtend.value = fmt;
	document.getElementById('timefmt_dtend').innerHTML = (fmt != '24' ? fmt : '');
}

function open_filtered_contactsIfAccounts(fromlink, fldname, MODULE, ID) {
	var rel_type = '';
	var rel_id = '';
	var rel_id_elem = document.getElementById('rel_id');
	if (rel_id_elem) {
		rel_id = rel_id_elem.value;
		rel_type = document.getElementById('rel_id_type').value;
	} else {
		rel_id_elem = document.getElementById('dtlview_rel_id');
		if (rel_id_elem) {
			rel_id_elem = rel_id_elem.querySelector('span[type=vtlib_metainfo]');
			if (rel_id_elem) {
				rel_id = rel_id_elem.getAttribute('vtrecordid');
				rel_type = rel_id_elem.getAttribute('vtmodule');
			}
		}
	}
	var valmod = (rel_type=='Accounts' || rel_type=='Potentials' || rel_type=='Quotes' || rel_type=='PurchaseOrder' || rel_type=='Vendors' || rel_type=='SalesOrder' || rel_type=='Invoice' || rel_type=='Campaigns' || rel_type=='Products');
	if (rel_id != '' && valmod) {
		var BasicSearch = '&parent_module='+rel_type+'&relmod_id='+rel_id; // special hard coded contact search
		window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&form=vtlibPopupView&forfield='+fldname+'&srcmodule='+MODULE+'&forrecord='+ID+BasicSearch, 'vtlibui10', 'width=780,height=652,resizable=0,scrollbars=0,top=150,left=200');
	} else {
		vtlib_open_popup_window('', 'cto_id', 'cbCalendar', '');
	}
}

document.addEventListener('DOMContentLoaded', function (event) {
	let fldstart = document.getElementById('jscal_field_dtstart');
	if (fldstart != undefined) {
		fldstart.onchange = changeEndtime_StartTime;
		let timefmtstart = document.getElementById('inputtimefmt_dtstart');
		timefmtstart.onchange = changeEndtime_StartTime;
	}
});
