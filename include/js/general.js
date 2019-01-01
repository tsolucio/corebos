/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

//Utility Functions

function getTagCloud(crmid) {
	var obj = document.getElementById('tagfields');
	if (obj != null && typeof(obj) != undefined) {
		jQuery.ajax({
			method:'POST',
			url:'index.php?module='+gVTModule+'&action='+gVTModule+'Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid='+crmid,
		}).done(function (response) {
			document.getElementById('tagfields').innerHTML=response;
			document.getElementById('txtbox_tagfields').value ='';
		});
	}
}

function DeleteTag(id, recordid) {
	VtigerJS_DialogBox.showbusy();
	jQuery('#tag_'+id).fadeOut();
	jQuery.ajax({
		method:'POST',
		url:'index.php?file=TagCloud&module='+gVTModule+'&action='+gVTModule+'Ajax&ajxaction=DELETETAG&recordid='+recordid+'&tagid=' +id,
	}).done(function (response) {
		getTagCloud();
		VtigerJS_DialogBox.hidebusy();
	});
}

function c_toggleAssignType(currType) {
	if (currType=='U') {
		document.getElementById('c_assign_user').style.display='block';
		document.getElementById('c_assign_team').style.display='none';
	} else {
		document.getElementById('c_assign_user').style.display='none';
		document.getElementById('c_assign_team').style.display='block';
	}
}

if (document.all) {
	var browser_ie=true;
} else if (document.layers) {
	var browser_nn4=true;
} else if (document.layers || (!document.all && document.getElementById)) {
	var browser_nn6=true;
}

var gBrowserAgent = navigator.userAgent.toLowerCase();

function doNothing() {
}

function hideSelect() {
	var oselect_array = document.getElementsByTagName('SELECT');
	for (var i=0; i<oselect_array.length; i++) {
		oselect_array[i].style.display = 'none';
	}
}

function showSelect() {
	var oselect_array = document.getElementsByTagName('SELECT');
	for (var i=0; i<oselect_array.length; i++) {
		oselect_array[i].style.display = 'block';
	}
}

function getObj(n, d) {
	var p, i, x;

	if (!d) {
		d=document;
	}

	if (n != undefined) {
		if ((p=n.indexOf('?'))>0&&parent.frames.length) {
			d=parent.frames[n.substring(p+1)].document;
			n=n.substring(0, p);
		}
	}

	if (d.getElementById) {
		x=d.getElementById(n);
		// IE7 was returning form element with name = n (if there was multiple instance)
		// But not firefox, so we are making a double check
		if (x && x.id != n) {
			x = false;
		}
	}

	if (!x && d.getElementById) {
		x=d.getElementById('txtbox_'+n); // for detail view edit fields
	}

	for (i=0; !x && i<d.forms.length; i++) {
		x=d.forms[i][n];
	}

	for (i=0; !x && d.layers && i<d.layers.length; i++) {
		x=getObj(n, d.layers[i].document);
	}

	if (!x && !(x=d[n]) && d.all) {
		x=d.all[n];
	}

	if (typeof x == 'string') {
		x=null;
	}

	return x;
}

function getOpenerObj(n) {
	return getObj(n, opener.document);
}

function findPosX(obj) {
	var pos = getPosition(obj);
	return pos.x;
}

function findPosY(obj) {
	var pos = getPosition(obj);
	return pos.y;
}

function getPosition(element) {
	//Get absolute position, using JQuery
	var screen_elem = jQuery(element);
	if (screen_elem == undefined || screen_elem.get(0).tagName == undefined) {
		offset = {left: 0, top: 0};
	} else {
		var offset = screen_elem.offset();
		if (offset == undefined) {
			offset = {left: 0, top: 0};
		}
	}
	return { x: offset.left, y: offset.top };
}

function clearTextSelection() {
	if (browser_ie) {
		document.selection.empty();
	} else if (browser_nn4 || browser_nn6) {
		window.getSelection().removeAllRanges();
	}
}

// Setting cookies
function set_cookie( name, value, exp_y, exp_m, exp_d, path, domain, secure ) {
	var cookie_string = name + '=' + escape(value);

	if (exp_y) {
		//delete_cookie(name)
		var expires = new Date(exp_y, exp_m, exp_d);
		cookie_string += '; expires=' + expires.toGMTString();
	}

	if (path) {
		cookie_string += '; path=' + escape(path);
	}
	if (domain) {
		cookie_string += '; domain=' + escape(domain);
	}
	if (secure) {
		cookie_string += '; secure';
	}

	document.cookie = cookie_string;
}

// Retrieving cookies
function get_cookie(cookie_name) {
	var results = document.cookie.match('(^| )' + cookie_name + '=(.*?)(;|$)');
	if (results) {
		if (results[1]==' ') {
			return (unescape(results[2]));
		} else {
			return (unescape(results[1]));
		}
	} else {
		return null;
	}
}

// Delete cookies
function delete_cookie(cookie_name) {
	var cookie_date = new Date();  // current date & time
	cookie_date.setTime(cookie_date.getTime() - 1);
	document.cookie = cookie_name += '=; expires=' + cookie_date.toGMTString();
}
//End of Utility Functions

function emptyCheck(fldName, fldLabel, fldType) {
	var currObj = getObj(fldName);
	if (fldType=='text') {
		if (currObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) {
			alert(fldLabel+alert_arr.CANNOT_BE_EMPTY);
			try {
				currObj.focus();
			} catch (error) {
			// Fix for IE: If element or its wrapper around it is hidden, setting focus will fail
			// So using the try { } catch(error) { }
			}
			return false;
		} else {
			return true;
		}
	} else if ((fldType == 'textarea') && (typeof(CKEDITOR)!=='undefined' && CKEDITOR.instances[fldName] !== undefined)) {
		var textObj = CKEDITOR.instances[fldName];  // thank you Stefan (from developers list)
		var textValue = trim(textObj.getData());
		if (textValue == '' || /^<br *\/?>$/.test(textValue)) {
			alert(fldLabel+alert_arr.CANNOT_BE_NONE);
			return false;
		} else {
			return true;
		}
	} else {
		if (trim(currObj.value) == '') {
			alert(fldLabel+alert_arr.CANNOT_BE_NONE);
			return false;
		} else {
			return true;
		}
	}
}

