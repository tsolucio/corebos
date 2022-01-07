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
<div id="page-header-placeholder"></div>
<div id="page-header" class="slds-page-header slds-m-vertical_medium">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action=index&module={$MODULE}">
						{assign var='MODULEICON' value=$MODULE|@getModuleIcon}
						<span class="{$MODULEICON.__ICONContainerClass}" title="{$MODULE|@getTranslatedString:$MODULE}">
							<svg class="slds-icon slds-page-header__icon" id="page-header-icon" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink"
									xlink:href="include/LD/assets/icons/{$MODULEICON.__ICONLibrary}-sprite/svg/symbols.svg#{$MODULEICON.__ICONName}" />
							</svg>
							<span class="slds-assistive-text">{$MODULE|@getTranslatedString:$MODULE}</span>
						</span>
					</a>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span>{$MODULE|@getTranslatedString:$MODULE}</span>
								<span class="slds-page-header__title slds-truncate" title="{$MODULE|@getTranslatedString:$MODULE|@addslashes}">
									<a class="hdrLink" href="index.php?action=index&module={$MODULE}">{'My Home Page'|@getTranslatedString:$MODULE}</a>
								</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-page-header__col-actions">
			<div class="slds-page-header__controls">
				<div class="slds-page-header__control">
					<ul class="slds-button-group-list">
						<li class="slds-m-right_small slds-m-top_x-small">
							<span id="vtbusy_info" style="display:none;">
								<div role="status" class="slds-spinner slds-spinner_brand slds-spinner_x-small" style="position:relative; top:6px;">
									<div class="slds-spinner__dot-a"></div>
									<div class="slds-spinner__dot-b"></div>
								</div>
							</span>
						</li>
						<li>
							<button
								class="slds-button slds-button_neutral"
								onClick='fnAddWindow(this,"addWidgetDropDown");'
								onMouseOut='fnRemoveWindow();'>
									{$MOD.LBL_HOME_ADDWINDOW}
							</button>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div id="page-header-surplus">
		<div class="slds-page-header__row">
			<div class="slds-page-header__col-meta" style="min-width: 0;">
				<div class="slds-page-header__meta-text slds-grid">
					{if !empty($isDetailView) || !empty($isEditView)}
					<div class="slds-p-right_small">{$UPDATEINFO}</div>
					{/if}
					{assign var=ANNOUNCEMENT value=get_announcements()}
					{if $ANNOUNCEMENT}
					<style>
						#marquee span {
							display: inline-block;
							padding-left: 100%;
							animation: marquee {math equation="max(15, y/3)" y=$ANNOUNCEMENT|count_characters}s linear infinite;
						}
						#marquee span:hover {
							animation-play-state: paused
						}
						@keyframes marquee {
							0% {
							transform: translate(0, 0);
							}
							100% {
							transform: translate(-100%, 0);
							}
						}
					</style>
					<div class="slds-col slds-truncate" id="marquee">
						<span>{$ANNOUNCEMENT}</span>
					</div>
					{/if}
				</div>
			</div>
			<div class="slds-page-header__col-controls">
				<div class="slds-page-header__controls">
					<div class="slds-page-header__control">
						{if $ANNOUNCEMENT}
						<button
							class="slds-button slds-button_icon slds-button_icon-border-filled"
							aria-haspopup="true"
							style="transform: scale(-1,1); color: #d3451d;">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#announcement"></use>
								</svg>
						</button>
						{/if}
						<div class="slds-button-group" role="group">
							{* Calendar button *}
							{if $CALENDAR_DISPLAY eq 'true'}
								{$canusecalendar = true}
								{if $CHECK.Calendar != 'yes'}
									{$canusecalendar = false}
								{/if}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								{if $canusecalendar == false}disabled=""{/if}
								onclick="fnvshobj(this,'miniCal');getITSMiniCal('');"
								title="{$APP.LBL_CALENDAR_TITLE}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#monthlyview"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_CALENDAR_TITLE}
									</span>
							</button>
							{/if}
							{* World clock button *}
							{if $WORLD_CLOCK_DISPLAY eq 'true'}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								onClick="fnvshobj(this,'wclock');"
								title="{$APP.LBL_CLOCK_TITLE}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#world"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_CLOCK_TITLE}
									</span>
							</button>
							{/if}
							{* Change layout button *}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								title="{$MOD.LBL_HOME_LAYOUT}"
								onclick='showOptions("changeLayoutDiv");'>
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#layout"></use>
									</svg>
									<span class="slds-assistive-text">
										{$MOD.LBL_HOME_LAYOUT}
									</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{*<!--button related stuff -->*}
<form name="Homestuff" id="formStuff" style="display: inline;" method="post">
	<input type="hidden" name="action" value="homestuff">
	<input type="hidden" name="module" value="Home">
	<div id='addWidgetDropDown' class="slds-dropdown slds-dropdown_right slds-m-right_small slds-dropdown_actions" onmouseover='fnShowWindow()' onmouseout='fnRemoveWindow()'>
	<ul class="slds-dropdown__list" role="menu">
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:;' role="menuitem" id="addmodule">
				{$MOD.LBL_HOME_MODULE}
			</a>
		</li>
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:chooseType("CustomWidget");fnRemoveWindow();setFilter(document.getElementById("selmodule_id"));' role="menuitem" id="addcustomwidget">
				{$MOD.LBL_HOME_CUSTOM_WIDGET}
			</a>
		</li>
{if $ALLOW_RSS eq "yes"}
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:;' role="menuitem" id="addrss">
				{$MOD.LBL_HOME_RSS}
			</a>
		</li>
{/if}
{if $ALLOW_DASH eq "yes"}
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:;' role="menuitem" id="adddash">
				{$MOD.LBL_HOME_DASHBOARD}
			</a>
		</li>
{/if}
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:;' role="menuitem" id="addNotebook">
				{$MOD.LBL_NOTEBOOK}
			</a>
		</li>

{if $ALLOW_REPORT eq "yes"}
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:;' role="menuitem" id="addReportCharts">
				{'LBL_REPORTCHARTS'|@getTranslatedString:$MODULE}
			</a>
		</li>
{/if}
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:;' role="menuitem" id="defaultwidget">
				{'LBL_DEFAULT_WIDGET'|@getTranslatedString:$MODULE}
			</a>
		</li>
		{*<!-- this has been commented as some websites are opening up in full page (they have a target="_top")
		<li class="slds-dropdown__item" role="presentation">
			<a href='javascript:;' role="menuitem" id="addURL">
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
