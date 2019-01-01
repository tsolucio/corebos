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
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$LBL_CHARSET}">
	<title>{$MODULE|@getTranslatedString:$MODULE} - {$coreBOS_uiapp_name}</title>
	<link REL="SHORTCUT ICON" HREF="themes/images/blank.gif">
	<link rel="stylesheet" type="text/css" href="include/LD/assets/styles/override_lds.css">
<script type="text/javascript">
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
var gVTUserID = "{$CURRENT_USER_ID}";
var userFirstDayOfWeek = {$USER_FIRST_DOW};
var image_pth = '{$IMAGE_PATH}';
var userDateFormat = "{$USER_DATE_FORMAT}";
var userHourFormat = "{$USER_HOUR_FORMAT}";
var userCurrencySeparator = "{$USER_CURRENCY_SEPARATOR}";
var userDecimalSeparator = "{$USER_DECIMAL_FORMAT}";
var userNumberOfDecimals = "{$USER_NUMBER_DECIMALS}";
var gVTuserLanguage = "{$USER_LANGUAGE}";
var product_default_units = '{if isset($Product_Default_Units)}{$Product_Default_Units}{else}1{/if}';
var service_default_units = '{if isset($Service_Default_Units)}{$Service_Default_Units}{else}1{/if}';
var gPopupAlphaSearchUrl = '';
var gsorder = '';
var gstart = '';
var gpopupReturnAction = '{$RETURN_ACTION}';
var gpopupPopupMode = '{$POPUPMODE}';
var gpopupCallback = '{$CALLBACK}';
var product_labelarr = {ldelim}
	CLEAR_COMMENT:'{$APP.LBL_CLEAR_COMMENT}',
	DISCOUNT:'{$APP.LBL_DISCOUNT}',
	TOTAL_AFTER_DISCOUNT:'{$APP.LBL_TOTAL_AFTER_DISCOUNT}',
	TAX:'{$APP.LBL_TAX}',
	ZERO_DISCOUNT:'{$APP.LBL_ZERO_DISCOUNT}',
	PERCENT_OF_PRICE:'{$APP.LBL_OF_PRICE}',
	DIRECT_PRICE_REDUCTION:'{$APP.LBL_DIRECT_PRICE_REDUCTION}'
{rdelim};
var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>
<link rel="stylesheet" type="text/css" href="{$THEME_PATH}style.css">
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<link rel="stylesheet" type="text/css" href="include/LD/assets/styles/salesforce-lightning-design-system.css" />
{* corebos customization: Inclusion of custom javascript and css as registered in popup *}
{if $HEADERCSS}
	<!-- Custom Header CSS -->
	{foreach item=HDRCSS from=$HEADERCSS}
	<link rel="stylesheet" type="text/css" href="{$HDRCSS->linkurl}" />
	{/foreach}
	<!-- END -->
{/if}
{* END *}
<script type="text/javascript" src="include/js/{$LANGUAGE}.lang.js"></script>
<script type="text/javascript" src="include/js/meld.js"></script>
<script type='text/javascript' src='include/jquery/jquery.js'></script>
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/js/vtlib.js"></script>
<script type="text/javascript" src="include/js/QuickCreate.js"></script>
<script type="text/javascript" src="include/js/Inventory.js"></script>
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/Mail.js"></script>
<script type="text/javascript" src="modules/Tooltip/TooltipHeaderScript.js"></script>
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
{if !empty($RETURN_MODULE)}
<script type="text/javascript" src="modules/{$RETURN_MODULE}/{$RETURN_MODULE}.js"></script>
{else}
{assign var="RETURN_MODULE" value=""}
{/if}
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>

