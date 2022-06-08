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
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/Merge.js"></script>
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="include/js/FieldDepFunc.js"></script>
{if !isset($Document_Folder_View)}
	{assign var=Document_Folder_View value=1}
{/if}
<script>
var Document_Folder_View={$Document_Folder_View};
{if empty($moduleView)}
var Application_Landing_View='table';
{else}
var Application_Landing_View='{$moduleView}';
{/if}
</script>
{include file='Buttons_List.tpl'}
{*<!-- Contents -->*}
<table class="slds-m-around_medium" style="width:98%;">
	<tr>
		<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
		{include file='ListViewSearch.tpl'}
			<div id="mergeDup" style="z-index:1;display:none;position:relative;">
				{include file="MergeColumns.tpl"}
			</div>
			<div id="ListViewContents" class="small" style="width:100%;">
				{include file="DocumentsListViewEntries.tpl"}
			</div>
		</td>
	</tr>
</table>
<script type="text/javascript" src="modules/Documents/Documents.js"></script>