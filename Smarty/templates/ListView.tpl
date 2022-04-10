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
<script>
var Document_Folder_View={$Document_Folder_View};
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
<table class="slds-m-around_medium" style="width: 98%;">
	<tr>
	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
	<!-- SEARCH -->
	{include file='ListViewSearch.tpl'}
	{*<!-- Searching UI -->*}

<div id="mergeDup" style="z-index:1;display:none;position:relative;">
	{include file="MergeColumns.tpl"}
</div>
	<!-- PUBLIC CONTENTS STARTS-->
	<div id="ListViewContents" class="small" style="width:100%;">
	{if $MODULE neq "Documents" || $Document_Folder_View eq 0}
		{if empty($moduleView) || $moduleView=='table'}
			{include file="ListViewEntries.tpl"}
		{elseif $moduleView=='Kanban'}
			{include file="KanbanView.tpl"}
		{elseif $moduleView=='Dashboard'}
			{include file="DashboardView.tpl"}
		{elseif $moduleView=='Pivot'}
			{include file="PivotView.tpl"}
		{elseif $moduleView=='tuigrid'}
			{include file="ListViewTUIGrid.tpl"}
		{elseif $moduleView=='MassCreateGrid'}
			{include file="MassCreateGridView.tpl"}
		{/if}
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
{if (vt_hasRTE()) && $moduleView!='MassCreateGrid'}
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
{if vt_hasRTESpellcheck()}
<script type="text/javascript" src="include/ckeditor/config_spellcheck.js"></script>
{/if}
{/if}
