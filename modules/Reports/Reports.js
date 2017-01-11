/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var gcurrepfolderid=0;

// Setting cookies
function set_cookie ( name, value, exp_y, exp_m, exp_d, path, domain, secure )
{
	var cookie_string = name + "=" + escape ( value );

	if ( exp_y ) {
		var expires = new Date ( exp_y, exp_m, exp_d );
		cookie_string += "; expires=" + expires.toGMTString();
	}

	if ( path )
		cookie_string += "; path=" + escape ( path );

	if ( domain )
		cookie_string += "; domain=" + escape ( domain );

	if ( secure )
		cookie_string += "; secure";

	document.cookie = cookie_string;
}

// Retrieving cookies
function get_cookie ( cookie_name )
{
	var results = document.cookie.match ( cookie_name + '=(.*?)(;|$)' );
	if ( results )
		return ( unescape ( results[1] ) );
	else
		return null;
}

// Delete cookies
function delete_cookie ( cookie_name )
{
	var cookie_date = new Date ( );  // current date & time
	cookie_date.setTime ( cookie_date.getTime() - 1 );
	document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}
function goToURL( url )
{
	document.location.href = url;
}

function invokeAction( actionName )
{
	if( actionName == "newReport" ) {
		goToURL( "?module=Reports&action=NewReport0&return_module=Reports&return_action=index" );
		return;
	}
	goToURL( "/crm/ScheduleReport.do?step=showAllSchedules" );
}
function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	if (trim(form.folderName.value) == "") {
		isError = true;
		errorMessage += "\nFolder Name";
	}
	// Here we decide whether to submit the form.
	if (isError == true) {
		alert(alert_arr.MISSING_FIELDS + errorMessage);
		return false;
	}
	return true;
}

function setObjects()
{
	availListObj=getObj("availList");
	selectedColumnsObj=getObj("selectedColumns");
	moveupLinkObj=getObj("moveup_link");
	moveupDisabledObj=getObj("moveup_disabled");
	movedownLinkObj=getObj("movedown_link");
	movedownDisabledObj=getObj("movedown_disabled");
}

function addColumn()
{
	for (i=0;i<selectedColumnsObj.length;i++)
	{
		selectedColumnsObj.options[i].selected=false;
	}
	addColumnStep1();
}

function addColumnStep1()
{
	//the below line is added for report not woking properly in browser IE7
	document.getElementById("selectedColumns").style.width="164px";

	if (availListObj.options.selectedIndex > -1)
	{
		for (i=0;i<availListObj.length;i++)
		{
			if (availListObj.options[i].selected==true)
			{
				var rowFound=false;
				for (j=0;j<selectedColumnsObj.length;j++)
				{
					if (selectedColumnsObj.options[j].value==availListObj.options[i].value)
					{
						var rowFound=true;
						var existingObj=selectedColumnsObj.options[j];
						break;
					}
				}

				if (rowFound!=true)
				{
					var newColObj=document.createElement("OPTION");
					newColObj.value=availListObj.options[i].value;
					if (browser_ie) newColObj.innerText=availListObj.options[i].innerText;
					else if (browser_nn4 || browser_nn6) newColObj.text=availListObj.options[i].text;
					selectedColumnsObj.appendChild(newColObj);
					newColObj.selected=true;
				}
				else
				{
					existingObj.selected=true;
				}
				availListObj.options[i].selected=false;
				addColumnStep1();
			}
		}
	}
}
//this function is done for checking,whether the user has access to edit the field
function selectedColumnClick(oSel)
{
	var error_msg = '';
	var error_str = false;
	if(oSel.selectedIndex > -1) {
		for(var i = 0; i < oSel.options.length; ++i) {
			if(oSel.options[i].selected == true && oSel.options[i].disabled == true) {
				error_msg = error_msg + oSel.options[i].text+',';
				error_str = true;
				oSel.options[i].selected = false;
			}
		}
	}
	if(error_str)
	{
		error_msg = error_msg.substr(0,error_msg.length-1);
		alert(alert_arr.NOT_ALLOWED_TO_EDIT_FIELDS+"\n"+error_msg);
		return false;
	}
	else
		return true;
}
function delColumn()
{
	if (selectedColumnsObj.options.selectedIndex > -1)
	{
		for (i=0;i < selectedColumnsObj.options.length;i++)
		{
			if(selectedColumnsObj.options[i].selected == true)
			{
				selectedColumnsObj.remove(i);
				delColumn();
			}
		}
	}
}

