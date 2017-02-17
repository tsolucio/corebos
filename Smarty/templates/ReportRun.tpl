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
			<a href="javascript:void(0);" onclick="gotourl(CrearEnlace('CreatePDF',{$REPORTID}));"><img src="{'pdf-file.jpg'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTPDF_BUTTON}" title="{$MOD.LBL_EXPORTPDF_BUTTON}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="gotourl(CrearEnlace('CreateXL',{$REPORTID}));"><img src="{'xls-file.jpg'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTXL_BUTTON}" title="{$MOD.LBL_EXPORTXL_BUTTON}" border="0"></a>
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
						<td align="left" style="padding-left:5px;"><textarea name="newreportdescription" id="newreportdescription" class="txtBox" rows="5">{if isset($REPORTDESC)}{$REPORTDESC}{/if}</textarea></td>
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
var i18nLBL_PRINT_REPORT = "{$MOD.LBL_PRINT_REPORT}";
Pagination._config.langFirst = "{$APP.LNK_LIST_START}";
Pagination._config.langLast = "{$APP.LNK_LIST_END}";
Pagination._config.langPrevious = "< {$APP.LNK_LIST_PREVIOUS}";
Pagination._config.langNext = "{$APP.LNK_LIST_NEXT} >";
{literal}
Template.define('report_row_template', {});
Pagination._config.langStats = "{from}-{to} {/literal}{$APP.LBL_LIST_OF}{literal} {total} ({/literal}{$APP.Page}{literal} {currentPage} {/literal}{$APP.LBL_LIST_OF}{literal} {lastPage})";
DataTableConfig.loadingImg = 'themes/images/loading.svg';
DataTable.onRedraw(document.getElementsByTagName('datatable')[0], (data) => {
	if(document.getElementById('_reportrun_total')) document.getElementById('_reportrun_total').innerHTML=data.total;
});
{/literal}
</script>
