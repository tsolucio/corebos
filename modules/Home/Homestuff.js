/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *******************************************************************************/

/**
 * this function is used to show hide the columns in the add widget div based on the option selected
 * @param string typeName - the selected option
 */
function chooseType(typeName) {
	VtigerJS_DialogBox.showbusy();
	document.getElementById('stufftype_id').value=typeName;

	var typeLabel = typeName;
	if (alert_arr[typeName] != null && alert_arr[typeName] != '' && alert_arr[typeName] != 'undefined') {
		typeLabel = alert_arr[typeName];
	}
	if (typeLabel == 'defaultwidget') {
		document.getElementById('divHeader').innerHTML='<b>'+alert_arr.LBL_SELECT+'</b>';
		VtigerJS_DialogBox.showbusy();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&home=homewidget'
		}).done(function (response) {
			var responseVal=response;
			document.getElementById('home').innerHTML=response;
			show('addWidgetsDiv');
			placeAtCenter(document.getElementById('addWidgetsDiv'));
			document.getElementById('homewidget').style.display='block';
			document.getElementById('moduleNameRow').style.display='none';
			document.getElementById('moduleFilterRow').style.display='none';
			document.getElementById('modulePrimeRow').style.display='none';
			document.getElementById('rssRow').style.display='none';
			document.getElementById('showrow').style.display='none';
			document.getElementById('dashTypeRow').style.display='none';
			document.getElementById('dashNameRow').style.display='none';
			document.getElementById('StuffTitleId').style.display='none';
			VtigerJS_DialogBox.hidebusy();
			document.getElementById('reportNameRow').style.display='none';
			document.getElementById('reportTypeRow').style.display='none';
		});
	} else {
		document.getElementById('divHeader').innerHTML='<b>'+alert_arr.LBL_ADD+typeLabel+'</b>';
	}
	if (typeName=='Module') {
		document.getElementById('moduleNameRow').style.display='block';
		document.getElementById('moduleFilterRow').style.display='block';
		document.getElementById('modulePrimeRow').style.display='block';
		document.getElementById('showrow').style.display='block';
		document.getElementById('rssRow').style.display='none';
		document.getElementById('dashNameRow').style.display='none';
		document.getElementById('dashTypeRow').style.display='none';
		document.getElementById('StuffTitleId').style.display='block';
		document.getElementById('homewidget').style.display='none';
		document.getElementById('reportNameRow').style.display='none';
		document.getElementById('reportTypeRow').style.display='none';
		//document.getElementById('homeURLField').style.display = "none";
	} else if (typeName=='DashBoard') {
		document.getElementById('moduleNameRow').style.display='none';
		document.getElementById('moduleFilterRow').style.display='none';
		document.getElementById('modulePrimeRow').style.display='none';
		document.getElementById('rssRow').style.display='none';
		document.getElementById('showrow').style.display='none';
		document.getElementById('dashNameRow').style.display='block';
		document.getElementById('dashTypeRow').style.display='block';
		document.getElementById('StuffTitleId').style.display='block';
		document.getElementById('reportNameRow').style.display='none';
		document.getElementById('reportTypeRow').style.display='none';
		document.getElementById('homewidget').style.display='none';
		//document.getElementById('homeURLField').style.display = "none";
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&dash=dashboard'
		}).done(function (response) {
			var responseVal=response;
			document.getElementById('selDashName').innerHTML=response;
			show('addWidgetsDiv');
			placeAtCenter(document.getElementById('addWidgetsDiv'));
			VtigerJS_DialogBox.hidebusy();
		});
	} else if (typeName=='RSS') {
		document.getElementById('moduleNameRow').style.display='none';
		document.getElementById('moduleFilterRow').style.display='none';
		document.getElementById('modulePrimeRow').style.display='none';
		document.getElementById('showrow').style.display='block';
		document.getElementById('rssRow').style.display='block';
		document.getElementById('dashNameRow').style.display='none';
		document.getElementById('dashTypeRow').style.display='none';
		document.getElementById('StuffTitleId').style.display='block';
		document.getElementById('homewidget').style.display='none';
		VtigerJS_DialogBox.hidebusy();
		document.getElementById('reportNameRow').style.display='none';
		document.getElementById('reportTypeRow').style.display='none';
		//document.getElementById('homeURLField').style.display = "none";
	} else if (typeName=='Default') {
		document.getElementById('moduleNameRow').style.display='none';
		document.getElementById('moduleFilterRow').style.display='none';
		document.getElementById('modulePrimeRow').style.display='none';
		document.getElementById('showrow').style.display='none';
		document.getElementById('rssRow').style.display='none';
		document.getElementById('dashNameRow').style.display='none';
		document.getElementById('dashTypeRow').style.display='none';
		document.getElementById('StuffTitleId').style.display='none';
		document.getElementById('homewidget').style.display='none';
		document.getElementById('url_id').style.display = 'none';
		document.getElementById('reportNameRow').style.display='none';
		document.getElementById('reportTypeRow').style.display='none';
	} else if (typeName == 'Notebook') {
		document.getElementById('moduleNameRow').style.display='none';
		document.getElementById('moduleFilterRow').style.display='none';
		document.getElementById('modulePrimeRow').style.display='none';
		document.getElementById('showrow').style.display='none';
		document.getElementById('rssRow').style.display='none';
		document.getElementById('dashNameRow').style.display='none';
		document.getElementById('dashTypeRow').style.display='none';
		document.getElementById('StuffTitleId').style.display='block';
		VtigerJS_DialogBox.hidebusy();
		document.getElementById('homewidget').style.display='none';
		document.getElementById('reportNameRow').style.display='none';
		document.getElementById('reportTypeRow').style.display='none';
		//document.getElementById('homeURLField').style.display = "none";
	} else if (typeName == 'ReportCharts') {
		document.getElementById('moduleNameRow').style.display='none';
		document.getElementById('moduleFilterRow').style.display='none';
		document.getElementById('modulePrimeRow').style.display='none';
		document.getElementById('rssRow').style.display='none';
		document.getElementById('showrow').style.display='none';
		document.getElementById('StuffTitleId').style.display='block';
		document.getElementById('reportNameRow').style.display='block';
		document.getElementById('reportTypeRow').style.display='block';
		VtigerJS_DialogBox.hidebusy();
		document.getElementById('dashNameRow').style.display='none';
		document.getElementById('dashTypeRow').style.display='none';
		document.getElementById('homewidget').style.display='none';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Home&action=HomeAjax&file=HomeReportChart&ajax=true'
		}).done(function (response) {
			document.getElementById('selReportName').innerHTML=response;
			show('addWidgetsDiv');
			placeAtCenter(document.getElementById('addWidgetsDiv'));
			VtigerJS_DialogBox.hidebusy();
		});
	}
	/*else if(typeName == 'URL'){
		document.getElementById('moduleNameRow').style.display="none";
		document.getElementById('moduleFilterRow').style.display="none";
		document.getElementById('modulePrimeRow').style.display="none";
		document.getElementById('showrow').style.display="none";
		document.getElementById('rssRow').style.display="none";
		document.getElementById('dashNameRow').style.display="none";
		document.getElementById('dashTypeRow').style.display="none";
		document.getElementById('StuffTitleId').style.display="block";
		VtigerJS_DialogBox.hidebusy();
		//document.getElementById('homeURLField').style.display = "block";
	}*/
}

