{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<br>
<script type="text/javascript">
	var rel_fields = {$REL_FIELDS};
</script>
<script type="text/javascript" src="modules/Reports/Reports.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="include/calculator/calc.js"></script>
<script src="include/chart.js/Chart.min.js"></script>
<script src="include/chart.js/randomColor.js"></script>
<a name="rpttop"></a>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
    <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<table class="small reportGenHdr mailClient mailClientBg" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<form name="NewReport" action="index.php" method="POST" onsubmit="VtigerJS_DialogBox.block();">
    <input type="hidden" name="booleanoperator" value="5"/>
    <input type="hidden" name="record" value="{$REPORTID}"/>
    <input type="hidden" name="reload" value=""/>    
    <input type="hidden" name="module" value="Reports"/>
    <input type="hidden" name="action" value="SaveAndRun"/>
    <input type="hidden" name="dlgType" value="saveAs"/>
    <input type="hidden" name="reportName"/>
    <input type="hidden" name="folderid" value="{$FOLDERID}"/>
    <input type="hidden" name="reportDesc"/>
    <input type="hidden" name="folder"/>

	<tbody>
	<tr>
	<td style="padding: 10px; text-align: left;" width="70%">
		<span class="moduleName">{$REPORTNAME|@getTranslatedString:$MODULE}</span>&nbsp;&nbsp;
		{if $IS_EDITABLE eq 'true'}
		<input type="button" name="custReport" value="{$MOD.LBL_CUSTOMIZE_REPORT}" class="crmButton small edit" onClick="editReport('{$REPORTID}');">
		{/if}
		<br>
		<a href="index.php?module=Reports&action=ListView" class="reportMnu" style="border-bottom: 0px solid rgb(0, 0, 0);">&lt;{$MOD.LBL_BACK_TO_REPORTS}</a>
	</td>
	<td style="border-left: 2px dotted rgb(109, 109, 109); padding: 10px;" width="30%">
		<b>{$MOD.LBL_SELECT_ANOTHER_REPORT} : </b><br>
		<select name="another_report" class="detailedViewTextBox" onChange="selectReport()">
		{foreach key=report_in_fld_id item=report_in_fld_name from=$REPINFOLDER}
			<option value={$report_in_fld_id} {if $report_in_fld_id eq $REPORTID}selected{/if}>{$report_in_fld_name|@getTranslatedString:$MODULE}</option>
		{/foreach}
		</select>&nbsp;&nbsp;
	</td>
	</tr>
	</tbody>
</table>

<!-- Generate Report UI Filter -->
<table class="small reportGenerateTable" align="center" cellpadding="5" cellspacing="0" width="95%" border=0>
	<tr>
		<td align="left" style="padding:5px" width="80%">
			{include file='AdvanceFilter.tpl' SOURCE='reports1'}
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" class="small create" onclick="generateReport({$REPORTID});" value="{$MOD.LBL_GENERATE_NOW}" title="{$MOD.LBL_GENERATE_NOW}" />
			&nbsp;
			<input type="button" class="small edit" onclick="saveReportAdvFilter({$REPORTID});" value="     {$MOD.LBL_SAVE_REPORT}     " title="{$MOD.LBL_SAVE_REPORT}" />
			&nbsp;
			<input type="button" class="small edit" onclick="SaveAsReport({$REPORTID});" value="     {$APP.LBL_SAVE_AS}     " title="{$APP.LBL_SAVE_AS}" />
		</td>
	</tr>
</table>

<table class="small reportGenHdr mailClient mailClientBg" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="right" valign="bottom" style="padding:5px">
			<a href="javascript:void(0);" onclick="location.href='index.php?module=Reports&action=SaveAndRun&record={$REPORTID}&folderid={$FOLDERID}'"><img src="{'revert.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{'LBL_RELOAD_REPORT'|@getTranslatedString:$MODULE}" title="{'LBL_RELOAD_REPORT'|@getTranslatedString:$MODULE}" border="0"></a>
			&nbsp;
			{if $SHOWCHARTS eq 'true'}
				<a href="javascript:void(0);" onclick="window.location.href = '#viewcharts'"><img src="{'chart_60.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{'LBL_VIEW_CHARTS'|@getTranslatedString:$MODULE}" title="{'LBL_VIEW_CHARTS'|@getTranslatedString:$MODULE}" border="0" width="24px"></a>
				&nbsp;
			{/if}
			{if TRUE || $CHECK.Export eq 'yes'} {*<!-- temporarily deactivate this check: we need to add a ReportExport action on each module's preferences -->*}
			<a href="javascript:void(0);" onclick="saveReportAs(this,'duplicateReportLayout');"><img src="{'saveas.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_SAVE_REPORT_AS}" title="{$MOD.LBL_SAVE_REPORT_AS}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="goToURL(CrearEnlace('CreatePDF',{$REPORTID}));"><img src="{'pdf-file.jpg'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTPDF_BUTTON}" title="{$MOD.LBL_EXPORTPDF_BUTTON}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="goToURL(CrearEnlace('CreateXL',{$REPORTID}));"><img src="{'xls-file.jpg'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTXL_BUTTON}" title="{$MOD.LBL_EXPORTXL_BUTTON}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="gotourl(CrearEnlace('CreateCSV',{$REPORTID}));"><img src="{'csv.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTCSV}" title="{$MOD.LBL_EXPORTCSV}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="goToPrintReport({$REPORTID});"><img src="{'fileprint.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_PRINT_REPORT}" title="{$MOD.LBL_PRINT_REPORT}" border="0"></a>
			{/if}
		</td>
	</tr>
</table>

<div style="display: block;" id="Generate" align="center">
	{include file="ReportRunContents.tpl"}
</div>
<br>

</td>
<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
</tr>
</table>

<!-- Save Report As.. UI -->
<div id="duplicateReportLayout" style="display:none;width:350px;" class="layerPopup">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerHeadingULine">
		<tr>
			<td class="genHeaderSmall" nowrap align="left" width="30%">{$MOD.LBL_SAVE_REPORT_AS}</td>
			<td align="right"><a href="javascript:;" onClick="fninvsh('duplicateReportLayout');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
		<tr>
			<td class="small">
				<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
					<tr>
						<td width="30%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REPORT_NAME} : </b></td>
						<td width="70%" align="left" style="padding-left:5px;"><input type="text" name="newreportname" id="newreportname" class="txtBox" value=""></td>
					</tr>
					<tr>
						<td width="30%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REP_FOLDER} : </b></td>
						<td width="70%" align="left" style="padding-left:5px;">
							<select name="reportfolder" id="reportfolder" class="txtBox">
							{foreach item=folder from=$REP_FOLDERS}
							{if $FOLDERID eq $folder.id}
								<option value="{$folder.id}" selected>{$folder.name}</option>
							{else}
								<option value="{$folder.id}">{$folder.name}</option>
							{/if}
							{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" style="padding-right:5px;" valign="top"><b>{$MOD.LBL_DESCRIPTION}: </b></td>
						<td align="left" style="padding-left:5px;"><textarea name="newreportdescription"  id="newreportdescription" class="txtBox" rows="5">{$REPORTDESC}</textarea></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
		<tr>
			<td class="small" align="center">
			<input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save" onClick="duplicateReport({$REPORTID});" type="button">&nbsp;&nbsp;
			<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="fninvsh('duplicateReportLayout');" type="button">
			</td>
		</tr>
	</table>
</div>
{literal}
<link rel="stylesheet" href="include/bunnyjs/css/svg-icons.css">
<script src="include/bunnyjs/utils-dom.min.js"></script>
<script src="include/bunnyjs/ajax.min.js"></script>
<script src="include/bunnyjs/template.min.js"></script>
<script src="include/bunnyjs/pagination.min.js"></script>
<script src="include/bunnyjs/url.min.js"></script>
<script src="include/bunnyjs/utils-svg.min.js"></script>
<script src="include/bunnyjs/spinner.min.js"></script>
<script src="include/bunnyjs/datatable.min.js"></script>
<script src="include/bunnyjs/datatable.icons.min.js"></script>
<script src="include/bunnyjs/element.min.js"></script>
<script src="include/bunnyjs/datatable.scrolltop.min.js"></script>
<script type="text/javascript">
Template.define('report_row_template', {});
{/literal}
Pagination._config.langFirst = "{$APP.LNK_LIST_START}";
Pagination._config.langLast = "{$APP.LNK_LIST_END}";
Pagination._config.langPrevious = "< {$APP.LNK_LIST_PREVIOUS}";
Pagination._config.langNext = "{$APP.LNK_LIST_NEXT} >";
{literal}
Pagination._config.langStats = "{from}-{to} {/literal}{$APP.LBL_LIST_OF}{literal} {total} ({/literal}{$APP.Page}{literal} {currentPage} {/literal}{$APP.LBL_LIST_OF}{literal} {lastPage})";
DataTableConfig.loadingImg = 'themes/images/loading.svg';
DataTable.onRedraw(document.getElementsByTagName('datatable')[0], (data) => {
	if(document.getElementById('_reportrun_total')) document.getElementById('_reportrun_total').innerHTML=data.total;
});

function CrearEnlace(tipo,id){
	if(!checkAdvancedFilter()) return false;
	var advft_criteria = encodeURIComponent(document.getElementById('advft_criteria').value);
	var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;
	return "index.php?module=Reports&action=ReportsAjax&file="+tipo+"&record="+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups;
}

function goToURL(url) {
	document.location.href = url;
}

function saveReportAs(oLoc,divid) {
	document.getElementById('newreportname').value = '';
	document.getElementById('newreportdescription').value = '';
	fnvshobj(oLoc,divid);
}

function duplicateReport(id) {

	VtigerJS_DialogBox.block();
	
	var newreportname = document.getElementById('newreportname').value;
	if (trim(newreportname) == "") {
		VtigerJS_DialogBox.unblock();
		alert(alert_arr.MISSING_REPORT_NAME);
		return false;
	} else {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=reportCheck&reportName='+encodeURIComponent(newreportname)
		}).done(function (response) {
					if(response != 0) {
						VtigerJS_DialogBox.unblock();
						alert(alert_arr.REPORT_NAME_EXISTS);
						return false;
					} else {
						createDuplicateReport(id);
					}
				}
		);
	}
}

function createDuplicateReport(id) {
	var newreportname = document.getElementById('newreportname').value;
	var newreportdescription = document.getElementById('newreportdescription').value;
	var newreportfolder = document.getElementById('reportfolder').value;

	if(!checkAdvancedFilter()) return false;

	var advft_criteria = document.getElementById('advft_criteria').value;
	var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;

	jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ReportsAjax&file=DuplicateReport&mode=ajax&module=Reports&record='+id+'&newreportname='+encodeURIComponent(newreportname)+'&newreportdescription='+encodeURIComponent(newreportdescription)+'&newreportfolder='+newreportfolder+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups
			}).done(function (response) {
							var responseArray = JSON.parse(response);
							if(trim(responseArray['errormessage']) != '') {
								VtigerJS_DialogBox.unblock();
								alert(resonseArray['errormessage']);
							}
							var reportid = responseArray['reportid'];
							var folderid = responseArray['folderid'];
							var url ='index.php?action=SaveAndRun&module=Reports&record='+reportid+'&folderid='+folderid;
							goToURL(url);
					}
			);
}

