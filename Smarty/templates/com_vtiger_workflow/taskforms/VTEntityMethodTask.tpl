{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}
<script src="modules/com_vtiger_workflow/resources/entitymethodtask.js" ></script>
<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{$MOD.LBL_METHOD_NAME}</b></td>
		<td class='dvtCellInfo'>
			<span id="method_name_select_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select name="methodName" id="method_name_select" class="small" style="display: none;"></select>
			<span id="message_text" style="display: none;">{$MOD.NO_METHOD_AVAILABLE}</span>
		</td>
	</tr>
</table>
<script>
var moduleName = '{$entityName}';
var methodName = '{if isset($task->methodName)}{$task->methodName}{/if}';
entityMethodScript(jQuery);
</script>