/**
 * this function is used to set the filter list when the module name is changed
 * @param string modName - the modula name for which you want the filter list
 */
function setFilter(modName) {
	var modval=modName.value;
	document.getElementById('savebtn').disabled = true;
	if (modval!='') {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&modname='+modval
		}).done(function (response) {
			var responseVal=response;
			document.getElementById('selModFilter_id').innerHTML=response;
			setPrimaryFld(document.getElementById('selFilterid'));
			show('addWidgetsDiv');
			placeAtCenter(document.getElementById('addWidgetsDiv'));
		});
	}
}

/**
 * this function is used to set the field list when the module name is changed
 * @param string modName - the modula name for which you want the field list
 */
function setPrimaryFld(Primeval) {
	primecvid=Primeval.value;
	var fldmodule = document.getElementById('selmodule_id').options[document.getElementById('selmodule_id').selectedIndex].value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&primecvid='+primecvid+'&fieldmodname='+fldmodule
	}).done(function (response) {
		var responseVal=response;
		document.getElementById('selModPrime_id').innerHTML=response;
		document.getElementById('selPrimeFldid').selectedIndex = 0;
		VtigerJS_DialogBox.hidebusy();
		document.getElementById('savebtn').disabled = false;
	});
}

/**
 * this function displays the div for selecting the number of rows in a widget
 * @param string sid - the id of the widget for which the div is being displayed
 */
