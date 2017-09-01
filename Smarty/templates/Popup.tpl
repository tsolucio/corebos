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
<script type="text/javascript">
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
var gVTUserID = "{$CURRENT_USER_ID}";
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
{literal}
function QCreate(module,urlpop) {
	if (module != 'none') {
		document.getElementById("status").style.display="inline";
		if (module == 'Events') {
			module = 'Calendar';
			var urlstr = '&activity_mode=Events&from=popup&pop='+urlpop;
		} else if(module == 'Calendar') {
			module = 'Calendar';
			var urlstr = '&activity_mode=Task&from=popup&pop='+urlpop;
		} else {
			var urlstr = '&from=popup&pop='+urlpop;
		}
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module='+module+'&action='+module+'Ajax&file=QuickCreate'+urlstr
		}).done(function(response) {
			document.getElementById("status").style.display="none";
			document.getElementById("qcformpop").style.display="inline";
			document.getElementById("qcformpop").innerHTML = response;
			// Evaluate all the script tags in the response text.
			var scriptTags = document.getElementById("qcformpop").getElementsByTagName("script");
			for (var i = 0; i< scriptTags.length; i++) {
				var scriptTag = scriptTags[i];
				eval(scriptTag.innerHTML);
			}
		});
	} else {
		hide('qcformpop');
	}
}
{/literal}
</script>
<link rel="stylesheet" type="text/css" href="{$THEME_PATH}style.css">
<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css" />
<link rel="stylesheet" href="include/LD/assets/styles/customLD.css" type="text/css" />
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
<script type="text/javascript" src="include/js/QuickCreate.js"></script>
<script type="text/javascript" src="include/js/Inventory.js"></script>
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/vtlib.js"></script>
<script type="text/javascript" src="include/js/Mail.js"></script>
<script type="text/javascript" src="modules/Tooltip/TooltipHeaderScript.js"></script>
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
			<table id="LB_buttonlist" width="98%" border="0" cellpadding="0" cellspacing="0">
				<tr class="slds-text-title--caps">
					{if $recid_var_value neq ''}
						<th scope="col" style="padding: 1rem 1.5rem 1rem 1rem;">
							<div class="slds-truncate moduleName">{$MODULE|@getTranslatedString:$MODULE}&nbsp;{$APP.LBL_RELATED_TO}&nbsp;{$PARENT_MODULE|@getTranslatedString:$PARENT_MODULE}</div>
						</th>
					{else}
						{if $RECORD_ID}
							<th scope="col" style="padding: 1rem 1.5rem 1rem 1rem;">
								<div class="moduleName">
									<a href="javascript:;" onclick="window.history.back();">{$MODULE|@getTranslatedString:$MODULE}</a> > {$PRODUCT_NAME}
								</div>
							</th>
						{else}
							<th scope="col" style="padding: 1rem 1.5rem 1rem 1rem;">
								<div class="moduleName">{$MODULE|@getTranslatedString:$MODULE}</div>
							</th>
						{/if}
					{/if}
					<th scope="col" style="padding: 1rem 0;">
						<div class="componentName" align=right>{$coreBOS_uiapp_name}
							&nbsp;&nbsp;
							<input type="hidden" id='closewindow' value="true"/>
							<img src="themes/images/unlocked.png" style="width:16px;vertical-align: top;" id='closewindowimage' onclick="if (document.getElementById('closewindow').value=='true') {ldelim}document.getElementById('closewindowimage').src='themes/images/locked.png';document.getElementById('closewindow').value='false';{rdelim} else {ldelim}document.getElementById('closewindowimage').src='themes/images/unlocked.png';document.getElementById('closewindow').value='true';{rdelim};"/>
						</div>
					</th>
				</tr>
			</table>
			<div id="status" style="position:absolute;display:none;right:135px;top:15px;height:27px;white-space:nowrap;"><img src="{'status.gif'|@vtiger_imageurl:$THEME}"></div>
				<table class="slds-table slds-no-row-hover slds-table-moz">
					<tr>
						<td valign="top" width=98% style="padding:0;">
							<div id="searchAcc" style="display: block;position:relative;">
								<form name="basicSearch" action="index.php" onsubmit="callSearch('Basic');return false;" method="post">
									<table width="98%" cellpadding="5" cellspacing="0" class="searchUIBasic mediaQuery" align="center" border=0>
										<tr>
											<td class="searchUIName" nowrap align="left" width="20%">
												<span class="moduleName">{$APP.LBL_SEARCH}</span>
												<br>
												<span class="small">
													<a href="#" onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';">{$APP.LBL_GO_TO} {$APP.LNK_ADVANCED_SEARCH}</a>
												</span>
											</td>
											<td>
												<input type="text" name="search_text" id="search_txt" class="slds-input txtBox">
											</td>
											<td nowrap style="padding: .5rem 0;"><b>{$APP.LBL_IN}</b>&nbsp;</td>
											<td nowrap style="padding: .5rem 0;">
												<div class="slds-form-element">
													<div class="slds-form-element__control">
														<div class="slds-select_container">
															<select name ="search_field" class="slds-select">
																{html_options options=$SEARCHLISTHEADER }
															</select>
														</div>
													</div>
												</div>
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
											<td>
												<input type="button" class="slds-button slds-button_success slds-button--small" name="search" value=" &nbsp;{$APP.LBL_SEARCH_NOW_BUTTON}&nbsp; " onClick="callSearch('Basic');">
											</td>
											<td style="padding: .5rem 0;">
												{if in_array($MODULE,$QCMODULEARRAY)}<a href="javascript:QCreate('{$MODULE}','{$POPUP}');"><img src="{'btnL3Add.gif'|@vtiger_imageurl:$THEME}" align="left" border="0" width="18"></a>{/if}
											</td>
										</tr>
										<tr>
											<td colspan="7" align="center" class="small">
												<table border=0 cellspacing=0 cellpadding=0 width=100%>
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
							<td align="right"><input id="all_contacts" alt="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.$MODULE}" title="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.$MODULE}" accessKey="" class="slds-button slds-button--small slds-button--brand" value="{$APP.SHOW_ALL}&nbsp;{$APP.$MODULE}" onclick="window.location.href=showAllRecords();" type="button" name="button"></td>
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
								<td align="center" class="small"><input type="button" class="slds-button slds-button--small slds-button_success" value=" {$APP.LBL_SEARCH_NOW_BUTTON} " onClick="callSearch('Advanced');">
								</td>
							</tr>
						</table>
					</form>
					<br>
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