function patternValidateObject(fldObject, fldLabel, type) {
	fldObject.value = trim(fldObject.value);
	let checkval = fldObject.value;
	let typeUC = type.toUpperCase();
	if (typeUC=='EMAIL') { //Email ID validation
		var re=new RegExp(/^[a-z0-9!#$%&'*+/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/i);
	}

	if (typeUC=='DATE') { //DATE validation
		//YMD
		//var reg1 = /^\d{2}(\-|\/|\.)\d{1,2}\1\d{1,2}$/ //2 digit year
		//var re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/ //4 digit year

		//MYD
		//var reg1 = /^\d{1,2}(\-|\/|\.)\d{2}\1\d{1,2}$/
		//var reg2 = /^\d{1,2}(\-|\/|\.)\d{4}\1\d{1,2}$/

		//DMY
		//var reg1 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2}$/
		//var reg2 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/

		switch (userDateFormat) {
		case 'yyyy-mm-dd' :
			var re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/;
			break;
		case 'mm-dd-yyyy' :
		case 'dd-mm-yyyy' :
			var re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;
		}
		if (checkval.indexOf(' ')>0) {
			var dt = checkval.split(' ');
			checkval = dt[0];
		}
	}

	if (typeUC=='TIME') { //TIME validation
		var re = /^\d{1,2}\:\d{2}:\d{2}$|^\d{1,2}\:\d{2}$/;
		if (checkval.indexOf(' ')>0) {
			var dt = checkval.split(' ');
			checkval = dt[1];
		}
	}
	if (typeof(re) != 'undefined' && !re.test(checkval)) {
		alert(alert_arr.ENTER_VALID + fldLabel + ' ('+type+')');
		try {
			fldObject.focus();
		} catch (error) {
		// Fix for IE: If element or its wrapper around it is hidden, setting focus will fail
		// So using the try { } catch(error) { }
		}
		return false;
	} else {
		return true;
	}
}

function patternValidate(fldName, fldLabel, type) {
	var currObj=getObj(fldName);
	return patternValidateObject(currObj, fldLabel, type);
}

function splitDateVal(dateval) {
	var datesep;
	var dateelements = new Array(3);
	if (dateval==undefined) {
		return dateelements;
	}
	if (dateval.indexOf('-')>=0) {
		datesep='-';
	} else if (dateval.indexOf('.')>=0) {
		datesep='.';
	} else if (dateval.indexOf('/')>=0) {
		datesep='/';
	}

	switch (userDateFormat) {
	case 'yyyy-mm-dd' :
		dateelements[0]=dateval.substr(dateval.lastIndexOf(datesep)+1, dateval.length); //dd
		dateelements[1]=dateval.substring(dateval.indexOf(datesep)+1, dateval.lastIndexOf(datesep)); //mm
		dateelements[2]=dateval.substring(0, dateval.indexOf(datesep)); //yyyyy
		break;
	case 'mm-dd-yyyy' :
		dateelements[0]=dateval.substring(dateval.indexOf(datesep)+1, dateval.lastIndexOf(datesep));
		dateelements[1]=dateval.substring(0, dateval.indexOf(datesep));
		dateelements[2]=dateval.substr(dateval.lastIndexOf(datesep)+1, dateval.length);
		break;
	case 'dd-mm-yyyy' :
		dateelements[0]=dateval.substring(0, dateval.indexOf(datesep));
		dateelements[1]=dateval.substring(dateval.indexOf(datesep)+1, dateval.lastIndexOf(datesep));
		dateelements[2]=dateval.substr(dateval.lastIndexOf(datesep)+1, dateval.length);
	}

	return dateelements;
}

function compareDates(date1, fldLabel1, date2, fldLabel2, type, message) {
	if (message == undefined) {
		message = true;
	}
	var ret=true;
	switch (type) {
	case 'L':
		if (date1>=date2) {//DATE1 VALUE LESS THAN DATE2
			if (message) {
				alert(fldLabel1+ alert_arr.SHOULDBE_LESS +fldLabel2);
			}
			ret=false;
		}
		break;
	case 'LE':
		if (date1>date2) {//DATE1 VALUE LESS THAN OR EQUAL TO DATE2
			if (message) {
				alert(fldLabel1+alert_arr.SHOULDBE_LESS_EQUAL+fldLabel2);
			}
			ret=false;
		}
		break;
	case 'E':
		if (date1-date2) {//DATE1 VALUE EQUAL TO DATE
			if (message) {
				alert(fldLabel1+alert_arr.SHOULDBE_EQUAL+fldLabel2);
			}
			ret=false;
		}
		break;
	case 'G':
		if (date1<=date2) {//DATE1 VALUE GREATER THAN DATE2
			if (message) {
				alert(fldLabel1+alert_arr.SHOULDBE_GREATER+fldLabel2);
			}
			ret=false;
		}
		break;
	case 'GE':
		if (date1<date2) {//DATE1 VALUE GREATER THAN OR EQUAL TO DATE2
			if (message) {
				alert(fldLabel1+alert_arr.SHOULDBE_GREATER_EQUAL+fldLabel2);
			}
			ret=false;
		}
		break;
	}
	return ret;
}

function dateTimeValidate(dateFldName, timeFldName, fldLabel, type) {
	return dateTimeValidateObject(getObj(dateFldName), getObj(timeFldName), fldLabel, type);
}

function dateTimeValidateObject(dateFldObj, timeFldObj, fldLabel, type) {
	if (patternValidateObject(dateFldObj, fldLabel, 'DATE')==false) {
		return false;
	}
	let dateval = dateFldObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '');

	if (timeFldObj==undefined) {
		timeFldObj = dateFldObj;
		let dt = dateval.split(' ');
		dateval = dt[0];
	}
	var dateelements=splitDateVal(dateval);

	var dd=dateelements[0];
	var mm=dateelements[1];
	var yyyy=dateelements[2];

	if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			dateFldObj.focus();
		} catch (error) { }
		return false;
	}

	if ((mm==2) && (dd>29)) {//checking of no. of days in february month
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			dateFldObj.focus();
		} catch (error) { }
		return false;
	}

	if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			dateFldObj.focus();
		} catch (error) { }
		return false;
	}

	switch (parseInt(mm)) {
	case 2 :
	case 4 :
	case 6 :
	case 9 :
	case 11 :
		if (dd>30) {
			alert(alert_arr.ENTER_VALID+fldLabel);
			try {
				dateFldObj.focus();
			} catch (error) { }
			return false;
		}
	}

	if (patternValidateObject(timeFldObj, fldLabel, 'TIME')==false) {
		return false;
	}

	var timeval=timeFldObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	if (timeval.indexOf(' ')>0) {
		let dt = timeval.split(' ');
		timeval = dt[1];
	}
	var hourval=parseInt(timeval.substring(0, timeval.indexOf(':')));
	var minval=parseInt(timeval.substring(timeval.indexOf(':')+1, timeval.length));
	var currObj=timeFldObj;

	if (hourval>23 || minval>59) {
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			currObj.focus();
		} catch (error) { }
		return false;
	}

	var currdate=new Date();
	var chkdate=new Date();

	chkdate.setYear(yyyy);
	chkdate.setMonth(mm-1);
	chkdate.setDate(dd);
	chkdate.setHours(hourval);
	chkdate.setMinutes(minval);

	if (type!='OTH') {
		if (!compareDates(chkdate, fldLabel, currdate, 'current date & time', type)) {
			try {
				dateFldObj.focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function dateTimeComparison(dateFldName1, timeFldName1, fldLabel1, dateFldName2, timeFldName2, fldLabel2, type) {
	var dateval1=getObj(dateFldName1).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var dateval2=getObj(dateFldName2).value.replace(/^\s+/g, '').replace(/\s+$/g, '');

	var dateelements1=splitDateVal(dateval1);
	var dateelements2=splitDateVal(dateval2);

	var dd1=dateelements1[0];
	var mm1=dateelements1[1];
	var yyyy1=dateelements1[2];

	var dd2=dateelements2[0];
	var mm2=dateelements2[1];
	var yyyy2=dateelements2[2];

	var timeval1=getObj(timeFldName1).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var timeval2=getObj(timeFldName2).value.replace(/^\s+/g, '').replace(/\s+$/g, '');

	var hh1=timeval1.substring(0, timeval1.indexOf(':'));
	var min1=timeval1.substring(timeval1.indexOf(':')+1, timeval1.length);

	var hh2=timeval2.substring(0, timeval2.indexOf(':'));
	var min2=timeval2.substring(timeval2.indexOf(':')+1, timeval2.length);

	var date1=new Date();
	var date2=new Date();

	date1.setYear(yyyy1);
	date1.setMonth(mm1-1);
	date1.setDate(dd1);
	date1.setHours(hh1);
	date1.setMinutes(min1);

	date2.setYear(yyyy2);
	date2.setMonth(mm2-1);
	date2.setDate(dd2);
	date2.setHours(hh2);
	date2.setMinutes(min2);

	if (type!='OTH') {
		if (!compareDates(date1, fldLabel1, date2, fldLabel2, type)) {
			try {
				getObj(dateFldName1).focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function dateTimeFieldComparison(dateFld1, fldLabel1, dateFld2, fldLabel2, type, message) {
	var dateval1=getObj(dateFld1).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var dateval2=getObj(dateFld2).value.replace(/^\s+/g, '').replace(/\s+$/g, '');

	let dt1array = dateval1.split(' ');
	let dt2array = dateval2.split(' ');
	var dateelements1=splitDateVal(dt1array[0]);
	var dateelements2=splitDateVal(dt2array[0]);

	var dd1=dateelements1[0];
	var mm1=dateelements1[1];
	var yyyy1=dateelements1[2];

	var dd2=dateelements2[0];
	var mm2=dateelements2[1];
	var yyyy2=dateelements2[2];

	var timeval1=dt1array[1];
	var timeval2=dt2array[1];

	var hh1=timeval1.substring(0, timeval1.indexOf(':'));
	var tf1 = document.getElementById('inputtimefmt_' + dateFld1);
	if (tf1 != undefined) {
		if (tf1.value == 'PM') {
			if (hh1 != '12') {
				hh1 = +hh1 + 12;
			}
		}
	}
	var min1=timeval1.substring(timeval1.indexOf(':')+1, timeval1.length);

	var hh2=timeval2.substring(0, timeval2.indexOf(':'));
	var tf2 = document.getElementById('inputtimefmt_' + dateFld2);
	if (tf2 != undefined) {
		if (tf2.value == 'PM') {
			if (hh2 != '12') {
				hh2 = +hh2 + 12;
			}
		}
	}
	var min2=timeval2.substring(timeval2.indexOf(':')+1, timeval2.length);

	var date1=new Date();
	var date2=new Date();

	date1.setYear(yyyy1);
	date1.setMonth(mm1-1);
	date1.setDate(dd1);
	date1.setHours(hh1);
	date1.setMinutes(min1);

	date2.setYear(yyyy2);
	date2.setMonth(mm2-1);
	date2.setDate(dd2);
	date2.setHours(hh2);
	date2.setMinutes(min2);

	if (type!='OTH') {
		if (!compareDates(date1, fldLabel1, date2, fldLabel2, type, message)) {
			try {
				getObj(dateFld1).focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function dateValidate(fldName, fldLabel, type) {
	if (patternValidate(fldName, fldLabel, 'DATE')==false) {
		return false;
	}
	return dateValidateObject(getObj(fldName), fldLabel, type);
}

function dateValidateObject(fldObj, fldLabel, type) {
	var dateval=fldObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '');

	var dateelements=splitDateVal(dateval);

	var dd=dateelements[0];
	var mm=dateelements[1];
	var yyyy=dateelements[2];

	if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			fldObj.focus();
		} catch (error) { }
		return false;
	}

	if ((mm==2) && (dd>29)) {//checking of no. of days in february month
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			fldObj.focus();
		} catch (error) { }
		return false;
	}

	if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			fldObj.focus();
		} catch (error) { }
		return false;
	}

	switch (parseInt(mm)) {
	case 2 :
	case 4 :
	case 6 :
	case 9 :
	case 11 :
		if (dd>30) {
			alert(alert_arr.ENTER_VALID+fldLabel);
			try {
				fldObj.focus();
			} catch (error) { }
			return false;
		}
	}

	var currdate=new Date();
	var chkdate=new Date();

	chkdate.setYear(yyyy);
	chkdate.setMonth(mm-1);
	chkdate.setDate(dd);

	if (type!='OTH') {
		if (!compareDates(chkdate, fldLabel, currdate, 'current date', type)) {
			try {
				fldObj.focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function dateComparison(fldName1, fldLabel1, fldName2, fldLabel2, type) {
	return dateComparisonObject(getObj(fldName1), fldLabel1, getObj(fldName2), fldLabel2, type);
}

function dateComparisonObject(fldObj1, fldLabel1, fldObj2, fldLabel2, type) {
	var dateval1=fldObj1.value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var dateval2=fldObj2.value.replace(/^\s+/g, '').replace(/\s+$/g, '');

	var dateelements1=splitDateVal(dateval1);
	var dateelements2=splitDateVal(dateval2);

	var dd1=dateelements1[0];
	var mm1=dateelements1[1];
	var yyyy1=dateelements1[2];

	var dd2=dateelements2[0];
	var mm2=dateelements2[1];
	var yyyy2=dateelements2[2];

	var date1=new Date();
	var date2=new Date();

	date1.setYear(yyyy1);
	date1.setMonth(mm1-1);
	date1.setDate(dd1);

	date2.setYear(yyyy2);
	date2.setMonth(mm2-1);
	date2.setDate(dd2);

	if (type!='OTH') {
		if (!compareDates(date1, fldLabel1, date2, fldLabel2, type)) {
			try {
				fldObj1.focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function timeValidate(fldName, fldLabel, type) {
	if (patternValidate(fldName, fldLabel, 'TIME')==false) {
		return false;
	}

	var timeval=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var hourval=parseInt(timeval.substring(0, timeval.indexOf(':')));
	var minval=parseInt(timeval.substring(timeval.indexOf(':')+1, timeval.length));
	var secval=parseInt(timeval.substring(timeval.indexOf(':')+4, timeval.length));
	var currObj=getObj(fldName);

	if (hourval>23 || minval>59 || secval>59) {
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			currObj.focus();
		} catch (error) { }
		return false;
	}

	var currtime=new Date();
	var chktime=new Date();

	chktime.setHours(hourval);
	chktime.setMinutes(minval);
	chktime.setSeconds(secval);

	if (type!='OTH') {
		if (!compareDates(chktime, fldLabel, currtime, 'current time', type)) {
			try {
				getObj(fldName).focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function timeValidateObject(fldObject, fldLabel, type) {
	if (patternValidateObject(fldObject, fldLabel, 'TIME')==false) {
		return false;
	}

	var timeval=fldObject.value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var hourval=parseInt(timeval.substring(0, timeval.indexOf(':')));
	var minval=parseInt(timeval.substring(timeval.indexOf(':')+1, timeval.length));
	var secval=parseInt(timeval.substring(timeval.indexOf(':')+4, timeval.length));

	if (hourval>23 || minval>59 || secval>59) {
		alert(alert_arr.ENTER_VALID+fldLabel);
		try {
			fldObject.focus();
		} catch (error) { }
		return false;
	}

	var currtime=new Date();
	var chktime=new Date();

	chktime.setHours(hourval);
	chktime.setMinutes(minval);
	chktime.setSeconds(secval);

	if (type!='OTH') {
		if (!compareDates(chktime, fldLabel, currtime, 'current time', type)) {
			try {
				fldObject.focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function timeComparison(fldName1, fldLabel1, fldName2, fldLabel2, type) {
	var timeval1=getObj(fldName1).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var timeval2=getObj(fldName2).value.replace(/^\s+/g, '').replace(/\s+$/g, '');

	var hh1=timeval1.substring(0, timeval1.indexOf(':'));
	var min1=timeval1.substring(timeval1.indexOf(':')+1, timeval1.length);

	var hh2=timeval2.substring(0, timeval2.indexOf(':'));
	var min2=timeval2.substring(timeval2.indexOf(':')+1, timeval2.length);

	var time1=new Date();
	var time2=new Date();

	//added to fix the ticket #5028
	if (fldName1 == 'time_end' && (getObj('due_date') && getObj('date_start'))) {
		var due_date=getObj('due_date').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
		var start_date=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
		var dateval1 = splitDateVal(due_date);
		var dateval2 = splitDateVal(start_date);

		var dd1 = dateval1[0];
		var mm1 = dateval1[1];
		var yyyy1 = dateval1[2];

		var dd2 = dateval2[0];
		var mm2 = dateval2[1];
		var yyyy2 = dateval2[2];

		time1.setYear(yyyy1);
		time1.setMonth(mm1-1);
		time1.setDate(dd1);

		time2.setYear(yyyy2);
		time2.setMonth(mm2-1);
		time2.setDate(dd2);
	}

	time1.setHours(hh1);
	time1.setMinutes(min1);

	time2.setHours(hh2);
	time2.setMinutes(min2);
	if (type!='OTH') {
		if (!compareDates(time1, fldLabel1, time2, fldLabel2, type)) {
			try {
				getObj(fldName1).focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function numValidate(fldName, fldLabel, format, neg) {
	var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	if (typeof userCurrencySeparator != 'undefined' && userCurrencySeparator != '') {
		while (val.indexOf(userCurrencySeparator) != -1) {
			val = val.replace(userCurrencySeparator, '');
		}
	}
	if (typeof userDecimalSeparator != 'undefined' && userDecimalSeparator != '') {
		if (val.indexOf(userDecimalSeparator) != -1) {
			val = val.replace(userDecimalSeparator, '.');
		}
	}
	if (format!='any') {
		if (isNaN(val)) {
			var invalid=true;
		} else {
			var format=format.split(',');
			var splitval=val.split('.');
			if (neg==true) {
				if (splitval[0].indexOf('-')>=0) {
					if (splitval[0].length-1>format[0]) {
						invalid=true;
					}
				} else {
					if (splitval[0].length>format[0]) {
						invalid=true;
					}
				}
			} else {
				if (val<0) {
					invalid=true;
				} else if (format[0]==2 && splitval[0]==100 && (!splitval[1] || splitval[1]==0)) {
					invalid=false;
				} else if (splitval[0].length>format[0]) {
					invalid=true;
				}
			}
			if (splitval[1]) {
				if (splitval[1].length>format[1]) {
					invalid=true;
				}
			}
		}
		if (invalid==true) {
			alert(alert_arr.INVALID+fldLabel);
			try {
				getObj(fldName).focus();
			} catch (error) { }
			return false;
		} else {
			return true;
		}
	} else {
		// changes made -- to fix the ticket#3272
		if (fldName == 'probability' || fldName == 'commissionrate') {
			var splitval=val.split('.');
			var arr_len = splitval.length;
			var len = 0;

			if (arr_len > 1) {
				len = splitval[1].length;
			}
			if (isNaN(val)) {
				alert(alert_arr.INVALID+fldLabel);
				try {
					getObj(fldName).focus();
				} catch (error) { }
				return false;
			} else if (splitval[0] > 100 || len > 3 || (splitval[0] >= 100 && splitval[1] > 0)) {
				alert(fldLabel + alert_arr.EXCEEDS_MAX);
				return false;
			}
		} else {
			var splitval=val.split('.');
			if (splitval[0]>18446744073709551615) {
				alert(fldLabel + alert_arr.EXCEEDS_MAX);
				return false;
			}
		}

		if (neg==true) {
			var re=/^(-|)(\d)*(\.)?\d+(\.\d\d*)*$/;
		} else {
			var re=/^(\d)*(\.)?\d+(\.\d\d*)*$/;
		}
	}

	//for precision check. ie.number must contains only one "."
	var dotcount=0;
	for (var i = 0; i < val.length; i++) {
		if (val.charAt(i) == '.') {
			dotcount++;
		}
	}

	if (dotcount>1) {
		alert(alert_arr.INVALID+fldLabel);
		try {
			getObj(fldName).focus();
		} catch (error) { }
		return false;
	}

	if (!re.test(val)) {
		alert(alert_arr.INVALID+fldLabel);
		try {
			getObj(fldName).focus();
		} catch (error) { }
		return false;
	} else {
		return true;
	}
}

function intValidate(fldName, fldLabel) {
	var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	if (typeof userCurrencySeparator != 'undefined' && userCurrencySeparator != '') {
		while (val.indexOf(userCurrencySeparator) != -1) {
			val = val.replace(userCurrencySeparator, '');
		}
	}
	if (isNaN(val) || (val.indexOf('.')!=-1 && fldName != 'potential_amount' && fldName != 'list_price')) {
		alert(alert_arr.INVALID+fldLabel);
		try {
			getObj(fldName).focus();
		} catch (error) { }
		return false;
	} else if ((fldName != 'employees' || fldName != 'noofemployees') && (val < -2147483648 || val > 2147483647)) {
		alert(fldLabel +alert_arr.OUT_OF_RANGE);
		return false;
	} else if ((fldName == 'employees' || fldName != 'noofemployees') && (val < 0 || val > 2147483647)) {
		alert(fldLabel +alert_arr.OUT_OF_RANGE);
		return false;
	} else {
		return true;
	}
}

function numConstComp(fldName, fldLabel, type, constval) {
	return numConstCompObject(getObj(fldName), fldLabel, type, constval);
}

function numConstCompObject(fldObj, fldLabel, type, constval) {
	var val=parseFloat(fldObj.value.replace(/^\s+/g, '').replace(/\s+$/g, ''));
	constval=parseFloat(constval);

	var ret=true;
	switch (type) {
	case 'L' :
		if (val>=constval) {
			alert(fldLabel+alert_arr.SHOULDBE_LESS+constval);
			ret=false;
		}
		break;
	case 'LE' :
		if (val>constval) {
			alert(fldLabel+alert_arr.SHOULDBE_LESS_EQUAL+constval);
			ret=false;
		}
		break;
	case 'E' :
		if (val!=constval) {
			alert(fldLabel+alert_arr.SHOULDBE_EQUAL+constval);
			ret=false;
		}
		break;
	case 'NE' :
		if (val==constval) {
			alert(fldLabel+alert_arr.SHOULDNOTBE_EQUAL+constval);
			ret=false;
		}
		break;
	case 'G' :
		if (val<=constval) {
			alert(fldLabel+alert_arr.SHOULDBE_GREATER+constval);
			ret=false;
		}
		break;
	case 'GE' :
		if (val<constval) {
			alert(fldLabel+alert_arr.SHOULDBE_GREATER_EQUAL+constval);
			ret=false;
		}
		break;
	}

	if (ret==false) {
		try {
			fldObj.focus();
		} catch (error) { }
		return false;
	} else {
		return true;
	}
}

/* To get only filename from a given complete file path */
function getFileNameOnly(filename) {
	var onlyfilename = filename;
	// Normalize the path (to make sure we use the same path separator)
	var filename_normalized = filename.replace(/\\/g, '/');
	if (filename_normalized.lastIndexOf('/') != -1) {
		onlyfilename = filename_normalized.substring(filename_normalized.lastIndexOf('/') + 1);
	}
	return onlyfilename;
}

/* Function to validate the filename */
function validateFilename(form_ele) {
	if (form_ele.value == '') {
		return true;
	}
	var value = getFileNameOnly(form_ele.value);

	// Color highlighting logic
	var err_bg_color = '#FFAA22';

	if (typeof(form_ele.bgcolor) == 'undefined') {
		form_ele.bgcolor = form_ele.style.backgroundColor;
	}

	// Validation starts here
	var valid = true;

	/* Filename length is constrained to 255 at database level */
	if (value.length > 255) {
		alert(alert_arr.LBL_FILENAME_LENGTH_EXCEED_ERR);
		valid = false;
	}

	if (!valid) {
		form_ele.style.backgroundColor = err_bg_color;
		return false;
	}
	form_ele.style.backgroundColor = form_ele.bgcolor;
	form_ele.form[form_ele.name + '_hidden'].value = value;
	displayFileSize(form_ele);
	return true;
}

/* Function to validate the filsize */
function validateFileSize(form_ele, uploadSize) {
	if (form_ele.value == '') {
		return true;
	}
	var fileSize = form_ele.files[0].size;
	if (fileSize > uploadSize) {
		alert(alert_arr.LBL_SIZE_SHOULDNOTBE_GREATER + uploadSize/1000000+alert_arr.LBL_FILESIZEIN_MB);
		form_ele.value = '';
		document.getElementById('displaySize').innerHTML= '';
	} else {
		displayFileSize(form_ele);
	}
}

/* Function to Display FileSize while uploading */
function displayFileSize(form_ele) {
	var fileSize = form_ele.files[0].size;
	if (fileSize < 1024) {
		document.getElementById('displaySize').innerHTML = fileSize + alert_arr.LBL_FILESIZEIN_B;
	} else if (fileSize > 1024 && fileSize < 1048576) {
		document.getElementById('displaySize').innerHTML = Math.round(fileSize / 1024, 2) + alert_arr.LBL_FILESIZEIN_KB;
	} else if (fileSize > 1048576) {
		document.getElementById('displaySize').innerHTML = Math.round(fileSize / (1024 * 1024), 2) + alert_arr.LBL_FILESIZEIN_MB;
	}
}

function formValidate() {
	return doModuleValidation('');
}

function massEditFormValidate() {
	return doModuleValidation('mass_edit');
}

function run_massedit() {
	var me = massEditFormValidate();
	if (me==true) {
		var myFields = document.forms['massedit_form'];
		var sentForm = new Object();
		for (var f=0; f<myFields.length; f++) {
			if (myFields[f].type == 'checkbox') {
				if (myFields[f].checked != false) {
					var checked = 'on';
					sentForm[myFields[f].name] = checked;
				}
			} else if ((myFields[f].type == 'textarea') && (typeof(CKEDITOR)!=='undefined' && CKEDITOR.instances[myFields[f].name] !== undefined)) {
				sentForm[myFields[f].name] = CKEDITOR.instances[myFields[f].name].getData();
			} else if (myFields[f].type == 'radio' && myFields[f].checked) {
				sentForm[myFields[f].name] = myFields[f].value;
			} else if (myFields[f].type != 'radio' && myFields[f].type != 'button') {
				sentForm[myFields[f].name] = myFields[f].value;
			}
		}

		ExecuteFunctions('setSetting', 'skey=masseditids'+corebos_browsertabID+'&svalue='+sentForm['massedit_recordids']).then(function (response) {
		}, function (error) {
			console.log('error', error);
		});
		delete sentForm['massedit_recordids'];
		delete sentForm['idstring'];
		sentForm.corebos_browsertabID= corebos_browsertabID;

		var rdo = document.getElementById('relresultssection');
		rdo.style.visibility = 'visible';
		rdo.style.display = 'block';
		document.getElementById('massedit').style.display = 'none';

		var worker  = new Worker('massedit-worker.js');
		//a message is received
		worker.postMessage(sentForm);
		worker.addEventListener('message', function (e) {
			var message = e.data;
			if (e.data == 'CLOSE') {
				__addLog('<br><b>' + alert_arr.ProcessFINISHED + '!</b>');
				var pBar = document.getElementById('progressor');
				pBar.value = pBar.max; //max out the progress bar
			} else {
				__addLog(message.message);
				var pBar = document.getElementById('progressor');
				pBar.value = message.progress;
				var perc = document.getElementById('percentage');
				perc.innerHTML   = message.progress  + '% &nbsp;&nbsp;' + message.processed + '/' + message.total;
				perc.style.width = (Math.floor(pBar.clientWidth * (message.progress/100)) + 15) + 'px';
			}
		}, false);
		worker.postMessage(true);
	}
}

function stopTask() {
	cbierels_es.close();
	__addLog(mod_alert_arr.Interrupted);
}

function __addLog(message) {
	var r = document.getElementById('relresults');
	r.innerHTML += message + '<br>';
	r.scrollTop = r.scrollHeight;
}

function doModuleValidation(edit_type, editForm, callback) {
	if (editForm == undefined) {
		var formName = 'EditView';
	} else {
		var formName = editForm;
	}
	if (formName == 'QcEditView') {
		var isvalid = QCformValidate();
	} else {
		var isvalid = doformValidation(edit_type);
	}
	if (isvalid && edit_type!='mass_edit') {
		doServerValidation(edit_type, formName, callback);
	} else {
		return isvalid;
	}
	return false;
}

function doServerValidation(edit_type, formName, callback) {
	VtigerJS_DialogBox.block();
	if (edit_type=='mass_edit') {
		var action = 'MassEditSave';
	} else {
		var action = 'Save';
	}
	let SVModule = document.forms[formName].module.value;
	//Testing if a Validation file exists
	jQuery.ajax({
		url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=ValidationExists&valmodule='+SVModule,
		type:'get'
	}).fail(function (jqXHR, textStatus) {
		//Validation file does not exist
		if (typeof callback == 'function') {
			callback('submit');
		} else {
			submitFormForAction(formName, action);
		}
	}).done(function (data) {
		//Validation file exists
		if (data == 'yes') {
			// Create object which gets the values of all input, textarea, select and button elements from the form
			var myFields = document.forms[formName].elements;
			var sentForm = new Object();
			for (var f=0; f<myFields.length; f++) {
				if (myFields[f].type=='checkbox') {
					sentForm[myFields[f].name] = myFields[f].checked;
				} else if (myFields[f].type=='radio' && myFields[f].checked) {
					sentForm[myFields[f].name] = myFields[f].value;
				} else if (myFields[f].type!='radio') {
					sentForm[myFields[f].name] = myFields[f].value;
				}
			}
			//JSONize form data
			sentForm = JSON.stringify(sentForm);
			jQuery.ajax({
				type : 'post',
				data : {structure: sentForm},
				url : 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=ValidationLoad&valmodule='+SVModule
			}).done(function (msg) {
				//Validation file answers
				if (msg.search('%%%CONFIRM%%%') > -1) { //Allow to use confirm alert
					//message to display
					var display = msg.split('%%%CONFIRM%%%');
					if (confirm(display[1])) { //If you click on OK
						if (typeof callback == 'function') {
							callback('submit');
						} else {
							submitFormForAction(formName, action);
						}
					} else {
						VtigerJS_DialogBox.unblock();
					}
				} else if (msg.search('%%%OK%%%') > -1) { //No error
					if (typeof callback == 'function') {
						callback('submit');
					} else {
						submitFormForAction(formName, action);
					}
				} else { //Error
					alert(msg);
					VtigerJS_DialogBox.unblock();
				}
			}).fail(function () {
				//Error while asking file
				alert('Error with AJAX');
				VtigerJS_DialogBox.unblock();
			});
		} else { // no validation we send form
			if (typeof callback == 'function') {
				callback('submit');
			} else {
				submitFormForAction(formName, action);
			}
		}
	});
	return false;
}

function doformValidation(edit_type) {
	if (gVTModule == 'Contacts') {
		//Validation for Portal User
		//if existing portal value = 0, portal checkbox = checked, ( email field is not available OR  email is empty ) then we should not allow -- OR --
		//if existing portal value = 1, portal checkbox = checked, ( email field is available     AND email is empty ) then we should not allow
		if (edit_type=='') {
			if (getObj('existing_portal') != null && ((getObj('existing_portal').value == 0 && getObj('portal').checked && (getObj('email') == null
				|| trim(getObj('email').value) == '')) || (getObj('existing_portal').value == 1 && getObj('portal').checked && getObj('email') != null
				&& trim(getObj('email').value) == ''))
			) {
				alert(alert_arr.PORTAL_PROVIDE_EMAILID);
				return false;
			}
		} else {
			// This checks mass edit mode, but it doesn't make much sense to obligate this in mass edit mode
			//			if(getObj('portal') != null && getObj('portal').checked && getObj('portal_mass_edit_check').checked && (getObj('email') == null || trim(getObj('email').value) == '' || getObj('email_mass_edit_check').checked==false))
			//			{
			//				alert(alert_arr.PORTAL_PROVIDE_EMAILID);
			//				return false;
			//			}
			//			if((getObj('email') != null && trim(getObj('email').value) == '' && getObj('email_mass_edit_check').checked) && !(getObj('portal').checked==false && getObj('portal_mass_edit_check').checked))
			//			{
			//				alert(alert_arr.EMAIL_CHECK_MSG);
			//				return false;
			//			}
		}
	}
	if (gVTModule == 'SalesOrder') {
		if (edit_type == 'mass_edit') {
			if (getObj('enable_recurring_mass_edit_check') != null
				&& getObj('enable_recurring_mass_edit_check').checked
				&& getObj('enable_recurring') != null) {
				if (getObj('enable_recurring').checked && (getObj('recurring_frequency') == null
					|| trim(getObj('recurring_frequency').value) == '--None--' || getObj('recurring_frequency_mass_edit_check').checked==false)) {
					alert(alert_arr.RECURRING_FREQUENCY_NOT_PROVIDED);
					return false;
				}
				if (getObj('enable_recurring').checked == false && getObj('recurring_frequency_mass_edit_check').checked
					&& getObj('recurring_frequency') != null && trim(getObj('recurring_frequency').value) != '--None--') {
					alert(alert_arr.RECURRING_FREQNECY_NOT_ENABLED);
					return false;
				}
			}
		} else if (getObj('enable_recurring') != null && getObj('enable_recurring').checked) {
			if (getObj('recurring_frequency') == null || getObj('recurring_frequency').value == '--None--') {
				alert(alert_arr.RECURRING_FREQUENCY_NOT_PROVIDED);
				return false;
			}
			var start_period = getObj('start_period');
			var end_period = getObj('end_period');
			if (trim(start_period.value) == '' || trim(end_period.value) == '') {
				alert(alert_arr.START_PERIOD_END_PERIOD_CANNOT_BE_EMPTY);
				return false;
			}
		}
	}
	for (var i=0; i<fieldname.length; i++) {
		if (edit_type == 'mass_edit') {
			if (fieldname[i]!='salutationtype') {
				var obj = getObj(fieldname[i]+'_mass_edit_check');
			}
			if (obj == null || obj.checked == false) {
				continue;
			}
		}
		if (getObj(fieldname[i]) != null) {
			var type=fielddatatype[i].split('~');
			if (type[1]=='M') {
				if (!emptyCheck(fieldname[i], fieldlabel[i], getObj(fieldname[i]).type)) {
					return false;
				}
			}
			switch (type[0]) {
			case 'O' :
				break;
			case 'V' :
				break;
			case 'C' :
				break;
			case 'DT' :
				if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (type[1]=='M') {
						if (!emptyCheck(fieldname[2], fieldlabel[i], (type[2]!=undefined ? getObj(type[2]).type :''))) {
							return false;
						}
					}

					if (typeof(type[3])=='undefined') {
						var currdatechk='OTH';
					} else {
						var currdatechk=type[3];
					}

					if (!dateTimeValidate(fieldname[i], type[2], fieldlabel[i], currdatechk)) {
						return false;
					}
					if (type[4]) {
						if (!dateTimeComparison(fieldname[i], type[2], fieldlabel[i], type[5], type[6], type[4])) {
							return false;
						}
					}
				}
				break;
			case 'D' :
				if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (typeof(type[2])=='undefined') {
						var currdatechk='OTH';
					} else {
						var currdatechk=type[2];
					}

					if (!dateValidate(fieldname[i], fieldlabel[i], currdatechk)) {
						return false;
					}
					if (type[3]) {
						if (gVTModule == 'SalesOrder' && fieldname[i] == 'end_period'
								&& (getObj('enable_recurring') == null || getObj('enable_recurring').checked == false)) {
							continue;
						}
						if (!dateComparison(fieldname[i], fieldlabel[i], type[4], type[5], type[3])) {
							return false;
						}
					}
				}
				break;
			case 'T' :
				if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (typeof(type[2])=='undefined') {
						var currtimechk='OTH';
					} else {
						var currtimechk=type[2];
					}

					if (!timeValidate(fieldname[i], fieldlabel[i], currtimechk)) {
						return false;
					}
					if (type[3]) {
						if (!timeComparison(fieldname[i], fieldlabel[i], type[4], type[5], type[3])) {
							return false;
						}
					}
				}
				break;
			case 'I' :
				if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (getObj(fieldname[i]).value.length!=0) {
						if (!intValidate(fieldname[i], fieldlabel[i])) {
							return false;
						}
						if (type[2]) {
							if (!numConstComp(fieldname[i], fieldlabel[i], type[2], type[3])) {
								return false;
							}
						}
					}
				}
				break;
			case 'N' :
			case 'NN' :
				if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (getObj(fieldname[i]).value.length!=0) {
						if (typeof(type[2])=='undefined') {
							var numformat='any';
						} else {
							var numformat=type[2];
						}
						if (type[0]=='NN') {
							if (!numValidate(fieldname[i], fieldlabel[i], numformat, true)) {
								return false;
							}
						} else if (!numValidate(fieldname[i], fieldlabel[i], numformat)) {
							return false;
						}
						if (type[3]) {
							if (!numConstComp(fieldname[i], fieldlabel[i], type[3], type[4])) {
								return false;
							}
						}
					}
				}
				break;
			case 'E' :
				if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (getObj(fieldname[i]).value.length!=0) {
						var etype = 'EMAIL';
						if (!patternValidate(fieldname[i], fieldlabel[i], etype)) {
							return false;
						}
					}
				}
				break;
			}
			//start Birth day date validation
			if (fieldname[i] == 'birthday' && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0 ) {
				var now =new Date();
				var currtimechk='OTH';
				var datelabel = fieldlabel[i];
				var datefield = fieldname[i];
				var datevalue =getObj(datefield).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
				if (!dateValidate(fieldname[i], fieldlabel[i], currdatechk)) {
					try {
						getObj(datefield).focus();
					} catch (error) { }
					return false;
				} else {
					var datearr=splitDateVal(datevalue);
					var dd=datearr[0];
					var mm=datearr[1];
					var yyyy=datearr[2];
					var datecheck = new Date();
					datecheck.setYear(yyyy);
					datecheck.setMonth(mm-1);
					datecheck.setDate(dd);
					if (!compareDates(datecheck, datelabel, now, 'Current Date', 'L')) {
						try {
							getObj(datefield).focus();
						} catch (error) { }
						return false;
					}
				}
			}
		//End Birth day
		}
	}
	if (gVTModule == 'Contacts') {
		if (getObj('imagename')) {
			if (getObj('imagename').value != '') {
				var image_arr = new Array();
				image_arr = (getObj('imagename').value).split('.');
				var image_arr_last_index = image_arr.length - 1;
				if (image_arr_last_index < 0) {
					alert(alert_arr.LBL_WRONG_IMAGE_TYPE);
					return false;
				}
				var image_ext = image_arr[image_arr_last_index].toLowerCase();
				if (image_ext == 'jpeg' || image_ext == 'png' || image_ext == 'jpg' || image_ext == 'pjpeg' || image_ext == 'x-png' || image_ext == 'gif') {
					return true;
				} else {
					alert(alert_arr.LBL_WRONG_IMAGE_TYPE);
					return false;
				}
			}
		}
	}
	return true;
}

function clearId(fldName) {
	var currObj=getObj(fldName);
	currObj.value='';
}

function openPopUp(winInst, currObj, baseURL, winName, width, height, features) {
	var left=parseInt(findPosX(currObj));
	var top=parseInt(findPosY(currObj));

	if (window.navigator.appName!='Opera') {
		top+=parseInt(currObj.offsetHeight);
	} else {
		top+=(parseInt(currObj.offsetHeight)*2)+10;
	}

	if (browser_ie)	{
		top+=window.screenTop-document.body.scrollTop;
		left-=document.body.scrollLeft;
		if (top+height+30>window.screen.height) {
			top=findPosY(currObj)+window.screenTop-height-30;
		}//30 is a constant to avoid positioning issue
		if (left+width>window.screen.width) {
			left=findPosX(currObj)+window.screenLeft-width;
		}
	} else if (browser_nn4 || browser_nn6) {
		top+=(scrY-pgeY);
		left+=(scrX-pgeX);
		if (top+height+30>window.screen.height) {
			top=findPosY(currObj)+(scrY-pgeY)-height-30;
		}
		if (left+width>window.screen.width) {
			left=findPosX(currObj)+(scrX-pgeX)-width;
		}
	}

	features='width='+width+',height='+height+',top='+top+',left='+left+';'+features;
	eval(winInst+'=window.open("'+baseURL+'","'+winName+'","'+features+'")');
}

var scrX=0, scrY=0, pgeX=0, pgeY=0;

if (browser_nn4 || browser_nn6) {
	document.addEventListener('click', popUpListener, true);
}

function popUpListener(ev) {
	if (browser_nn4 || browser_nn6) {
		scrX=ev.screenX;
		scrY=ev.screenY;
		pgeX=ev.pageX;
		pgeY=ev.pageY;
	}
}

function toggleSelect(state, relCheckName) {
	if (getObj(relCheckName)) {
		if (typeof(getObj(relCheckName).length)=='undefined') {
			getObj(relCheckName).checked=state;
		} else {
			for (var i=0; i<getObj(relCheckName).length; i++) {
				getObj(relCheckName)[i].checked=state;
			}
		}
	}
}

function toggleSelectAll(relCheckName, selectAllName) {
	if (typeof(getObj(relCheckName).length)=='undefined') {
		getObj(selectAllName).checked=getObj(relCheckName).checked;
	} else {
		var atleastOneFalse=false;
		for (var i=0; i<getObj(relCheckName).length; i++) {
			if (getObj(relCheckName)[i].checked==false) {
				atleastOneFalse=true;
				break;
			}
		}
		getObj(selectAllName).checked=!atleastOneFalse;
	}
}
//added for show/hide 10July
function expandCont(bn) {
	var leftTab = document.getElementById(bn);
	leftTab.style.display = (leftTab.style.display == 'block') ? 'none' : 'block';
	var img = document.getElementById('img_'+bn);
	img.src=(img.src.indexOf('images/toggle1.gif')!=-1)?'themes/images/toggle2.gif':'themes/images/toggle1.gif';
	set_cookie(bn, leftTab.style.display);
}

function setExpandCollapse_gen() {
	var x = leftpanelistarray.length;
	for (var i = 0; i < x; i++) {
		var listObj=getObj(leftpanelistarray[i]);
		var tgImageObj=getObj('img_'+leftpanelistarray[i]);
		var status = get_cookie(leftpanelistarray[i]);
		if (status == 'block') {
			listObj.style.display='block';
			tgImageObj.src='themes/images/toggle2.gif';
		} else if (status == 'none') {
			listObj.style.display='none';
			tgImageObj.src='themes/images/toggle1.gif';
		}
	}
}

function toggleDiv(id) {
	var listTableObj=getObj(id);
	if (listTableObj.style.display=='block') {
		listTableObj.style.display='none';
	} else {
		listTableObj.style.display='block';
	}
//set_cookie(id,listTableObj.style.display)
}

/** This is Javascript Function which is used to toogle between
  * assigntype user and group/team select options while assigning owner to entity.
  */
function toggleAssignType(currType) {
	if (currType=='U') {
		getObj('assign_user').style.display='block';
		getObj('assign_team').style.display='none';
	} else {
		getObj('assign_user').style.display='none';
		getObj('assign_team').style.display='block';
	}
}

//to display type of address for google map
function showLocateMapMenu() {
	getObj('dropDownMenu').style.display='block';
	getObj('dropDownMenu').style.left=findPosX(getObj('locateMap'));
	getObj('dropDownMenu').style.top=findPosY(getObj('locateMap'))+getObj('locateMap').offsetHeight;
}

function hideLocateMapMenu(ev) {
	if (browser_ie) {
		currElement=window.event.srcElement;
	} else if (browser_nn4 || browser_nn6) {
		currElement=ev.target;
	}
	if (currElement.id!='locateMap') {
		if (getObj('dropDownMenu').style.display=='block') {
			getObj('dropDownMenu').style.display='none';
		}
	}
}

/*
* display the div tag
* @param divId :: div tag ID
*/
function show(divId) {
	if (getObj(divId)) {
		document.getElementById(divId).style.display = 'inline';
	}
}

/*
* display the div tag
* @param divId :: div tag ID
*/
function showBlock(divId) {
	document.getElementById(divId).style.display = 'block';
}

/*
* hide the div tag
* @param divId :: div tag ID
*/
function hide(divId) {
	document.getElementById(divId).style.display = 'none';
}

function fnhide(divId) {
	document.getElementById(divId).style.display = 'none';
}

function fnCopy(source, design) {
	document.getElementById(source).value=document.getElementById(design).value;
	document.getElementById(source).disabled=true;
}

function fnClear(source) {
	document.getElementById(source).value=' ';
	document.getElementById(source).disabled=false;
}

function fnCpy() {
	var tagName=document.getElementById('cpy');
	if (tagName.checked==true) {
		fnCopy('shipaddress', 'address');
		fnCopy('shippobox', 'pobox');
		fnCopy('shipcity', 'city');
		fnCopy('shipcode', 'code');
		fnCopy('shipstate', 'state');
		fnCopy('shipcountry', 'country');
	} else {
		fnClear('shipaddress');
		fnClear('shippobox');
		fnClear('shipcity');
		fnClear('shipcode');
		fnClear('shipstate');
		fnClear('shipcountry');
	}
}

function fnDown(obj) {
	var tagName = document.getElementById(obj);
	var tabName = document.getElementById('one');
	if (tagName.style.display == 'none') {
		tagName.style.display = 'block';
		tabName.style.display = 'block';
	} else {
		tabName.style.display = 'none';
		tagName.style.display = 'none';
	}
}

/*
* javascript function to add field rows
* @deprecated: not used anymore
*/
var count = 0;
var rowCnt = 1;
function fnAddSrch() {

	var tableName = document.getElementById('adSrc');
	var prev = tableName.rows.length;
	var count = prev;
	var row = tableName.insertRow(prev);

	if (count%2) {
		row.className = 'dvtCellLabel';
	} else {
		row.className = 'dvtCellInfo';
	}

	var fieldObject = document.getElementById('Fields0');
	var conditionObject = document.getElementById('Condition0');
	var searchValueObject = document.getElementById('Srch_value0');

	var columnone = document.createElement('td');
	var colone = fieldObject.cloneNode(true);
	colone.setAttribute('id', 'Fields'+count);
	colone.setAttribute('name', 'Fields'+count);
	colone.setAttribute('value', '');
	colone.onchange = function () {
		updatefOptions(colone, 'Condition'+count);
	};
	columnone.appendChild(colone);
	row.appendChild(columnone);

	var columntwo = document.createElement('td');
	var coltwo = conditionObject.cloneNode(true);
	coltwo.setAttribute('id', 'Condition'+count);
	coltwo.setAttribute('name', 'Condition'+count);
	coltwo.setAttribute('value', '');
	columntwo.appendChild(coltwo);
	row.appendChild(columntwo);

	var columnthree = document.createElement('td');
	var colthree = searchValueObject.cloneNode(true);
	colthree.setAttribute('id', 'Srch_value'+count);
	colthree.setAttribute('name', 'Srch_value'+count);
	colthree.setAttribute('value', '');
	colthree.value = '';
	columnthree.appendChild(colthree);
	row.appendChild(columnthree);

	updatefOptions(colone, 'Condition'+count);
}

function totalnoofrows() {
	var tableName = document.getElementById('adSrc');
	document.basicSearch.search_cnt.value = tableName.rows.length;
}

/*
* javascript function to delete field rows in advance search
* @param void :: void
*/
function delRow() {
	var tableName = document.getElementById('adSrc');
	var prev = tableName.rows.length;
	if (prev > 1) {
		document.getElementById('adSrc').deleteRow(prev-1);
	}
}

function fnVis(obj) {
	var profTag = document.getElementById('prof');
	var moreTag = document.getElementById('more');
	var addrTag = document.getElementById('addr');

	if (obj == 'prof') {
		document.getElementById('mnuTab').style.display = 'block';
		document.getElementById('mnuTab1').style.display = 'none';
		document.getElementById('mnuTab2').style.display = 'none';
		profTag.className = 'dvtSelectedCell';
		moreTag.className = 'dvtUnSelectedCell';
		addrTag.className = 'dvtUnSelectedCell';
	} else if (obj == 'more') {
		document.getElementById('mnuTab1').style.display = 'block';
		document.getElementById('mnuTab').style.display = 'none';
		document.getElementById('mnuTab2').style.display = 'none';
		moreTag.className = 'dvtSelectedCell';
		profTag.className = 'dvtUnSelectedCell';
		addrTag.className = 'dvtUnSelectedCell';
	} else if (obj == 'addr') {
		document.getElementById('mnuTab2').style.display = 'block';
		document.getElementById('mnuTab').style.display = 'none';
		document.getElementById('mnuTab1').style.display = 'none';
		addrTag.className = 'dvtSelectedCell';
		profTag.className = 'dvtUnSelectedCell';
		moreTag.className = 'dvtUnSelectedCell';
	}
}

function fnvsh(obj, Lay) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	tagName.style.left= leftSide + 175 + 'px';
	tagName.style.top= topSide + 'px';
	tagName.style.visibility = 'visible';
}

function fnvshobj(obj, Lay) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	if (maxW == '') {
		maxW = tagName.getBoundingClientRect();
		var widthM = maxW.width;
	} else {
		var widthM = maxW.substring(0, maxW.length-2);
	}
	if (widthM==0) {
		widthM = 360;
	} // element is still empty, we estimate some size to avoid going off screen
	if (Lay == 'editdiv') {
		leftSide = leftSide - 225;
		topSide = topSide - 125;
	} else if (Lay == 'transferdiv') {
		leftSide = leftSide - 10;
		//topSide = topSide;
	}
	var IE = document.all?true:false;
	if (IE) {
		if (document.getElementById('repposition1')) {
			if (topSide > 1200) {
				topSide = topSide-250;
			}
		}
	}

	var getVal = eval(leftSide) + eval(widthM);
	if (getVal > document.body.clientWidth ) {
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 34 + 'px';
	} else {
		tagName.style.left= leftSide + 'px';
	}
	tagName.style.top= topSide + 'px';
	tagName.style.display = 'block';
	tagName.style.visibility = 'visible';
}

function posLay(obj, Lay) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0, maxW.length-2);
	var getVal = eval(leftSide) + eval(widthM);
	if (getVal > document.body.clientWidth ) {
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 'px';
	} else {
		tagName.style.left= leftSide + 'px';
	}
	tagName.style.top= topSide + 'px';
}

function fninvsh(Lay) {
	var tagName = document.getElementById(Lay);
	tagName.style.visibility = 'hidden';
	tagName.style.display = 'none';
}

function fnvshNrm(Lay) {
	var tagName = document.getElementById(Lay);
	tagName.style.visibility = 'visible';
	tagName.style.display = 'block';
}

function cancelForm() {
	window.history.back();
}

function trim(str) {
	var s = str.replace(/\s+$/, '');
	s = s.replace(/^\s+/, '');
	return s;
}

function clear_form(form) {
	for (var j = 0; j < form.elements.length; j++) {
		if (form.elements[j].type == 'text' || form.elements[j].type == 'select-one') {
			form.elements[j].value = '';
		}
	}
}

function ActivateCheckBox() {
	var map = document.getElementById('saved_map_checkbox');
	var source = document.getElementById('saved_source');
	if (map.checked == true) {
		source.disabled = false;
	} else {
		source.disabled = true;
	}
}

function addOnloadEvent(fnc) {
	if (typeof window.addEventListener != 'undefined') {
		window.addEventListener('load', fnc, false);
	} else if (typeof window.attachEvent != 'undefined') {
		window.attachEvent('onload', fnc);
	} else {
		if (window.onload != null) {
			var oldOnload = window.onload;
			window.onload = function (e) {
				oldOnload(e);
				window[fnc]();
			};
		} else {
			window.onload = fnc;
		}
	}
}

function InternalMailer(record_id, field_id, field_name, par_module, type) {
	var url;
	switch (type) {
	case 'record_id':
		url = 'index.php?module=Emails&action=EmailsAjax&internal_mailer=true&type='+type+'&field_id='+field_id+'&rec_id='+record_id+'&fieldname='+field_name+'&file=EditView&par_module='+par_module;//query string field_id added for listview-compose email issue
		break;
	case 'email_addy':
		url = 'index.php?module=Emails&action=EmailsAjax&internal_mailer=true&type='+type+'&email_addy='+record_id+'&file=EditView';
		break;
	}
	var opts = 'menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes';
	openPopUp('xComposeEmail', this, url, 'createemailWin', 1000, 800, opts);
}

function fnHide_Event(obj) {
	document.getElementById(obj).style.visibility = 'hidden';
}

function ReplyCompose(id, mode) {
	var url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&record='+id+'&reply=true';
	openPopUp('xComposeEmail', this, url, 'createemailWin', 1000, 800, 'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
}

function OpenCompose(id, mode, crmid) {
	var modeparts = mode.split(':');
	var url = '';
	var i18n = '';
	if (modeparts.length>1) {
		mode = modeparts[0];
		i18n = modeparts[1];
	}
	switch (mode) {
	case 'edit':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&record='+id;
		break;
	case 'create':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView';
		break;
	case 'forward':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&record='+id+'&forward=true';
		break;
	case 'reply':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&record='+id+'&reply=true';
		break;
	case 'Invoice':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&pmodule=Invoice&attachment='+i18n+'_'+id+'.pdf&invmodid='+crmid;
		break;
	case 'PurchaseOrder':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&pmodule=PurchaseOrder&attachment='+i18n+'_'+id+'.pdf&invmodid='+crmid;
		break;
	case 'SalesOrder':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&pmodule=SalesOrder&attachment='+i18n+'_'+id+'.pdf&invmodid='+crmid;
		break;
	case 'Quote':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&pmodule=Quotes&attachment='+i18n+'_'+id+'.pdf&invmodid='+crmid;
		break;
	case 'Documents':
		url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&pmodule=Documents&attachment='+id+'';
		break;
	case 'print':
		url = 'index.php?module=Emails&action=EmailsAjax&file=PrintEmail&record='+id+'&print=true';
	}
	openPopUp('xComposeEmail', this, url, 'createemailWin', 920, 700, 'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
}

//Function added for Mass select in Popup - Philip
function SelectAll(mod, parmod) {
	var k=0;
	var i=0;
	if (parmod === 'Emails' && document.selectall.selected_id !== undefined) {
		var tempids = document.selectall.selected_id;
		for (k=0; k < tempids.length; k++) {
			var id = tempids[k].value;
			var attachment = document.getElementById('document_attachment_' + id).value;
			if (tempids[k].checked && attachment !== '') {
				window.opener.addOption(id, attachment);
			}
		}
		return false;
	}
	if (document.selectall.selected_id != undefined) {
		x = document.selectall.selected_id.length;
		var y=0;
		if (parmod != 'Calendar') {
			var module = window.opener.document.getElementById('RLreturn_module').value;
			var entity_id = window.opener.document.getElementById('RLparent_id').value;
			var parenttab = window.opener.document.getElementById('parenttab').value;
		}
		idstring = '';
		namestr = '';

		if (x == undefined) {
			if (document.selectall.selected_id.checked) {
				idstring = document.selectall.selected_id.value;
				if (parmod == 'Calendar') {
					namestr = document.getElementById('calendarCont'+idstring).innerHTML;
				}
				y=1;
			} else {
				alert(alert_arr.SELECT);
				return false;
			}
		} else {
			y=0;
			for (i = 0; i < x; i++) {
				if (document.selectall.selected_id[i].checked) {
					idstring = document.selectall.selected_id[i].value +';'+idstring;
					if (parmod == 'Calendar') {
						idval = document.selectall.selected_id[i].value;
						namestr = document.getElementById('calendarCont'+idval).innerHTML+'\n'+namestr;
					}
					y=y+1;
				}
			}
		}
		if (y != 0) {
			document.selectall.idlist.value=idstring;
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
		if (confirm(alert_arr.ADD_CONFIRMATION+y+alert_arr.RECORDS)) {
			if (parmod == 'Calendar') {
				//this blcok has been modified to provide delete option for contact in Calendar
				idval = window.opener.document.EditView.contactidlist.value;
				if (idval != '') {
					var avalIds = new Array();
					avalIds = idstring.split(';');

					var selectedIds = new Array();
					selectedIds = idval.split(';');

					for (i=0; i < (avalIds.length-1); i++) {
						var rowFound=false;
						for (k=0; k < selectedIds.length; k++) {
							if (selectedIds[k]==avalIds[i]) {
								rowFound=true;
								break;
							}
						}
						if (rowFound != true) {
							idval = idval+';'+avalIds[i];
							window.opener.document.EditView.contactidlist.value = idval;
							var str=document.getElementById('calendarCont'+avalIds[i]).innerHTML;
							window.opener.addOption(avalIds[i], str);
						}
					}
				} else {
					window.opener.document.EditView.contactidlist.value = idstring;
					var temp = new Array();
					temp = namestr.split('\n');

					var tempids = new Array();
					tempids = idstring.split(';');

					for (k=0; k < temp.length; k++) {
						window.opener.addOption(tempids[k], temp[k]);
					}
				}
			//end
			} else {
				opener.document.location.href='index.php?module='+module+'&parentid='+entity_id+'&action=updateRelations&destination_module='+mod+'&idlist='+idstring+'&parenttab='+parenttab;
			}
			if (document.getElementById('closewindow').value=='true') {
				self.close();
			}
		} else {
			return false;
		}
	}
}

function ShowEmail(id) {
	var url = 'index.php?module=Emails&action=EmailsAjax&file=DetailView&record='+id;
	openPopUp('xComposeEmail', this, url, 'createemailWin', 820, 695, 'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
}

var bSaf = (navigator.userAgent.indexOf('Safari') != -1);
var bOpera = (navigator.userAgent.indexOf('Opera') != -1);
var bMoz = (navigator.appName == 'Netscape');

function execJS(node) {
	var st = node.getElementsByTagName('SCRIPT');
	var strExec;
	for (var i=0; i<st.length; i++) {
		if (bSaf) {
			strExec = st[i].innerHTML;
		} else if (bOpera) {
			strExec = st[i].text;
		} else if (bMoz) {
			strExec = st[i].textContent;
		} else {
			strExec = st[i].text;
		}
		try {
			eval(strExec);
		} catch (e) {
			alert(e);
		}
	}
}

//Function added for getting the Tab Selected Values (Standard/Advanced Filters) for Custom View - Ahmed
function fnLoadCvValues(obj1, obj2, SelTab, unSelTab) {
	var tabName1 = document.getElementById(obj1);
	var tabName2 = document.getElementById(obj2);
	var tagName1 = document.getElementById(SelTab);
	var tagName2 = document.getElementById(unSelTab);
	if (tabName1.className == 'dvtUnSelectedCell') {
		tabName1.className = 'dvtSelectedCell';
	}

	if (tabName2.className == 'dvtSelectedCell') {
		tabName2.className = 'dvtUnSelectedCell';
	}

	tagName1.style.display='block';
	tagName2.style.display='none';
}

// Drop Dwon Menu
function fnDropDown(obj, Lay) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0, maxW.length-2);
	var getVal = eval(leftSide) + eval(widthM);
	if (getVal > document.body.clientWidth ) {
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 34 + 'px';
	} else {
		tagName.style.left= leftSide + 'px';
	}
	tagName.style.top= topSide + obj.clientHeight +'px';
	tagName.style.display = 'block';
}

function fnShowDrop(obj) {
	document.getElementById(obj).style.display = 'block';
}

function fnHideDrop(obj) {
	document.getElementById(obj).style.display = 'none';
}

function getCalendarPopup(imageid, fieldid, dateformat) {
	Calendar.setup({
		inputField : fieldid,
		ifFormat : dateformat,
		showsTime : false,
		button : imageid,
		singleClick : true,
		step : 1
	});
}

//Added to check duplicate account creation
function AjaxDuplicateValidate(module, fieldname, oform) {
	var fieldvalue = encodeURIComponent(trim(getObj(fieldname).value));
	var recordid = getObj('record').value;
	if (fieldvalue == '') {
		alert(alert_arr.ACCOUNTNAME_CANNOT_EMPTY);
		return false;
	}
	VtigerJS_DialogBox.block();
	var url = 'module='+module+'&action='+module+'Ajax&file=Save&'+fieldname+'='+fieldvalue+'&dup_check=true&record='+recordid;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+url
	}).done(function (response) {
		var str = response;
		if (str.indexOf('SUCCESS') > -1) {
			oform.submit();
		} else {
			VtigerJS_DialogBox.unblock();
			alert(str);
			return false;
		}
	});
}

/**to get SelectContacts Popup
check->to check select options enable or disable
*type->to differentiate from task
*frmName->form name*/
function selectContact(check, type, frmName) {
	var record = document.getElementsByName('record')[0].value;
	if (document.getElementById('single_accountid')) {
		var potential_id = '';
		if (document.getElementById('potential_id')) {
			potential_id = frmName.potential_id.value;
		}
		account_id = frmName.account_id.value;
		if (potential_id != '') {
			record_id = potential_id;
			module_string = '&parent_module=Potentials';
		} else {
			record_id = account_id;
			module_string = '&parent_module=Accounts';
		}
		if (record_id != '') {
			window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView'+module_string+'&relmod_id='+record_id, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
		} else {
			window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
		}
	} else if (document.getElementById('vendor_name') && gVTModule=='PurchaseOrder') {
		record_id = frmName.vendor_id.value;
		module_string = '&parent_module=Vendors';
		if (record_id != '') {
			window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView'+module_string+'&relmod_id='+record_id, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
		} else {
			window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
		}
	} else if ((document.getElementById('parentid')) && type != 'task') {
		if (getObj('parent_type')) {
			rel_parent_module = frmName.parent_type.value;
			record_id = frmName.parent_id.value;
			module = rel_parent_module.split('&');
			if (record_id != '' && module[0] == 'Leads') {
				alert(alert_arr.CANT_SELECT_CONTACTS);
			} else {
				if (check == 'true') {
					search_string = '&return_module=Calendar&select=enable&popuptype=detailview&form_submit=false';
				} else {
					search_string='&popuptype=specific';
				}
				if (record_id != '') {
					window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&form=EditView'+search_string+'&relmod_id='+record_id+'&parent_module='+module[0], 'test', 'width=640,height=602,resizable=0,scrollbars=0');
				} else {
					window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&form=EditView'+search_string, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
				}
			}
		} else {
			window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&return_module=Calendar&select=enable&popuptype=detailview&form=EditView&form_submit=false', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
		}
	} else if ((document.getElementById('contact_name')) && type == 'task') {
		var formName = frmName.name;
		var task_recordid = '';
		var popuptype = '';
		if (formName == 'EditView') {
			if (document.getElementById('parent_type')) {
				task_parent_module = frmName.parent_type.value;
				task_recordid = frmName.parent_id.value;
				task_module = task_parent_module.split('&');
				popuptype='&popuptype=specific';
			}
		}
		if (task_recordid != '' && task_module[0] == 'Leads' ) {
			alert(alert_arr.CANT_SELECT_CONTACTS);
		} else {
			if (popuptype != '') {
				window.open('index.php?module=Contacts&action=Popup&html=Popup_picker'+popuptype+'&form='+formName+'&task_relmod_id='+task_recordid+'&task_parent_module='+task_module[0], 'test', 'width=640,height=602,resizable=0,scrollbars=0');
			} else {
				window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form='+formName, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
			}
		}
	} else {
		window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView&recordid='+record, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	}
}

//to get Select Potential Popup
function selectPotential() {
	// To support both B2B and B2C model
	var record_id = '';
	var parent_module = '';
	var acc_element = document.EditView.account_id;
	var cnt_element = document.EditView.contact_id;
	if (acc_element != null) {
		record_id= acc_element.value;
		parent_module = 'Accounts';
	} else if (cnt_element != null) {
		record_id= cnt_element.value;
		parent_module = 'Contacts';
	}
	if (record_id != '') {
		window.open('index.php?module=Potentials&action=Popup&html=Popup_picker&popuptype=specific_potential_account_address&form=EditView&relmod_id='+record_id+'&parent_module='+parent_module, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	} else {
		window.open('index.php?module=Potentials&action=Popup&html=Popup_picker&popuptype=specific_potential_account_address&form=EditView', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	}
}

//to select Quote Popup
function selectQuote() {
	// To support both B2B and B2C model
	var record_id = '';
	var parent_module = '';
	var acc_element = document.EditView.account_id;
	var cnt_element = document.EditView.contact_id;
	if (acc_element != null) {
		record_id= acc_element.value;
		parent_module = 'Accounts';
	} else if (cnt_element != null) {
		record_id= cnt_element.value;
		parent_module = 'Contacts';
	}
	if (record_id != '') {
		window.open('index.php?module=Quotes&action=Popup&html=Popup_picker&popuptype=specific&form=EditView&relmod_id='+record_id+'&parent_module='+parent_module, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	} else {
		window.open('index.php?module=Quotes&action=Popup&html=Popup_picker&popuptype=specific&form=EditView', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	}
}

//to get select SalesOrder Popup
function selectSalesOrder() {
	// To support both B2B and B2C model
	var record_id = '';
	var parent_module = '';
	var acc_element = document.EditView.account_id;
	var cnt_element = document.EditView.contact_id;
	if (acc_element != null) {
		record_id= acc_element.value;
		parent_module = 'Accounts';
	} else if (cnt_element != null) {
		record_id= cnt_element.value;
		parent_module = 'Contacts';
	}
	if (record_id != '') {
		window.open('index.php?module=SalesOrder&action=Popup&html=Popup_picker&popuptype=specific&form=EditView&relmod_id='+record_id+'&parent_module='+parent_module, 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	} else {
		window.open('index.php?module=SalesOrder&action=Popup&html=Popup_picker&popuptype=specific&form=EditView', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	}
}

function checkEmailid(parent_module, emailid, secondaryemail) {
	var check = true;
	if (emailid == '' && secondaryemail == '') {
		alert(alert_arr.LBL_THIS+parent_module+alert_arr.DOESNOT_HAVE_MAILIDS);
		check=false;
	}
	return check;
}

function calQCduedatetime() {
	var datefmt = document.QcEditView.dateFormat.value;
	var type = document.QcEditView.activitytype.value;
	var dateval1=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var dateelements1=splitDateVal(dateval1);
	var dd1=parseInt(dateelements1[0], 10);
	var mm1=dateelements1[1];
	var yyyy1=dateelements1[2];
	var date1=new Date();
	date1.setYear(yyyy1);
	date1.setMonth(mm1-1, dd1+1);
	var yy = date1.getFullYear();
	var mm = date1.getMonth() + 1;
	var dd = date1.getDate();
	var date = document.QcEditView.date_start.value;
	var starttime = document.QcEditView.time_start.value;
	if (!timeValidate('time_start', ' Start Date & Time', 'OTH')) {
		return false;
	}
	var timearr = starttime.split(':');
	var hour = parseInt(timearr[0], 10);
	var min = parseInt(timearr[1], 10);
	dd = _2digit(dd);
	mm = _2digit(mm);
	var tempdate = yy+'-'+mm+'-'+dd;
	if (datefmt == '%d-%m-%Y') {
		tempdate = dd+'-'+mm+'-'+yy;
	} else if (datefmt == '%m-%d-%Y') {
		tempdate = mm+'-'+dd+'-'+yy;
	}
	if (type == 'Meeting') {
		hour = hour + 1;
		if (hour == 24) {
			hour = 0;
			date = tempdate;
		}
		hour = _2digit(hour);
		min = _2digit(min);
		document.QcEditView.due_date.value = date;
		document.QcEditView.time_end.value = hour+':'+min;
	}
	if (type == 'Call') {
		if (min >= 55) {
			min = min%55;
			hour = hour + 1;
		} else {
			min = min + 5;
		}
		if (hour == 24) {
			hour = 0;
			date = tempdate;
		}
		hour = _2digit(hour);
		min = _2digit(min);
		document.QcEditView.due_date.value = date;
		document.QcEditView.time_end.value = hour+':'+min;
	}
}

function _2digit( no ) {
	if (no < 10) {
		return '0' + no;
	} else {
		return '' + no;
	}
}

function confirmdelete(url) {
	if (confirm(alert_arr.ARE_YOU_SURE)) {
		document.location.href=url;
	}
}

//function modified to apply the patch ref : Ticket #4065
function valid(c, type) {
	if (type == 'name') {
		return (((c >= 'a') && (c <= 'z')) ||((c >= 'A') && (c <= 'Z')) ||((c >= '0') && (c <= '9')) || (c == '.') || (c == '_') || (c == '-') || (c == '@') );
	} else if (type == 'namespace') {
		return (((c >= 'a') && (c <= 'z')) ||((c >= 'A') && (c <= 'Z')) ||((c >= '0') && (c <= '9')) || (c == '.')||(c==' ') || (c == '_') || (c == '-') );
	}
}

function CharValidation(s, type) {
	for (var i = 0; i < s.length; i++) {
		if (!valid(s.charAt(i), type)) {
			return false;
		}
	}
	return true;
}

/** Check Upload file is in specified format(extension).
  * @param fldName -- name of the file field
  * @param filter -- List of file extensions to allow. each extension must be seperated with a | sybmol.
  * Example: upload_filter("imagename","Image", "jpg|gif|bmp|png")
  * @returns true -- if the extension is IN  specified extension.
  * @returns false -- if the extension is NOT IN specified extension.
  *
  * NOTE: If this field is mandatory,  please call emptyCheck() function before calling this function.
 */
function upload_filter(fldName, filter) {
	var currObj=getObj(fldName);
	if (currObj.value !='') {
		var file=currObj.value;
		var type=file.toLowerCase().split('.');
		var valid_extn=filter.toLowerCase().split('|');
		if (valid_extn.indexOf(type[type.length-1]) == -1) {
			alert(alert_arr.PLS_SELECT_VALID_FILE+valid_extn);
			try {
				currObj.focus();
			} catch (error) {
			// Fix for IE: If element or its wrapper around it is hidden, setting focus will fail
			// So using the try { } catch(error) { }
			}
			return false;
		}
	}
	return true;
}

function validateUrl(name) {
	var Url = getObj(name);
	var wProtocol;
	var oRegex = new Object();
	oRegex.UriProtocol = new RegExp('');
	oRegex.UriProtocol.compile('^(((http):\/\/)|mailto:)', 'gi');
	oRegex.UrlOnChangeProtocol = new RegExp('');
	oRegex.UrlOnChangeProtocol.compile('^(http)://(?=.)', 'gi');
	wUrl = Url.value;
	wProtocol=oRegex.UrlOnChangeProtocol.exec(wUrl);
	if (wProtocol) {
		wUrl = wUrl.substr(wProtocol[0].length);
		Url.value = wUrl;
	}
}

function LTrim( value ) {
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, '$1');
}

function selectedRecords(module, category) {
	var allselectedboxes = document.getElementById('allselectedboxes');
	var idstring = (allselectedboxes == null)? '' : allselectedboxes.value;
	var viewid = getviewId();
	var url = '&viewname='+viewid;
	if (document.getElementById('excludedRecords') != null && typeof(document.getElementById('excludedRecords')) != 'undefined') {
		var excludedRecords = document.getElementById('excludedRecords').value;
		var searchurl = document.getElementById('search_url').value;
		url = url+searchurl+'&excludedRecords='+excludedRecords;
	}
	if (idstring != '') {
		window.location.href='index.php?module='+module+'&action=ExportRecords&parenttab='+category+'&idstring='+idstring+url;
	} else {
		window.location.href='index.php?module='+module+'&action=ExportRecords&parenttab='+category;
	}
	return false;
}

function record_export(module, category, exform, idstring) {
	var searchType = document.getElementsByName('search_type');
	var exportData = document.getElementsByName('export_data');
	for (var i=0; i<2; i++) {
		if (searchType[i].checked == true) {
			var sel_type = searchType[i].value;
		}
	}
	for (i=0; i<3; i++) {
		if (exportData[i].checked == true) {
			var exp_type = exportData[i].value;
		}
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+module+'&action=ExportAjax&export_record=true&search_type='+sel_type+'&export_data='+exp_type+'&idstring='+idstring
	}).done(function (response) {
		if (response == 'NOT_SEARCH_WITHSEARCH_ALL') {
			document.getElementById('not_search').style.display = 'block';
			document.getElementById('not_search').innerHTML='<font color=\'red\'><b>'+alert_arr.LBL_NOTSEARCH_WITHSEARCH_ALL+' '+module+'</b></font>';
			setTimeout(hideErrorMsg1, 6000);
			exform.submit();
		} else if (response == 'NOT_SEARCH_WITHSEARCH_CURRENTPAGE') {
			document.getElementById('not_search').style.display = 'block';
			document.getElementById('not_search').innerHTML='<font color=\'red\'><b>'+alert_arr.LBL_NOTSEARCH_WITHSEARCH_CURRENTPAGE+' '+module+'</b></font>';
			setTimeout(hideErrorMsg1, 7000);
			exform.submit();
		} else if (response == 'NO_DATA_SELECTED') {
			document.getElementById('not_search').style.display = 'block';
			document.getElementById('not_search').innerHTML='<font color=\'red\'><b>'+alert_arr.LBL_NO_DATA_SELECTED+'</b></font>';
			setTimeout(hideErrorMsg1, 3000);
		} else if (response == 'SEARCH_WITHOUTSEARCH_ALL') {
			if (confirm(alert_arr.LBL_SEARCH_WITHOUTSEARCH_ALL)) {
				exform.submit();
			}
		} else if (response == 'SEARCH_WITHOUTSEARCH_CURRENTPAGE') {
			if (confirm(alert_arr.LBL_SEARCH_WITHOUTSEARCH_CURRENTPAGE)) {
				exform.submit();
			}
		} else {
			exform.submit();
		}
	});
}

function hideErrorMsg1() {
	document.getElementById('not_search').style.display = 'none';
}

// Replace the % sign with %25 to make sure the AJAX url is going wel.
function escapeAll(tagValue) {
	//return escape(tagValue.replace(/%/g, '%25'));
	if (default_charset.toLowerCase() == 'utf-8') {
		return encodeURIComponent(tagValue.replace(/%/g, '%25'));
	} else {
		return escape(tagValue.replace(/%/g, '%25'));
	}
}

function removeHTMLFormatting(str) {
	str = str.replace(/<([^<>]*)>/g, ' ');
	str = str.replace(/&nbsp;/g, ' ');
	return str;
}

function get_converted_html(str) {
	var temp = str.toLowerCase();
	if (temp.indexOf('<') != '-1' || temp.indexOf('>') != '-1') {
		str = str.replace(/</g, '&lt;');
		str = str.replace(/>/g, '&gt;');
	}
	if (temp.match(/(script).*(\/script)/)) {
		str = str.replace(/&/g, '&amp;');
	} else if (temp.indexOf('&') != '-1') {
		str = str.replace(/&/g, '&amp;');
	}
	return str;
}

//To select the select all check box(if all the items are selected) when the form loads.
function default_togglestate(obj_id, elementId) {
	var all_state=true;
	var groupElements = document.getElementsByName(obj_id);
	for (var i=0; i<groupElements.length; i++) {
		var state=groupElements[i].checked;
		if (state == false) {
			all_state=false;
			break;
		}
	}
	if (typeof elementId=='undefined') {
		elementId = 'selectall';
	}
	if (getObj(elementId)) {
		getObj(elementId).checked=all_state;
	}
}

//for select multiple check box in multiple pages for Campaigns related list:
function rel_check_object(sel_id, module) {
	var selected;
	var select_global = new Array();
	var cookie_val = get_cookie(module+'_all');
	if (cookie_val == null) {
		selected = sel_id.value+';';
	} else {
		selected = trim(cookie_val);
	}
	select_global = selected.split(';');
	var box_value = sel_id.checked;
	var id = sel_id.value;
	var duplicate = select_global.indexOf(id);
	var size = select_global.length-1;
	var result = '';
	var currentModule = document.getElementById('return_module').value;
	var excluded = document.getElementById(currentModule+'_'+module+'_excludedRecords').value;
	var i=0;
	if (box_value == true) {
		if (document.getElementById(currentModule+'_'+module+'_selectallActivate').value == 'true') {
			document.getElementById(currentModule+'_'+module+'_excludedRecords').value = excluded.replace(excluded.match(id+';'), '');
		} else {
			if (duplicate == '-1') {
				select_global[size]=id;
			}
			size = select_global.length-1;
			for (i=0; i<=size; i++) {
				if (trim(select_global[i])!='') {
					result = select_global[i]+';'+result;
				}
			}
		}
		rel_default_togglestate(module);
	} else {
		if (document.getElementById(currentModule+'_'+module+'_selectallActivate').value == 'true') {
			document.getElementById(currentModule+'_'+module+'_excludedRecords').value= id+';'+excluded;
		}
		if (duplicate != '-1') {
			select_global.splice(duplicate, 1);
		}
		size=select_global.length-1;
		for (i=size; i>=0; i--) {
			if (trim(select_global[i])!='') {
				result=select_global[i]+';'+result;
			}
		}
		getObj(module+'_selectall').checked=false;
	}
	set_cookie(module+'_all', result);
}

//Function to select all the items in the current page for Campaigns related list:.
function rel_toggleSelect(state, relCheckName, module) {
	var obj = document.getElementsByName(relCheckName);
	if (obj) {
		for (var i=0; i<obj.length; i++) {
			obj[i].checked = state;
			rel_check_object(obj[i], module);
		}
	}
	var current_module = document.getElementById('return_module').value;
	if (current_module == 'Campaigns') {
		if (state == true) {
			var count = document.getElementById(current_module+'_'+module+'_numOfRows').value;
			if (count == '') {
				getNoOfRelatedRows(current_module, module);
			}
			if (parseInt(document.getElementById('maxrecords').value) < parseInt(count)) {
				document.getElementById(current_module+'_'+module+'_linkForSelectAll').style.display='block';
			}
		} else {
			if (document.getElementById(current_module+'_'+module+'_selectallActivate').value == 'true') {
				document.getElementById(current_module+'_'+module+'_linkForSelectAll').style.display='block';
			} else {
				document.getElementById(current_module+'_'+module+'_linkForSelectAll').style.display='none';
			}
		}
	}
}

//To select the select all check box(if all the items are selected) when the form loads for Campaigns related list:.
function rel_default_togglestate(module) {
	var all_state=true;
	var currentModule = document.getElementById('return_module').value;
	if (currentModule == 'Campaigns') {
		var groupElements = document.getElementsByName(currentModule+'_'+module+'_selected_id');
	} else {
		var groupElements = document.getElementsByName(module+'_selected_id');
	}
	if (typeof(groupElements) == 'undefined') {
		return;
	}

	for (var i=0; i<groupElements.length; i++) {
		var state=groupElements[i].checked;
		if (state == false) {
			all_state=false;
			break;
		}
	}
	if (getObj(module+'_selectall')) {
		getObj(module+'_selectall').checked=all_state;
	}
}

//To clear all the checked items in all the pages for Campaigns related list:
function clear_checked_all(module) {
	var cookie_val=get_cookie(module+'_all');
	if (cookie_val != null) {
		delete_cookie(module+'_all');
	}
	//Uncheck all the boxes in current page..
	var obj = document.getElementsByName(module+'_selected_id');
	if (obj) {
		for (var i=0; i<obj.length; i++) {
			obj[i].checked=false;
		}
	}
	if (getObj(module+'_selectall')) {
		getObj(module+'_selectall').checked=false;
	}
}

//groupParentElementId is added as there are multiple groups in Documents listview.
function toggleSelect_ListView(state, relCheckName, groupParentElementId) {
	var obj = document.getElementsByName(relCheckName);
	if (obj) {
		for (var i=0; i<obj.length; i++) {
			obj[i].checked=state;
			if (typeof(check_object) == 'function') {
				// This function is defined in ListView.js (check for existence)
				check_object(obj[i], groupParentElementId);
			}
		}
	}
	if (document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View) {
		if (state==true) {
			var count = document.getElementById('numOfRows_'+groupParentElementId).value;
			if (count == '') {
				getNoOfRows(groupParentElementId);
				count = document.getElementById('numOfRows_'+groupParentElementId).value;
			}
			if (parseInt(document.getElementById('maxrecords').value) < parseInt(count)) {
				document.getElementById('linkForSelectAll_'+groupParentElementId).style.display='table-cell';
			}
		} else {
			if (document.getElementById('selectedboxes_'+groupParentElementId).value == 'all') {
				document.getElementById('linkForSelectAll_'+groupParentElementId).style.display='table-cell';
			} else {
				document.getElementById('linkForSelectAll_'+groupParentElementId).style.display='none';
			}
		}
	} else {
		if (state==true) {
			var count = document.getElementById('numOfRows').value;
			if (count == '') {
				getNoOfRows();
				count = document.getElementById('numOfRows').value;
			}
			if (parseInt(document.getElementById('maxrecords').value) < parseInt(count)) {
				document.getElementById('linkForSelectAll').style.display='table-cell';
			}
		} else {
			if (document.getElementById('allselectedboxes').value == 'all') {
				document.getElementById('linkForSelectAll').style.display='table-cell';
			} else {
				document.getElementById('linkForSelectAll').style.display='none';
			}
		}
	}
}

function gotourl(url) {
	document.location.href=url;
}

// Function to display the element with id given by showid and hide the element with id given by hideid
function toggleShowHide(showid, hideid) {
	var show_ele = document.getElementById(showid);
	var hide_ele = document.getElementById(hideid);
	if (show_ele != null) {
		show_ele.style.display = 'inline';
	}
	if (hide_ele != null) {
		hide_ele.style.display = 'none';
	}
}

// Refactored APIs from DisplayFiels.tpl
function fnshowHide(currObj, txtObj) {
	if (currObj.checked == true) {
		document.getElementById(txtObj).style.visibility = 'visible';
	} else {
		document.getElementById(txtObj).style.visibility = 'hidden';
	}
}

function fntaxValidation(txtObj) {
	if (!numValidate(txtObj, 'Tax', 'any', true)) {
		document.getElementById(txtObj).value = 0;
	}
}

function fnpriceValidation(txtObj) {
	if (!numValidate(txtObj, 'Price', 'any')) {
		document.getElementById(txtObj).value = 0;
	}
}

function delimage(id, fname, aname) {
	if (id == 0) {
		document.getElementById(fname+'_replaceimage').innerHTML=alert_arr.LBL_IMAGE_DELETED;
	} else {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=delImage&ImageModule='+gVTModule+'&recordid='+id+'&fieldname='+fname+'&attachmentname='+aname,
		}).done(function (response) {
			if (response.indexOf('SUCCESS')>-1) {
				document.getElementById(fname+'_replaceimage').innerHTML=alert_arr.LBL_IMAGE_DELETED;
			} else {
				alert(alert_arr.ERROR_WHILE_EDITING);
			}
		});
	}
	document.getElementById(fname+'_hidden').value = '';
}

function delUserImage(id) {
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Users&action=UsersAjax&file=Save&deleteImage=true&recordid='+id,
	}).done(function (response) {
		if (response.indexOf('SUCCESS')>-1) {
			document.getElementById('replaceimage').innerHTML=alert_arr.LBL_IMAGE_DELETED;
		} else {
			alert(alert_arr.ERROR_WHILE_EDITING);
		}
	});
}

// Function to enable/disable related elements based on whether the current object is checked or not
function fnenableDisable(currObj, enableId) {
	var disable_flag = true;
	if (currObj.checked == true) {
		disable_flag = false;
	}

	document.getElementById('curname'+enableId).disabled = disable_flag;
	document.getElementById('cur_reset'+enableId).disabled = disable_flag;
	document.getElementById('base_currency'+enableId).disabled = disable_flag;
}

// Update current value with current value of base currency and the conversion rate
function updateCurrencyValue(currObj, txtObj, base_curid, conv_rate) {
	var unit_price = document.getElementById(base_curid).value;

	if (typeof userCurrencySeparator != 'undefined') {
		while (unit_price.indexOf(userCurrencySeparator) != -1) {
			unit_price = unit_price.replace(userCurrencySeparator, '');
		}
	}
	if (typeof userDecimalSeparator != 'undefined') {
		if (unit_price.indexOf(userDecimalSeparator) != -1) {
			unit_price = unit_price.replace(userDecimalSeparator, '.');
		}
	}
	document.getElementById(txtObj).value = unit_price * conv_rate;
}

// Synchronize between Unit price and Base currency value.
function updateUnitPrice(from_cur_id, to_cur_id) {
	var from_ele = document.getElementById(from_cur_id);
	if (from_ele == null) {
		return;
	}

	var to_ele = document.getElementById(to_cur_id);
	if (to_ele == null) {
		return;
	}

	to_ele.value = from_ele.value;
}

// Update hidden base currency value, everytime the base currency value is changed in multi-currency UI
function updateBaseCurrencyValue() {
	var cur_list = document.getElementsByName('base_currency_input');
	if (cur_list == null) {
		return;
	}

	var base_currency_ele = document.getElementById('base_currency');
	if (base_currency_ele == null) {
		return;
	}

	for (var i=0; i<cur_list.length; i++) {
		var cur_ele = cur_list[i];
		if (cur_ele != null && cur_ele.checked == true) {
			base_currency_ele.value = cur_ele.value;
		}
	}
}

function standarizeFormatCurrencyValue(val) {
	if (val != undefined && val != null && val != 0 && typeof val != 'number') {
		if (typeof userCurrencySeparator != 'undefined') {
			while (val.indexOf(userCurrencySeparator) != -1) {
				val = val.replace(userCurrencySeparator, '');
			}
		}
		if (typeof userDecimalSeparator != 'undefined') {
			if (val.indexOf(userDecimalSeparator) != -1) {
				val = val.replace(userDecimalSeparator, '.');
			}
		}
	}
	return val;
}

/******************************************************************************/
/* Activity reminder Customization: Setup Callback */
function ActivityReminderProgressIndicator(show) {
	if (show) {
		document.getElementById('status').style.display = 'inline';
	} else {
		document.getElementById('status').style.display = 'none';
	}
}

function ActivityReminderSetupCallback(cbmodule, cbrecord) {
	if (cbmodule && cbrecord) {
		ActivityReminderProgressIndicator(true);
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Calendar&action=CalendarAjax&ajax=true&file=ActivityReminderSetupCallbackAjax&cbmodule='+
				encodeURIComponent(cbmodule) + '&cbrecord=' + encodeURIComponent(cbrecord)
		}).done(function (response) {
			document.getElementById('ActivityReminder_callbacksetupdiv').innerHTML=response;
			ActivityReminderProgressIndicator(false);
		});
	}
}

function ActivityReminderSetupCallbackSave(form) {
	var cbmodule = form.cbmodule.value;
	var cbrecord = form.cbrecord.value;
	var cbaction = form.cbaction.value;
	var cbdate   = form.cbdate.value;
	var cbtime   = form.cbhour.value + ':' + form.cbmin.value;
	if (cbmodule && cbrecord) {
		ActivityReminderProgressIndicator(true);
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Calendar&action=CalendarAjax&ajax=true&file=ActivityReminderSetupCallbackAjax' +
			'&cbaction=' + encodeURIComponent(cbaction) +
			'&cbmodule='+ encodeURIComponent(cbmodule) +
			'&cbrecord=' + encodeURIComponent(cbrecord) +
			'&cbdate=' + encodeURIComponent(cbdate) +
			'&cbtime=' + encodeURIComponent(cbtime)
		}).done(function (response) {
			ActivityReminderSetupCallbackSaveProcess(response);
		});
	}
}

function ActivityReminderSetupCallbackSaveProcess(message) {
	ActivityReminderProgressIndicator(false);
	document.getElementById('ActivityReminder_callbacksetupdiv_lay').style.display='none';
}

function ActivityReminderPostponeCallback(cbmodule, cbrecord, cbreminderid) {
	if (cbmodule && cbrecord) {
		ActivityReminderProgressIndicator(true);
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Calendar&action=CalendarAjax&ajax=true&file=ActivityReminderSetupCallbackAjax&cbaction=POSTPONE&cbmodule='+
			encodeURIComponent(cbmodule) + '&cbrecord=' + encodeURIComponent(cbrecord) + '&cbreminderid=' + encodeURIComponent(cbreminderid)
		}).done(function (response) {
			ActivityReminderPostponeCallbackProcess(response);
		});
	}
}

function ActivityReminderPostponeCallbackProcess(message) {
	ActivityReminderProgressIndicator(false);
}

function ActivityReminderRemovePopupDOM(id) {
	if (jQuery('#'+id).length) {
		jQuery('#'+id).remove();
	}
}

/* ActivityReminder Customization: Pool Callback */
var ActivityReminder_regcallback_timer;

var ActivityReminder_callback_delay = 40 * 1000; // Milli Seconds
var ActivityReminder_autohide = false; // If the popup should auto hide after callback_delay?

var ActivityReminder_popup_maxheight = 75;

var ActivityReminder_callback;
var ActivityReminder_timer;
var ActivityReminder_progressive_height = 2; // px
var ActivityReminder_popup_onscreen = 2 * 1000; // Milli Seconds (should be less than ActivityReminder_callback_delay)

var ActivityReminder_callback_win_uniqueids = new Object();

function ActivityReminderCallback() {
	if (typeof(jQuery) == 'undefined') {
		return;
	}
	if (ActivityReminder_regcallback_timer) {
		window.clearTimeout(ActivityReminder_regcallback_timer);
		ActivityReminder_regcallback_timer = null;
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Calendar&action=CalendarAjax&file=ActivityReminderCallbackAjax&ajax=true'
	}).done(function (response) {
		if (response=='Login') {
			document.location.href='index.php?module=Users&action=Login';
		} else {
			ActivityReminderCallbackProcess(response);
		}
	});
}

function ActivityReminderCallbackProcess(message) {
	ActivityReminder_callback = document.getElementById('ActivityRemindercallback');
	if (ActivityReminder_callback == null) {
		return;
	}
	if (message == 'None') {
		if (ActivityReminder_timer) {
			window.clearTimeout(ActivityReminder_timer);
			ActivityReminder_timer = null;
		}
		return;
	}
	ActivityReminder_callback.style.display = 'block';

	var winuniqueid = 'ActivityReminder_callback_win_' + (new Date()).getTime();
	if (ActivityReminder_callback_win_uniqueids[winuniqueid]) {
		winuniqueid += '-' + (new Date()).getTime();
	}
	ActivityReminder_callback_win_uniqueids[winuniqueid] = true;

	var ActivityReminder_callback_win = document.createElement('span');
	ActivityReminder_callback_win.id  = winuniqueid;
	ActivityReminder_callback.appendChild(ActivityReminder_callback_win);

	ActivityReminder_callback_win.innerHTML=message;
	ActivityReminder_callback_win.style.height = '0px';
	ActivityReminder_callback_win.style.display = '';

	var ActivityReminder_Newdelay_response_node = '_vtiger_activityreminder_callback_interval_';
	if (document.getElementById(ActivityReminder_Newdelay_response_node)) {
		var ActivityReminder_Newdelay_response_value = parseInt(document.getElementById(ActivityReminder_Newdelay_response_node).innerHTML);
		if (ActivityReminder_Newdelay_response_value > 0) {
			ActivityReminder_callback_delay = ActivityReminder_Newdelay_response_value;
		}
		// We don't need the no any longer, it will be sent from server for next Popup
		jQuery('#'+ActivityReminder_Newdelay_response_node).remove();
	}
	if (message == '' || trim(message).indexOf('<script') == 0) {
		// We got only new delay value but no popup information, let us remove the callback win created
		jQuery('#'+ActivityReminder_callback_win.id).remove();
		ActivityReminder_callback_win = false;
		message = '';
	}

	if (message != '') {
		ActivityReminderCallbackRollout(ActivityReminder_popup_maxheight, ActivityReminder_callback_win);
	} else {
		ActivityReminderCallbackReset(0, ActivityReminder_callback_win);
	}
}

function ActivityReminderCallbackRollout(z, ActivityReminder_callback_win) {
	if (typeof(ActivityReminder_callback_win)=='string') {
		ActivityReminder_callback_win = document.getElementById(ActivityReminder_callback_win);
	}

	if (ActivityReminder_timer) {
		window.clearTimeout(ActivityReminder_timer);
	}
	if (ActivityReminder_callback_win && parseInt(ActivityReminder_callback_win.style.height) < z) {
		ActivityReminder_callback_win.style.height = parseInt(ActivityReminder_callback_win.style.height) + ActivityReminder_progressive_height + 'px';
		ActivityReminder_timer = setTimeout('ActivityReminderCallbackRollout(' + z + ',\'' + ActivityReminder_callback_win.id + '\')', 1);
	} else {
		ActivityReminder_callback_win.style.height = z + 'px';
		if (ActivityReminder_autohide) {
			ActivityReminder_timer = setTimeout('ActivityReminderCallbackRollin(1,\'' + ActivityReminder_callback_win.id + '\')', ActivityReminder_popup_onscreen);
		} else {
			ActivityReminderRegisterCallback(ActivityReminder_callback_delay);
		}
	}
}

function ActivityReminderCallbackRollin(z, ActivityReminder_callback_win) {
	ActivityReminder_callback_win = document.getElementById(ActivityReminder_callback_win);
	if (ActivityReminder_timer) {
		window.clearTimeout(ActivityReminder_timer);
	}
	if (parseInt(ActivityReminder_callback_win.style.height) > z) {
		ActivityReminder_callback_win.style.height = parseInt(ActivityReminder_callback_win.style.height) - ActivityReminder_progressive_height + 'px';
		ActivityReminder_timer = setTimeout('ActivityReminderCallbackRollin(' + z + ',\'' + ActivityReminder_callback_win.id + '\')', 1);
	} else {
		ActivityReminderCallbackReset(z, ActivityReminder_callback_win);
	}
}

function ActivityReminderCallbackReset(z, ActivityReminder_callback_win) {
	ActivityReminder_callback_win = document.getElementById(ActivityReminder_callback_win);
	if (ActivityReminder_callback_win) {
		ActivityReminder_callback_win.style.height = z + 'px';
		ActivityReminder_callback_win.style.display = 'none';
	}
	if (ActivityReminder_timer) {
		window.clearTimeout(ActivityReminder_timer);
		ActivityReminder_timer = null;
	}
	ActivityReminderRegisterCallback(ActivityReminder_callback_delay);
}

function ActivityReminderRegisterCallback(timeout) {
	if (timeout == null) {
		timeout = 1;
	}
	if (ActivityReminder_regcallback_timer == null) {
		ActivityReminder_regcallback_timer = setTimeout('ActivityReminderCallback()', timeout);
	}
}

function ajaxChangeCalendarStatus(statusname, activityid) {
	document.getElementById('status').style.display = 'inline';
	var viewid = document.getElementById('viewname') ? document.getElementById('viewname').options[document.getElementById('viewname').options.selectedIndex].value : '';
	var idstring = document.getElementById('idlist') ? document.getElementById('idlist').value : '';
	var searchurl = document.getElementById('search_url') ? document.getElementById('search_url').value : '';
	var urlstring = 'module=cbCalendar&action=cbCalendarAjax&file=calendarops&op=changestatus&ajax=true&newstatus=' + statusname + '&activityid=' + activityid + '&viewname=' + viewid + '&idlist=' + idstring + searchurl;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?' + urlstring
	}).done(function (response) {
		document.getElementById('status').style.display = 'none';
		var result = response.split('&#&#&#');
		if (document.getElementById('ListViewContents')) {
			document.getElementById('ListViewContents').innerHTML = result[2];
			document.getElementById('basicsearchcolumns').innerHTML = '';
		}
		if (result[1] != '') {
			alert(result[1]);
		}
	});
	return false;
}

//added for finding duplicates
function movefields() {
	availListObj=getObj('availlist');
	selectedColumnsObj=getObj('selectedCol');
	for (var i=0; i<selectedColumnsObj.length; i++) {
		selectedColumnsObj.options[i].selected=false;
	}
	movefieldsStep1();
}

function movefieldsStep1() {
	availListObj=getObj('availlist');
	selectedColumnsObj=getObj('selectedCol');
	document.getElementById('selectedCol').style.width='164px';
	var count=0;
	for (var i=0; i<availListObj.length; i++) {
		if (availListObj.options[i].selected==true) {
			count++;
		}
	}
	var total_fields=count+selectedColumnsObj.length;
	if (total_fields >4 ) {
		alert(alert_arr.MAX_RECORDS);
		return false;
	}
	if (availListObj.options.selectedIndex > -1) {
		for (i=0; i<availListObj.length; i++) {
			if (availListObj.options[i].selected==true) {
				var rowFound=false;
				for (var j=0; j<selectedColumnsObj.length; j++) {
					selectedColumnsObj.options[j].value==availListObj.options[i].value;
					if (selectedColumnsObj.options[j].value==availListObj.options[i].value) {
						var rowFound=true;
						var existingObj=selectedColumnsObj.options[j];
						break;
					}
				}
				if (rowFound!=true) {
					var newColObj=document.createElement('OPTION');
					newColObj.value=availListObj.options[i].value;
					if (browser_ie) {
						newColObj.innerText=availListObj.options[i].innerText;
					} else if (browser_nn4 || browser_nn6) {
						newColObj.text=availListObj.options[i].text;
					}
					selectedColumnsObj.appendChild(newColObj);
					newColObj.selected=true;
				} else {
					existingObj.selected=true;
				}
				availListObj.options[i].selected=false;
				movefieldsStep1();
			}
		}
	}
}

function selectedColClick(oSel) {
	if (oSel.selectedIndex == -1 || oSel.options[oSel.selectedIndex].disabled == true) {
		alert(alert_arr.NOT_ALLOWED_TO_EDIT);
		oSel.options[oSel.selectedIndex].selected = false;
	}
}

function delFields() {
	selectedColumnsObj=getObj('selectedCol');
	selected_tab = document.getElementById('dupmod').value;
	if (selectedColumnsObj.options.selectedIndex > -1) {
		var del = false;
		for (var i=0; i < selectedColumnsObj.options.length; i++) {
			if (selectedColumnsObj.options[i].selected == true) {
				if (selected_tab == 4) {
					if (selectedColumnsObj.options[i].innerHTML == 'Last Name') {
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					} else {
						del = true;
					}
				} else if (selected_tab == 7) {
					if (selectedColumnsObj.options[i].innerHTML == 'Last Name' || selectedColumnsObj.options[i].innerHTML == 'Company') {
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					} else {
						del = true;
					}
				} else if (selected_tab == 6) {
					if (selectedColumnsObj.options[i].innerHTML == 'Account Name') {
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					} else {
						del = true;
					}
				} else if (selected_tab == 14) {
					if (selectedColumnsObj.options[i].innerHTML == 'Product Name') {
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					} else {
						del = true;
					}
				}
				if (del == true) {
					selectedColumnsObj.remove(i);
					delFields();
				}
			}
		}
	}
}

function moveFieldUp() {
	selectedColumnsObj=getObj('selectedCol');
	var currpos=selectedColumnsObj.options.selectedIndex;
	var tempdisabled= false;
	var temp = '';
	for (var i=0; i<selectedColumnsObj.length; i++) {
		if (i != currpos) {
			selectedColumnsObj.options[i].selected=false;
		}
	}
	if (currpos>0) {
		var prevpos=selectedColumnsObj.options.selectedIndex-1;
		if (browser_ie) {
			temp=selectedColumnsObj.options[prevpos].innerText;
			tempdisabled = selectedColumnsObj.options[prevpos].disabled;
			selectedColumnsObj.options[prevpos].innerText=selectedColumnsObj.options[currpos].innerText;
			selectedColumnsObj.options[prevpos].disabled = false;
			selectedColumnsObj.options[currpos].innerText=temp;
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		} else if (browser_nn4 || browser_nn6) {
			temp=selectedColumnsObj.options[prevpos].text;
			tempdisabled = selectedColumnsObj.options[prevpos].disabled;
			selectedColumnsObj.options[prevpos].text=selectedColumnsObj.options[currpos].text;
			selectedColumnsObj.options[prevpos].disabled = false;
			selectedColumnsObj.options[currpos].text=temp;
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		temp=selectedColumnsObj.options[prevpos].value;
		selectedColumnsObj.options[prevpos].value=selectedColumnsObj.options[currpos].value;
		selectedColumnsObj.options[currpos].value=temp;
		selectedColumnsObj.options[prevpos].selected=true;
		selectedColumnsObj.options[currpos].selected=false;
	}
}

function moveFieldDown() {
	selectedColumnsObj=getObj('selectedCol');
	var currpos=selectedColumnsObj.options.selectedIndex;
	var tempdisabled= false;
	var temp = '';
	for (var i=0; i<selectedColumnsObj.length; i++) {
		if (i != currpos) {
			selectedColumnsObj.options[i].selected=false;
		}
	}
	if (currpos<selectedColumnsObj.options.length-1) {
		var nextpos=selectedColumnsObj.options.selectedIndex+1;
		if (browser_ie) {
			temp=selectedColumnsObj.options[nextpos].innerText;
			tempdisabled = selectedColumnsObj.options[nextpos].disabled;
			selectedColumnsObj.options[nextpos].innerText=selectedColumnsObj.options[currpos].innerText;
			selectedColumnsObj.options[nextpos].disabled = false;
			selectedColumnsObj.options[nextpos];
			selectedColumnsObj.options[currpos].innerText=temp;
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		} else if (browser_nn4 || browser_nn6) {
			temp=selectedColumnsObj.options[nextpos].text;
			tempdisabled = selectedColumnsObj.options[nextpos].disabled;
			selectedColumnsObj.options[nextpos].text=selectedColumnsObj.options[currpos].text;
			selectedColumnsObj.options[nextpos].disabled = false;
			selectedColumnsObj.options[nextpos];
			selectedColumnsObj.options[currpos].text=temp;
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		temp=selectedColumnsObj.options[nextpos].value;
		selectedColumnsObj.options[nextpos].value=selectedColumnsObj.options[currpos].value;
		selectedColumnsObj.options[currpos].value=temp;
		selectedColumnsObj.options[nextpos].selected=true;
		selectedColumnsObj.options[currpos].selected=false;
	}
}

function lastImport(module, req_module) {
	var module_name= module;
	var parent_tab= document.getElementById('parenttab').value;
	if (module == '') {
		return false;
	} else {
		//alert("index.php?module="+module_name+"&action=lastImport&req_mod="+req_module+"&parenttab="+parent_tab);
		window.open('index.php?module='+module_name+'&action=lastImport&req_mod='+req_module+'&parenttab='+parent_tab, 'lastImport', 'width=750,height=602,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
	}
}

function merge_fields(selectedNames, module, parent_tab) {
	var select_options=document.getElementsByName(selectedNames);
	var x= select_options.length;
	var req_module=module;
	var num_group=document.getElementById('group_count').innerHTML;
	var pass_url='';
	var flag=0;
	//var i=0;
	var xx = 0;
	for (var i = 0; i < x; i++) {
		if (select_options[i].checked) {
			pass_url = pass_url+select_options[i].value +',';
			xx++;
		}
	}
	var tmp = 0;
	if ( xx != 0) {
		if (xx > 3) {
			alert(alert_arr.MAX_THREE);
			return false;
		}
		if (xx > 0) {
			for (var j=0; j<num_group; j++) {
				flag = 0;
				var group_options=document.getElementsByName('group'+j);
				for (i = 0; i < group_options.length; i++) {
					if (group_options[i].checked) {
						flag++;
					}
				}
				if (flag > 0) {
					tmp++;
				}
			}
			if (tmp > 1) {
				alert(alert_arr.SAME_GROUPS);
				return false;
			}
			if (xx <2) {
				alert(alert_arr.ATLEAST_TWO);
				return false;
			}
		}
		window.open('index.php?module='+req_module+'&action=ProcessDuplicates&mergemode=mergefields&passurl='+pass_url+'&parenttab='+parent_tab, 'Merge', 'width=750,height=602,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
	} else {
		alert(alert_arr.ATLEAST_TWO);
		return false;
	}
}

function delete_fields(module) {
	var select_options=document.getElementsByName('del');
	var x=select_options.length;
	var xx=0;
	url_rec='';

	for (var i=0; i<x; i++) {
		if (select_options[i].checked) {
			url_rec=url_rec+select_options[i].value +',';
			xx++;
		}
	}
	if (document.getElementById('current_action')) {
		cur_action = document.getElementById('current_action').innerHTML;
	}
	if (xx == 0) {
		alert(alert_arr.SELECT);
		return false;
	}
	var alert_str = alert_arr.DELETE + xx +alert_arr.RECORDS;
	if (module=='Accounts') {
		alert_str = alert_arr.DELETE_ACCOUNT + xx +alert_arr.RECORDS;
	}
	if (confirm(alert_str)) {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module='+module+'&action='+module+'Ajax&file=FindDuplicateRecords&del_rec=true&ajax=true&return_module='+module+'&idlist='+url_rec+'&current_action='+cur_action+'&'+dup_start
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('duplicate_ajax').innerHTML= response;
		});
	} else {
		return false;
	}
}

function deleteExactDuplicates(module) {
	var alert_msg= alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE_EXACT_DUPLICATE;
	if (confirm(alert_msg)) {
		VtigerJS_DialogBox.block();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module='+module+'&action='+module+'Ajax&file=FindDuplicateRecords&del_exact_dup_rec=true&ajax=true'
		}).done(function (response) {
			VtigerJS_DialogBox.unblock();
			document.getElementById('duplicate_ajax').innerHTML= response;
		});
	} else {
		return false;
	}
}

function validate_merge(module) {
	var check_var=false;
	var check_lead1=false;
	var check_lead2=false;

	var select_parent=document.getElementsByName('record');
	var len = select_parent.length;
	for (var i=0; i<len; i++) {
		if (select_parent[i].checked) {
			var check_parentvar=true;
		}
	}
	if (check_parentvar!=true) {
		alert(alert_arr.Select_one_record_as_parent_record);
		return false;
	}
	return true;
}

function select_All(fieldnames, cnt, module) {
	var new_arr = Array();
	new_arr = fieldnames.split(',');
	var len=new_arr.length;
	for (var i=0; i<len; i++) {
		var fld_names=new_arr[i];
		var value=document.getElementsByName(fld_names);
		var fld_len=document.getElementsByName(fld_names).length;
		for (var j=0; j<fld_len; j++) {
			value[cnt].checked='true';
		//	alert(value[j].checked)
		}
	}
}

function selectAllDel(state, checkedName) {
	var selectedOptions=document.getElementsByName(checkedName);
	var length=document.getElementsByName(checkedName).length;
	if (typeof(length) == 'undefined') {
		return false;
	}
	for (var i=0; i<length; i++) {
		selectedOptions[i].checked=state;
	}
}

function selectDel(ThisName, CheckAllName) {
	var ThisNameOptions=document.getElementsByName(ThisName);
	var CheckAllNameOptions=document.getElementsByName(CheckAllName);
	var len1=document.getElementsByName(ThisName).length;
	var flag = true;
	if (typeof(document.getElementsByName(ThisName).length)=='undefined') {
		flag=true;
	} else {
		for (var j=0; j<len1; j++) {
			if (ThisNameOptions[j].checked==false) {
				flag=false;
				break;
			}
		}
	}
	CheckAllNameOptions[0].checked=flag;
}

// Added for page navigation in duplicate-listview
var dup_start = '';
function getDuplicateListViewEntries_js(module, url) {
	dup_start = url;
	document.getElementById('status').style.display='block';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+module+'&action='+module+'Ajax&file=FindDuplicateRecords&ajax=true&'+dup_start,
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('duplicate_ajax').innerHTML = response;
	});
}

function getUnifiedSearchEntries_js(search, module, url) {
	var qryStr = document.getElementsByName('search_criteria')[0].value;
	document.getElementById('status').style.display='block';
	var recordCount = document.getElementById(module+'RecordCount').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+module+'&search_tag='+search+'&action='+module+'Ajax&file=UnifiedSearch&ajax=true&'+url+
		'&query_string='+qryStr+'&search_onlyin='+encodeURIComponent('--USESELECTED--')+'&recordCount='+recordCount
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('global_list_'+module).innerHTML = response;
	});
}

//Added after 5.0.4 for Documents Module
function positionDivToCenter(targetDiv) {
	//Gets the browser's viewport dimension
	getViewPortDimension();
	//Gets the Target DIV's width & height in pixels using parseInt function
	divWidth =(parseInt(document.getElementById(targetDiv).style.width))/2;
	divHeight=(parseInt(document.getElementById(targetDiv).style.height))/2;
	//calculate horizontal and vertical locations relative to Viewport's dimensions
	mx = parseInt(XX/2)-parseInt(divWidth);
	my = parseInt(YY/3)-parseInt(divHeight);
	//Prepare the DIV and show in the center of the screen.
	document.getElementById(targetDiv).style.left=mx+'px';
	document.getElementById(targetDiv).style.top=my+'px';
}

function getViewPortDimension() {
	if (!document.all) {
		XX = self.innerWidth;
		YY = self.innerHeight;
	} else if (document.all) {
		XX = document.documentElement.clientWidth;
		YY = document.documentElement.clientHeight;
	}
}

function toggleTable(id) {
	var listTableObj=getObj(id);
	if (listTableObj.style.display=='none') {
		listTableObj.style.display='';
	} else {
		listTableObj.style.display='none';
	}
//set_cookie(id,listTableObj.style.display)
}

function FileAdd(obj, Lay, return_action) {
	fnvshobj(obj, Lay);
	window.frames['AddFile'].document.getElementById('divHeader').innerHTML='Add file';
	window.frames['AddFile'].document.FileAdd.return_action.value=return_action;
	positionDivToCenter(Lay);
}

function dldCntIncrease(fileid) {
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=DocumentsAjax&mode=ajax&file=SaveFile&module=Documents&file_id='+fileid+'&act=updateDldCnt',
	}).done(function (response) {
	});
}
//End Documents Module