function formSelectColumnString()
{
	var selectedColStr = "";
	for (i=0;i<selectedColumnsObj.options.length;i++)
	{
		selectedColStr += selectedColumnsObj.options[i].value + ";";
	}
	document.NewReport.selectedColumnsString.value = selectedColStr;
}

function moveUp()
{
	var currpos=selectedColumnsObj.options.selectedIndex;
	var tempdisabled= false;
	for (i=0;i<selectedColumnsObj.length;i++)
	{
		if(i != currpos)
			selectedColumnsObj.options[i].selected=false;
	}
	if (currpos>0)
	{
		var prevpos=selectedColumnsObj.options.selectedIndex-1;

		if (browser_ie)
		{
			temp=selectedColumnsObj.options[prevpos].innerText;
			tempdisabled = selectedColumnsObj.options[prevpos].disabled;
			selectedColumnsObj.options[prevpos].innerText=selectedColumnsObj.options[currpos].innerText;
			selectedColumnsObj.options[prevpos].disabled = false;
			selectedColumnsObj.options[currpos].innerText=temp;
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		else if (browser_nn4 || browser_nn6)
		{
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

function moveDown()
{
	var currpos=selectedColumnsObj.options.selectedIndex;
	var tempdisabled= false;
	for (i=0;i<selectedColumnsObj.length;i++)
	{
		if(i != currpos)
			selectedColumnsObj.options[i].selected=false;
	}
	if (currpos<selectedColumnsObj.options.length-1)
	{
		var nextpos=selectedColumnsObj.options.selectedIndex+1;

		if (browser_ie)
		{
			temp=selectedColumnsObj.options[nextpos].innerText;
			tempdisabled = selectedColumnsObj.options[nextpos].disabled;
			selectedColumnsObj.options[nextpos].innerText=selectedColumnsObj.options[currpos].innerText;
			selectedColumnsObj.options[nextpos].disabled = false;
			selectedColumnsObj.options[nextpos];
			selectedColumnsObj.options[currpos].innerText=temp;
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		else if (browser_nn4 || browser_nn6)
		{
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

function disableMove()
{
	var cnt=0;
	for (i=0;i<selectedColumnsObj.options.length;i++) {
		if (selectedColumnsObj.options[i].selected==true)
			cnt++;
	}
	if (cnt>1) {
		moveupLinkObj.style.display=movedownLinkObj.style.display="none";
		moveupDisabledObj.style.display=movedownDisabledObj.style.display="block";
	} else {
		moveupLinkObj.style.display=movedownLinkObj.style.display="block";
		moveupDisabledObj.style.display=movedownDisabledObj.style.display="none";
	}
}

function hideTabs()
{
	// Check the selected report type
	var objreportType = document.forms.NewReport['reportType'];
	if(objreportType[0].checked == true) objreportType = objreportType[0];
	else if(objreportType[1].checked == true) objreportType = objreportType[1];

	if(objreportType.value == 'tabular')
	{
		divarray = new Array('step1','step2','step4','step5','step6','step7');
	}
	else
	{
		divarray = new Array('step1','step2','step3','step4','step5','step6','step7');
	}
}

function showSaveDialog()
{
	url = "index.php?module=Reports&action=SaveReport";
	window.open(url,"Save_Report","width=550,height=350,top=20,left=20;toolbar=no,status=no,menubar=no,directories=no,resizable=yes,scrollbar=no");
}

function saveAndRunReport()
{
	if(selectedColumnsObj.options.length == 0)
	{
		alert(alert_arr.COLUMNS_CANNOT_BE_EMPTY);
		return false;
	}
	formSelectedColumnString();
	formSelectColumnString();
	document.NewReport.submit();
}

function saveas() {
	if(selectedColumnsObj.options.length == 0) {
		alert(alert_arr.COLUMNS_CANNOT_BE_EMPTY);
		return false;
	}
	formSelectedColumnString();
	formSelectColumnString();
	var reportname = prompt(alert_arr.LBL_REPORT_NAME);
	if (reportname != null) {
		document.getElementById("newreportname").value = reportname;
		document.NewReport.submit();
	} else
		alert(alert_arr.LBL_REPORT_NAME_ERROR);
}

function changeSteps1()
{
	if(getObj('step5').style.display != 'none')
	{
		if(!checkAdvancedFilter())
			return false;

		var date1=getObj("startdate");
		var date2=getObj("enddate");

		//# validation added for date field validation in final step of report creation
		if ((date1.value != '') || (date2.value != '')) {
			if(!dateValidate("startdate","Start Date","D"))
				return false;

			if(!dateValidate("enddate","End Date","D"))
				return false;

			if(! dateComparison("startdate",'Start Date',"enddate",'End Date','LE'))
				return false;
		}
	}
	if(getObj('step6').style.display != 'none' && document.getElementsByName('record')[0].value!='') {
		var id = document.getElementById('save_as');
		id.style.display = 'inline';
	}
	if (getObj('step7').style.display != 'none') {

		var isScheduledObj = getObj("isReportScheduled");
		if(isScheduledObj.checked == true) {
			var selectedRecipientsObj = getObj("selectedRecipients");

			if (selectedRecipientsObj.options.length == 0) {
				alert(alert_arr.RECIPIENTS_CANNOT_BE_EMPTY);
				return false;
			}

			var selectedUsers = new Array();
			var selectedGroups = new Array();
			var selectedRoles = new Array();
			var selectedRolesAndSub = new Array();
			for(i = 0; i < selectedRecipientsObj.options.length; i++){
				var selectedCol = selectedRecipientsObj.options[i].value;
				var selectedColArr = selectedCol.split("::");
				if(selectedColArr[0] == "users")
					selectedUsers.push(selectedColArr[1]);
				else if(selectedColArr[0] == "groups")
					selectedGroups.push(selectedColArr[1]);
				else if(selectedColArr[0] == "roles")
					selectedRoles.push(selectedColArr[1]);
				else if(selectedColArr[0] == "rs")
					selectedRolesAndSub.push(selectedColArr[1]);
			}

			var selectedRecipients = { users : selectedUsers, groups : selectedGroups,
										roles : selectedRoles, rs : selectedRolesAndSub };
			var selectedRecipientsJson = JSON.stringify(selectedRecipients);
			document.NewReport.selectedRecipientsString.value = selectedRecipientsJson;

			var scheduledInterval= { scheduletype : document.NewReport.scheduledType.value,
									month : document.NewReport.scheduledMonth.value,
									date : document.NewReport.scheduledDOM.value,
									day : document.NewReport.scheduledDOW.value,
									time : document.NewReport.scheduledTime.value
								};

			var scheduledIntervalJson = JSON.stringify(scheduledInterval);
			document.NewReport.scheduledIntervalString.value = scheduledIntervalJson;
		}
		saveAndRunReport();
	} else {
		for (i = 0; i < divarray.length; i++) {
			if (getObj(divarray[i]).style.display != 'none') {
				if (i == 1 && selectedColumnsObj.options.length == 0) {
					alert(alert_arr.COLUMNS_CANNOT_BE_EMPTY);
					return false;
				}
				if (divarray[i + 1] == 'step7') {
					document.getElementById("next").value = finish_text;
				}
				hide(divarray[i]);
				show(divarray[i + 1]);
				tableid = divarray[i] + 'label';
				newtableid = divarray[i + 1] + 'label';
				getObj(tableid).className = 'settingsTabList';
				getObj(newtableid).className = 'settingsTabSelected';
				document.getElementById('back_rep').disabled = false;
				break;
			}
		}
	}
}
function changeStepsback1()
{
	if(getObj('step1').style.display != 'none')
	{
		document.NewReport.action.value='ReportsAjax';
		document.NewReport.file.value='NewReport0';
		document.NewReport.submit();
	}else
	{
		for(i = 0; i < divarray.length ;i++)
		{
			if(getObj(divarray[i]).style.display != 'none')
			{
				if(divarray[i] == 'step2' && !backwalk_flag)
				{
					document.getElementById('back_rep').disabled = true;
				}
				document.getElementById("next").value = next_text+'>';
				hide(divarray[i]);
				show(divarray[i-1]);
				tableid = divarray[i]+'label';
				newtableid = divarray[i-1]+'label';
				getObj(tableid).className = 'settingsTabList';
				getObj(newtableid).className = 'settingsTabSelected';
				break;
			}
		}
	}
}
function changeSteps() {
	if(getObj('step1').style.display != 'none') {
		if (trim(document.NewRep.reportname.value) == "") {
			alert(alert_arr.MISSING_REPORT_NAME);
			return false;
		} else {
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=reportCheck&reportName='+encodeURIComponent(document.NewRep.reportname.value)+'&reportid='+document.NewRep.record.value
			}).done(function (response) {
					if(response!=0) {
						alert(alert_arr.REPORT_NAME_EXISTS);
						return false;
					} else {
						hide('step1');
						show('step2');
						document.getElementById('back_rep').disabled = false;
						getObj('step1label').className = 'settingsTabList';
						getObj('step2label').className = 'settingsTabSelected';
					}
				}
			);
		}
	} else {
		// only two related modules in report due to mysql performance limitations
		var mods = document.getElementsByTagName("input");
		var modsselected = 0;
		for(var i = 0; i < mods.length; i++) {
			if (mods[i].name.indexOf('secondarymodule_') == 0 && mods[i].checked) {
				modsselected++;
			}
		}
		if (modsselected<=2) {
			document.NewRep.submit();
		} else {
			alert(alert_arr.MAXIMUM_OF_TWO_MODULES_PERMITTED);
		}
	}
}
function changeStepsback()
{
	hide('step2');
	show('step1');
	document.getElementById('back_rep').disabled = true;
	getObj('step1label').className = 'settingsTabSelected';
	getObj('step2label').className = 'settingsTabList';
}
function editReport(id)
{
	var arg = 'index.php?module=Reports&action=ReportsAjax&file=NewReport0&record='+id;
	fnPopupWin(arg);
}
function CreateReport(element)
{
	if(document.getElementById(element) == null) return;
	var module = document.getElementById(element).value;
	var arg ='index.php?module=Reports&action=ReportsAjax&file=NewReport0&folder='+gcurrepfolderid+'&reportmodule='+module;
	fnPopupWin(arg);
}
function fnPopupWin(winName){
	window.open(winName, "ReportWindow","width=790px,height=680px,scrollbars=yes");
}
function re_dateValidate(fldval,fldLabel,type) {
	if(re_patternValidate(fldval,fldLabel,"DATE")==false)
		return false;
	dateval=fldval.replace(/^\s+/g, '').replace(/\s+$/g, '');

	var dateelements=splitDateVal(dateval);

	dd=dateelements[0];
	mm=dateelements[1];
	yyyy=dateelements[2];

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
		case 11 :if (dd>30) {
			alert(alert_arr.ENTER_VALID+fldLabel);
			return false;
		}
	}

	var currdate=new Date();
	var chkdate=new Date();

	chkdate.setYear(yyyy);
	chkdate.setMonth(mm-1);
	chkdate.setDate(dd);

	if (type!="OTH") {
		if (!compareDates(chkdate,fldLabel,currdate,"current date",type)) {
			return false;
		} else return true;
	} else return true;
}

//Copied from general.js and altered some lines. becos we cant send vales to function present in general.js. it accept only field names.
function re_patternValidate(fldval,fldLabel,type) {
	if (type.toUpperCase()=="DATE") {//DATE validation
		switch (userDateFormat) {
			case "yyyy-mm-dd" :
				var re = /^\d{4}(-)\d{1,2}\1\d{1,2}$/;
				break;
			case "mm-dd-yyyy" :
			case "dd-mm-yyyy" :
				var re = /^\d{1,2}(-)\d{1,2}\1\d{4}$/;
		}
	}
	if (type.toUpperCase()=="TIMESECONDS") {//TIME validation
		var re = new RegExp("^([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$|^([0-1][0-9]|[2][0-3]):([0-5][0-9])$");
	}
	if (!re.test(fldval)) {
		alert(alert_arr.ENTER_VALID + fldLabel);
		return false;
	}
	else return true;
}

//added to fix the ticket #5117
function standardFilterDisplay()
{
	if(document.NewReport.stdDateFilterField.options.length <= 0 || (document.NewReport.stdDateFilterField.selectedIndex > -1 && document.NewReport.stdDateFilterField.options[document.NewReport.stdDateFilterField.selectedIndex].value == "Not Accessible"))
	{
		getObj('stdDateFilter').disabled = true;
		getObj('startdate').disabled = true;getObj('enddate').disabled = true;
		getObj('jscal_trigger_date_start').style.visibility="hidden";
		getObj('jscal_trigger_date_end').style.visibility="hidden";
	}
	else
	{
		getObj('stdDateFilter').disabled = false;
		getObj('startdate').disabled = false;
		getObj('enddate').disabled = false;
		getObj('jscal_trigger_date_start').style.visibility="visible";
		getObj('jscal_trigger_date_end').style.visibility="visible";
	}
}

function updateRelFieldOptions(sel, opSelName) {
	var selObj = document.getElementById(opSelName);
	var fieldtype = null ;
	var currOption = selObj.options[selObj.selectedIndex];
	var currField = sel.options[sel.selectedIndex];

	if(currField.value != null && currField.value.length != 0) {
		fieldtype = trimfValues(currField.value);
		ops = rel_fields[fieldtype];
		var off = 0;
		if(ops != null) {
			var nMaxVal = selObj.length;
			for(nLoop = 0; nLoop < nMaxVal; nLoop++) {
				selObj.remove(0);
			}
			selObj.options[0] = new Option ('None', '');
			if (currField.value == '') {
				selObj.options[0].selected = true;
			}
			off = 1;
			for (var i = 0; i < ops.length; i++) {
				var field_array = ops[i].split("::");
				var label = field_array[1];
				var field = field_array[0];
				if (label == null) continue;
				var option = new Option (label, field);
				selObj.options[i + off] = option;
				if (currOption != null && currOption.value == option.value) {
					option.selected = true;
				}
			}
		}
	} else {
		var nMaxVal = selObj.length;
		for(nLoop = 0; nLoop < nMaxVal; nLoop++)
		{
			selObj.remove(0);
		}
		selObj.options[0] = new Option ('None', '');
		if (currField.value == '') {
			selObj.options[0].selected = true;
		}
	}
}

function AddFieldToFilter(id, sel){
	if(trim(document.getElementById("fval"+id).value)==''){
		document.getElementById("fval"+id).value = document.getElementById("fval_"+id).value;
	} else {
		document.getElementById("fval"+id).value = document.getElementById("fval"+id).value+","+document.getElementById("fval_"+id).value;
	}
}
function fnLoadRepValues(tab1,tab2,block1,block2){
	document.getElementById(block1).style.display='block';
	document.getElementById(block2).style.display='none';
	document.getElementById(tab1).className='dvtSelectedCell';
	document.getElementById(tab2).className='dvtUnSelectedCell';
}
function addChartsToHomepage(reportid) {
	var windowtitle = document.getElementById('windowtitle_id').value;
	if (windowtitle.length == 0) {
		alert(alert_arr.LBL_ENTER_WINDOW_TITLE);
		return false;
	}
	var charttype = document.getElementById('selreportcharttype_id').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Reports&action=ReportsAjax&file=UpdatedashbordReportRel&ajax=true&reportid=' + reportid + '&windowtitle=' + windowtitle + '&charttype=' + charttype
	}).done(function (response) {
		if ((response != '')) {
			alert(response);
		}
		fnhide('addcharttoHomepage');
		document.getElementById('widgetsuccess').style.display = 'block';
		document.getElementById('widgetsuccess').style.display = 'none';
		jQuery('#widgetsuccess').show();
		setTimeout(hidewidgetmessage, 3000);
	});
	return true;
}
function hidewidgetmessage() {
	jQuery('#widgetsuccess').fadeOut();
}
function getDateFieldGrouping(group1){
	var selectfield = document.getElementById(group1).value;
	var selectfieldname = selectfield.split(':');
	var typeofdata = selectfieldname[4];
	var id_div = group1+"time";
	if(typeofdata == 'D'){
		show(id_div);
	}else{
		var id = document.getElementById(id_div);
		id.style.display = 'none';
	}
}

/**
 * IE has a bug where document.getElementsByName doesnt include result of dynamically created elements
 */
function vt_getElementsByName(tagName, elementName) {
	var inputs = document.getElementsByTagName( tagName );
	var selectedElements = [];
	for(var i=0;i<inputs.length;i++){
		if(inputs.item(i).getAttribute( 'name' ) == elementName ){
			selectedElements.push( inputs.item(i) );
		}
	}
	return selectedElements;
}

function setScheduleOptions() {
	var stid = document.getElementById('scheduledType').value;
	switch( stid ) {
		case "0": // nothing choosen
		case "1": // hourly
			document.getElementById('scheduledMonthSpan').style.display = 'none';
			document.getElementById('scheduledDOMSpan').style.display = 'none';
			document.getElementById('scheduledDOWSpan').style.display = 'none';
			document.getElementById('scheduledTimeSpan').style.display = 'none';
			break;
		case "2": // daily
			document.getElementById('scheduledMonthSpan').style.display = 'none';
			document.getElementById('scheduledDOMSpan').style.display = 'none';
			document.getElementById('scheduledDOWSpan').style.display = 'none';
			document.getElementById('scheduledTimeSpan').style.display = 'inline';
			break;
		case "3": // weekly
		case "4": // bi-weekly
			document.getElementById('scheduledMonthSpan').style.display = 'none';
			document.getElementById('scheduledDOMSpan').style.display = 'none';
			document.getElementById('scheduledDOWSpan').style.display = 'inline';
			document.getElementById('scheduledTimeSpan').style.display = 'inline';
			break;
		case "5": // monthly
			document.getElementById('scheduledMonthSpan').style.display = 'none';
			document.getElementById('scheduledDOMSpan').style.display = 'inline';
			document.getElementById('scheduledDOWSpan').style.display = 'none';
			document.getElementById('scheduledTimeSpan').style.display = 'inline';
			break;
		case "6": // annually
			document.getElementById('scheduledMonthSpan').style.display = 'inline';
			document.getElementById('scheduledDOMSpan').style.display = 'inline';
			document.getElementById('scheduledDOWSpan').style.display = 'none';
			document.getElementById('scheduledTimeSpan').style.display = 'inline';
			break;
	}
}

/*
* javascript function to display the div tag
* @param divId :: div tag ID
*/
function showAddChartPopup()
{
	jQuery('#addcharttoHomepage').css('display', 'inline');
	placeAtCenterChartPopup(jQuery('#addcharttoHomepage'));
}

function placeAtCenterChartPopup(element){
	element.css("position","absolute");
	element.css("top", (((jQuery(window).height()-800) - element.outerHeight()) / 2) + jQuery(window).scrollTop() + "px");
	element.css("left", ((jQuery(window).width() - element.outerWidth()) / 2) + jQuery(window).scrollLeft() + "px");
}

function reports_goback() {
	$("#not_premitted").css("display","none");
	$("#example-vertical").css("display","block");
}

/**
 * [fillReportColumnsTotal description]
 * @param  {Object} block
 */
function fillReportColumnsTotal(block) {
	var block_length = block.length;
	var tbody = $("<tbody>");
	var is_empty = true;
	for(var i=0;i<block_length;i++) {
		if(block[i].length>0) {
			is_empty = false;
			var obj = block[i];
			for(var j=0;j<obj.length;j++) {
				var tr = $("<tr>",{"class":"lvtColData","onmouseover":"this.className='lvtColDataHover'","onmouseout":"this.className='lvtColData'","bgcolor":"white"});
				var td = $("<td>");
				var label = obj[j].label[0];
				var checkboxes = obj[j].checkboxes;
				var b = $("<b>");
				b.append(label);
				td.append(b);
				tr.append(td);
				for(k=0;k<checkboxes.length;k++) {
					var checkbox = $("<input>",{"type":"checkbox","name":checkboxes[k].name});
					if(checkboxes[k].hasOwnProperty('checked'))
						checkbox.attr("checked",true);
					var td = $("<td>");
					td.append(checkbox);
					tr.append(td);
				}
				tbody.append(tr);
			}
		}
	}
	if(is_empty) {
		var tr = $("<tr>",{"class":"lvtColData","bgcolor":"white"});
		var td = $("<td>",{"colspan":5});
		td.append(NO_COLUMN);
		tr.append(td);
		tbody.append(tr);
	}
	$("#totalcolumns").html("");
	$("#totalcolumns").append(tbody.html());
}

/**
 * [returnList description]
 * @param  {json} block
 * @return {html}
 */
function returnList(block) {
	if(block == null)
		return "";
	var list = block;
	var list_length = Object.keys(list).length;
	if( list_length > 0) {
		var $html = $("<select>");
		for(i=0;i<list_length;i++) {
			var option = $("<option>",{"value":list[i].value});
			if(list[i].hasOwnProperty('selected') && list[i].selected == true)
				option.attr("selected",true);
			if(list[i].hasOwnProperty('permission'))
				option.attr("permission","yes");
			if(list[i].hasOwnProperty('disabled'))
				option.attr("disabled",true);

			option.append(list[i].label);
			$html.append(option);
		}
		return $html.html()
	}
}

/**
 * [fillList description]
 * @param  {Object} block
 * @param  {String} element_id
 */
function fillList(block,element_id) {
	var html = returnList(block);
	if(html !== "")
		$("#"+element_id).html("");
		$("#"+element_id).append(html);
}

/**
 * returns full List that has optgroup elements
 * @param  {Object} block
 * @return {HTML} Select list in HTML format
 */
function returnFullList(block) {

	var block_length = block.length;
	if( block_length > 0) {
		var $html = $("<select>");
		for(i=0;i<block_length;i++) {

			var node = block[i];
			var optgroup =  $("<optgroup>",{"class":node.class,"label":node.label,"style":block.style});
			var options_length = 0;
			if(node.hasOwnProperty('options'))
				options_length = node.options.length;

			for(j = 0; j<options_length;j++) {
				var option = node.options[j];
				var option_el = $("<option>",{"value":option.value});
				option_el.append(option.label);
				if(option.hasOwnProperty('disabled'))
					option_el.attr("disabled",true);
				if(option.hasOwnProperty('selected'))
					option_el.attr("selected",true);
				optgroup.append(option_el);
			}
			$html.append(optgroup);
		}
		return $html.html();
	}
}

/**
 * [fillList description]
 * @param  {Object} block
 * @param  {String} element_id
 */
function fillFullList(block,element_id,has_none=false,label_none="") {
	var html = returnFullList(block);
	if(has_none)
		html = "<option value='none'>"+label_none+"</option>" + html;
	if(html !== "")
		$("#"+element_id).html("");
		$("#"+element_id).append(html);
}

/**
 * Set request data for Ajax Call
 * @param {Number} step
 */
function setStepData(step){
	var data = {};
	data['record'] = document.NewReport.record.value;
	data['step'] = step;
	data['primarymodule'] = document.NewReport.primarymodule.value;
	$(".secondarymodule:checkbox:checked").each(function(){
		var $this = $(this);
		data[$this.attr("name")] = $this.val();
	});
	return data;
}

/**
 * Validate Start and end date for Standart Filters
 * @return {bool}
 */
function validateDate() {
	if(!checkAdvancedFilter())
		return false;

	var date1=getObj("startdate");
	var date2=getObj("enddate");

	if ((date1.value != '') || (date2.value != '')) {
		if(!dateValidate("startdate","Start Date","D"))
			return false;

		if(!dateValidate("enddate","End Date","D"))
			return false;

		if(! dateComparison("startdate",'Start Date',"enddate",'End Date','LE'))
			return false;
	}
	return true;
}

/**
 * Schedule emails for reports
 * @return {bool}
 */
function ScheduleEmail() {
	var isScheduledObj = getObj("isReportScheduled");
	if(isScheduledObj.checked == true) {
		var selectedRecipientsObj = getObj("selectedRecipients");

		if (selectedRecipientsObj.options.length == 0) {
			alert(alert_arr.RECIPIENTS_CANNOT_BE_EMPTY);
			return false;
		}

		var selectedUsers = new Array();
		var selectedGroups = new Array();
		var selectedRoles = new Array();
		var selectedRolesAndSub = new Array();
		for(i = 0; i < selectedRecipientsObj.options.length; i++){
			var selectedCol = selectedRecipientsObj.options[i].value;
			var selectedColArr = selectedCol.split("::");
			if(selectedColArr[0] == "users")
				selectedUsers.push(selectedColArr[1]);
			else if(selectedColArr[0] == "groups")
				selectedGroups.push(selectedColArr[1]);
			else if(selectedColArr[0] == "roles")
				selectedRoles.push(selectedColArr[1]);
			else if(selectedColArr[0] == "rs")
				selectedRolesAndSub.push(selectedColArr[1]);
		}

		var selectedRecipients = { users : selectedUsers, groups : selectedGroups,
									roles : selectedRoles, rs : selectedRolesAndSub };
		var selectedRecipientsJson = JSON.stringify(selectedRecipients);
		document.NewReport.selectedRecipientsString.value = selectedRecipientsJson;

		var scheduledInterval= {
			scheduletype : document.NewReport.scheduledType.value,
			month : document.NewReport.scheduledMonth.value,
			date : document.NewReport.scheduledDOM.value,
			day : document.NewReport.scheduledDOW.value,
			time : document.NewReport.scheduledTime.value
		};

		var scheduledIntervalJson = JSON.stringify(scheduledInterval);
		document.NewReport.scheduledIntervalString.value = scheduledIntervalJson;
	}
	return true;
}

/**
 * Set Report type from Json response
 * @param  {Json} response
 * @return {bool}
 */
function setReportType(response) {
	if(response.permission == 1) {
		document.NewReport.secondarymodule.value = response.secondarymodule;
		var selected_report_type = response.selectedreporttype;
		if(selected_report_type == "tabular")
			$("#tabular").attr("checked",true);
		else
			$("#summary").attr("checked",true);
		return true;
	}
	else {
		$("#deny_msg").html(LBL_NO_PERMISSION+" "+response.primarymodule+" "+response.secondarymodule);
		wizard.css("display","none");
		$("#not_premitted").css("display","block");
		return false;
	}
}

/**
 * Function to populate Select Columns
 * @param  {Json} response
 * @return {bool}
 */
function fillSelectedColumns(response) {

	if(response.permission === 1) {
		fillFullList(response.BLOCK1, "availList");
		if(response.hasOwnProperty('BLOCK2') && response.BLOCK2.length > 0)
			fillList(response.BLOCK2,"selectedColumns");
		setObjects();
		return true;
	}

	return false;
}

/**
 * Function to populate Filter options
 * @param  {Json} response
 * @return {bool}
 */
function fillFilterInfo(response) {
	COL_BLOCK = returnFullList(response.COLUMNS_BLOCK);
	FOPTION_ADV = returnList(response.FOPTION);
	REL_FIELDS = response.REL_FIELDS;
	rel_fields = jQuery.parseJSON( response.REL_FIELDS );
	fillList(response.BLOCKJS,"stdDateFilterField");
	fillList(response.BLOCKCRITERIA,"stdDateFilter");
	if( response.hasOwnProperty("CRITERIA_GROUPS") && !updated_grouping_criteria ) {
		add_grouping_criteria(response.CRITERIA_GROUPS);
		updated_grouping_criteria = true;
	}

	if(response.hasOwnProperty("STARTDATE") && response.hasOwnProperty("ENDDATE")) {
		$("#jscal_field_date_start").val(response.STARTDATE);
		$("#jscal_field_date_end").val(response.ENDDATE);
		$("#jscal_trigger_date_start").css("visibility","visible");
		$("#jscal_trigger_date_end").css("visibility","visible");
	}
	return true;
}

/**
 * Function to populate Grouping options
 * @param  {Json} response
 * @return {bool}
 */
function fillGroupingInfo(response) {
	userIdArr 	= response.USERIDSTR.split(",");
	userNameArr = response.USERNAMESTR.split(",");
	grpIdArr 	= response.GROUPIDSTR.split(",");
	grpNameArr 	= response.GROUPNAMESTR.split(",");
	set_Objects();
	show_Options();
	fillList(response.VISIBLECRITERIA,"stdtypeFilter");
	if(response.hasOwnProperty("MEMBER")) {
		fillList(response.MEMBER,"columnsSelected");
		toggleAssignType("Shared");
	}
	return true;
}
