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

{*<!-- buttons for the home page -->*}
<table border=0 cellspacing=0 cellpadding=5 width="50%" class="small homePageButtons">
<tr style="cursor: pointer;">
	<td style="padding-left:10px;padding-right:50px" width=10% class="moduleName" nowrap>
		<a class="hdrLink" href="index.php?action=index&module={$MODULE}">
			{$APP.$MODULE}
		</a>
	</td>
	<td class="sep1">
		&nbsp;
	</td>

	<td align='left'>
		<img width="27" height="27" onClick='fnAddWindow(this,"addWidgetDropDown");' onMouseOut='fnRemoveWindow();' src="{'btnL3Add.gif'|@vtiger_imageurl:$THEME}" title="{$MOD.LBL_HOME_ADDWINDOW}" alt"{$MOD.LBL_HOME_ADDWINDOW}" style="cursor:pointer;border:0;max-width:initial;">
	</td>

{if $CHECK.Calendar eq 'yes' && $CALENDAR_ACTIVE eq 'yes' && $CALENDAR_DISPLAY eq 'true'}
	<td id="LB_CalButton" class="LB_Button" style="padding-right:0px;padding-left:10px;">
		<a href="javascript:;" onclick="fnvshobj(this,'miniCal');getITSMiniCal('');">
		<img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" style="border:0;max-width:initial;">
		</a>
	</td>
{/if}
{if $WORLD_CLOCK_DISPLAY eq 'true' }
	<td id="LB_ClockButton" class="LB_Button" style="padding-right:0px">
		<a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" style="border:0;max-width:initial;" onClick="fnvshobj(this,'wclock');"></a>
	</td>
{/if}
	<td align='left'>
		<img width="27" height="27" onClick='showOptions("changeLayoutDiv");' src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" style="border:0;max-width:initial;" title="{$MOD.LBL_HOME_LAYOUT}" alt"{$MOD.LBL_HOME_LAYOUT}" style="cursor:pointer;">
	</td>

	<td width="100%" align="center">
		<span id="vtbusy_info" style="display:none;" valign="bottom">
		<div role="status" class="slds-spinner slds-spinner_brand slds-spinner_x-small" style="position:relative; top:6px;">
			<div class="slds-spinner__dot-a"></div>
			<div class="slds-spinner__dot-b"></div>
		</div>
		</span>
		{assign var=ANNOUNCEMENT value=get_announcements()}
		{if $ANNOUNCEMENT}
			<div width="100%">
				<div align=center>
					<marquee id="rss" style="margin-left:12px;width:90%;" direction="left" scrolldelay="10" scrollamount="3" behavior="scroll" class="marStyle slds-float_left" onMouseOver="javascript:stop();" onMouseOut="javascript:start();">&nbsp;{$ANNOUNCEMENT}</marquee>
					<div align="right" style="padding-right:38px;width:8%;" class="slds-float_right"><img src="{'Announce.PNG'|@vtiger_imageurl:$THEME}"></div>
				</div>
			</div>
		{/if}
	</td>
</tr>
</table>

{*<!--button related stuff -->*}
<form name="Homestuff" id="formStuff" style="display: inline;" method="post">
	<input type="hidden" name="action" value="homestuff">
	<input type="hidden" name="module" value="Home">
	<div id='addWidgetDropDown' style='background-color: #fff; display:none;' onmouseover='fnShowWindow()' onmouseout='fnRemoveWindow()'>
		<ul class="widgetDropDownList">
		<li>
			<a href='javascript:;' class='drop_down' id="addmodule">
				{$MOD.LBL_HOME_MODULE}
			</a>
		</li>
		<li>
			<a href='javascript:chooseType("CustomWidget");fnRemoveWindow();setFilter(document.getElementById("selmodule_id"));' class='drop_down' id="addcustomwidget">
				{$MOD.LBL_HOME_CUSTOM_WIDGET}
			</a>
		</li>
{if $ALLOW_RSS eq "yes"}
		<li>
			<a href='javascript:;' class='drop_down' id="addrss">
				{$MOD.LBL_HOME_RSS}
			</a>
		</li>
{/if}	
{if $ALLOW_DASH eq "yes"}
		<li>
			<a href='javascript:;' class='drop_down' id="adddash">
				{$MOD.LBL_HOME_DASHBOARD}
			</a>
		</li>
{/if}
		<li>
			<a href='javascript:;' class='drop_down' id="addNotebook">
				{$MOD.LBL_NOTEBOOK}
			</a>
		</li>