//asterisk integration :: starts

/**
 * this function accepts a node and puts it at the center of the screen
 * @param object node - the dom object which you want to set in the center
 */
function placeAtCenter(node) {
	var centerPixel = getViewPortCenter();
	node.style.position = 'absolute';
	var point = getDimension(node);
	var topvalue = (centerPixel.y - point.y/2);
	var rightvalue = (centerPixel.x - point.x/2);

	//to ensure that values will not be negative
	if (topvalue<0) {
		topvalue = 0;
	}
	if (rightvalue < 0) {
		rightvalue = 0;
	}

	node.style.top = topvalue + 'px';
	node.style.right =rightvalue + 'px';
	node.style.left = '';
	node.style.bottom = '';
}

/**
 * this function gets the dimension of a node
 * @param node - the node whose dimension you want
 * @return height and width in array format
 */
function getDimension(node) {
	var ht = node.offsetHeight;
	var wdth = node.offsetWidth;
	var nodeChildren = node.getElementsByTagName('*');
	var noOfChildren = nodeChildren.length;
	for (var index =0; index<noOfChildren; ++index) {
		ht = Math.max(nodeChildren[index].offsetHeight, ht);
		wdth = Math.max(nodeChildren[index].offsetWidth, wdth);
	}
	return {
		x: wdth,
		y: ht
	};
}