function showEditrow(sid) {
	document.getElementById('editRowmodrss_'+sid).className='show_tab';
}

/**
 * this function is used to hide the div for selecting the number of rows in a widget
 * @param string editRow - the id of the div
 */
function cancelEntries(editRow) {
	document.getElementById(editRow).className='hide_tab';
}

/**
 * this function is used to save the maximum entries that a widget can display
 * @param string selMaxName - the widget name
 */
function saveEntries(selMaxName) {
	sidarr=selMaxName.split('_');
	sid=sidarr[1];
	document.getElementById('refresh_'+sid).innerHTML=document.getElementById('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+sid);
	showmax=document.getElementById(selMaxName).value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&showmaxval='+showmax+'&sid='+sid
	}).done(function (response) {
		var responseVal=response;
		eval(response);
		document.getElementById('refresh_'+sid).innerHTML='';
	});
}

/**
 * this function is used to save the dashboard values
 */
function saveEditDash(dashRowId) {
	document.getElementById('refresh_'+dashRowId).innerHTML=document.getElementById('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+dashRowId);
	var dashVal='';
	var iter=0;
	for (iter=0; iter<3; iter++) {
		if (document.getElementById('dashradio_'+[iter]).checked) {
			dashVal=document.getElementById('dashradio_'+[iter]).value;
		}
	}
	did=dashRowId;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&dashVal='+dashVal+'&did='+did
	}).done(function (response) {
		var responseVal=response;
		eval(response);
		document.getElementById('refresh_'+did).innerHTML='';
	});
}

/**
 * this function is used to delete widgets form the home page
 * @param string sid - the stuffid of the widget
 */
function DelStuff(sid) {
	if (confirm(alert_arr.SURE_TO_DELETE)) {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&homestuffid='+sid,
		}).done(function (response) {
			var responseVal=response;
			if (response.indexOf('SUCCESS') > -1) {
				var delchild = document.getElementById('stuff_'+sid);
				odeletedChild = document.getElementById('MainMatrix').removeChild(delchild);
				document.getElementById('seqSettings').innerHTML= '<table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small"><tr>'
					+ '<td align="center">Widget deleted sucessfully.</td></tr></table>';
				document.getElementById('seqSettings').style.display = 'block';
				document.getElementById('seqSettings').style.display = 'none';
				placeAtCenter(document.getElementById('seqSettings'));
				jQuery('#seqSettings').fadeIn();
				setTimeout(hideSeqSettings, 3000);
			} else {
				alert(alert_arr.ERROR_DELETING_TRY_AGAIN);
			}
		});
	}
}

/**
 * this function loads the newly added div to the home page
 * @param string stuffid - the id of the newly created div
 * @param string stufftype - the stuff type for the new div (for e.g. rss)
 */