function generateReport(id) {

	if(!checkAdvancedFilter()) return false;
	
	VtigerJS_DialogBox.block();
	
	var advft_criteria = document.getElementById('advft_criteria').value;
	var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;

	jQuery.ajax({
			method: 'POST',
			data : {'advft_criteria': advft_criteria, 'advft_criteria_groups': advft_criteria_groups},
			url: 'index.php?action=ReportsAjax&file=SaveAndRun&mode=ajax&module=Reports&submode=generateReport&record='+id,
	}).done(function (response) {
							getObj('Generate').innerHTML = response;
							vtlib_executeJavascriptInElement(getObj('Generate'));
							Template.define('report_row_template', {});
							DataTable.initAll();
							DataTable.onRedraw(document.getElementsByTagName('datatable')[0], (data) => {
								if(document.getElementById('_reportrun_total')) document.getElementById('_reportrun_total').innerHTML=data.total;
							});
							setTimeout(function(){
								DataTable.changePage(document.getElementById('rptDatatable'),1);
							}, 500);
							VtigerJS_DialogBox.unblock();
					}
		);
}

function saveReportAdvFilter(id) {

	if(!checkAdvancedFilter()) return false;
	
	VtigerJS_DialogBox.block();
	
	var advft_criteria = document.getElementById('advft_criteria').value;
	var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;

	jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ReportsAjax&file=SaveAndRun&mode=ajax&module=Reports&submode=saveCriteria&record='+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups
	}).done(function (response) {
							getObj('Generate').innerHTML = response;
							vtlib_executeJavascriptInElement(getObj('Generate'));
							Template.define('report_row_template', {});
							DataTable.initAll();
							DataTable.onRedraw(document.getElementsByTagName('datatable')[0], (data) => {
								if(document.getElementById('_reportrun_total')) document.getElementById('_reportrun_total').innerHTML=data.total;
							});
							VtigerJS_DialogBox.unblock();
					}
			);
}