/**
 * this function returns the center co-ordinates of the viewport as an array
 */
function getViewPortCenter() {
	var height;
	var width;

	if (typeof window.pageXOffset != 'undefined') {
		height = window.innerHeight/2;
		width = window.innerWidth/2;
		height +=window.pageYOffset;
		width +=window.pageXOffset;
	} else if (document.documentElement && typeof document.documentElement.scrollTop != 'undefined') {
		height = document.documentElement.clientHeight/2;
		width = document.documentElement.clientWidth/2;
		height += document.documentElement.scrollTop;
		width += document.documentElement.scrollLeft;
	} else if (document.body && typeof document.body.clientWidth != 'undefined') {
		height = window.screen.availHeight/2;
		width = window.screen.availWidth/2;
		height += document.body.clientHeight;
		width += document.body.clientWidth;
	}
	return {
		x: width,
		y: height
	};
}

/**
 * this function accepts a number and displays a div stating that there is an outgoing call
 * then it calls the number
 * @param number - the number to be called
 */
function startCall(number, recordid) {
	div = document.getElementById('OutgoingCall').innerHTML;
	outgoingPopup = _defPopup();
	outgoingPopup.content = div;
	outgoingPopup.displayPopup(outgoingPopup.content);

	//var ASTERISK_DIV_TIMEOUT = 6000;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=PBXManagerAjax&mode=ajax&file=StartCall&ajax=true&module=PBXManager&number='+encodeURIComponent(number)+'&recordid='+recordid
	}).done(function (response) {
		if (response == '') {
			//successfully called
		} else {
			alert(response);
		}
	});
}
//asterisk integration :: ends