function loadAddedDiv(stuffid, stufftype) {
	gstuffId = stuffid;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=NewBlock&stuffid='+stuffid+'&stufftype='+stufftype
	}).done(function (response) {
		var responseVal=response;
		document.getElementById('MainMatrix').style.display= 'none';
		document.getElementById('MainMatrix').innerHTML = response + document.getElementById('MainMatrix').innerHTML;
		positionDivInAccord('stuff_'+gstuffId, '', stufftype);
		initHomePage();
		loadStuff(stuffid, stufftype);
		document.getElementById('MainMatrix').style.display='block';
	});
}

/**
 * this function is used to reload a widgets' content based on its id and type
 * @param string stuffid - the widget id
 * @param string stufftype - the type of the widget
 */
function loadStuff(stuffid, stufftype) {
	document.getElementById('refresh_'+stuffid).innerHTML=document.getElementById('vtbusy_homeinfo').innerHTML;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomeBlock&homestuffid='+stuffid+'&blockstufftype='+stufftype
	}).done(function (response) {
		var responseVal=response;
		document.getElementById('stuffcont_'+stuffid).innerHTML=response;
		vtlib_executeJavascriptInElement(document.getElementById('stuffcont_'+stuffid));
		if (stufftype=='Module') {
			if (document.getElementById('more_'+stuffid).value != null && document.getElementById('more_'+stuffid).value != '') {
				document.getElementById('a_'+stuffid).href = 'index.php?module='+document.getElementById('more_'+stuffid).value+'&action=ListView&viewname='
					+ document.getElementById('cvid_'+stuffid).value;
			}
		}
		if (stufftype=='Default' && typeof(document.getElementById('a_'+stuffid)) != 'undefined') {
			if (document.getElementById('more_'+stuffid).value != '') {
				document.getElementById('a_'+stuffid).style.display = 'block';
				var url = 'index.php?module='+document.getElementById('more_'+stuffid).value+'&action=index';
				if (document.getElementById('search_qry_'+stuffid)!='') {
					url += document.getElementById('search_qry_'+stuffid).value;
				}
				document.getElementById('a_'+stuffid).href = url;
			} else {
				if (document.getElementById('a_'+stuffid)) {
					document.getElementById('a_'+stuffid).display = 'none';
				}
			}
		}
		if (stufftype=='RSS') {
			document.getElementById('a_'+stuffid).href = document.getElementById('more_'+stuffid).value;
		}
		if (stufftype=='DashBoard') {
			document.getElementById('a_'+stuffid).href = 'index.php?module=Dashboard&action=index&type='+document.getElementById('more_'+stuffid).value;
		}
		if (stufftype=='ReportCharts') {
			document.getElementById('a_'+stuffid).href = 'index.php?module=Reports&action=SaveAndRun&record='+document.getElementById('more_'+stuffid).value;
		}
		if (stufftype=='Tag Cloud') {
			TagCanvas.Start('tagcloudCanvas', '', {
				shape: user_tag_showas,
				lock: ((user_tag_showas=='vcylinder' || user_tag_showas=='vring') ? 'y' : 'x'),
				weight: true,
				weightMode: 'both'
			});
		}
		document.getElementById('refresh_'+stuffid).innerHTML='';
	});
}

