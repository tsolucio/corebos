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
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type='text/javascript' src='include/js/Merge.js'></script>
<script type='text/javascript' src='modules/RecycleBin/language/{$LANGUAGE}.lang.js'></script>
<script>
{if empty($moduleView)}
var Application_Landing_View='table';
{else}
var Application_Landing_View='{$moduleView}';
{/if}
</script>
{if !empty($moduleView) && $moduleView=='tuigrid'}
<script src="./include/js/ListViewRenderes.js"></script>
<script src="./include/js/ListViewJSON.js"></script>
{/if}
{include file='Buttons_List.tpl'}
{*<!-- Contents -->*}
<table class="slds-m-around_medium" style="width:98%;margin:auto;">
<tr>
	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">

		<form name="basicSearch" action="index.php" onsubmit="return false;">
		<div id="searchAcc" style="display: block;position:relative;">
			<table width="80%" cellpadding="5" cellspacing="0" class="searchUIBasic small" align="center" border=0>
				<tr>
					<td class="searchUIName small" nowrap align="left" width="15%">
						<span class="moduleName">{$APP.LBL_SEARCH}</span><br>
					</td>
					<td class="small" width="20%">
						<div class="slds-form-element">
							<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
								<svg class="slds-icon slds-input__icon slds-input__icon_left slds-icon-text-default" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<input type="text" id="search_text" name="search_text" placeholder="{$APP.LBL_SEARCH_FOR}" class="slds-input" />
							</div>
						</div>
					</td>
					<td class="small" nowrap width="1%">
						<label class="slds-form-element__label">{$APP.LBL_IN}</label>
					</td>
					<td class="small" nowrap  width="20%">
						<div id="basicsearchcolumns_real">
							<div class="slds-form-element">
								<div class="slds-form-element__control">
									<div class="slds-select_container">
										<select class="slds-select" name="search_field" id="bas_searchfield">
											{html_options options=$SEARCHLISTHEADER }
										</select>
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" name="searchtype" value="BasicSearch">
						<input type="hidden" name="module" value="{$SELECTED_MODULE}">
						<input type="hidden" name="action" value="index">
						<input type="hidden" name="query" value="true">
						<input type="hidden" name="search_cnt">
					</td>
					<td class="small" nowrap width="30%">
						<div class="slds-button-group" role="group">
							<a onClick="callRBSearch('Basic');" class="slds-button slds-button_neutral">
								<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								{$APP.LBL_SEARCH_NOW_BUTTON}
							</a>
							<a onClick="ListView.Reload()" class="slds-button slds-button_text-destructive">
								<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#refresh"></use>
								</svg>
								{$APP.LBL_CLEAR}
							</a>
						</div>
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
		</div>
		</form>

{*<!-- Searching UI -->*}

	<div id="modules_datas" class="small" style="width:100%;">
	{if !empty($moduleView) && $moduleView=='tuigrid'}
		{include file="modules/$MODULE/RecycleBinContentsTGrid.tpl"}
	{else}
		{include file="modules/$MODULE/RecycleBinContents.tpl"}
	{/if}
	</div>
</tr></td>

</div>
</td>
</tr>
</table>
</td>
</tr>
</table>

	</td>
</tr>
</tbody>
</table>

<div style="display: none;" class="veil_new small" id="rb_empty_conf_id">
	<table cellspacing="0" cellpadding="18" border="0" class="options small">
	<tbody>
		<tr>
			<td nowrap="" align="center" style="color: rgb(255, 255, 255); font-size: 15px;">
				<b>{$MOD.MSG_EMPTY_RB_CONFIRMATION}</b>
			</td>
		</tr>
		<tr>
			<td align="center" class="cblds-t-align_center">
				<input type="button" onclick="return emptyRecyclebin('rb_empty_conf_id');" value="{$APP.LBL_YES}"/>
				<input type="button" onclick="document.getElementById('rb_empty_conf_id').style.display='none';" value="{$APP.LBL_NO}"/>
			</td>
		</tr>
	</tbody>
	</table>
</div>