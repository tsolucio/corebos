/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function showhide(argg) {
	var x=document.getElementById(argg).style;
	if (x.display=='none') {
		x.display='block';
	} else {
		x.display='none';
	}
}

function gshow(argg1, type, startdate, enddate, starthr, startmin, startfmt, endhr, endmin, endfmt, viewOption, subtab) {
	var smin = parseInt(startmin, 10);
	smin = smin - (smin % 5);
	var y = document.getElementById(argg1).style;
	if (type != 'todo' && type != '') {
		for (var i = 0; ; i++) {
			if (document.EditView.activitytype[i].value == type) {
				document.EditView.activitytype[i].selected = 'yes';
				triggerOnChangeHandler(('activitytype'));
				break;
			}
		}
		smin = _2digit(smin);
		document.EditView.date_start.value = startdate;
		document.EditView.starthr.value = starthr;
		document.EditView.startmin.value = smin;
		document.EditView.startfmt.value = startfmt;
		document.EditView.viewOption.value = viewOption;
		document.EditView.subtab.value = subtab;
		changeEndtime_StartTime();
	}
	if (type == 'todo') {
		smin = _2digit(smin);
		document.createTodo.task_date_start.value = startdate;
		document.createTodo.task_due_date.value = enddate;
		document.createTodo.starthr.value = starthr;
		document.createTodo.startmin.value = smin;
		document.createTodo.startfmt.value = startfmt;
		document.createTodo.viewOption.value = viewOption;
		document.createTodo.subtab.value = subtab;
	}
	if (y.display == 'none') {
		y.display = 'block';
	}
}

function rptoptDisp(Opt) {
	var currOpt = Opt.options[Opt.selectedIndex].value;
	if (currOpt == 'Daily' || currOpt == 'Yearly') {
		ghide('repeatWeekUI');
		ghide('repeatMonthUI');
	} else if (currOpt == 'Weekly') {
		document.getElementById('repeatWeekUI').style.display = 'block';
		ghide('repeatMonthUI');
	} else if (currOpt == 'Monthly') {
		ghide('repeatWeekUI');
		document.getElementById('repeatMonthUI').style.display = 'block';
	}
}

function ghide(argg2) {
	var z=document.getElementById(argg2).style;
	if (z.display=='block' ) {
		z.display='none';
	}
}

function switchClass(myModule, toStatus) {
	var x=document.getElementById(myModule);
	if ((x)) {
		if (toStatus=='on') {
			x.className='dvtSelectedCell';
		}
		if (toStatus=='off') {
			x.className='dvtUnSelectedCell';
		}
	}
}

function enableCalstarttime() {
	document.SharingForm.start_hour.disabled = !document.SharingForm.sttime_check.checked;
}

var moveupLinkObj, moveupDisabledObj, movedownLinkObj, movedownDisabledObj;

function userEventSharing(selectedusrid, selcolid) {
	document.getElementById('activity_view').disabled=false;
	document.getElementById('dayoftheweek').disabled=false;
	document.getElementById('user_view').disabled=false;
	document.getElementById('start_hour').disabled=false;
	formSelectColumnString(selectedusrid, selcolid);
}

function incUser(avail_users, sel_users) {
	availListObj=getObj(avail_users);
	selectedColumnsObj=getObj(sel_users);

	for (var i=0; i<selectedColumnsObj.length; i++) {
		selectedColumnsObj.options[i].selected=false;
	}
	for (i=0; i<availListObj.length; i++) {
		if (availListObj.options[i].selected) {
			var rowFound = false;
			var existingObj = null;
			for (var j=0; j<selectedColumnsObj.length; j++) {
				if (selectedColumnsObj.options[j].value==availListObj.options[i].value) {
					rowFound=true;
					existingObj=selectedColumnsObj.options[j];
					break;
				}
			}
			if (!rowFound) {
				var newColObj=document.createElement('OPTION');
				newColObj.value=availListObj.options[i].value;
				if (browser_ie) {
					newColObj.innerText=availListObj.options[i].innerText;
				} else if (browser_nn4 || browser_nn6) {
					newColObj.text=availListObj.options[i].text;
				}
				selectedColumnsObj.appendChild(newColObj);
				availListObj.options[i].selected=false;
				newColObj.selected=true;
			} else {
				if (existingObj != null) {
					existingObj.selected=true;
				}
			}
		}
	}
}

function rmvUser(sel_users) {
	selectedColumnsObj=getObj(sel_users);
	var selectlength=selectedColumnsObj.options.length;
	for (var i = 0; i <= selectlength; i++) {
		if (selectedColumnsObj.options.selectedIndex >= 0) {
			selectedColumnsObj.remove(selectedColumnsObj.options.selectedIndex);
		}
	}
}

