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
<div>
{assign var='MODULEICON' value=$MODULE|@getModuleIcon}
	<div class="slds-page-header slds-p-around_x-small">
		<div class="slds-page-header__row">
			<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__figure">
						<span class="{$MODULEICON.__ICONContainerClass}" title="{$MODULE|@getTranslatedString:$MODULE}">
							<svg class="slds-icon slds-page-header__icon" id="page-header-icon" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink"
									xlink:href="include/LD/assets/icons/{$MODULEICON.__ICONLibrary}-sprite/svg/symbols.svg#{$MODULEICON.__ICONName}" />
							</svg>
						</span>
						<div class="slds-page-header__name">
							<div class="slds-page-header__name-title">
								<h1>
									<span><strong>&nbsp;
									{if $recid_var_value neq ''}
										{$MODULE|@getTranslatedString:$MODULE}&nbsp;{$APP.LBL_RELATED_TO}&nbsp;{$PARENT_MODULE|@getTranslatedString:$PARENT_MODULE}
									{else}
										{if $RECORD_ID}
											<a href="javascript:;" onclick="window.history.back();">{$MODULE|@getTranslatedString:$MODULE}</a> > {$PRODUCT_NAME}
										{else}
											{$MODULE|@getTranslatedString:$MODULE}
										{/if}
									{/if}
									</strong></span>
								</h1>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="slds-page-header__col-actions">
				<div class="slds-page-header__controls">
					<input type="hidden" id='closewindow' value="true"/>
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-icon_small" id="closewindowimageunlock" onclick="togglePopupLock();">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#unlock"></use>
					</svg>
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-icon_small" id="closewindowimagelock" style="display:none" onclick="togglePopupLock();">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#lock"></use>
					</svg>
				</div>
			</div>
		</div>
	</div>
	<div id="status" style="position:absolute;display:none;right:135px;top:15px;height:27px;white-space:nowrap;">
		<img src="{'status.gif'|@vtiger_imageurl:$THEME}">
	</div>
	<div class="slds-p-around_x-small slds-card">
		<div id="searchAcc" style="display: block;position:relative;">
		<form name="basicSearch" action="index.php" onsubmit="callSearch('Basic');return false;" method="post">
			<div class="slds-grid">
				<div class="slds-col slds-p-horizontal_medium slds-size_1-of-12">
					<span class="moduleName">{$APP.LBL_SEARCH}</span>
				</div>
				<div class="slds-col slds-p-horizontal_medium slds-size_7-of-12">
					<div class="slds-grid">
					<div class="slds-col slds-p-horizontal_medium slds-size_5-of-12">
					<input type="text" name="search_text" id="search_txt" class="slds-input">
					</div>
					<div class="slds-col slds-p-horizontal_medium slds-size_1-of-12">
					<div class="slds-m-top_x-small">&nbsp;{$APP.LBL_IN}&nbsp;</div>
					</div>
					<div class="slds-col slds-p-horizontal_medium slds-size_6-of-12">
					<select name ="search_field" class="slds-select">
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
					</div>
					</div>
				</div>
				<div class="slds-col slds-p-horizontal_medium slds-size_4-of-12">
					<button name="search" onclick="callSearch('Basic');document.basicSearch.searchtype.searchlaunched='basic';" class="slds-button slds-button_neutral" type="button">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
						</svg>
						{$APP.LBL_SEARCH_NOW_BUTTON}
					</button>
					{if empty($NOADVANCEDSEARCH)}
					<button class="slds-button slds-button_icon slds-button_icon-more" title="{'LNK_ADVANCED_SEARCH'|@getTranslatedString}" type="button"
						onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';document.basicSearch.searchtype.searchlaunched='';document.getElementById('cbds-advanced-search').classList.add('cbds-advanced-search--active')">
						<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_lookup"></use>
						</svg>
						<span class="slds-assistive-text">{'LNK_ADVANCED_SEARCH'|@getTranslatedString}</span>
					</button>
					{/if}
					<button class="slds-button slds-button_icon slds-button_icon-more" title="{'LNK_ALPHABETICAL_SEARCH'|@getTranslatedString}" type="button"
						onClick="toggleDiv('alphasearchtable');">
						<img class="slds-button__icon slds-button__icon_large" aria-hidden="true" src="include/LD/assets/icons/utility/az.png">
						<span class="slds-assistive-text">{'LNK_ALPHABETICAL_SEARCH'|@getTranslatedString}</span>
					</button>
					{if in_array($MODULE,$QCMODULEARRAY)}
						<button class="slds-button slds-button_icon slds-button_icon-border" title="{$APP.LBL_QUICK_CREATE}" type="button"
							id="popupqcreate" onclick="QCreatePop('{$MODULE}','{$POPUP}');">
						<svg aria-hidden="true" class="slds-button__icon slds-button__icon_large">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
						</svg>
						<span class="slds-assistive-text">{'LBL_QUICK_CREATE'|@getTranslatedString:$MODULE_NAME}</span>
						</button>
					{/if}
				</div>
			</div>
			<div id="alphasearchtable" style="display:none;" class="slds-m-around_xx-small">
				<table style="width:100%;">
					<tr>
						{$ALPHABETICAL}
					</tr>
				</table>
			</div>
		</form>
		</div>
	</div>
	{if $recid_var_value neq ''}
		<input id="all_contacts" alt="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" title="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" accessKey="" class="crmbutton small edit" value="{$APP.SHOW_ALL}&nbsp;{$MODULE|@getTranslatedString:$MODULE}" onclick="window.location.href=showAllRecords();" type="button" name="button">
	{/if}
	<!-- ADVANCED SEARCH -->
	{if empty($NOADVANCEDSEARCH)}
	<div id="advSearch" style="display:none;">
	<form name="advSearch" method="post" action="index.php" onSubmit="callSearch('Advanced');return false">
		<table cellspacing=0 cellpadding=5 width=100% class="searchUIAdv1 small" align="center" border=0>
			<tr>
				<td class="searchUIName small" nowrap align="left"><span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="show('searchAcc');fnhide('advSearch')">{$APP.LBL_GO_TO} {$APP.LNK_BASIC_SEARCH}</a></span></td>
				<td class="small" align="right" valign="top">
					<button type="button"
						class="slds-button slds-button_icon slds-button_icon-border slds-float_right"
						onClick="show('searchAcc');fnhide('advSearch');document.basicSearch.searchtype.value='basic';document.basicSearch.searchtype.searchlaunched='';document.getElementById('cbds-advanced-search').classList.remove('cbds-advanced-search--active')">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
						</svg>
					</button>
				</td>
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
								{include file='AdvanceFilter.tpl' SOURCE='customview' COLUMNS_BLOCK=$FIELDNAMES MODULES_BLOCK=$FIELDNAMES_ARRAY}
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="searchUIAdv3 small" align="center">
			<tr>
				<td align="center" class="small">
					<button name="search" onclick="callSearch('Advanced');document.basicSearch.searchtype.searchlaunched='advance';" class="slds-button slds-button_neutral" type="button">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
						</svg>
						{$APP.LBL_SEARCH_NOW_BUTTON}
					</button>
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
<script type="text/javascript" src="include/js/popup.js"></script>
</body>