function loadAllWidgets(widgetInfoList, batchSize) {
	var batchWidgetInfoList = [];
	var widgetInfo = {};
	for (var index =0; index < widgetInfoList.length; ++index) {
		var widgetId = widgetInfoList[index].widgetId;
		var widgetType = widgetInfoList[index].widgetType;
		widgetInfo[widgetId] = widgetType;
		document.getElementById('refresh_'+widgetId).innerHTML=document.getElementById('vtbusy_homeinfo').innerHTML;
		batchWidgetInfoList.push(widgetInfoList[index]);
		if ((index > 0 && (index+1) % batchSize == 0) || index+1 == widgetInfoList.length) {
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Home&action=HomeAjax&file=HomeWidgetBlockList&widgetInfoList=' + JSON.stringify(batchWidgetInfoList)
			}).done(function (response) {
				var responseVal=JSON.parse(response);
				var tagcloudfound = false;
				for (var widgetId in responseVal) {
					if (responseVal.hasOwnProperty(widgetId)) {
						document.getElementById('stuffcont_'+widgetId).innerHTML = responseVal[widgetId];
						document.getElementById('refresh_'+widgetId).innerHTML='';
						var widgetType = widgetInfo[widgetId];
						if (widgetType=='Module' && document.getElementById('more_'+widgetId).value != null &&
								document.getElementById('more_'+widgetId).value != '') {
							document.getElementById('a_'+widgetId).href = 'index.php?module='+
							document.getElementById('more_'+widgetId).value+'&action=ListView&viewname='+
							document.getElementById('cvid_'+widgetId).value;
						} else if (widgetType=='Default' && typeof(document.getElementById('a_'+widgetId))!='undefined' && document.getElementById('a_'+widgetId)!=null) {
							if (typeof document.getElementById('more_'+widgetId) != 'undefined' && document.getElementById('more_'+widgetId).value != '') {
								document.getElementById('a_'+widgetId).style.display = 'block';
								var url = 'index.php?module='+document.getElementById('more_'+widgetId).value+
									'&action=index';
								if (document.getElementById('search_qry_'+widgetId)!='') {
									url += document.getElementById('search_qry_'+widgetId).value;
								}
								document.getElementById('a_'+widgetId).href = url;
							} else {
								document.getElementById('a_'+widgetId).style.display = 'none';
							}
						} else if (widgetType=='RSS') {
							document.getElementById('a_'+widgetId).href = document.getElementById('more_'+widgetId).value;
						} else if (widgetType=='DashBoard') {
							document.getElementById('a_'+widgetId).href = 'index.php?module=Dashboard&action='+
								'index&type='+document.getElementById('more_'+stuffid).value;
						} else if (widgetType=='Tag Cloud') {
							tagcloudfound = true;
						}
					}
				}
				if (tagcloudfound) {
					TagCanvas.Start('tagcloudCanvas', '', {
						shape: user_tag_showas,
						lock: ((user_tag_showas=='vcylinder' || user_tag_showas=='vring') ? 'y' : 'x'),
						weight: true,
						weightMode: 'both'
					});
				}
			});
			batchWidgetInfoList = [];
		}
	}
}

var Application_ListView_MaxColumns = 12;
GlobalVariable_getVariable('Application_ListView_MaxColumns', 12, 'Home', gVTUserID).then(function (response) {
	var obj = JSON.parse(response);
	Application_ListView_MaxColumns = parseInt(obj.Application_ListView_MaxColumns);
}, function (error) {
	Application_ListView_MaxColumns = 12;
});
/**
 * this function validates the form for creating a new widget
 */
