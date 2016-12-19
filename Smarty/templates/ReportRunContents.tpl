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

<table style="border: 1px solid rgb(0, 0, 0);" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tbody><tr>
	<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>
	<td style="padding: 5px;" valign="top">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
		<td align="left" width="75%">
		<span class="genHeaderGray">{$REPORTNAME|@getTranslatedString:'Reports'}</span><br>
		</td>
		<td align="right" width="25%">
		<span class="genHeaderGray">{$APP.LBL_TOTAL} : <span id='_reportrun_total'></span>  {$APP.LBL_RECORDS}</span>
		</td>
		</tr>
		<tr><td id="report_info" align="left" colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2">
		{if empty($ERROR_MSG)}
			<div class="rptContainer">
				<datatable id="rptDatatable" url="index.php?module=Reports&action=ReportsAjax&file=getJSON&record={$REPORTID}" template="report_row_template">
					<footer>
						<pagination limit="12" outer></pagination>
						<stats></stats>
					</footer>
					<table class="rptTable">
						<tr>
						{foreach item=dtheader from=$TABLEHEADERS}
							<th class="rptCellLabel">{$dtheader}</th>
						{/foreach}
						</tr>
					</table>
				</datatable>
			</div>
			<table id="report_row_template" hidden>
				<tr>
					{foreach item=dtheader from=$JSONHEADERS}
						{if $dtheader eq 'reportrowaction'}
						<td class="rptData"><a av="href:reportrowaction">{'LBL_VIEW_DETAILS'|@getTranslatedString:'Reports'}</a></td>
						{else}
						<td v="{$dtheader}" class="rptData"></td>
						{/if}
					{/foreach}
				</tr>
			</table>
		{else}
			{$ERROR_MSG}
		{/if}
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">
			{$REPORTTOTHTML}
		</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		</tbody>
	</table>
	</td>
	<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>
	</tr>
	</tbody>
</table>
<br><br>
{if $SHOWCHARTS eq 'true'}
<div name="viewcharts" id="viewcharts">
<table style="border: 1px solid rgb(0, 0, 0);" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
		<tr>
			<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>
			<td>
				<table border=0 cellspacing=1 cellpadding=0 width="100%" class="lvtBg">
					<tr>
						<td><canvas id="rptpiechart" style="width:400px;height:400px;margin:auto;padding:10px;"></canvas></td>
						<td><canvas id="rptbarchart" style="width:400px;height:400px;margin:auto;padding:10px;"></canvas></td>
					</tr>
				</table>
			</td>
			<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
{literal}
function getRandomColor() {
	return randomColor({
		luminosity: 'dark',
		hue: 'random'
	});
}
let chartDataObject = {
	labels: [{/literal}{foreach item=LABEL name=chartlabels from=$CHARTDATA.xaxisData}"{$LABEL}"{if not $smarty.foreach.chartlabels.last},{/if}{/foreach}{literal}],
	datasets: [{
		data: [{/literal}{foreach item=CVALUE name=chartvalues from=$CHARTDATA.yaxisData}"{$CVALUE}"{if not $smarty.foreach.chartvalues.last},{/if}{/foreach}{literal}],
		backgroundColor: [{/literal}{foreach item=CVALUE name=chartvalues from=$CHARTDATA.yaxisData}getRandomColor(){if not $smarty.foreach.chartvalues.last},{/if}{/foreach}{literal}]
	}]
};
let rptpiechart = document.getElementById('rptpiechart');
let rptbarchart = document.getElementById('rptbarchart');
let pchart = new Chart(rptpiechart,{
	type: 'pie',
	data: chartDataObject,
	options: {
		responsive: false,
		legend: {
			position: "right",
			labels: {
				fontSize: 11,
				boxWidth: 18
			}
		}
	}
});
let barchar = new Chart(rptbarchart,{
	type: 'horizontalBar',
	data: chartDataObject,
	options: {
		responsive: false,
		legend: {
			display: false,
			labels: {
				fontSize: 11
			}
		}
	}
});
rptpiechart.addEventListener('click',pieclick);
function pieclick(evt) {
	let activePoint = pchart.getElementAtEvent(evt);
	let clickzone = {
		{/literal}{foreach item=CLICKVALUE key=CLICKINDEX name=clickvalues from=$CHARTDATA.targetLink}{$CLICKINDEX}:"{$CLICKVALUE}"{if not $smarty.foreach.clickvalues.last},{/if}{/foreach}{literal}
	};
	let a = document.createElement("a");
	a.target = "_blank";
	a.href = clickzone[activePoint[0]._index];
	document.body.appendChild(a);
	a.click();
}
rptbarchart.addEventListener('click',barclick);
function barclick(evt) {
	let activePoint = barchar.getElementAtEvent(evt);
	let clickzone = {
		{/literal}{foreach item=CLICKVALUE key=CLICKINDEX name=clickvalues from=$CHARTDATA.targetLink}{$CLICKINDEX}:"{$CLICKVALUE}"{if not $smarty.foreach.clickvalues.last},{/if}{/foreach}{literal}
	};
	let a = document.createElement("a");
	a.target = "_blank";
	a.href = clickzone[activePoint[0]._index];
	document.body.appendChild(a);
	a.click();
}
{/literal}
</script>
</div>
{/if}
<table align="center" border="0" cellpadding="5" cellspacing="0" width="100%" class="mailSubHeader">
	<tbody><tr>
	{if $SHOWCHARTS eq 'true'}
		<td align="right" width="100%">
			<a href="javascript:void(0);" onclick="showAddChartPopup();"><img src="{'dashboard_60.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{'LBL_ADD_CHARTS'|@getTranslatedString:$MODULE}" title="{'LBL_ADD_CHARTS'|@getTranslatedString:$MODULE}" style="background:#E85313;border:0;width:24px;" id="addChartstodashboard" name="addChartstodashboard"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="window.location.href = '#rpttop'"><img src="{'jump_to_top_60.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{'LBL_JUMP_To'|@getTranslatedString:$MODULE}" title="{'LBL_JUMP_To'|@getTranslatedString:$MODULE}" border="0" width="24px"></a>
		</td>
	{/if}
	</tr>
	</tbody>