function selectReport() {
	var id = document.NewReport.another_report.options  [document.NewReport.another_report.selectedIndex].value;
	var folderid = getObj('folderid').value;
	url ='index.php?action=SaveAndRun&module=Reports&record='+id+'&folderid='+folderid;
	goToURL(url);
}

function SaveAsReport(id) {
	if(!checkAdvancedFilter()) return false;
	var reportname = prompt(alert_arr.LBL_REPORT_NAME);
	if (reportname !== null  && reportname !=='' && reportname!== undefined) {
		document.getElementById("newreportname").value = reportname;
		VtigerJS_DialogBox.block();
		var advft_criteria = document.getElementById('advft_criteria').value;
		var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;
		jQuery.ajax({
				method: 'POST',
				url: 'index.php?action=ReportsAjax&file=Save&mode=ajax&module=Reports&record='+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups+'&saveashidden=saveas&newreportname='+reportname
		}).done(function (response) {
					if(response.indexOf('Error')!=-1 ||response.indexOf('error')!=-1 )
					getObj('Generate').innerHTML = response;
					VtigerJS_DialogBox.unblock();
				}
		);
	} else
		alert(alert_arr.LBL_REPORT_NAME_ERROR);
}
{/literal}

function goToPrintReport(id) {ldelim}
	if(!checkAdvancedFilter()) return false;
	var advft_criteria = document.getElementById('advft_criteria').value;
	var advft_criteria_groups = document.getElementById('advft_criteria_groups').value;
	window.open("index.php?module=Reports&action=ReportsAjax&file=PrintReport&record="+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups,"{$MOD.LBL_PRINT_REPORT}","width=800,height=650,resizable=1,scrollbars=1,left=100");
{rdelim}
</script>