//added for tooltip manager
function ToolTipManager() {
	var state = false;
	var secondshowTimer = 0;
	var secondshowTimeout = 1800;
	/**
	 * this function creates the tooltip div and adds the information to it
	 * @param string text - the text to be added to the tooltip
	 */
	function tip(node, text, id, fieldname) {
		state=true;
		var divName = getDivId(id, fieldname);
		var div = document.getElementById(divName);
		if (!div) {
			div = document.createElement('div');
			div.id = divName;
			div.style.position = 'absolute';
			if (typeof div.style.opacity == 'string') {
				div.style.opacity = 0.8;
			}
			div.className = 'tooltipClass';
		}

		div.innerHTML = text;
		document.body.appendChild(div);
		div.style.display = 'block';
		div.style.zIndex = '1000000';
		positionTooltip(node, divName);
	}

	function getDivId(id, fieldname) {
		return '__VT_tooltip_'+id+'_'+fieldname;
	}

	function exists(id, fieldname) {
		return (typeof document.getElementById(getDivId(id, fieldname)) != 'undefined' &&
			document.getElementById(getDivId(id, fieldname)) != null);
	}

	function show(node, id, fieldname) {
		var div = document.getElementById(getDivId(id, fieldname));
		if (typeof div !='undefined' && div != null) {
			div.style.display = '';
			positionTooltip(node, getDivId(id, fieldname));
		}
	}

	/**
	 * this function removes the tooltip div
	 */
	function unTip(nodelay, id, fieldname) {
		state=false;
		var divName = getDivId(id, fieldname);
		var div = document.getElementById(divName);
		if (typeof div != 'undefined' && div != null ) {
			if (typeof nodelay != 'undefined' && nodelay != null) {
				setTimeout(function () {
					div.style.display = 'none';
				}, secondshowTimeout);
			} else {
				setTimeout(function () {
					if (!state) {
						div.style.display = 'none';
					}
				}, secondshowTimeout);
			}
		}
	}

	/**
	 * this function is used to position the tooltip div
	 * @param string obj - the id of the element where the div has to appear
	 * @param object div - the div which contains the info
	 */
	function positionTooltip(obj, div) {
		var tooltip = document.getElementById(div);
		var leftSide = findPosX(obj);
		var topSide = findPosY(obj);
		var dimensions = getDimension(tooltip);
		var widthM = dimensions.x;
		var getVal = eval(leftSide) + eval(widthM);
		var tooltipDimensions = getDimension(obj);
		var tooltipWidth = tooltipDimensions.x;
		if (leftSide == 0 && topSide == 0) {
			tooltip.style.display = 'none';
		} else {
			if (getVal > document.body.clientWidth ) {
				leftSide = eval(leftSide) - eval(widthM);
			} else {
				leftSide = eval(leftSide) + (eval(tooltipWidth)/2);
			}
			if (leftSide < 0) {
				leftSide = findPosX(obj) + tooltipWidth;
			}
			tooltip.style.left = leftSide + 'px';

			var heightTooltip = dimensions.y;
			var bottomSide = eval(topSide) + eval(heightTooltip);
			if (bottomSide > document.body.clientHeight) {
				topSide = topSide - (bottomSide - document.body.clientHeight) - 10;
				if (topSide < 0 ) {
					topSide = 10;
				}
			} else {
				topSide = eval(topSide) - eval(heightTooltip)/2;
				if (topSide<0) {
					topSide = 10;
				}
			}
			tooltip.style.top= topSide + 'px';
		}
	}

	return {
		tip:tip,
		untip:unTip,
		'exists': exists,
		'show': show,
		'getDivId':getDivId
	};
}
if (!tooltip) {
	var tooltip = ToolTipManager();
}
//tooltip manager changes end