{if $ALLOW_REPORT eq "yes"}
		<li>
			<a href='javascript:;' class='drop_down' id="addReportCharts">
				{'LBL_REPORTCHARTS'|@getTranslatedString:$MODULE}
			</a>
		</li>
{/if}
		<li>
			<a href='javascript:;' class='drop_down' id="defaultwidget">
				{'LBL_DEFAULT_WIDGET'|@getTranslatedString:$MODULE}
			</a>
		</li>
		{*<!-- this has been commented as some websites are opening up in full page (they have a target="_top")
		<li>
			<a href='javascript:;' class='drop_down' id="addURL">
				{$MOD.LBL_URL}
			</a>
		</li>
		-->*}
	</div>

	{*<!-- the following div is used to display the contents for the add widget window -->*}
	<div id="addWidgetsDiv" class="layerPopup" style="z-index:2000; display:none; width:400px;">
		<input type="hidden" name="stufftype" id="stufftype_id">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
		<tr>
			<td class="layerPopupHeading" align="left" id="divHeader"></td>
			<td align="right"><a href="javascript:;" onclick="fnhide('addWidgetsDiv');document.getElementById('stufftitle_id').value='';">
				<img src="{'close.gif'|@vtiger_imageurl:$THEME}" style="border:0;max-width:initial;" align="absmiddle" /></a>
			</td>
		</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
		<tr>
			<td class=small >
			{*<!-- popup specific content fill in starts -->*}
			<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center" bgcolor="white">
			<tr id="StuffTitleId" style="display:block;">
				<td class="dvtCellLabel" width="110" align="right">
					{$MOD.LBL_HOME_STUFFTITLE}
					<font color='red'>*</font>
				</td>
				<td class="dvtCellInfo" colspan="2" width="300">
					<input type="text" name="stufftitle" id="stufftitle_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:57%">
				</td>
			</tr>
			{*<!--
			<tr id="homeURLField" style="display:block;">
				<td class="dvtCellLabel" width="110" align="right">
					{$MOD.LBL_URL}
					<font color='red'>*</font>
				</td>
				<td class="dvtCellInfo" colspan="2" width="300">
					<input type="text" name="url" id="url_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:57%">
				</td>
			</tr>
			-->*}
			<tr id="showrow">
				<td class="dvtCellLabel" width="110" align="right">{$MOD.LBL_HOME_SHOW}</td>
				<td class="dvtCellInfo" width="300" colspan="2">
					<select name="maxentries" id="maxentryid" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:60%">
						{section name=iter start=1 loop=13 step=1}
						<option value="{$smarty.section.iter.index}">{$smarty.section.iter.index}</option>
						{/section}
					</select>&nbsp;&nbsp;{$MOD.LBL_HOME_ITEMS}
				</td>
			</tr>
			<tr id="moduleNameRow" style="display:block">
				<td class="dvtCellLabel" width="110" align="right">{$MOD.LBL_HOME_MODULE}</td>
				<td width="300" class="dvtCellInfo" colspan="2">
					<select name="selmodule" id="selmodule_id" onchange="setFilter(this)" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:60%">
						{foreach item=arr from=$MODULE_NAME}
							{assign var="MODULE_LABEL" value=$arr.1|getTranslatedString:$arr.1}
							<option value="{$arr.1}">{$MODULE_LABEL}</option>
						{/foreach}
					</select>
					<input type="hidden" name="fldname">
				</td>
			</tr>
			<tr id="moduleFilters" style="display:block">
				<td class="dvtCellLabel" id="filterby" align="right" width="110" >{$MOD.LBL_HOME_FILTERBY}</td>
				<td class="dvtCellLabel" id="filterbyim" align="right" width="110" ><img width="27" height="27" alt"{$MOD.LBL_HOME_ADDWINDOW}" onClick='filterValidate();' onMouseOut='fnRemoveWindow();' src="{'btnL3Add.gif'|@vtiger_imageurl:$THEME}" border="0" title="{$MOD.LBL_HOME_ADDWINDOW}" style="cursor:pointer;">
					&nbsp;{$MOD.LBL_HOME_FILTERBY}
				</td>
				<td id="selModFilter_id" width="300" colspan="2" class="dvtCellInfo"> </td>
			</tr>
			<tr id="moduleLabelsRow" style="display:block">
				<td class="dvtCellLabel" align="right" id="aggr" width="110">{$MOD.LBL_HOME_AGGREGATE}</td>
				<td id="selModAggregate_id" width="300" colspan="2" class="dvtCellInfo">
					<select class="detailedViewTextBox" id="selAggregateid" name="selAggregatename" style="width:60%">
					<option value="sum">{'SUM'|getTranslatedString:'Reports'}</option>
					<option value="avg">{'AVG'|getTranslatedString:'Reports'}</option>
					<option value="max">{'MAX'|getTranslatedString:'Reports'}</option>
					<option value="min">{'MIN'|getTranslatedString:'Reports'}</option>
					<option value="count">{'COUNT'|getTranslatedString:'Reports'}</option>
					</select>
				</td>
			</tr>
			<tr id="moduleCombosRow" style="display:block">
				<td class="dvtCellLabel" align="right" id="fields" width="110">{$MOD.LBL_HOME_AG_FIELDS}</td>
				<td id="selModPrime_id" width="300" colspan="2" class="dvtCellInfo"></td>
			</tr>
			<tr id="rssRow" style="display:none">
				<td class="dvtCellLabel" width="110" align="right">{$MOD.LBL_HOME_RSSURL}<font color='red'>*</font></td>
				<td width="300" colspan="2" class="dvtCellInfo"><input type="text" name="txtRss" id="txtRss_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:58%"></td>
			</tr>
			<tr id="dashNameRow" style="display:none">
				<td class="dvtCellLabel" width="110" align="right">{$MOD.LBL_HOME_DASHBOARD_NAME}</td>
				<td id="selDashName" class="dvtCellInfo" colspan="2" width="300"></td>
			</tr>
			<tr id="homewidget" style="display:none">
				<td id="home" class="dvtCellInfo" colspan="2" width="300"></td>
			</tr>
			<tr id="dashTypeRow" style="display:none">
				<td class="dvtCellLabel" align="right" width="110">{$MOD.LBL_HOME_DASHBOARD_TYPE}</td>
				<td id="selDashType" class="dvtCellInfo" width="300" colspan="2">
					<select name="seldashtype" id="seldashtype_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:60%">
						<option value="horizontalbarchart">{$MOD.LBL_HOME_HORIZONTAL_BARCHART}</option>
						<option value="verticalbarchart">{$MOD.LBL_HOME_VERTICAL_BARCHART}</option>
						<option value="piechart">{$MOD.LBL_HOME_PIE_CHART}</option>
					</select>
				</td>
			</tr>
			<tr id="reportNameRow" style="display:none">
				<td class="dvtCellLabel" width="110" align="right">{'LBL_HOME_REPORT_NAME'|@getTranslatedString:$MODULE}</td>
				<td id="selReportName" class="dvtCellInfo" colspan="2" width="300"></td>
			</tr>
			<tr id="reportTypeRow" style="display:none">
				<td class="dvtCellLabel" align="right" width="110">{'LBL_HOME_REPORT_TYPE'|@getTranslatedString:$MODULE}</td>
				<td id="selReportType" class="dvtCellInfo" width="300" colspan="2">
					<select name="selreporttype" id="selreportcharttype_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:60%">
						<option value="horizontalbarchart">{$MOD.LBL_HOME_HORIZONTAL_BARCHART}</option>
						<option value="verticalbarchart">{$MOD.LBL_HOME_VERTICAL_BARCHART}</option>
						<option value="piechart">{$MOD.LBL_HOME_PIE_CHART}</option>
					</select>
				</td>
			</tr>
		</table>
			{*<!-- popup specific content fill in ends -->*}
			</td>
		</tr>
		</table>

		<table border=0 cellspacing=0 cellpadding=5 width=95% align="center">
			<tr>
				<td align="right">
					<input type="button" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " id="savebtn" class="crmbutton small save" onclick="frmValidate()"></td>
				<td align="left"><input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" onclick="fnhide('addWidgetsDiv');document.getElementById('stufftitle_id').value='';">
				</td>
			</tr>
		</table>
	</div>
