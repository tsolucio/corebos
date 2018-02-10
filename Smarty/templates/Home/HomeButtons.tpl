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
<table align="center" width="98%" class="small homePageButtons" style="background-color: #f7f9fb;">
	<tr class="slds-text-title--caps">
		<th scope="col" style="padding: 1rem 1.5rem 1rem 1rem;">
			<div class="slds-truncate moduleName" title="{$MODULELABEL}">
				<a class="hdrLink" href="index.php?action=index&module={$MODULE}">
					{$APP.$MODULE}
				</a>
			</div>
		</th>
		<td width=100% nowrap>
			<table border="0" cellspacing="0" cellpadding="0" class="slds-table-buttons">
				<tr>
					<td class=small>
						<table class="slds-table slds-no-row-hover background">
							<tr class="LD_buttonList">
								<th scope="col">
									<div class="globalCreateContainer oneGlobalCreate">
										<div class="forceHeaderMenuTrigger">
											<img width="27" height="27" onClick='fnAddWindow(this,"addWidgetDropDown");' onMouseOut='fnRemoveWindow();' src="{'btnL3Add.gif'|@vtiger_imageurl:$THEME}" border="0" title="{$MOD.LBL_HOME_ADDWINDOW}" alt"{$MOD.LBL_HOME_ADDWINDOW}" style="cursor:pointer;">
										</div>
									</div>
								</th>
								{if $CHECK.Calendar eq 'yes' && $CALENDAR_ACTIVE eq 'yes' && $CALENDAR_DISPLAY eq 'true'}
									<th scope="col">
										<div class="globalCreateContainer oneGlobalCreate">
											<div class="forceHeaderMenuTrigger">
												<a href="javascript:;" onclick="fnvshobj(this,'miniCal');getITSMiniCal('');"><img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a>
											</div>
										</div>
									</th>
								{/if}
								{if $WORLD_CLOCK_DISPLAY eq 'true' }
									<th scope="col">
										<div class="globalCreateContainer oneGlobalCreate">
											<div class="forceHeaderMenuTrigger">
												<a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock');"></a>
											</div>
										</div>
									</th>
								{/if}
								{if $CALCULATOR_DISPLAY eq 'true' }
									<th scope="col">
										<div class="globalCreateContainer oneGlobalCreate">
											<div class="forceHeaderMenuTrigger">
												<a href="#"><img src="{$IMAGE_PATH}btnL3Calc.gif" alt="{$APP.LBL_CALCULATOR_ALT}" title="{$APP.LBL_CALCULATOR_TITLE}" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();"></a>
											</div>
										</div>
									</th>
								{/if}
									<th scope="col">
										<div class="globalCreateContainer oneGlobalCreate">
											<div class="forceHeaderMenuTrigger">
												<img width="27" height="27" src="{'btnL3Tracker.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_LAST_VIEWED}" title="{$APP.LBL_LAST_VIEWED}" border="0" onClick="fnvshobj(this,'tracker');">
											</div>
										</div>
									</th>
									<th scope="col">
										<div class="globalCreateContainer oneGlobalCreate">
											<div class="forceHeaderMenuTrigger">
												<img width="27" height="27" onClick='showOptions("changeLayoutDiv");' src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" border="0" title="{$MOD.LBL_HOME_LAYOUT}" alt"{$MOD.LBL_HOME_LAYOUT}" style="cursor:pointer;">
											</div>
										</div>
									</th>
									<th scope="col">
										<div id="vtbusy_info" style="display: none;">
											<img src="{'status.gif'|@vtiger_imageurl:$THEME}" border="0" />
										</div>
									</th>
							</tr>
						</table>
					</td>
				</tr>
			</table>
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
		<table class="slds-table slds-no-row-hover" width="100%" style="border-bottom: 1px solid #ddd;">
			<tr class="slds-text-title--header">
				<th scope="col">
					<div class="slds-truncate moduleName" id="divHeader"></div>
				</th>
				<th scope="col" style="padding: .5rem;text-align: right;">
					<div class="slds-truncate">
						<a href="javascript:;" onclick="fnhide('addWidgetsDiv');document.getElementById('stufftitle_id').value='';">
							<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" />
						</a>
					</div>
				</th>
			</tr>
		</table>
		<table class="slds-table slds-no-row-hover" width=95% align=center>
			<tr>
				<td class=small >
				{*<!-- popup specific content fill in starts -->*}
					<div class="slds-truncate">
					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
						<tr class="slds-line-height--reset" id="StuffTitleId" style="display:block;">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_STUFFTITLE}<font color='red'>*</font></td>
							<td class="dvtCellInfo" style="width:250px;"><input type="text" name="stufftitle" id="stufftitle_id" class="slds-input"></td>
						</tr>
						{*<!--
						<tr id="homeURLField" style="display:block;">
							<td class="dvtCellLabel"  width="110" align="right">
								{$MOD.LBL_URL}
								<font color='red'>*</font>
							</td>
							<td class="dvtCellInfo" colspan="2" style="width:120px;">
								<input type="text" name="url" id="url_id" class="detailedViewTextBox"  style="width:57%">
							</td>
						</tr>
						-->*}
						<tr class="slds-line-height--reset" id="showrow">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_SHOW}</td>
							<td class="dvtCellInfo" style="width:250px;">
								<select name="maxentries" id="maxentryid" class="slds-select" style="width: 75%;">
									{section name=iter start=1 loop=13 step=1}
										<option value="{$smarty.section.iter.index}">{$smarty.section.iter.index}</option>
									{/section}
								</select>&nbsp;&nbsp;{$MOD.LBL_HOME_ITEMS}
							</td>
						</tr>
						<tr class="slds-line-height--reset" id="moduleNameRow" style="display:block">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_MODULE}</td>
							<td class="dvtCellInfo" style="width:250px;">
								<select name="selmodule" id="selmodule_id" onchange="setFilter(this)" class="slds-select" style="width: 75%;">
									{foreach item=arr from=$MODULE_NAME}
										{assign var="MODULE_LABEL" value=$arr.1|getTranslatedString:$arr.1}
										<option value="{$arr.1}">{$MODULE_LABEL}</option>
									{/foreach}
								</select>
								<input type="hidden" name="fldname">
							</td>
						</tr>
						<tr class="slds-line-height--reset" id="moduleFilterRow" style="display:block">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_FILTERBY}</td>
							<td class="dvtCellInfo" style="width:250px;" id="selModFilter_id"></td>
						</tr>
						<tr class="slds-line-height--reset" id="modulePrimeRow" style="display:block">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_Fields}</td>
							<td class="dvtCellInfo" style="width:250px;" id="selModPrime_id"></td>
						</tr>
						<tr class="slds-line-height--reset" id="rssRow" style="display:none">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_RSSURL}<font color='red'>*</font></td>
							<td class="dvtCellInfo" style="width:250px;"><input type="text" name="txtRss" id="txtRss_id" class="slds-input"></td>
						</tr>
						<tr class="slds-line-height--reset" id="dashNameRow" style="display:none">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_DASHBOARD_NAME}</td>
							<td id="selDashName" class="dvtCellInfo" style="width:250px;"></td>
						</tr>
						<tr class="slds-line-height--reset" id="homewidget" style="display:none">
							<td id="home" class="dvtCellInfo" style="width:250px;"></td>
						</tr>
						<tr class="slds-line-height--reset" id="dashTypeRow" style="display:none">
							<td class="dvtCellLabel" style="width:120px;">{$MOD.LBL_HOME_DASHBOARD_TYPE}</td>
							<td id="selDashType" class="dvtCellInfo" style="width:250px;">
								<select name="seldashtype" id="seldashtype_id" class="slds-select" style="width: 75%;">
									<option value="horizontalbarchart">{$MOD.LBL_HOME_HORIZONTAL_BARCHART}</option>
									<option value="verticalbarchart">{$MOD.LBL_HOME_VERTICAL_BARCHART}</option>
									<option value="piechart">{$MOD.LBL_HOME_PIE_CHART}</option>
								</select>
							</td>
						</tr>
						<tr class="slds-line-height--reset" id="reportNameRow" style="display:none">
							<td class="dvtCellLabel" style="width:120px;">{'LBL_HOME_REPORT_NAME'|@getTranslatedString:$MODULE}</td>
							<td id="selReportName" class="dvtCellInfo" style="width:250px;"></td>
						</tr>
						<tr class="slds-line-height--reset" id="reportTypeRow" style="display:none">
							<td class="dvtCellLabel" style="width:120px;">{'LBL_HOME_REPORT_TYPE'|@getTranslatedString:$MODULE}</td>
							<td id="selReportType" class="dvtCellInfo" style="width:250px;">
								<select name="selreporttype" id="selreportcharttype_id" class="slds-select" style="width: 75%;">
									<option value="horizontalbarchart">{$MOD.LBL_HOME_HORIZONTAL_BARCHART}</option>
									<option value="verticalbarchart">{$MOD.LBL_HOME_VERTICAL_BARCHART}</option>
									<option value="piechart">{$MOD.LBL_HOME_PIE_CHART}</option>
								</select>
							</td>
						</tr>
					</table>
					</div>
					{*<!-- popup specific content fill in ends -->*}
				</td>
			</tr>
		</table>

		<table border=0 cellspacing=0 cellpadding=5 width=95% align="center">
			<tr class="slds-line-height--reset">
				<td align="center" style="padding: .5rem;">
					<input type="button" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " id="savebtn" class="slds-button slds-button--small slds-button_success" onclick="frmValidate()">
					<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick="fnhide('addWidgetsDiv');document.getElementById('stufftitle_id').value='';">
				</td>
			</tr>
		</table>
	</div>