function formSelectColumnString(usr, col) {
	var selectedColumnsObj=getObj(col);
	var usr_id = document.getElementById(usr);
	var selectedColStr = '';
	for (var i=0; i<selectedColumnsObj.options.length; i++) {
		selectedColStr += selectedColumnsObj.options[i].value + ';';
	}
	usr_id.value = selectedColStr;
}

function fnRedirect() {
	var OptionData = document.getElementById('view_Option').options[document.getElementById('view_Option').selectedIndex].value;
	if (OptionData == 'listview') {
		document.EventViewOption.action.value = 'index';
		VtigerJS_DialogBox.block();
		window.document.EventViewOption.submit();
	}
	if (OptionData == 'hourview') {
		document.EventViewOption.action.value = 'index';
		VtigerJS_DialogBox.block();
		window.document.EventViewOption.submit();
	}
}

function fnAddEvent(obj, CurrObj, start_date, end_date, start_hr, start_min, start_fmt, end_hr, end_min, end_fmt, viewOption, subtab, eventlist) {
	var tagName = document.getElementById(CurrObj);
	var left_Side = findPosX(obj);
	var top_Side = findPosY(obj);
	tagName.style.left= left_Side  + 'px';
	tagName.style.top= top_Side + 22+ 'px';
	tagName.style.display = 'block';
	eventlist = eventlist.split(';');
	for (var i=0; i<(eventlist.length-1); i++) {
		document.getElementById('add'+eventlist[i].toLowerCase()).href='javascript:gshow(\'addEvent\',\''+eventlist[i]+'\',\''+start_date+'\',\''+end_date+'\',\''+start_hr+'\',\''+start_min+'\',\''+start_fmt+'\',\''+end_hr+'\',\''+end_min+'\',\''+end_fmt+'\',\''+viewOption+'\',\''+subtab+'\');fnRemoveEvent();';
	}
	document.getElementById('addtodo').href='javascript:gshow(\'createTodo\',\'todo\',\''+start_date+'\',\''+end_date+'\',\''+start_hr+'\',\''+start_min+'\',\''+start_fmt+'\',\''+end_hr+'\',\''+end_min+'\',\''+end_fmt+'\',\''+viewOption+'\',\''+subtab+'\');fnRemoveEvent();';
}

function fnRemoveEvent() {
	document.getElementById('addEventDropDown').style.display = 'none';
}

function fnRemoveButton() {
	document.getElementById('addButtonDropDown').style.display = 'none';
}

function fnShowEvent() {
	document.getElementById('addEventDropDown').style.display = 'block';
}
function fnShowButton() {
	document.getElementById('addButtonDropDown').style.display = 'block';
}

function updateStatus(record, status, view, hour, day, month, year, type) {
	if (type == 'event') {
		var OptionData = document.getElementById('view_Option').options[document.getElementById('view_Option').selectedIndex].value;
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=cbCalendar&action=ActivityAjax&record=' + record + '&' + status + '&view=' + view + '&hour=' + hour + '&day=' + day + '&month=' + month + '&year=' + year + '&type=change_status&viewOption=' + OptionData + '&subtab=event&ajax=true'
		}).done(function (response) {
			var result = response.split('####');
			if (OptionData == 'listview') {
				document.getElementById('total_activities').innerHTML = result[1];
				document.EventViewOption.action.value = 'index';
				window.document.EventViewOption.submit();
			}
			if (OptionData == 'hourview') {
				document.getElementById('total_activities').innerHTML = result[1];
				document.EventViewOption.action.value = 'index';
				window.document.EventViewOption.submit();
			}
		});
	}
	if (type == 'todo') {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=cbCalendar&action=ActivityAjax&record=' + record + '&' + status + '&view=' + view + '&hour=' + hour + '&day=' + day + '&month=' + month + '&year=' + year + '&type=change_status&subtab=todo&ajax=true'
		}).done(function (response) {
			var result = response.split('####');
			document.getElementById('total_activities').innerHTML = result[1];
			document.getElementById('mnuTab2').innerHTML = result[0];
		});
	}
}