</form>
{*<!-- add widget code ends -->*}

<div id="seqSettings" style="background-color:#E0ECFF;z-index:6000000;display:none;">
</div>


<div id="changeLayoutDiv" class="layerPopup" style="z-index:2000; display:none;">
	<table>
	<tr class="layerHeadingULine">
		<td class="big">
			{$MOD.LBL_HOME_LAYOUT}
		</td>
		<td>
			<img onclick="hideOptions('changeLayoutDiv');" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="right" style="cursor: pointer;border:0;max-width:initial;"/>
		</td>
	</tr>
	<tr id="numberOfColumns">
		<td class="dvtCellLabel" align="right">
			{$MOD.LBL_NUMBER_OF_COLUMNS}
		</td>
		<td class="dvtCellLabel">
			<select id="layoutSelect" class="small">
				<option value="2">
					{$MOD.LBL_TWO_COLUMN}
				</option>
				<option value="3">
					{$MOD.LBL_THREE_COLUMN}
				</option>
				<option value="4">
					{$MOD.LBL_FOUR_COLUMN}
				</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">
			<input type="button" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " id="savebtn" class="crmbutton small save" onclick="saveLayout();">
		</td>
		<td align="left">
			<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" onclick="hideOptions('changeLayoutDiv');">
		</td>
	</tr>

	</table>
</div>