function submitFormForActionWithConfirmation(formName, action, confirmationMsg) {
	if (confirm(confirmationMsg)) {
		return submitFormForAction(formName, action);
	}
	return false;
}

function submitFormForAction(formName, action) {
	var form = document.forms[formName];
	if (!form) {
		return false;
	}
	form.action.value = action;
	form.submit();
	return true;
}

/** Javascript dialog box utility functions **/
VtigerJS_DialogBox = {
	_olayer : function (toggle) {
		var olayerid = '__vtigerjs_dialogbox_olayer__';
		VtigerJS_DialogBox._removebyid(olayerid);

		if (typeof(toggle) == 'undefined' || !toggle) {
			return;
		}

		var olayer = document.getElementById(olayerid);
		if (!olayer) {
			olayer = document.createElement('div');
			olayer.id = olayerid;
			olayer.className = 'slds-spinner_container slds-is-fixed';

			// Avoid zIndex going beyond integer max
			//olayer.style.zIndex = parseInt((new Date()).getTime() / 1000);

			// In case zIndex goes to negative side!
			//if (olayer.style.zIndex < 0) {
			//	olayer.style.zIndex *= -1;
			//}
			if (browser_ie) {
				olayer.style.height = document.body.offsetHeight + (document.body.scrollHeight - document.body.offsetHeight) + 'px';
			} else if (browser_nn4 || browser_nn6) {
				olayer.style.height = document.body.offsetHeight + 'px';
			}
			//olayer.style.width = '100%';
			var spinner = document.createElement('div');
			spinner.role = 'status';
			spinner.className = 'slds-spinner slds-spinner_inline slds-spinner_large slds-spinner_brand';
			var spininside2 = document.createElement('div');
			spininside2.className = 'slds-spinner__dot-a';
			var spininside3 = document.createElement('div');
			spininside3.className = 'slds-spinner__dot-b';
			spinner.appendChild(spininside2);
			spinner.appendChild(spininside3);
			olayer.appendChild(spinner);
			document.body.appendChild(olayer);

			var closeimg = document.createElement('img');
			closeimg.src = 'themes/images/popuplay_close.png';
			closeimg.alt = 'X';
			closeimg.style.right= '10px';
			closeimg.style.top  = '5px';
			closeimg.style.position = 'absolute';
			closeimg.style.cursor = 'pointer';
			closeimg.onclick = VtigerJS_DialogBox.unblock;
			olayer.appendChild(closeimg);
		}
		if (olayer) {
			if (toggle) {
				olayer.style.display = 'block';
			} else {
				olayer.style.display = 'none';
			}
		}
		return olayer;
	},
	_removebyid : function (id) {
		if (jQuery('#'+id).length) {
			jQuery('#'+id).remove();
		}
	},
	unblock : function () {
		VtigerJS_DialogBox._olayer(false);
		VtigerJS_DialogBox.hidebusy();
	},
	block : function (opacity) {
		if (typeof(opactiy)=='undefined') {
			opacity = '0.3';
		}
		var olayernode = VtigerJS_DialogBox._olayer(true);
		olayernode.style.opacity = opacity;
		VtigerJS_DialogBox.showbusy();
	},
	showbusy : function () {
		document.getElementById('status').style.display='inline';
	},
	hidebusy : function () {
		document.getElementById('status').style.display='none';
	},
	hideprogress : function () {
		VtigerJS_DialogBox._olayer(false);
		VtigerJS_DialogBox._removebyid('__vtigerjs_dialogbox_progress_id__');
	},
	progress : function (imgurl) {
		VtigerJS_DialogBox._olayer(true);
		if (typeof(imgurl) == 'undefined') {
			imgurl = 'themes/images/plsWaitAnimated.gif';
		}

		var prgbxid = '__vtigerjs_dialogbox_progress_id__';
		var prgnode = document.getElementById(prgbxid);
		if (!prgnode) {
			prgnode = document.createElement('div');
			prgnode.id = prgbxid;
			prgnode.className = 'veil_new';
			prgnode.style.position = 'absolute';
			prgnode.style.width = '100%';
			prgnode.style.top = '0';
			prgnode.style.left = '0';
			prgnode.style.display = 'block';

			document.body.appendChild(prgnode);

			prgnode.innerHTML =
			'<table border="5" cellpadding="0" cellspacing="0" align="center" style="vertical-align:middle;width:100%;height:100%;">' +
			'<tr><td class="big" align="center"><img src="'+ imgurl + '"></td></tr></table>';
		}
		if (prgnode) {
			prgnode.style.display = 'block';
		}
	},
	hideconfirm : function () {
		VtigerJS_DialogBox._olayer(false);
		VtigerJS_DialogBox._removebyid('__vtigerjs_dialogbox_alert_boxid__');
	},
	confirm : function (msg, onyescode) {
		VtigerJS_DialogBox._olayer(true);

		var dlgbxid = '__vtigerjs_dialogbox_alert_boxid__';
		var dlgbxnode = document.getElementById(dlgbxid);
		if (!dlgbxnode) {
			dlgbxnode = document.createElement('div');
			dlgbxnode.style.display = 'none';
			dlgbxnode.className = 'veil_new small';
			dlgbxnode.id = dlgbxid;
			dlgbxnode.innerHTML =
			'<table cellspacing="0" cellpadding="18" border="0" class="options small">' +
			'<tbody>' +
			'<tr>' +
			'<td nowrap="" align="center" style="color: rgb(255, 255, 255); font-size: 15px;">' +
			'<b>'+ msg + '</b></td>' +
			'</tr>' +
			'<tr>' +
			'<td align="center">' +
			'<input type="button" style="text-transform: capitalize;" onclick="document.getElementById(\''+ dlgbxid + '\').style.display=\'none\';VtigerJS_DialogBox._olayer(false);VtigerJS_DialogBox._confirm_handler();" value="'+ alert_arr.YES + '"/>' +
			'<input type="button" style="text-transform: capitalize;" onclick="document.getElementById(\''+ dlgbxid + '\').style.display=\'none\';VtigerJS_DialogBox._olayer(false)" value="' + alert_arr.NO + '"/>' +
			'</td>'+
			'</tr>' +
			'</tbody>' +
			'</table>';
			document.body.appendChild(dlgbxnode);
		}
		if (typeof(onyescode) == 'undefined') {
			onyescode = '';
		}
		dlgbxnode._onyescode = onyescode;
		if (dlgbxnode) {
			dlgbxnode.style.display = 'block';
		}
	},
	_confirm_handler : function () {
		var dlgbxid = '__vtigerjs_dialogbox_alert_boxid__';
		var dlgbxnode = document.getElementById(dlgbxid);
		if (dlgbxnode) {
			if (typeof(dlgbxnode._onyescode) != 'undefined' && dlgbxnode._onyescode != '') {
				eval(dlgbxnode._onyescode);
			}
		}
	}
};