</table>

<div id="addcharttoHomepage"  class="layerPopup" style="z-index:2000; display:none; width: 400px;">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
    <tr>
        <td align="left" id="divHeader" class="layerPopupHeading" width="80%"><b>{'Add ReportCharts'|@getTranslatedString:$MODULE}</b></td>
        <td align="right">
                <a onclick="fnhide('addcharttoHomepage');" href="javascript:;">
                <img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"></a>
        </td>
    </tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
<tr>
    <td class="small">
        <table border="0" cellspacing="0" cellpadding="3" width="100%" align="center" bgcolor="white">
            <tr>
                <td class="dvtCellLabel" width="110" align="right">{'LBL_HOME_WINDOW_TITLE'|@getTranslatedString:$MODULE}<font color='red'>*</font></td>
                <td class="dvtCellInfo" colspan="2" width="300" align="left"><input type="text" name="windowtitle" id="windowtitle_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:57%"></td>
            </tr>
            <tr>
                <td class="dvtCellLabel"  width="110" align="right">{'LBL_HOME_REPORT_NAME'|@getTranslatedString:$MODULE}</td>
                <td id="selReportName" class="dvtCellInfo" colspan="2" width="300" align="left">{$REPORTNAME}</td>
            </tr>
            <tr>
                <td class="dvtCellLabel" width="110" align="right">{'LBL_HOME_REPORT_TYPE'|@getTranslatedString:$MODULE}</td>
                <td id="selReportType" class="dvtCellInfo" width="300" colspan="2" align="left">
                        <select name="selreporttype" id="selreportcharttype_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:60%">
                                <option value="horizontalbarchart">{'LBL_HOME_HORIZONTAL_BARCHART'|@getTranslatedString:$MODULE}</option>
                                <option value="verticalbarchart">{'LBL_HOME_VERTICAL_BARCHART'|@getTranslatedString:$MODULE}</option>
                                <option value="piechart">{'LBL_HOME_PIE_CHART'|@getTranslatedString:$MODULE}</option>
                        </select>
                </td>
            </tr>
        </table>
      </td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align="center">
    <tr>
        <td align="right">
            <input type="button" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " id="savebtn" class="crmbutton small save" onclick="addChartsToHomepage({$REPORTID})"></td>
            <td align="left"><input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" onclick="fnhide('addcharttoHomepage');">
        </td>
    </tr>
 </table>
</div>

<div name="widgetmessage" id="widgetsuccess" style="display:none;background-color:#E0ECFF;width:150px;top:600px;right:481px;position:absolute">
    <table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small">
        <tr>
            <td align="center">
               {'LBL_WIDGET_ADDED'|@getTranslatedString:$MODULE}
            </td>
        </tr>
    </table>
</div>
