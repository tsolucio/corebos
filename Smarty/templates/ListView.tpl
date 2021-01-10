{*<!--
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/massive.js"></script>
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/Merge.js"></script>
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="include/js/FieldDepFunc.js"></script>
{if !isset($Document_Folder_View)}
	{assign var=Document_Folder_View value=0}
{/if}
<script>var Document_Folder_View={$Document_Folder_View};</script>
{include file='Buttons_List.tpl'}

{*<!-- Contents -->*}
<table class="slds-m-around_medium" style="width: 98%;">
	<tr>
	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
	<!-- SIMPLE SEARCH -->
<div id="searchAcc" style="{$DEFAULT_SEARCH_PANEL_STATUS};position:relative;">
<form name="basicSearch" method="post" action="index.php" onSubmit="document.basicSearch.searchtype.searchlaunched='basic';return callSearch('Basic');">
<table width="100%" cellpadding="5" cellspacing="0" class="searchUIBasic small" align="center" border=0>
	<tr>
		<td class="searchUIName small" nowrap align="left">
		<span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';document.basicSearch.searchtype.searchlaunched='';document.getElementById('cbds-advanced-search').classList.add('cbds-advanced-search--active')">{$APP.LBL_GO_TO} {$APP.LNK_ADVANCED_SEARCH}</a></span>
		<!-- <img src="themes/images/basicSearchLens.gif" align="absmiddle" alt="{$APP.LNK_BASIC_SEARCH}" title="{$APP.LNK_BASIC_SEARCH}" border=0>&nbsp;&nbsp;-->
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
		<td class="small" nowrap width="20%">
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
			<input type="hidden" name="module" value="{$MODULE}" id="curmodule">
			<input name="maxrecords" type="hidden" value="{$MAX_RECORDS}" id='maxrecords'>
			<input type="hidden" name="parenttab" value="{$CATEGORY}">
			<input type="hidden" name="action" value="index">
			<input type="hidden" name="query" value="true">
			<input type="hidden" name="search_cnt">
		</td>
		<td class="small" nowrap width="30%">
			<div class="slds-button-group" role="group">
				<a onClick="callSearch('Basic');document.basicSearch.searchtype.searchlaunched='basic';" class="slds-button slds-button_neutral">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
					</svg>
					{$APP.LBL_SEARCH_NOW_BUTTON}
				</a>
				<a onClick="ListView.ListViewReloadData()" class="slds-button slds-button_text-destructive">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#refresh"></use>
					</svg>
					{$APP.LBL_CLEAR}
				</a>
			</div>
		</td>
		<td class="small closeX" valign="top"></td>
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
</form><br class="searchbreak">
</div>
<!-- ADVANCED SEARCH -->
<!-- Advanced search row -->
<div id="advSearch">
	<form name="advSearch" method="post" action="index.php" onSubmit="document.basicSearch.searchtype.searchlaunched='advance';return callSearch('Advanced');">
		<input type="hidden" name="advft_criteria" id="advft_criteria" value="">
		<input type="hidden" name="advft_criteria_groups" id="advft_criteria_groups" value="">
		<div class="slds-grid slds-m-top--large cbds-advanced-search--inactive" id="cbds-advanced-search">
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
					{include file='AdvanceFilter.tpl' SOURCE='listview' MODULES_BLOCK=$FIELDNAMES_ARRAY}
				</div>
			</div>
		</div>
	</form>
</div>
<!-- // Advanced search row -->
{include file='masstag.tpl'}
{*<!-- Searching UI -->*}

<div id="mergeDup" style="z-index:1;display:none;position:relative;">
	{include file="MergeColumns.tpl"}
</div>
	<!-- PUBLIC CONTENTS STARTS-->
	<div id="ListViewContents" class="small" style="width:100%;">
	{if $MODULE neq "Documents" || $Document_Folder_View eq 0}
		{include file="ListViewEntries.tpl"}
	{else}
		{include file="DocumentsListViewEntries.tpl"}
	{/if}
	</div>

	</td>
	</tr>
</table>

{include file='MassEditHtml.tpl'}
{if $MODULE|hasEmailField}
<form name="SendMail" method="post"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
{/if}
{if (vt_hasRTE())}
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
{if vt_hasRTESpellcheck()}
<script type="text/javascript" src="include/ckeditor/config_spellcheck.js"></script>
{/if}
{/if}