function frmValidate() {
	if (document.getElementById('stufftype_id').value=='defaultwidget') {
		var namelist = new Array();
		VtigerJS_DialogBox.showbusy();
		var elem = document.getElementsByName('names');
		for (var i = 0; i < elem.length; i++) {
			if (elem[i].checked) {
				namelist[i] = elem[i].value;
			}
		}

		var values = JSON.stringify(namelist);
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=HomeAjax&module=Home&file=HomeWidgetsSave&values='+encodeURIComponent(values)
		}).done(function (response) {
			document.getElementById('addWidgetsDiv').style.display='none';
			VtigerJS_DialogBox.hidebusy();
			window.location.reload();
		});
	} else {
		if (trim(document.getElementById('stufftitle_id').value)=='') {
			alert(alert_arr.LBL_ENTER_WINDOW_TITLE);
			document.getElementById('stufftitle_id').focus();
			return false;
		}
		if (document.getElementById('stufftype_id').value=='RSS') {
			if (document.getElementById('txtRss_id').value=='') {
				alert(alert_arr.LBL_ENTER_RSS_URL);
				document.getElementById('txtRss_id').focus();
				return false;
			}
		}
		/*if($('stufftype_id').value=="URL"){
			if($('url_id').value==""){
				alert("Please enter URL");
				$('url_id').focus();
				return false;
			}
		}*/
		if (document.getElementById('stufftype_id').value=='Module') {
			var selLen;
			var fieldval=new Array();
			var cnt=0;
			selVal=document.Homestuff.PrimeFld;
			for (k=0; k<selVal.options.length; k++) {
				if (selVal.options[k].selected) {
					fieldval[cnt]=selVal.options[k].value;
					cnt= cnt+1;
				}
			}
			if (cnt>Application_ListView_MaxColumns) {
				alert(alert_arr.LBL_SELECT_ONLY_FIELDS);
				selVal.focus();
				return false;
			} else {
				document.Homestuff.fldname.value=fieldval;
			}
		}
		var stufftype=document.getElementById('stufftype_id').value;
		var stufftitle=document.getElementById('stufftitle_id').value;
		document.getElementById('stufftitle_id').value = '';
		var selFiltername='';
		var fldname='';
		var selmodule='';
		var maxentries='';
		var txtRss='';
		var seldashbd='';
		var seldashtype='';
		var seldeftype='';
		var selreport='';
		var selreportcharttype='';
		//var txtURL = '';

		if (stufftype=='Module') {
			selFiltername =document.Homestuff.selFiltername[document.Homestuff.selFiltername.selectedIndex].value;
			fldname = fieldval;
			selmodule =document.getElementById('selmodule_id').value;
			maxentries =document.getElementById('maxentryid').value;
		} else if (stufftype=='RSS') {
			txtRss=document.getElementById('txtRss_id').value;
			maxentries =document.getElementById('maxentryid').value;
		} else if (stufftype=='DashBoard') {
			seldashbd=document.getElementById('seldashbd_id').value;
			seldashtype=document.getElementById('seldashtype_id').value;
		} else if (stufftype=='Default') {
			seldeftype=document.Homestuff.seldeftype[document.Homestuff.seldeftype.selectedIndex].value;
		} else if (stufftype=='ReportCharts') {
			selreport = document.getElementById('selreportchart_id').value;
			selreportcharttype = document.getElementById('selreportcharttype_id').value;
		}/*else if (stufftype=="URL") {
			txtURL=document.getElementById('url_id').value;
		}*/

		var url='stufftype='+stufftype+'&stufftitle='+stufftitle+'&selmodule='+selmodule+'&maxentries='+maxentries+'&selFiltername='+selFiltername+'&fldname='
			+ encodeURIComponent(fldname)+'&txtRss='+txtRss+'&seldashbd='+seldashbd+'&seldashtype='+seldashtype+'&seldeftype='+seldeftype+'&selreport='+selreport
			+ '&selreportcharttype='+selreportcharttype;//+'&txtURL='+txtURL;
		var stuffarr=new Array();
		VtigerJS_DialogBox.showbusy();

		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Home&action=HomeAjax&file=Homestuff&'+url
		}).done(function (response) {
			var responseVal=response;
			if (!response) {
				alert(alert_arr.LBL_ADD_HOME_WIDGET);
				VtigerJS_DialogBox.hidebusy();
				document.getElementById('stufftitle_id').value='';
				document.getElementById('txtRss_id').value='';
				return false;
			} else {
				hide('addWidgetsDiv');
				VtigerJS_DialogBox.hidebusy();
				document.getElementById('stufftitle_id').value='';
				document.getElementById('txtRss_id').value='';
				eval(response);
			}
		});
	}
}

/**
 * this function is used to hide the default widgets
 * @param string sid - the id of the widget
 */