function validateInputData(value, fieldLabel, typeofdata) {
	var typeinfo = typeofdata.split('~');
	var type = typeinfo[0];

	if (type == 'T') {
		if (!re_patternValidate(value, fieldLabel+' (Time)', 'TIMESECONDS')) {
			return false;
		}
	} else if (type == 'D' || type == 'DT') {
		if (!re_dateValidate(value, fieldLabel+' (Current User Date Format)', 'OTH')) {
			return false;
		}
	} else if (type == 'I') {
		if (isNaN(value) || value.indexOf('.')!=-1) {
			alert(alert_arr.INVALID+fieldLabel);
			return false;
		}
	} else if (type == 'N' || type == 'NN') {
		if (typeof(typeinfo[2]) == 'undefined') {
			var numformat = 'any';
		} else {
			var numformat = typeinfo[2];
		}

		var negativeallowed = (type == 'NN');

		if (numformat != 'any') {
			if (isNaN(value)) {
				var invalid=true;
			} else {
				var format = numformat.split(',');
				var splitval = value.split('.');

				if (negativeallowed == true) {
					if (splitval[0].indexOf('-') >= 0) {
						if (splitval[0].length-1 > format[0]) {
							invalid=true;
						}
					} else {
						if (splitval[0].length > format[0]) {
							invalid=true;
						}
					}
				} else {
					if (value < 0) {
						invalid=true;
					} else if (format[0] == 2 && splitval[0] == 100 && (!splitval[1] || splitval[1]==0)) {
						invalid=false;
					} else if (splitval[0].length > format[0]) {
						invalid=true;
					}
				}

				if (splitval[1]) {
					if (splitval[1].length > format[1]) {
						invalid=true;
					}
				}
			}

			if (invalid==true) {
				alert(alert_arr.INVALID + fieldLabel);
				return false;
			} else {
				return true;
			}
		} else {
			var splitval = value.split('.');
			var arr_len = splitval.length;
			var len = 0;
			if (splitval[0] > 18446744073709551615) {
				alert(fieldLabel + alert_arr.EXCEEDS_MAX);
				return false;
			}
			if (negativeallowed == true) {
				var re=/^(-|)(\d)*(\.)?\d+(\.\d\d*)*$/;
			} else {
				var re=/^(\d)*(\.)?\d+(\.\d\d*)*$/;
			}
		}

		//for precision check. ie.number must contains only one "."
		var dotcount=0;
		for (var i = 0; i < value.length; i++) {
			if (value.charAt(i) == '.') {
				dotcount++;
			}
		}

		if (dotcount>1) {
			alert(alert_arr.INVALID+fieldLabel);
			return false;
		}

		if (!re.test(value)) {
			alert(alert_arr.INVALID+fieldLabel);
			return false;
		}
	} else if (type == 'E') {
		if (!re_patternValidate(value, fieldLabel+' (Email Id)', 'EMAIL')) {
			return false;
		}
	}
	return true;
}

function re_dateValidate(fldval, fldLabel, type) {
	if (re_patternValidate(fldval, fldLabel, 'DATE')==false) {
		return false;
	}
	var dateval=fldval.replace(/^\s+/g, '').replace(/\s+$/g, '');

	var dateelements=splitDateVal(dateval);

	var dd=dateelements[0];
	var mm=dateelements[1];
	var yyyy=dateelements[2];

	if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
		alert(alert_arr.ENTER_VALID+fldLabel);
		return false;
	}

	if ((mm==2) && (dd>29)) {//checking of no. of days in february month
		alert(alert_arr.ENTER_VALID+fldLabel);
		return false;
	}

	if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
		alert(alert_arr.ENTER_VALID+fldLabel);
		return false;
	}

	switch (parseInt(mm)) {
	case 2 :
	case 4 :
	case 6 :
	case 9 :
	case 11 :
		if (dd>30) {
			alert(alert_arr.ENTER_VALID+fldLabel);
			return false;
		}
	}

	var currdate=new Date();
	var chkdate=new Date();

	chkdate.setYear(yyyy);
	chkdate.setMonth(mm-1);
	chkdate.setDate(dd);

	if (type!='OTH') {
		if (!compareDates(chkdate, fldLabel, currdate, 'current date', type)) {
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

//Copied from general.js and altered some lines. because we cant send values to function present in general.js. it accept only field names.
function re_patternValidate(fldval, fldLabel, type) {
	if (type.toUpperCase()=='EMAIL') {
		/*changes made to fix -- ticket#3278 & ticket#3461
		  var re=new RegExp(/^.+@.+\..+$/)*/
		//Changes made to fix tickets #4633, #5111  to accomodate all possible email formats
		var re=new RegExp(/^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/);
	}

	if (type.toUpperCase()=='DATE') {//DATE validation
		switch (userDateFormat) {
		case 'yyyy-mm-dd' :
			var re = /^\d{4}(-)\d{1,2}\1\d{1,2}$/;
			break;
		case 'mm-dd-yyyy' :
		case 'dd-mm-yyyy' :
			var re = /^\d{1,2}(-)\d{1,2}\1\d{4}$/;
		}
	}

	if (type.toUpperCase()=='TIMESECONDS') {//TIME validation
		var re = new RegExp('^([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$|^([0-1][0-9]|[2][0-3]):([0-5][0-9])$');
	}
	if (!re.test(fldval)) {
		alert(alert_arr.ENTER_VALID + fldLabel);
		return false;
	} else {
		return true;
	}
}

function getTranslatedString(key, alertArray) {
	if (alertArray != undefined) {
		if (alertArray[key] != undefined) {
			return alertArray[key];
		}
	}
	if (alert_arr[key] != undefined) {
		return alert_arr[key];
	} else {
		return key;
	}
}

function copySelectedOptions(source, destination) {
	var srcObj = document.getElementById(source);
	var destObj = document.getElementById(destination);

	if (typeof(srcObj) == 'undefined' || typeof(destObj) == 'undefined') {
		return;
	}
	for (var i=0; i<srcObj.length; i++) {
		if (srcObj.options[i].selected==true) {
			var rowFound=false;
			var existingObj=null;
			for (var j=0; j<destObj.length; j++) {
				if (destObj.options[j].value==srcObj.options[i].value) {
					rowFound=true;
					existingObj=destObj.options[j];
					break;
				}
			}

			if (rowFound!=true) {
				var newColObj=document.createElement('OPTION');
				newColObj.value=srcObj.options[i].value;
				if (browser_ie) {
					newColObj.innerText=srcObj.options[i].innerText;
				} else if (browser_nn4 || browser_nn6) {
					newColObj.text=srcObj.options[i].text;
				}
				destObj.appendChild(newColObj);
				srcObj.options[i].selected=false;
				newColObj.selected=true;
				rowFound=false;
			} else {
				if (existingObj != null) {
					existingObj.selected=true;
				}
			}
		}
	}
}

function removeSelectedOptions(objName) {
	var obj = getObj(objName);
	if (obj == null || typeof(obj) == 'undefined') {
		return;
	}
	for (var i=obj.options.length-1; i>=0; i--) {
		if (obj.options[i].selected == true) {
			obj.options[i] = null;
		}
	}
}

function convertOptionsToJSONArray(objName, targetObjName) {
	var obj = document.getElementById(objName);
	var arr = [];
	if (typeof(obj) != 'undefined') {
		for (var i=0; i<obj.options.length; ++i) {
			arr.push(obj.options[i].value);
		}
	}
	if (targetObjName != 'undefined') {
		var targetObj = document.getElementById(targetObjName);
		if (typeof(targetObj) != 'undefined') {
			targetObj.value = JSON.stringify(arr);
		}
	}
	return arr;
}

function fnvshobjMore(obj, Lay) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = tagName.offsetWidth;
	if (Lay == 'editdiv') {
		leftSide = leftSide - 225;
		topSide = topSide - 225;
	} else if (Lay == 'transferdiv') {
		leftSide = leftSide - 10;
		//topSide = topSide;
	}
	var IE = document.all?true:false;
	if (IE) {
		if (document.getElementById('repposition1')) {
			if (topSide > 1200) {
				topSide = topSide-250;
			}
		}
	}

	if ((leftSide > 100) && (leftSide < 500)) {
		tagName.style.left= leftSide -50 + 'px';
	} else if ((leftSide >= 500) && (leftSide < 800)) {
		tagName.style.left= leftSide -150 + 'px';
	} else if ((leftSide >= 800) && (leftSide < 1400)) {
		if ((widthM > 100) && (widthM < 250)) {
			tagName.style.left= leftSide- 100 + 'px';
		} else if ((widthM >= 250) && (widthM < 350)) {
			tagName.style.left= leftSide- 200 + 'px';
		} else if ((widthM >= 350) && (widthM < 500)) {
			tagName.style.left= leftSide- 300 + 'px';
		} else {
			tagName.style.left= leftSide -550 + 'px';
		}
	} else {
		tagName.style.left= leftSide + 5 +'px';
	}
	var menuBar = document.getElementsByClassName('hdrTabBg')[0];
	tagName.style.top = (menuBar.offsetTop + menuBar.clientHeight)+'px';
	tagName.style.display = 'block';
	tagName.style.visibility = 'visible';
}

function fnvshobjsearch(obj, Lay) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0, maxW.length-2);
	if (Lay == 'editdiv') {
		leftSide = leftSide - 225;
		topSide = topSide - 125;
	} else if (Lay == 'transferdiv') {
		leftSide = leftSide - 10;
		//topSide = topSide;
	}
	var IE = document.all?true:false;
	if (IE) {
		if (document.getElementById('repposition1')) {
			if (topSide > 1200) {
				topSide = topSide-250;
			}
		}
	}

	var getVal = eval(leftSide) + eval(widthM);
	if (getVal > document.body.clientWidth ) {
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 91 + 'px';
	} else {
		tagName.style.left= leftSide - 324 + 'px';
	}
	tagName.style.top= topSide + obj.clientHeight + 'px';
	tagName.style.display = 'block';
	tagName.style.visibility = 'visible';
}

function fnDropDownUser(obj, Lay) {
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0, maxW.length-2);
	var getVal = eval(leftSide) + eval(widthM);
	if (getVal > document.body.clientWidth ) {
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 34 + 'px';
	} else {
		tagName.style.left= leftSide - 50 + 'px';
	}
	tagName.style.top= topSide + 28 +'px';
	tagName.style.display = 'block';
}

//select the records across the pages
function toggleSelectAll_Records(module, state, relCheckName) {
	toggleSelect_ListView(state, relCheckName);
	if (state == true) {
		document.getElementById('allselectedboxes').value = 'all';
		document.getElementById('selectAllRec').style.display = 'none';
		document.getElementById('deSelectAllRec').style.display = 'inline';
	} else {
		document.getElementById('allselectedboxes').value = '';
		document.getElementById('excludedRecords').value = '';
		document.getElementById('selectCurrentPageRec').checked = false;
		document.getElementById('selectAllRec').style.display = 'inline';
		document.getElementById('deSelectAllRec').style.display = 'none';
		document.getElementById('linkForSelectAll').style.display = 'none';
	}
}

function toggleSelectDocumentRecords(module, state, relCheckName, parentEleId) {
	toggleSelect_ListView(state, relCheckName, parentEleId);
	if (state == true) {
		document.getElementById('selectedboxes_'+parentEleId).value = 'all';
		document.getElementById('selectAllRec_'+parentEleId).style.display = 'none';
		document.getElementById('deSelectAllRec_'+parentEleId).style.display = 'inline';
	} else {
		document.getElementById('selectedboxes_'+parentEleId).value = '';
		document.getElementById('excludedRecords_'+parentEleId).value = '';
		document.getElementById('currentPageRec_'+parentEleId).checked = false;
		document.getElementById('selectAllRec_'+parentEleId).style.display = 'inline';
		document.getElementById('deSelectAllRec_'+parentEleId).style.display = 'none';
		document.getElementById('linkForSelectAll_'+parentEleId).style.display = 'none';
	}
}

//Compute the number of rows in the current module
function getNoOfRows(id) {
	var module = document.getElementById('curmodule').value;
	var searchurl = document.getElementById('search_url').value;
	var viewid = getviewId();
	var url = 'module='+module+'&action='+module+'Ajax&file=ListViewCount&viewname='+viewid+searchurl;
	if (module != undefined && module == 'Documents' && Document_Folder_View) {
		var folderid = document.getElementById('folderid_'+id).value;
		url = url+'&folderidstring='+folderid;
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+url
	}).done(function (response) {
		if (module != 'Documents' || Document_Folder_View == 0) {
			document.getElementById('numOfRows').value = response;
			document.getElementById('count').innerHTML = response;
			if (parseInt(document.getElementById('maxrecords').value) < parseInt(response)) {
				document.getElementById('linkForSelectAll').style.display='table-cell';
			}
		} else {
			document.getElementById('numOfRows_'+id).value = response;
			document.getElementById('count_'+id).innerHTML = response;
			if (parseInt(document.getElementById('maxrecords').value) < parseInt(response)) {
				document.getElementById('linkForSelectAll_'+id).style.display='table-cell';
			}
		}
	});
}

//select all function for related list of campaign module
function rel_toggleSelectAll_Records(module, relmodule, state, relCheckName) {
	rel_toggleSelect(state, relCheckName, relmodule);
	if (state == true) {
		document.getElementById(module+'_'+relmodule+'_selectallActivate').value = 'true';
		document.getElementById(module+'_'+relmodule+'_selectAllRec').style.display = 'none';
		document.getElementById(module+'_'+relmodule+'_deSelectAllRec').style.display = 'inline';
	} else {
		document.getElementById(module+'_'+relmodule+'_selectallActivate').value = 'false';
		document.getElementById(module+'_'+relmodule+'_excludedRecords').value = '';
		document.getElementById(module+'_'+relmodule+'_selectCurrentPageRec').checked = false;
		document.getElementById(module+'_'+relmodule+'_selectAllRec').style.display = 'inline';
		document.getElementById(module+'_'+relmodule+'_deSelectAllRec').style.display = 'none';
		document.getElementById(module+'_'+relmodule+'_linkForSelectAll').style.display = 'none';
	}
}

// Compute the number of records related to campaign record
function getNoOfRelatedRows(current_module, related_module) {
	var recordid = document.getElementById('recordid').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module='+current_module+'&related_module='+related_module+'&action='+current_module+'Ajax&idlist='+recordid+'&file=ListViewCount&mode=relatedlist',
	}).done(function (response) {
		document.getElementById(current_module+'_'+related_module+'_numOfRows').value = response;
		document.getElementById(related_module+'_count').innerHTML = response;
		if (parseInt(document.getElementById('maxrecords').value) < parseInt(response)) {
			document.getElementById(current_module+'_'+related_module+'_linkForSelectAll').style.display='block';
		}
	});
}

function updateParentCheckbox(obj, id) {
	var parentCheck=true;
	if (obj) {
		for (var i=0; i<obj.length; ++i) {
			if (obj[i].checked != true) {
				var parentCheck=false;
			}
		}
	}
	var selelem = document.getElementById(id+'_selectCurrentPageRec');
	if (selelem && parentCheck) {
		selelem.checked=parentCheck;
	}
}

function showSelectAllLink(obj, exculdedArray) {
	var viewForSelectLink = true;
	for (var i=0; i<obj.length; ++i) {
		obj[i].checked = true;
		for (var j=0; j<exculdedArray.length; ++j) {
			if (exculdedArray[j] == obj[i].value) {
				obj[i].checked = false;
				viewForSelectLink = false;
			}
		}
	}
	return viewForSelectLink;
}

function getMaxMassOperationLimit() {
	return 500;
}

function getviewId() {
	if (document.getElementById('viewname') != null && typeof(document.getElementById('viewname')) != 'undefined') {
		var oViewname = document.getElementById('viewname');
		var viewid = oViewname.options[oViewname.selectedIndex].value;
	} else {
		var viewid = '';
	}
	return viewid;
}

function getFormValidate() {
	return doModuleValidation('', 'QcEditView');
}

function QCformValidate() {
	var st = document.getElementById('qcvalidate');
	eval(st.innerHTML);
	for (var i=0; i<qcfieldname.length; i++) {
		var curr_fieldname = qcfieldname[i];
		if (window.document.QcEditView[curr_fieldname] != null) {
			var type=qcfielddatatype[i].split('~');
			var input_type = window.document.QcEditView[curr_fieldname].type;
			if (type[1]=='M' && !qcemptyCheck(curr_fieldname, qcfieldlabel[i], input_type)) {
				return false;
			}
			switch (type[0]) {
			case 'O' : break;
			case 'V' : break;
			case 'C' : break;
			case 'DT':
				if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0 && type[2] != undefined) {
					if (type[1]=='M' && !qcemptyCheck(type[2], qcfieldlabel[i], getObj(type[2]).type)) {
						return false;
					}
					if (typeof(type[3])=='undefined') {
						var currdatechk='OTH';
					} else {
						var currdatechk=type[3];
					}
					if (!qcdateTimeValidate(curr_fieldname, type[2], qcfieldlabel[i], currdatechk)) {
						return false;
					}
					if (type[4]) {
						if (!dateTimeComparison(curr_fieldname, type[2], qcfieldlabel[i], type[5], type[6], type[4])) {
							return false;
						}
					}
				}
				break;
			case 'D':
				if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0 && type[2] != undefined) {
					if (typeof(type[2])=='undefined') {
						var currdatechk='OTH';
					} else {
						var currdatechk=type[2];
					}
					if (!qcdateValidate(curr_fieldname, qcfieldlabel[i], currdatechk)) {
						return false;
					}
					if (type[3]) {
						if (!qcdateComparison(curr_fieldname, qcfieldlabel[i], type[4], type[5], type[3])) {
							return false;
						}
					}
				}
				break;
			case 'T':
				if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (typeof(type[2])=='undefined') {
						var currtimechk='OTH';
					} else {
						var currtimechk=type[2];
					}
					if (!timeValidateObject(window.document.QcEditView[curr_fieldname], qcfieldlabel[i], currtimechk)) {
						return false;
					}
					if (type[3]) {
						if (!timeComparison(curr_fieldname, qcfieldlabel[i], type[4], type[5], type[3])) {
							return false;
						}
					}
				}
				break;
			case 'I':
				if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (window.document.QcEditView[curr_fieldname].value.length!=0) {
						if (!qcintValidate(curr_fieldname, qcfieldlabel[i])) {
							return false;
						}
						if (type[2]) {
							if (!qcnumConstComp(curr_fieldname, qcfieldlabel[i], type[2], type[3])) {
								return false;
							}
						}
					}
				}
				break;
			case 'N':
			case 'NN':
				if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (window.document.QcEditView[curr_fieldname].value.length!=0) {
						if (typeof(type[2])=='undefined') {
							var numformat='any';
						} else {
							var numformat=type[2];
						}
						if (type[0]=='NN') {
							if (!numValidate(curr_fieldname, qcfieldlabel[i], numformat, true)) {
								return false;
							}
						} else {
							if (!numValidate(curr_fieldname, qcfieldlabel[i], numformat)) {
								return false;
							}
						}
						if (type[3]) {
							if (!numConstComp(curr_fieldname, qcfieldlabel[i], type[3], type[4])) {
								return false;
							}
						}
					}
				}
				break;
			case 'E':
				if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) {
					if (window.document.QcEditView[curr_fieldname].value.length!=0) {
						var etype = 'EMAIL';
						if (!patternValidateObject(window.document.QcEditView[curr_fieldname], qcfieldlabel[i], etype)) {
							return false;
						}
					}
				}
				break;
			}
		}
	}
	return true;
}