{* corebos customization: Inclusion of custom javascript and css as registered in popup *}
{if $HEADERSCRIPTS}
	<!-- Custom Header Script -->
	{foreach item=HEADERSCRIPT from=$HEADERSCRIPTS}
	<script type="text/javascript" src="{$HEADERSCRIPT->linkurl}"></script>
	{/foreach}
	<!-- END -->
{/if}
</head>
<body onload=set_focus() class="small" marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 bottommargin=0 rightmargin=0>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					{if $recid_var_value neq ''}
						<td class="moduleName" width="80%" style="padding-left:10px;">{$MODULE|@getTranslatedString:$MODULE}&nbsp;{$APP.LBL_RELATED_TO}&nbsp;{$PARENT_MODULE|@getTranslatedString:$PARENT_MODULE}</td>
					{else}
						{if $RECORD_ID}
							<td class="moduleName" width="80%" style="padding-left:10px;"><a href="javascript:;" onclick="window.history.back();">{$MODULE|@getTranslatedString:$MODULE}</a> > {$PRODUCT_NAME}</td>
						{else}
							<td class="moduleName" width="80%" style="padding-left:10px;">{$MODULE|@getTranslatedString:$MODULE}</td>
						{/if}
					{/if}
					<td width=24% nowrap class="componentName" align=right>{$coreBOS_uiapp_name}</td>
					<td width=6% nowrap class="componentName" align=right><input type="hidden" id='closewindow' value="true"/><img src="themes/images/unlocked.png" id='closewindowimage' onclick="if (document.getElementById('closewindow').value=='true') {ldelim}document.getElementById('closewindowimage').src='themes/images/locked.png';document.getElementById('closewindow').value='false';{rdelim} else {ldelim}document.getElementById('closewindowimage').src='themes/images/unlocked.png';document.getElementById('closewindow').value='true';{rdelim};"/></td>
				</tr>
			</table>
			<div id="status" style="position:absolute;display:none;right:135px;top:15px;height:27px;white-space:nowrap;"><img src="{'status.gif'|@vtiger_imageurl:$THEME}"></div>
			<table width="100%" cellpadding="5" cellspacing="0" border="0" class="homePageMatrixHdr">
				<tr>
					<td style="padding:10px;" >
						<div id="searchAcc" style="display: block;position:relative;">
						<form name="basicSearch" action="index.php" onsubmit="callSearch('Basic');return false;" method="post">
						<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td class="searchUIName small" nowrap align="left">
								<span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';">{$APP.LBL_GO_TO} {$APP.LNK_ADVANCED_SEARCH}</a></span>
							</td>
							<td width="30%" class="dvtCellLabel"><input type="text" name="search_text" id="search_txt" class="txtBox"> </td>
							<td width="30%" class="dvtCellLabel"><b>{$APP.LBL_IN}</b>&nbsp;
								<select name ="search_field" class="txtBox">
									{html_options options=$SEARCHLISTHEADER }
								</select>
								<input type="hidden" name="searchtype" value="BasicSearch">
								<input type="hidden" name="module" id="module" value="{$MODULE}">
								<input type="hidden" name="action" value="Popup">
								<input type="hidden" name="query" value="true">
								<input type="hidden" name="select_enable" id="select_enable" value="{$SELECT}">
								<input type="hidden" name="curr_row" id="curr_row" value="{$CURR_ROW}">
								<input type="hidden" name="fldname_pb" value="{$FIELDNAME}">
								<input type="hidden" name="productid_pb" value="{$PRODUCTID}">
								<input name="popuptype" id="popup_type" type="hidden" value="{$POPUPTYPE}">
								<input name="recordid" id="recordid" type="hidden" value="{$RECORDID}">
								<input name="record_id" id="record_id" type="hidden" value="{$RECORD_ID}">
								<input name="return_module" id="return_module" type="hidden" value="{$RETURN_MODULE}">
								<input name="from_link" id="from_link" type="hidden" value="{if isset($smarty.request.fromlink)}{$smarty.request.fromlink|@vtlib_purify}{/if}">
								<input name="maintab" id="maintab" type="hidden" value="{$MAINTAB}">
								<input type="hidden" id="relmod" name="{$mod_var_name}" value="{$mod_var_value}">
								<input type="hidden" id="relrecord_id" name="{$recid_var_name}" value="{$recid_var_value}">
								<input name="form" id="popupform" type="hidden" value="{$smarty.request.form|@vtlib_purify}">
								<input name="forfield" id="forfield" type="hidden" value="{if isset($smarty.request.forfield)}{$smarty.request.forfield|@vtlib_purify}{/if}">
								<input name="srcmodule" id="srcmodule" type="hidden" value="{if isset($smarty.request.srcmodule)}{$smarty.request.srcmodule|@vtlib_purify}{/if}">
								<input name="forrecord" id="forrecord" type="hidden" value="{if isset($smarty.request.forrecord)}{$smarty.request.forrecord|@vtlib_purify}{/if}">
								{if isset($CBCUSTOMPOPUPINFO_ARRAY)}
									{foreach from=$CBCUSTOMPOPUPINFO_ARRAY item=param}
										<input name="{$param}" id="{$param}" type="hidden" value="{if isset($smarty.request.$param)}{$smarty.request.$param|@vtlib_purify}{/if}">
									{/foreach}
									{if isset($CBCUSTOMPOPUPINFO)}
										<input name="cbcustompopupinfo" id="cbcustompopupinfo" type="hidden" value="{$CBCUSTOMPOPUPINFO}">
									{/if}
								{/if}
								{if !empty($smarty.request.currencyid)}
									<input type="hidden" name="currencyid" id="currencyid" value="{$smarty.request.currencyid|@vtlib_purify}">
								{/if}
							</td>
							<td width="18%" class="dvtCellLabel">
								<input type="button" name="search" value=" &nbsp;{$APP.LBL_SEARCH_NOW_BUTTON}&nbsp; " onClick="callSearch('Basic');" class="crmbutton small create">
							</td>
							<td width="2%" class="dvtCellLabel">
								{if in_array($MODULE,$QCMODULEARRAY)}<a href="javascript:QCreatePop('{$MODULE}','{$POPUP}');"><img src="{'select.gif'|@vtiger_imageurl:$THEME}" align="left" border="0"></a>{/if}
							</td>
						</tr>
						 <tr>
							<td colspan="5" align="center">
								<table width="100%" class="small">
								<tr>
									{$ALPHABETICAL}
								</tr>
								</table>
							</td>
						</tr>
						</table>
						</form>
						</div>
					</td>
				</tr>
				{if $recid_var_value neq ''}
					<tr>
						<td align="right"><input id="all_contacts" alt="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.$MODULE}" title="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.$MODULE}" accessKey="" class="crmbutton small edit" value="{$APP.SHOW_ALL}&nbsp;{$APP.$MODULE}" onclick="window.location.href=showAllRecords();" type="button" name="button"></td>
					</tr>
				{/if}
			</table>
			<!-- ADVANCED SEARCH -->
			<div id="advSearch" style="display:none;">
			<form name="advSearch" method="post" action="index.php" onSubmit="callSearch('Advanced');return false">
				<table cellspacing=0 cellpadding=5 width=100% class="searchUIAdv1 small" align="center" border=0>
					<tr>
						<td class="searchUIName small" nowrap align="left"><span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="show('searchAcc');fnhide('advSearch')">{$APP.LBL_GO_TO} {$APP.LNK_BASIC_SEARCH}</a></span></td>
						<td class="small" align="right" valign="top">&nbsp;</td>
					</tr>
				</table>
				<table cellpadding="2" cellspacing="0" width="100%" align="center" class="searchUIAdv2 small" border=0>
					<tr>
						<td align="center" class="small" width=90%>
							{include file='AdvanceFilter.tpl' SOURCE='customview' COLUMNS_BLOCK=$FIELDNAMES}
						</td>
					</tr>
				</table>
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="searchUIAdv3 small" align="center">
					<tr>
						<td align="center" class="small"><input type="button" class="crmbutton small create" value=" {$APP.LBL_SEARCH_NOW_BUTTON} " onClick="callSearch('Advanced');">
						</td>
					</tr>
				</table>
			</form><br>
			</div>
			</div>
			<div id="qcformpop"></div>
			<div id="ListViewContents">
				{include file="PopupContents.tpl"}
			</div>
		</td>
	</tr>
</table>
<script type="text/javascript" src="include/js/popup.js"></script>
</body>
