/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

// Get User Default calendar variables
var Calendar_Default_Reminder_Minutes = 0; // false
GlobalVariable_getVariable('Calendar_Default_Reminder_Minutes', 0, 'Calendar', gVTUserID).then(function(response) {
	var obj = JSON.parse(response);
	Calendar_Default_Reminder_Minutes = obj.Calendar_Default_Reminder_Minutes;
}, function(error) {
	Calendar_Default_Reminder_Minutes = 0; // false
});

function fnAddITSEvent(obj,CurrObj,start_date,end_date,start_hr,start_min,start_fmt,end_hr,end_min,end_fmt,viewOption,subtab,eventlist){
	var tagName = document.getElementById(CurrObj);
	var left_Side = findPosX(obj);
	var top_Side = findPosY(obj);
	tagName.style.left= left_Side + 'px';
	tagName.style.top= top_Side + 22+ 'px';
	tagName.style.display = 'block';
	eventlist = eventlist.split(";");
	for(var i=0;i<(eventlist.length-1);i++){
		document.getElementById("add"+eventlist[i].toLowerCase()).href="javascript:gITSshow('addITSEvent','"+eventlist[i]+"','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"','"+viewOption+"','"+subtab+"','');fnRemoveITSEvent();";
	}
	document.getElementById("addtodo").href="javascript:gITSshow('createTodo','todo','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"','"+viewOption+"','"+subtab+"','');fnRemoveITSEvent();";
}

function fnRemoveITSEvent(){
	var tagName = document.getElementById('addEventDropDown').style.display = 'none';
}
function fnRemoveITSButton(){
	var tagName = document.getElementById('addButtonDropDown').style.display = 'none';
}
function fnShowITSEvent(){
	var tagName = document.getElementById('addEventDropDown').style.display= 'block';
}
function fnShowITSButton(){
	var tagName = document.getElementById('addButtonDropDown').style.display= 'block';
}