function duplicate_record(module, record) {
	jQuery.ajax({
		url: ' index.php?module=Utilities&action=UtilitiesAjax&file=duplicate',
		type:'POST',
		data: {module_name: module, record_id: record },
		dataType:'JSON',
		error: function (data) {
			console.log(data);
		},
		success: function (response) {
			window.location = 'index.php?module=' + response.module + '&action=DetailView&record=' + response.record_id;
		}
	});
}

function getITSMiniCal(url) {
	if (url == undefined) {
		url = 'module=Calendar4You&action=ActivityAjax&file=ActivityAjax&type=minical&ajax=true';
	} else {
		url = 'module=Calendar4You&action=ActivityAjax&file=ActivityAjax&'+url+'&type=minical&ajax=true';
	}
	jQuery.ajax({
		method:'POST',
		url:'index.php?'+ url
	}).done(function (response) {
		document.getElementById('miniCal').innerHTML = response;
	});
}

function changeCalendarMonthDate(year, month, date) {
	if (jQuery('#calendar_div').fullCalendar == undefined) {
		return false;
	}
	changeCalendarDate(year, month, date);
	jQuery('#calendar_div').fullCalendar('changeView', 'month');
}

function changeCalendarWeekDate(year, month, date) {
	if (jQuery('#calendar_div').fullCalendar == undefined) {
		return false;
	}
	changeCalendarDate(year, month, date);
	jQuery('#calendar_div').fullCalendar('changeView', 'agendaWeek');
}

function changeCalendarDayDate(year, month, date) {
	if (jQuery('#calendar_div').fullCalendar == undefined) {
		return false;
	}
	changeCalendarDate(year, month, date);
	jQuery('#calendar_div').fullCalendar('changeView', 'agendaDay');
}

function changeCalendarDate(year, month, date) {
	if (jQuery('#calendar_div').fullCalendar == undefined) {
		return false;
	}
	var date1= year+'-'+month+'-'+date;
	jQuery('#calendar_div').fullCalendar('gotoDate', date1);
}

function fetch_clock() {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Utilities&action=UtilitiesAjax&file=Clock'
	}).done(function (response) {
		jQuery('#clock_cont').html(response);
		execJS(document.getElementById('clock_cont'));
	});
}

function fetch_calc() {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Utilities&action=UtilitiesAjax&file=Calculator'
	}).done(function (response) {
		jQuery('#calculator_cont').html(response);
		execJS(document.getElementById('calculator_cont'));
	});
}

function UnifiedSearch_SelectModuleForm(obj) {
	if (jQuery('#UnifiedSearch_moduleform').length) {
		// If we have loaded the form already.
		UnifiedSearch_SelectModuleFormCallback(obj);
	} else {
		jQuery('#status').show();
		jQuery.ajax({
			method:'POST',
			url:'index.php?module=Home&action=HomeAjax&file=UnifiedSearchModules&ajax=true'
		}).done(function (response) {
			jQuery('#status').hide();
			jQuery('#UnifiedSearch_moduleformwrapper').html(response);
			UnifiedSearch_SelectModuleFormCallback(obj);
		});
	}
}

function UnifiedSearch_SelectModuleFormCallback(obj) {
	fnvshobjsearch(obj, 'UnifiedSearch_moduleformwrapper');
}

function UnifiedSearch_SelectModuleToggle(flag) {
	jQuery('#UnifiedSearch_moduleform input[type=checkbox]').each(function () {
		this.checked = flag;
	});
}

function UnifiedSearch_SelectModuleCancel() {
	jQuery('#UnifiedSearch_moduleformwrapper').hide();
}

function UnifiedSearch_SelectModuleSave() {
	var UnifiedSearch_form = document.forms.UnifiedSearch;
	UnifiedSearch_form.search_onlyin.value = jQuery('#UnifiedSearch_moduleform').serialize().replace(/search_onlyin=/g, '').replace(/&/g, ',');
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Home&action=HomeAjax&file=UnifiedSearchModulesSave&search_onlyin=' + encodeURIComponent(UnifiedSearch_form.search_onlyin.value)
	}).done(function (response) {
		// continue
	});
	UnifiedSearch_SelectModuleCancel();
}

/**
 * image pasting into canvas
 * @param {string} canvas_id - canvas id
 * @param {boolean} autoresize - if canvas will be resized
 */
function CLIPBOARD_CLASS(canvas_id, autoresize) {
	var _self = this;
	var canvas = document.getElementById(canvas_id);
	var ctx = document.getElementById(canvas_id).getContext('2d');
	var startImage = new Image();
	startImage.onload = function () {
		ctx.drawImage(startImage, 0, 0, 60, 60, 12, 12, 30, 30);
	};
	startImage.src = 'include/LD/assets/icons/utility/paste_60.png';
	//handlers
	document.addEventListener('paste', function (e) {
		_self.paste_auto(e);
	}, false);

	//on paste
	this.paste_auto = function (e) {
		if (e.clipboardData && document.activeElement.id==canvas_id) {
			var items = e.clipboardData.items;
			if (!items) {
				return;
			}

			//access data directly
			for (var i = 0; i < items.length; i++) {
				if (items[i].type.indexOf('image') !== -1) {
					//image
					var blob = items[i].getAsFile();
					var URLObj = window.URL || window.webkitURL;
					var source = URLObj.createObjectURL(blob);
					this.paste_createImage(source);
					e.preventDefault();
					break;
				}
			}
		}
	};
	//draw pasted image to canvas
	this.paste_createImage = function (source) {
		var pastedImage = new Image();
		pastedImage.onload = function () {
			if (autoresize == true) {
				//resize
				canvas.width = pastedImage.width;
				canvas.height = pastedImage.height;
			} else {
				//clear canvas
				ctx.clearRect(0, 0, canvas.width, canvas.height);
			}
			ctx.drawImage(pastedImage, 0, 0);
			var cnv_img = document.getElementById(canvas_id+'_image');
			var cnv_img_set = document.getElementById(canvas_id+'_image_set');
			if (cnv_img) {
				cnv_img.value = canvas.toDataURL('image/png');
			}
			if (cnv_img_set) {
				cnv_img_set.value = '1';
			}
		};
		pastedImage.src = source;
	};
}

var throttle = function (func, limit) {
	var inThrottle = undefined;
	return function () {
		var args = arguments,
			context = this;
		if (!inThrottle) {
			func.apply(context, args);
			inThrottle = true;
			return setTimeout(function () {
				return inThrottle = false;
			}, limit);
		}
	};
};

document.addEventListener('DOMContentLoaded', function (event) {
	/* ======= Auto complete part relations ====== */
	var acInputs = document.querySelectorAll('.autocomplete-input,.searchBox');
	for (var i = 0; i < acInputs.length; i++) {
		(function (_i) {
			var ac = new AutocompleteRelation(acInputs[_i], _i);
			acInputs[_i].addEventListener('input', function (e) {
				throttle(ac.get(e), 500);
			});
			$('html').click(function () {
				ac.clearTargetUL();
				ac.targetUL.hide();
			});
		})(i);
	}
});

function AutocompleteRelation(target, i) {
	this.inputField 	= target;
	this.data			= JSON.parse(target.getAttribute('data-autocomp'));
	this.targetUL 		= document.getElementsByClassName('relation-autocomplete__target')[i];
	this.hiddenInput	= document.getElementsByClassName('relation-autocomplete__hidden')[i];
	this.displayFields 	= this.showFields();
	this.entityName		= this.entityField();
	this.moduleName 	= this.data.searchmodule;
	this.fillfields		= this.fillFields();
	this.maxResults 	= this.MaxResults();
	this.mincharstoSearch 	= this.MinCharsToSearch();
	this.multiselect 	= this.multiselect();
	if (this.multiselect==='true') {
		target.style.width='95%';
	}
	this.targetUL.show 	= function () {
		if (!this.classList.contains('active')) {
			(function () {
				var allAcLists = document.getElementsByClassName('relation-autocomplete__target');
				for (var i = 0; i < allAcLists.length; i++) {
					allAcLists[i].hide();
				}
			})();
			this.style.opacity = 1;
			this.classList.add('active');
		}
	};
	this.targetUL.hide 	= function () {
		if (this.classList.contains('active')) {
			this.style.opacity = 0;
			this.classList.remove('active');
		}
	};

	this.targetUL.style.transition = 'opacity 100ms ease';
}

AutocompleteRelation.prototype.get = function (e) {
	var term = e.target.value;
	if (this.multiselect==='true') {
		var array=term.split(',');
		var nr_opt=array.length;
		term=array[nr_opt-1];
	}
	if (term.length >= this.mincharstoSearch && (typeof(this.data.searchin) != 'undefined' || typeof(this.data.searchfields) != 'undefined') ) {
		this.data.term = term;
		var acInstance = this;

		this.displayFields 	= this.showFields();
		this.entityName		= this.entityField();
		this.fillfields		= this.fillFields();
		acInstance.isReferenceField(e);

		var r = new XMLHttpRequest();
		r.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				var json_data = JSON.parse(r.response);
				if (json_data.length == 0) {
					acInstance.clearTargetUL();
				} else {
					acInstance.set(json_data);
				}
			}
		};
		var params='data='+encodeURIComponent(JSON.stringify(this.data));
		if (e.target.name==='query_string') {
			r.open('POST', 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getGlobalSearch', true);
			r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			r.send(params);
		} else {
			r.open('GET', 'index.php?module=Utilities&action=UtilitiesAjax&file=getAutocomplete&'+params, true);
			r.send();
		}
	} else {
		this.clearTargetUL();
		this.targetUL.hide();
	}
};

AutocompleteRelation.prototype.set = function (items) {
	if (items.length > 0) {
		this.clearTargetUL();
		this.targetUL.show();
		var acInstance = this;
		var limit = acInstance.maxResults < items.length ? acInstance.maxResults : items.length;
		for (var i = 0; i < limit; i++) {
			var li = this.buildListItem(items[i]);
			this.targetUL.appendChild(li);

			li.addEventListener('click', function (e) {
				acInstance.select({
					label 		: this.getAttribute('data-label'),
					value 		: this.getAttribute('data-crmid')
				});
				acInstance.fillOtherFields(this);
				if (acInstance.inputField.name==='query_string') {
					acInstance.goToRec({
						crmmodule 	: this.getAttribute('data-crmmodule'),
						value 		: this.getAttribute('data-crmid')
					});
				}
			});
		}
		if (acInstance.inputField.name==='query_string') {
			var span = document.createElement('li');
			span.className= 'total_autocomplete';
			span.innerHTML = getTranslatedString('SHOWING') + ' '+ limit +' '+getTranslatedString('OF')+' '+items[0]['total'];
			this.targetUL.appendChild(span);
		}
	}
};

AutocompleteRelation.prototype.select = function (params) {
	// var label = params.label;
	// var value = params.value;

	// this.inputField.value 	= label;
	// this.hiddenInput.value 	= value;

	// Housekeeping after selection
	this.clearTargetUL();
	this.targetUL.hide();
	// Schedular.AutoComplete.Current.clear();
};

AutocompleteRelation.prototype.goToRec = function (params) {
	var value = params.value.split('x')[1];
	var crmmodule = params.crmmodule;
	window.open('index.php?module='+crmmodule+'&action=DetailView&record='+value);
};

AutocompleteRelation.prototype.buildListItem = function (item) {
	var li = document.createElement('li');
	li.className = 'slds-listbox__item';
	li.setAttribute('role', 'presentation');
	li.setAttribute('data-crmid', item.crmid);
	li.setAttribute('data-label', item[this.entityName]);

	for (var field in item) {
		li.setAttribute('data-' + field, item[field]);
	}

	var span = document.createElement('span');
	span.setAttribute('class', 'slds-media slds-listbox__option slds-listbox__option_entity slds-listbox__option_has-meta');
	span.setAttribute('role', 'option');

	li.appendChild(span);

	span = document.createElement('span');
	span.setAttribute('class', 'slds-media__figure');

	li.children[0].appendChild(span);

	span = document.createElement('span');
	span.setAttribute('class', 'slds-icon_container slds-icon-standard-account');
	span.setAttribute('title', 'TO FILL!');

	li.children[0].children[0].appendChild(span);

	var svg = document.createElement('svg');
	svg.setAttribute('class', 'slds-icon slds-icon_small');
	svg.setAttribute('aria-hidden', 'true');

	li.children[0].children[0].children[0].appendChild(svg);

	var use = document.createElement('use');
	use.setAttribute('xlink:href', 'include/LD/assets/icons/standard-sprite/svg/symbols.svg#account');

	li.children[0].children[0].children[0].children[0].appendChild(use);

	span = document.createElement('span');
	span.setAttribute('class', 'slds-assistive-text');
	span.innerText = 'Description of icon';

	li.children[0].children[0].children[0].appendChild(span);

	span = document.createElement('span');
	span.setAttribute('class', 'slds-media__body');

	li.children[0].appendChild(span);

	span = document.createElement('span');
	span.setAttribute('class', 'slds-listbox__option-text slds-listbox__option-text_entity');
	span.innerHTML = item[this.entityName];

	li.children[0].children[1].appendChild(span);

	span = document.createElement('span');
	span.setAttribute('class', 'slds-listbox__option-meta slds-listbox__option-meta_entity');
	if (this.inputField.name==='query_string') {
		span.innerText = this.buildSecondayReturnFieldsGS(item);
	} else {
		span.innerText = this.buildSecondayReturnFields(item);
	}

	li.children[0].children[1].appendChild(span);

	return li;
};

AutocompleteRelation.prototype.buildSecondayReturnFields = function (item) {
	var returnString = '';
	for (var i = 0; i < this.displayFields.length; i++) {
		if (i != 0) {
			returnString = returnString + item[this.displayFields[i]];
			if (i < this.displayFields.length - 1) {
				returnString += '\n';
			}
		}
	}
	return returnString;
};

AutocompleteRelation.prototype.buildSecondayReturnFieldsGS = function (item) {
	var returnString = '';
	var module=item['crmmodule'];
	var displayFld=this.data.searchin[module]['showfields'];
	for (var i = 0; i < displayFld.length; i++) {
		returnString = returnString + item[displayFld[i]];
		if (i < displayFld.length - 1) {
			returnString += '\n';
		}
	}
	return returnString;
};

AutocompleteRelation.prototype.clearTargetUL = function () {
	while (this.targetUL.firstChild) {
		this.targetUL.removeChild(this.targetUL.firstChild);
		this.targetUL.hide();
	}
};

AutocompleteRelation.prototype.fillOtherFields = function (data) {
	var fields = this.fillfields;
	for (var i = 0; i < fields.length; i++) {
		var this_field = fields[i].split('=');
		var get_field_value = data.getAttribute('data-' + this_field[1]);
		var field_element = document.getElementsByName(this_field[0])[0];

		if (this_field[0] == 'assigned_user_id') {
			field_element = this.fillAssignField(get_field_value);
		}
		var field_root_name = this.inputField.name.substring(0, this.inputField.name.indexOf('_display'));
		if (this.multiselect==='true' && (this_field[0]==field_root_name+'_display' || this_field[0]==field_root_name || this_field[0]==this.inputField.name)) {
			var nr_opt=0;
			if (this_field[0]==field_root_name+'_display') {
				var array=field_element.value.split(',');
				nr_opt=array.length;
				array[nr_opt-1]=get_field_value;
				field_element.value = array.join(',')+',';
			} else {
				var array=field_element.value.split(' |##| ').filter(item => item);
				nr_opt=array.length;
				array.push(get_field_value);
				field_element.value = array.join(' |##| ');
			}
		} else {
			field_element.value = get_field_value;
		}
	}
};

AutocompleteRelation.prototype.fillAssignField = function (value) {
	var type, active_piclist;
	var user_picklist = document.getElementById('assigned_user_id');
	var group_picklist = document.getElementById('assigned_group_id');

	var assigntype = document.getElementsByName('assigntype');

	if ( user_picklist.innerHTML.indexOf('value="' + value + '"') > -1 ) {
		type = 'U';
		active_piclist = user_picklist;
	} else {
		type = 'T';
		active_piclist = group_picklist;
	}

	for (var i = 0; i < assigntype.length; i++) {
		assigntype[i].checked = false;
		if (assigntype[i].value == type) {
			assigntype[i].checked = true;
		}
	}

	toggleAssignType(type);
	return active_piclist;
};

AutocompleteRelation.prototype.isReferenceField = function (e) {
	var current_field_name = e.target.name;
	if (current_field_name.indexOf('_display') !== -1) {
		var field_root_name = current_field_name.substring(0, current_field_name.indexOf('_display'));
		var reference_type_field = document.getElementsByName(field_root_name + '_type');
		if (reference_type_field.length > 0) {
			var ref_module = reference_type_field[0].value;
			var ref_field_id = document.getElementsByName(field_root_name);
			//var ref_record_id = ref_field_id[0].value;
			this.data.referencefield = {module:ref_module, fieldname:field_root_name};
			this.extendFillFields([field_root_name +'='+field_root_name, field_root_name+'_display='+field_root_name+'_display']);
		}
	}
};

AutocompleteRelation.prototype.getReferenceModule = function () {
	var current_field_name = this.inputField.name;
	var field_root_name = current_field_name.substring(0, current_field_name.indexOf('_display'));
	var reference_type_field = document.getElementsByName(field_root_name + '_type');
	return (reference_type_field[0] !== undefined ? reference_type_field[0].value : '');
};

AutocompleteRelation.prototype.extendFillFields = function (other_fields) {
	this.fillfields = this.fillfields.concat(other_fields);
};

AutocompleteRelation.prototype.showFields = function () {
	try {
		return this.data.showfields.split(',');
	} catch (e) {
		var ref_module = this.getReferenceModule();
		return (ref_module !== '' ? this.data.showfields[ref_module].split(',') : '');
	}
};

AutocompleteRelation.prototype.entityField = function () {
	if (typeof this.data.entityfield === 'string') {
		return this.data.entityfield;
	} else {
		var ref_module = this.getReferenceModule();
		return (ref_module !== '' ? this.data.entityfield[ref_module] : '');
	}
};

AutocompleteRelation.prototype.fillFields = function () {
	try {
		return this.data.fillfields.split(',');
	} catch (e) {
		var ref_module = this.getReferenceModule();
		return (ref_module !== '' ? this.data.fillfields[ref_module].split(',') : '');
	}
};

AutocompleteRelation.prototype.multiselect = function () {
	if (typeof this.data.multiselect === 'string') {
		return this.data.multiselect;
	} else if (typeof this.data.multiselect === undefined) {
		var ref_module = this.getReferenceModule();
		return (ref_module !== '' ? this.data.multiselect[ref_module] : '');
	}
};

AutocompleteRelation.prototype.MaxResults = function () {
	if (typeof this.data.maxresults === 'number') {
		return this.data.maxresults;
	} else if (typeof this.data.maxresults === undefined) {
		var ref_module = this.getReferenceModule();
		if (ref_module !== '' && this.data.maxresults[ref_module] !== undefined) {
			return this.data.maxresults[ref_module];
		}
	}
	return 5;
};

AutocompleteRelation.prototype.MinCharsToSearch = function () {
	if (typeof this.data.mincharstosearch === 'number') {
		return this.data.mincharstosearch;
	} else if (typeof this.data.mincharstosearch === undefined) {
		var ref_module = this.getReferenceModule();
		if (ref_module !== '' && this.data.mincharstosearch[ref_module] !== undefined) {
			return this.data.mincharstosearch[ref_module];
		}
	}
	return 3;
};