function HideDefault(sid) {
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&stuffid='+sid+'&act=hide'
	}).done(function (response) {
		var responseVal=response;
		if (response.indexOf('SUCCESS') > -1) {
			var delchild = document.getElementById('stuff_'+sid);
			odeletedChild = document.getElementById('MainMatrix').removeChild(delchild);
			document.getElementById('seqSettings').innerHTML= '<table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small"><tr><td align="center">'+alert_arr.LBL_WIDGET_HIDDEN+'.'+alert_arr.LBL_RESTORE_FROM_PREFERENCES+'.</td></tr></table>';
			document.getElementById('seqSettings').style.display = 'block';
			document.getElementById('seqSettings').style.display = 'none';
			placeAtCenter(document.getElementById('seqSettings'));
			jQuery('#seqSettings').fadeIn();
			setTimeout(hideSeqSettings, 5000);
		} else {
			alert(alert_arr.ERR_HIDING + '.'+ alert_arr.MSG_TRY_AGAIN + '.');
		}
	});
}


/**
 * this function removes the widget dropdown window
 */
function fnRemoveWindow() {
	var tagName = document.getElementById('addWidgetDropDown').style.display= 'none';
}

/**
 * this function displays the widget dropdown window
 */
function fnShowWindow() {
	var tagName = document.getElementById('addWidgetDropDown').style.display= 'block';
}

/**
 * this function is used to postion the widgets on home on page resize
 * @param string targetDiv - the id of the target widget
 * @param string stufftitle - the title of the target widget
 * @param string stufftype - the type of the target widget
 */
function positionDivInAccord(targetDiv, stufftitle, stufftype) {
	var layout=document.getElementById('homeLayout').value;
	var widgetWidth;
	var dashWidth;

	switch (layout) {
	case '2':
		widgetWidth = 49;
		dashWidth = 98.6;
		break;
	case '3':
		widgetWidth = 31;
		dashWidth = 64;
		break;
	case '4':
		widgetWidth = 24;
		dashWidth = 48.6;
		break;
	default:
		widgetWidth = 24;
		dashWidth = 48.6;
		break;
	}
	var mainX = parseInt(document.getElementById('MainMatrix').style.width);
	if (stufftitle != vtdashboard_defaultDashbaordWidgetTitle && stufftype != 'DashBoard' && stufftype != 'ReportCharts' && stufftype != 'Module') {
		var dx = mainX * widgetWidth/ 100;
	} else {
		var dx = mainX * dashWidth / 100;
	}
	document.getElementById(targetDiv).style.width=dx + '%';
	document.getElementById(targetDiv).style.position='relative';
}

/**
 * this function hides the seqSettings div
 */
function hideSeqSettings() {
	jQuery('#seqSettings').fadeOut();
}

/**
 * this function fetches the homepage dashboard
 * @param string stuffid - the id of the dashboard widget
 */
function fetch_homeDB(stuffid) {
	document.getElementById('refresh_'+stuffid).innerHTML=document.getElementById('vtbusy_homeinfo').innerHTML;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Dashboard&action=DashboardAjax&file=HomepageDB'
	}).done(function (response) {
		document.getElementById('stuffcont_'+stuffid).innerHTML=response;
		vtlib_executeJavascriptInElement(document.getElementById('stuffcont_'+stuffid));
		document.getElementById('refresh_'+stuffid).innerHTML='';
		jQuery('#stuffcont_'+stuffid).fadeIn();
	});
}

/**
 * this function initializes the homepage
 */
initHomePage = function () {
	jQuery('#MainMatrix').sortable({
		constraint: false, tag: 'div', overlap: 'Horizontal', handle: '.headerrow', opacity:0.7,
		update: function () {
			matrixarr = jQuery(this).sortable('serialize').split('&');
			matrixseqarr = new Array();
			seqarr = new Array();
			for (x = 0; x < matrixarr.length; x++) {
				matrixseqarr[x] = matrixarr[x].split('=')[1];
			}
			BlockSorting(matrixseqarr);
		}
	});
};

/**
 * this function is used to save the sorting order of elements when they are moved around on the home page
 * @param array matrixseqarr - the array containing the sequence of the widgets
 */
