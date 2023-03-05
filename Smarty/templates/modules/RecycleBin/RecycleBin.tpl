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
			{include "modules/RecycleBin/RecycleBinSearch.tpl"}
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