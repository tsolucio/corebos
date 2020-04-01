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
<script type="text/javascript" src="include/js/ListView.js"></script>
{* If duplicate merge is within same module show the headers ... *}
{if $MODULE eq $smarty.request.module}
{include file='Buttons_List.tpl'}
{/if}

{*<!-- Contents -->*}

{if $MODULE eq $smarty.request.module}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
		<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
{/if}
			{* Common Output: Within module Duplicate Search or Post Import Duplicate Search *}
			<div id="duplicate_ajax" style='margin: 0 10px;'>
				{include file='FindDuplicateAjax.tpl'}
			</div>
			<div id="current_action" style="display:none">{$smarty.request.action|@vtlib_purify}</div>
			{* END *}
{if $MODULE eq $smarty.request.module}
		</td>
	</tr>
</table>
{/if}