function BlockSorting(matrixseqarr) {
	var sequence = matrixseqarr.join('_');
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&matrixsequence='+sequence
	}).done(function (response) {
		document.getElementById('seqSettings').innerHTML=response;
		document.getElementById('seqSettings').style.display = 'block';
		document.getElementById('seqSettings').style.display = 'none';
		placeAtCenter(document.getElementById('seqSettings'));
		jQuery('#seqSettings').fadeIn();
		setTimeout(hideSeqSettings, 3000);
	});
}

/**
 * this function checks if the current browser is IE or not
 */
function isIE() {
	return navigator.userAgent.indexOf('MSIE') !=-1;
}

/**
 * this function adds a notebook widget to the homepage
 */
function addNotebookWidget() {
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&matrixsequence='+sequence
	}).done(function (response) {
		document.getElementById('seqSettings').innerHTML=response;
		document.getElementById('seqSettings').style.display = 'block';
		document.getElementById('seqSettings').style.display = 'none';
		placeAtCenter(document.getElementById('seqSettings'));
		jQuery('#seqSettings').fadeIn();
		setTimeout(hideSeqSettings, 3000);
	});
	loadAddedDiv(stuffid, stufftype);
}

/**
 * this function takes a widget id and adds scrolling property to it
 */
function addScrollBar(id) {
	document.getElementById('stuff_'+id).style['overflowX'] = 'scroll';
	document.getElementById('stuff_'+id).style['overflowY'] = 'scroll';
}

/**
 * this function will display the node passed to it in the center of the screen
 */
function showOptions(id) {
	var node = document.getElementById(id);
	node.style.display='block';
	placeAtCenter(node);
}

/**
 * this function will hide the node passed to it
 */
function hideOptions(id) {
	jQuery('#'+id).fadeOut();
}

/**
 * this function will be used to save the layout option
 */
function saveLayout() {
	document.getElementById('status').style.display='none';
	hideOptions('changeLayoutDiv');
	var sel = document.getElementById('layoutSelect');
	var layout = sel.options[sel.selectedIndex].value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&layout='+layout
	}).done(function (response) {
		var responseVal=response;
		window.location.href = window.location.href;
	});
}
function saveEditReportCharts(dashRowId) {
	document.getElementById('refresh_'+dashRowId).innerHTML=document.getElementById('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+dashRowId);
	var reportVal='';
	var iter=0;
	for (iter=0; iter<3; iter++) {
		if (document.getElementById('reportradio_'+dashRowId+'_'+iter).checked) {
			reportVal=document.getElementById('reportradio_'+dashRowId+'_'+iter).value;
		}
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=HomestuffAjax&reportVal='+reportVal+'&stuffid='+dashRowId
	}).done(function (response) {
		var responseVal=response;
		eval(response);
		document.getElementById('refresh_'+dashRowId).innerHTML='';
	});
}

function changeGraphType(chartid, type) {
	let ctype = 'pie';
	switch (trim(type)) {
	case 'piechart':
		ctype = 'pie';
		break;
	case 'verticalbarchart':
		ctype = 'bar';
		break;
	case 'horizontalbarchart':
		ctype = 'horizontalBar';
		break;
	}
	let chart_object = window['schart'+chartid];
	chart_object.destroy();
	window['doChart'+chartid](ctype);
}

function getRandomColor() {
	return randomColor({
		luminosity: 'dark',
		hue: 'random'
	});
}

function firsttime_login_welcome(popuplayer, popupcontent) {
	popuplayer.style.zIndex = parseInt((+new Date())/1000)+5; // To ensure z-Index is higher than the popup block
	popuplayer.style.display = 'block';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Home&action=HomeAjax&file=welcome'
	}).done(function (response) {
		if (response=='') {
			popuplayer.style.display = 'none';
		} else {
			popupcontent.innerHTML = response;
			vtlib_executeJavascriptInElement(popupcontent);
		}
	});
}