function getcalAction(obj, Lay, id, view, hour, dateVal, type) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0, maxW.length-2);
	var getVal = leftSide + +widthM;
	var vtDate = dateVal.split('-');
	var day = parseInt(vtDate[2], 10);
	var month = parseInt(vtDate[1], 10);
	var year = parseInt(vtDate[0], 10);
	if (getVal  > window.innerWidth ) {
		leftSide = leftSide - widthM;
		tagName.style.left = leftSide + 'px';
	} else {
		tagName.style.left= leftSide + 'px';
	}
	tagName.style.top= topSide + 'px';
	tagName.style.display = 'block';
	tagName.style.visibility = 'visible';
	if (type == 'event') {
		var heldstatus = 'eventstatus=Held';
		var notheldstatus = 'eventstatus=Not Held';
		var activity_mode = 'Events';
		var complete = document.getElementById('complete');
		var pending = document.getElementById('pending');
		var postpone = document.getElementById('postpone');
		var actdelete =	document.getElementById('actdelete');
		var OptionData = document.getElementById('view_Option').options[document.getElementById('view_Option').selectedIndex].value;
	}
	if (type == 'todo') {
		var heldstatus = 'status=Completed';
		var notheldstatus = 'status=Deferred';
		var activity_mode = 'Task';
		var complete = document.getElementById('taskcomplete');
		var pending = document.getElementById('taskpending');
		var postpone = document.getElementById('taskpostpone');
		var actdelete = document.getElementById('taskactdelete');
		var OptionData = '';
	}
	document.getElementById('idlist').value = id;
	if (complete) {
		complete.href='javascript:updateStatus('+id+',\''+heldstatus+'\',\''+view+'\','+hour+','+day+','+month+','+year+',\''+type+'\')';
	}
	if (pending) {
		pending.href='javascript:updateStatus('+id+',\''+notheldstatus+'\',\''+view+'\','+hour+','+day+','+month+','+year+',\''+type+'\')';
	}

	if (postpone) {
		postpone.href='index.php?module=cbCalendar&action=EditView&record='+id+'&return_action=index&activity_mode='+activity_mode+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&viewOption='+OptionData+'&subtab='+type;
	}

	if (actdelete) {
		actdelete.href='javascript:delActivity('+id+',\''+view+'\','+hour+','+day+','+month+','+year+',\''+type+'\')';
	}
}

function dispLayer(lay) {
	var tagName = document.getElementById(lay);
	tagName.style.visibility = 'visible';
	tagName.style.display = 'block';
}

function delActivity(id, view, hour, day, month, year, subtab) {
	if (subtab == 'event') {
		var users = document.getElementsByName('onlyforuser');
		var onlyforuser = users[0].value;
		var OptionData = document.getElementById('view_Option').options[document.getElementById('view_Option').selectedIndex].value;
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Users&action=massdelete&return_module=cbCalendar&return_action=ActivityAjax&idlist=' + id + '&view=' + view + '&hour=' + hour + '&day=' + day + '&month=' + month + '&year=' + year + '&type=activity_delete&viewOption=' + OptionData + '&subtab=event&ajax=true&onlyforuser=' + encodeURIComponent(onlyforuser)
		}).done(function (response) {
			var result = response.split('####');
			if (OptionData == 'listview') {
				document.getElementById('total_activities').innerHTML = result[1];
				document.getElementById('listView').innerHTML = result[0];
			}
			if (OptionData == 'hourview') {
				document.getElementById('total_activities').innerHTML = result[1];
				document.getElementById('hrView').innerHTML = result[0];
			}
		});
	}
	if (subtab == 'todo') {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Users&action=massdelete&return_module=cbCalendar&return_action=ActivityAjax&idlist=' + id + '&view=' + view + '&hour=' + hour + '&day=' + day + '&month=' + month + '&year=' + year + '&type=activity_delete&subtab=todo&ajax=true'
		}).done(function (response) {
			var result = response.split('####');
			document.getElementById('total_activities').innerHTML = result[1];
			document.getElementById('mnuTab2').innerHTML = result[0];
		});
	}
}

/*
* javascript function to display the div tag
* @param divId :: div tag ID
*/
function cal_show(divId) {
	var id = document.getElementById(divId);
	id.style.visibility = 'visible';
}

function fnShowPopup() {
	document.getElementById('popupLay').style.display = 'block';
}

function fnHidePopup() {
	document.getElementById('popupLay').style.display = 'none';
}

function getSelectedStatus() {
	var chosen = document.EditView.eventstatus.value;
	if (chosen == 'Held') {
		document.getElementById('date_table_firsttd').style.width = '33%';
		document.getElementById('date_table_secondtd').style.width = '33%';
		document.getElementById('date_table_thirdtd').style.display = 'block';
	} else {
		document.getElementById('date_table_firsttd').style.width = '50%';
		document.getElementById('date_table_secondtd').style.width = '50%';
		document.getElementById('date_table_thirdtd').style.display = 'none';
	}
}

/**this is for to add a option element while selecting contact in add event page
   lvalue ==> is a contact id
   ltext ==> is a contact name
**/
function addOption(lvalue, ltext) {
	var optObj = document.createElement('OPTION');
	if (browser_ie) {
		optObj.innerText = ltext;
	} else if (browser_nn4 || browser_nn6) {
		optObj.text = ltext;
	} else {
		optObj.text = ltext;
	}
	optObj.value = lvalue;
	document.getElementById('parentid').appendChild(optObj);
}