</form>
{*<!-- add widget code ends -->*}

<div id="seqSettings" style="background-color:#E0ECFF;z-index:6000000;display:none;"></div>

<div id="changeLayoutDiv" class="layerPopup" style="z-index:2000; display:none;">
	<table class="slds-table slds-no-row-hover slds-table--bordered">
		<tr class="slds-text-title--header">
			<th scope="col">
				<div class="slds-truncate moduleName">{$MOD.LBL_HOME_LAYOUT}</div>
			</th>
			<th scope="col" style="padding: .5rem;">
				<div class="slds-truncate"><img onclick="hideOptions('changeLayoutDiv');" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="right" style="cursor: pointer;"/></div>
			</th>
		</tr>
		<tr class="slds-line-height--reset" id="numberOfColumns">
			<td class="dvtCellLabel" align="right">
				{$MOD.LBL_NUMBER_OF_COLUMNS}
			</td>
			<td class="dvtCellLabel">
				<select id="layoutSelect" class="slds-select">
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
		<tr class="slds-line-height--reset">
			<td align="right">
				<input type="button" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " id="savebtn" class="slds-button slds-button--small slds-button_success" onclick="saveLayout();">
			</td>
			<td align="left">
				<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick="hideOptions('changeLayoutDiv');">
			</td>
		</tr>
	</table>
</div>