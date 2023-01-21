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
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$LBL_CHARSET}">
	<title>{$MODULE|@getTranslatedString:$MODULE} - {$coreBOS_uiapp_name}</title>
	<link REL="SHORTCUT ICON" HREF="themes/images/blank.gif">
{include file='BrowserVariables.tpl'}
{include file='Components/Components.tpl'}
<script type="text/javascript">
var image_pth = '{$IMAGE_PATH}';
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
{if !empty($SET_CSS_PROPERTIES) && is_file($SET_CSS_PROPERTIES)}
	<link rel="stylesheet" type="text/css" media="all" href="{$SET_CSS_PROPERTIES}">
{/if}
<link rel="stylesheet" type="text/css" href="{$THEME_PATH}style.css">
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<link rel="stylesheet" type="text/css" href="include/LD/assets/styles/salesforce-lightning-design-system.css" />
<link rel="stylesheet" type="text/css" href="include/LD/assets/styles/override_lds.css">
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
<script type="text/javascript" src="include/js/corebosjshooks.js"></script>
<script type='text/javascript' src='include/jquery/jquery.js'></script>
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/components/ldsprompt.js"></script>
<script type="text/javascript" src="include/js/vtlib.js"></script>
<script type="text/javascript" src="include/js/QuickCreate.js"></script>
<script type="text/javascript" src="include/js/Inventory.js"></script>
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/Mail.js"></script>
<script type="text/javascript" src="modules/Tooltip/TooltipHeaderScript.js"></script>
{include file='Components/ComponentsJS.tpl'}
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
					<td width=6% nowrap class="componentName" align=right>
						<input type="hidden" id='closewindow' value="true"/>
						<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-icon_small" id="closewindowimageunlock" onclick="togglePopupLock();">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#unlock"></use>
						</svg>
						<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-icon_small" id="closewindowimagelock" style="display:none" onclick="togglePopupLock();">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#lock"></use>
						</svg>
					</td>
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
								<span class="moduleName">{$APP.LBL_SEARCH}</span>
								<br>
								{if empty($NOADVANCEDSEARCH)}
								<span class="small"><a href="#" onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';">{$APP.LBL_GO_TO} {$APP.LNK_ADVANCED_SEARCH}</a></span>
								{/if}
							</td>
							<td width="30%" class="dvtCellLabel"><input type="text" name="search_text" id="search_txt" class="txtBox"> </td>
							<td width="30%" class="dvtCellLabel"><b>{$APP.LBL_IN}</b>&nbsp;
								<select name ="search_field" class="txtBox">
									{html_options options=$SEARCHLISTHEADER }
								</select>
								<input type="hidden" name="searchtype" value="{$searchtype|@urlencode}">
								<input type="hidden" name="module" id="module" value="{$MODULE}">
								<input type="hidden" name="action" value="Popup">
								<input type="hidden" name="query" value="true">
								<input type="hidden" name="select_enable" id="select_enable" value="{$SELECT}">
								<input type="hidden" name="curr_row" id="curr_row" value="{$CURR_ROW}">
								<input type="hidden" name="fldname_pb" value="{$FIELDNAME}">
								<input type="hidden" name="productid_pb" value="{$PRODUCTID}">
								<input type="hidden" name="popuptype" value="{$POPUPTYPE}">
								<input name="recordid" id="recordid" type="hidden" value="{$RECORDID}">
								<input name="record_id" id="record_id" type="hidden" value="{$RECORD_ID}">
								<input name="return_module" id="return_module" type="hidden" value="{$RETURN_MODULE}">
								<input name="from_link" id="from_link" type="hidden" value="{if isset($smarty.request.fromlink)}{$smarty.request.fromlink|@urlencode}{/if}">
								<input type="hidden" id="relmod" name="{$mod_var_name}" value="{$mod_var_value}">
								<input type="hidden" id="relrecord_id" name="{$recid_var_name}" value="{$recid_var_value}">
								<input name="form" id="popupform" type="hidden" value="{$smarty.request.form|@urlencode}">
								<input name="forfield" id="forfield" type="hidden" value="{if isset($smarty.request.forfield)}{$smarty.request.forfield|@urlencode}{/if}">
								<input name="srcmodule" id="srcmodule" type="hidden" value="{if isset($smarty.request.srcmodule)}{$smarty.request.srcmodule|@urlencode}{/if}">
								<input name="forrecord" id="forrecord" type="hidden" value="{if isset($smarty.request.forrecord)}{$smarty.request.forrecord|@urlencode}{/if}">
								{if isset($CBCUSTOMPOPUPINFO_ARRAY)}
									{foreach from=$CBCUSTOMPOPUPINFO_ARRAY item=param}
										<input name="{$param}" id="{$param}" type="hidden" value="{if isset($smarty.request.$param)}{$smarty.request.$param|@urlencode}{/if}">
									{/foreach}
									{if isset($CBCUSTOMPOPUPINFO)}
										<input name="cbcustompopupinfo" id="cbcustompopupinfo" type="hidden" value="{$CBCUSTOMPOPUPINFO}">
									{/if}
								{/if}
								{if !empty($smarty.request.currencyid)}
									<input type="hidden" name="currencyid" id="currencyid" value="{$smarty.request.currencyid|@urlencode}">
								{/if}
								{if !empty($smarty.request.srcwhid)}
									<input type="hidden" name="srcwhid" id="srcwhid" value="{$smarty.request.srcwhid|@urlencode}">
								{/if}
							</td>
							<td width="18%" class="dvtCellLabel">
								<input type="button" name="search" value=" &nbsp;{$APP.LBL_SEARCH_NOW_BUTTON}&nbsp; " onClick="callSearch('Basic');" class="crmbutton small create">
							</td>
							<td width="2%" class="dvtCellLabel">
								{if in_array($MODULE,$QCMODULEARRAY)}
									<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-icon_x-small" id="popupqcreate" onclick="QCreatePop('{$MODULE}','{$POPUP}');">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
									</svg>
								{/if}
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
						<td align="right">
						<input id="all_contacts" alt="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" title="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" accessKey="" class="crmbutton small edit" value="{$APP.SHOW_ALL}&nbsp;{$MODULE|@getTranslatedString:$MODULE}" onclick="window.location.href=showAllRecords();" type="button" name="button">
						</td>
					</tr>
				{/if}
			</table>
			<!-- ADVANCED SEARCH -->
			{if empty($NOADVANCEDSEARCH)}
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
							<input type="hidden" name="advft_criteria" id="advft_criteria" value='{$advft_criteria|@urlencode}'>
							<input type="hidden" name="advft_criteria_groups" id="advft_criteria_groups" value='{$advft_criteria_groups|@urlencode}'>
							<div class="slds-grid slds-m-top_small cbds-advanced-search--inactive" id="cbds-advanced-search">
								<div class="slds-col">
									<div class="slds-expression slds-p-bottom_xx-large">
										<div class="slds-grid">
											<div class="slds-col slds-size_11-of-12">
												<div class="slds-text-title_caps slds-align_absolute-center">{$APP.LBL_SEARCH}</div>
											</div>
											<div class="slds-col slds-size_1-of-12 slds-clearfix">
												<button type="button"
													class="slds-button slds-button_icon slds-button_icon-border slds-float_right"
													onClick="show('searchAcc');fnhide('advSearch');document.basicSearch.searchtype.value='basic';document.basicSearch.searchtype.searchlaunched='';document.getElementById('cbds-advanced-search').classList.remove('cbds-advanced-search--active')">
													<svg class="slds-button__icon" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
													</svg>
													<span class="slds-assistive-text">{$APP.LBL_DELETE_GROUP}</span>
												</button>
											</div>
										</div>
										<pre>
										</pre>
										{include file='AdvanceFilter.tpl' SOURCE='customview' COLUMNS_BLOCK=$FIELDNAMES MODULES_BLOCK=$FIELDNAMES_ARRAY}
									</div>
								</div>
							</div>
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
			{/if}
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