function gITSshow(argg1,type,startdate,enddate,starthr,startmin,startfmt,endhr,endmin,endfmt,viewOption,subtab,skipconvertendtime){
	document.EditView.subject.value = '';
	document.EditView.description.value = '';
	document.EditView.location.value = '';
	document.EditView.geventid.value = '';
	document.EditView.gevent_type.value = '';
	document.EditView.gevent_userid.value = '';
	document.createTodo.task_subject.value = '';
	document.createTodo.task_description.value = '';
	document.createTodo.geventid.value = '';
	document.createTodo.gevent_type.value = '';
	document.createTodo.gevent_userid.value = '';
	smin = parseInt(startmin,10);
	smin = smin - (smin%5);
	var y=document.getElementById(argg1).style;

	if(type != 'todo' && type!=''){
		fieldname = new Array();
		for(var i=0;;i++){
			if( document.EditView.activitytype[i].value == type){
				document.EditView.activitytype[i].selected='yes';
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
		document.EditView.parent_id.value = "";
		document.EditView.parent_name.value = "";
		if(typeof(document.EditView.contactidlist) != 'undefined') {
		document.EditView.contactidlist.value = "";
		document.EditView.deletecntlist.value = "";

		while (document.EditView.contactlist.options.length > 0){
			document.EditView.contactlist.remove(0);
		}
		}
		if (Calendar_Default_Reminder_Minutes>0) {
			document.getElementById('set_reminder1').checked = true;
			document.getElementById('set_reminder2').checked = false;
			show('reminderOptions');
		} else {
			document.getElementById('set_reminder1').checked = false;
			document.getElementById('set_reminder2').checked = true;
			fnhide('reminderOptions');
		}
		document.EditView.recurringcheck.checked = false;
		document.getElementById('repeatOptions').style.display="none";
		while (document.EditView.selectedusers.options.length > 0){
			document.EditView.selectedusers.remove(0);
		}
		if (skipconvertendtime == 'skip'){
			ehr = parseInt(endhr,10);
			emin = parseInt(endmin,10);
			emin = emin - (emin%5);
			ehr = _2digit(ehr);
			emin = _2digit(emin);
			document.EditView.due_date.value = enddate;
			document.EditView.endhr.value = ehr;
			document.EditView.endmin.value = emin;
			document.EditView.endfmt.value = endfmt;
			document.EditView.followup_date.value = enddate;
			document.EditView.followup_starthr.value = ehr;
			document.EditView.followup_startmin.value = emin;
			document.EditView.followup_startfmt.value = endfmt;
			document.EditView.calendar_repeat_limit_date.value = enddate;
		} else {
			calDuedatetime(type);
		}
	}
	if(type == 'todo'){
		fieldname = taskfieldname;
		smin = _2digit(smin);
		document.createTodo.task_date_start.value = startdate;
		document.createTodo.task_due_date.value = enddate;
		document.createTodo.starthr.value = starthr;
		document.createTodo.startmin.value = smin;
		document.createTodo.startfmt.value = startfmt;
		document.createTodo.viewOption.value = viewOption;
		document.createTodo.subtab.value = subtab;
		document.createTodo.task_sendnotification.checked = false;
		document.createTodo.task_parent_id.value = "";
		document.createTodo.task_parent_name.value = "";
		if(typeof(document.createTodo.task_contact_id) != 'undefined') {
		document.createTodo.task_contact_id.value = "";
		document.createTodo.task_contact_name.value = "";
		document.createTodo.deletecntlist.value = "";
		}
	}
	if (y.display=="none"){
		y.display="block";
	}
}

function getITSCalSettings(){
	url = getITSCalURL();
	jQuery.ajax({
			method:"POST",
			url:'index.php?module=Calendar4You&action=ActivityAjax' + url + '&type=settings&ajax=true',
	}).done(function(response) {
			document.getElementById("calSettings").innerHTML = response;
	});
}

function showEventIcon(icon_id){
	jQuery('#' + icon_id).show();
}

function hideEventIcon(icon_id){
	jQuery('#' + icon_id).hide();
}

function getITSCalURL(){
	var user_view_type = jQuery("#user_view_type :selected").val();
	var url = "&user_view_type="+user_view_type;
	var view_val = jQuery('#calendar_div').fullCalendar( 'getView' );
	url += "&view="+view_val.name;
	var cal_date = jQuery('#calendar_div').fullCalendar('getDate');
	var year_val = new Date(cal_date).getFullYear();
	url += "&year="+year_val;
	var month_val = new Date(cal_date).getMonth();
	month_val = (month_val * 1) + 1 ;
	url += "&month="+month_val;
	var day_val = new Date(cal_date).getDate();
	url += "&day="+day_val;
	return url;
}

function loadITSEventSettings(el,mode,id){
	document.getElementById('event_setting').innerHTML = '<img src=\'themes/images/vtbusy.gif\'>';
	fnvshobj(el, 'event_setting');

	var url = getITSCalURL();

	url += "&mode=" + mode + "&id=" + id;

	jQuery.ajax({
			method:"POST",
			url:'index.php?module=Calendar4You&action=ActivityAjax' + url + '&type=event_settings&ajax=true'
	}).done(function(response) {
			document.getElementById('event_setting').innerHTML = response;
	});
}

// Title: Tigra Color Picker
// URL: http://www.softcomplex.com/products/tigra_color_picker/
// Version: 1.1
// Date: 06/26/2003 (mm/dd/yyyy)
// Note: Permission given to use this script in ANY kind of applications if
//    header lines are left unchanged.
// Note: Script consists of two files: picker.js and picker.html

var C_TCP = new C_TColorPicker();

function C_TCPopup(field, palette){
	this.field = field;
	this.initPalette = !palette || palette > 3 ? 0 : palette;
	var w = 194, h = 240,
	move = screen ?
		',left=' + ((screen.width - w) >> 1) + ',top=' + ((screen.height - h) >> 1) : '',
	o_colWindow = window.open('modules/Calendar4You/color_picker.html', null, "help=no,status=no,scrollbars=no,resizable=no" + move + ",width=" + w + ",height=" + h + ",dependent=yes", true);
	o_colWindow.opener = window;
	o_colWindow.focus();
}

function C_TCBuildCell (R, G, B, w, h){
	return '<td bgcolor="#' + this.dec2hex((R << 16) + (G << 8) + B) + '"><a href="javascript:P.S(\'' + this.dec2hex((R << 16) + (G << 8) + B) + '\')" onmouseover="P.P(\'' + this.dec2hex((R << 16) + (G << 8) + B) + '\')"><img src="pixel.gif" width="' + w + '" height="' + h + '" border="0"></a></td>';
}

function C_TCSelect(c){
	this.field.value = '#' + c.toUpperCase();
	this.field.style.backgroundColor = this.field.value;
	this.win.close();
}

function C_TCPaint(c, b_noPref){
	c = (b_noPref ? '' : '#') + c.toUpperCase();
	if (this.o_samp)
		this.o_samp.innerHTML = '<font face=Tahoma size=2>' + c +' <font color=white>' + c + '</font></font>';
	if(this.doc.layers)
		this.sample.bgColor = c;
	else {
		if (this.sample.backgroundColor != null) this.sample.backgroundColor = c;
		else if (this.sample.background != null) this.sample.background = c;
	}
}

function C_TCGenerateSafe(){
	var s = '';
	for (j = 0; j < 12; j ++) {
		s += "<tr>";
		for (k = 0; k < 3; k ++)
			for (i = 0; i <= 5; i ++)
				s += this.bldCell(k * 51 + (j % 2) * 51 * 3, Math.floor(j / 2) * 51, i * 51, 8, 10);
		s += "</tr>";
	}
	return s;
}

function C_TCGenerateWind(){
	var s = '';
	for (j = 0; j < 12; j ++) {
		s += "<tr>";
		for (k = 0; k < 3; k ++)
			for (i = 0; i <= 5; i++)
				s += this.bldCell(i * 51, k * 51 + (j % 2) * 51 * 3, Math.floor(j / 2) * 51, 8, 10);
		s += "</tr>";
	}
	return s;
}

function C_TCGenerateMac(){
	var s = '';
	var c = 0,n = 1;
	var r,g,b;
	for (j = 0; j < 15; j ++) {
		s += "<tr>";
		for (k = 0; k < 3; k ++)
			for (i = 0; i <= 5; i++){
				if(j<12){
				s += this.bldCell( 255-(Math.floor(j / 2) * 51), 255-(k * 51 + (j % 2) * 51 * 3),255-(i * 51), 8, 10);
				}else{
					if(n<=14){
						r = 255-(n * 17);
						g=b=0;
					}else if(n>14 && n<=28){
						g = 255-((n-14) * 17);
						r=b=0;
					}else if(n>28 && n<=42){
						b = 255-((n-28) * 17);
						r=g=0;
					}else{
						r=g=b=255-((n-42) * 17);
					}
					s += this.bldCell( r, g,b, 8, 10);
					n++;
				}
			}
		s += "</tr>";
	}
	return s;
}

function C_TCGenerateGray(){
	var s = '';
	for (j = 0; j <= 15; j ++) {
		s += "<tr>";
		for (k = 0; k <= 15; k ++) {
			g = Math.floor((k + j * 16) % 256);
			s += this.bldCell(g, g, g, 9, 7);
		}
		s += '</tr>';
	}
	return s;
}

function C_TCDec2Hex(v){
	v = v.toString(16);
	for(; v.length < 6; v = '0' + v);
	return v;
}

function C_TCChgMode(v){
	for (var k in this.divs) this.hide(k);
	this.show(v);
}

function C_TColorPicker(field){
	this.build0 = C_TCGenerateSafe;
	this.build1 = C_TCGenerateWind;
	this.build2 = C_TCGenerateGray;
	this.build3 = C_TCGenerateMac;
	this.show = document.layers ?
		function (div) { this.divs[div].visibility = 'show'; } :
		function (div) { this.divs[div].visibility = 'visible'; };
	this.hide = document.layers ?
		function (div) { this.divs[div].visibility = 'hide'; } :
		function (div) { this.divs[div].visibility = 'hidden'; };
	// event handlers
	this.C       = C_TCChgMode;
	this.S       = C_TCSelect;
	this.P       = C_TCPaint;
	this.popup   = C_TCPopup;
	this.draw    = C_TCDraw;
	this.dec2hex = C_TCDec2Hex;
	this.bldCell = C_TCBuildCell;
	this.divs = [];
}

function C_TCDraw(o_win, o_doc){
	this.win = o_win;
	this.doc = o_doc;
	var
	s_tag_openT = o_doc.layers ?
		'layer visibility=hidden top=54 left=5 width=182' :
		'div style=visibility:hidden;position:absolute;left:6px;top:54px;width:182px;height:0',
	s_tag_openS = o_doc.layers ? 'layer top=32 left=6' : 'div',
	s_tag_close = o_doc.layers ? 'layer' : 'div';

	this.doc.write('<' + s_tag_openS + ' id=sam name=sam><table cellpadding=0 cellspacing=0 border=1 width=181 align=center class=bd><tr><td align=center height=18><div id="samp"><font face=Tahoma size=2>sample <font color=white>sample</font></font></div></td></tr></table></' + s_tag_close + '>');
	this.sample = o_doc.layers ? o_doc.layers['sam'] :
		o_doc.getElementById ? o_doc.getElementById('sam').style : o_doc.all['sam'].style;

	for (var k = 0; k < 4; k ++){
		this.doc.write('<' + s_tag_openT + ' id="p' + k + '" name="p' + k + '"><table cellpadding=0 cellspacing=0 border=1 align=center>' + this['build' + k]() + '</table></' + s_tag_close + '>');
		this.divs[k] = o_doc.layers
			? o_doc.layers['p' + k] : o_doc.all
				? o_doc.all['p' + k].style : o_doc.getElementById('p' + k).style;
	}
	if (!o_doc.layers && o_doc.body.innerHTML)
		this.o_samp = o_doc.all
			? o_doc.all.samp : o_doc.getElementById('samp');
	this.C(this.initPalette);
	if (this.field.value) this.P(this.field.value, true);
}


function saveITSEventSettings(){
	formSelectColumnString('day_selected_fields','selected_day_fields');
	formSelectColumnString('week_selected_fields','selected_week_fields');
	formSelectColumnString('month_selected_fields','selected_month_fields');
}

function changeInstallType(type){
	document.getElementById('next_button').disabled = false;
	document.getElementById('next_button').style.display = "inline";

	if (type == "express"){
		bad_files_count = document.getElementById('bad_files').value;
		if (bad_files_count != "0"){
			document.getElementById('next_button').disabled = true;
			document.getElementById('next_button').style.display = "none";
		}
		document.getElementById('total_steps').innerHTML = '4';
	} else if (type == "custom"){
		document.getElementById('total_steps').innerHTML = '5';
	}
}

function showGoogleSyncAccDiv(value){
	if (value != "")
		fnShowDrop('google_sync_acc_div');
	else
		fnHideDrop('google_sync_acc_div');
}

function controlGoogleSync(){
	if (document.getElementById('google_apikey')){
		// var google_password_val = document.getElementById('google_password').value;
		var google_login_val = document.getElementById('google_login').value;
		var google_apikey_val = document.getElementById('google_apikey').value;
		var google_clientid_val = document.getElementById('google_clientid').value;
		var google_keyfile_val = document.getElementById('google_keyfile').value;
		var google_refresh = document.getElementById('google_refresh').value;
		if(document.getElementById('googleinsert').checked==true) {
			var googleinsert =1;
			document.getElementById('googleinsert').value=1;
		} else {
			googleinsert=0;
			document.getElementById('googleinsert').value=0;
		}
		if (google_login_val != "" && google_apikey_val != "" && google_clientid_val != "" && google_keyfile_val != ""){
			fnShowDrop("google_sync_verifying");
			fnHideDrop("google_sync_text");
			jQuery.ajax({
					method:"POST",
					url:'index.php?module=Calendar4You&action=Calendar4YouAjax&file=GoogleSync4YouControl&ajax=true&login=' + google_login_val + '&apikey=' + google_apikey_val + '&keyfile=' + google_keyfile_val + '&clientid=' + google_clientid_val + "&refresh_token=" + google_refresh + "&googleinsert=" + googleinsert
			}).done(function(response) {
					if (google_refresh == '') {
						document.getElementById('google_sync_text').style.color = '#000000';
						document.forms["SharingForm"].submit();
					} else {
						result = JSON.parse(response);
						document.getElementById('google_sync_text').innerHTML = result["text"];
						fnHideDrop("google_sync_verifying");
						fnShowDrop("google_sync_text");
						if (result["status"] != "ok") {
							document.getElementById('google_sync_text').style.color = 'red';
							return false;
						} else {
							document.getElementById('google_sync_text').style.color = '#000000';
							document.forms["SharingForm"].submit();
						}
					}
			});
		}else{
			document.forms["SharingForm"].submit();
		}
	}else{
		document.forms["SharingForm"].submit();
	}
}

function changeGoogleAccount(){
	fnShowDrop("google_account_change_div");
	fnHideDrop("google_account_info_div");
	document.getElementById('update_google_account').value = "1";
}

function insertIntoCRM(userid,eventid,eventtype,geventid,start_date,end_date,start_hr,start_min,start_fmt,end_hr,end_min,end_fmt){
	if (eventtype == "todo")
		el_div = "createTodo";
	else
		el_div = "addITSEvent";
	gITSshow(el_div,eventtype,start_date,end_date,start_hr,start_min,start_fmt,end_hr,end_min,end_fmt,'hourview','','skip');
	var title_val = document.getElementById('google_info_'+eventid+'_title').innerHTML;
	var desc_val = document.getElementById('google_info_'+eventid+'_desc').innerHTML;
	var location_val = document.getElementById('google_info_'+eventid+'_location').innerHTML;
	if (eventtype == "todo"){
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
