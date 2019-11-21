{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="big" nowrap="nowrap">
			<strong>{$MOD.LBL_TASKS}</strong>
		</td>
		<td class="small cblds-t-align_right" align="right">
			<a href="{$module->activatedeactivateTaskUrl($workflow->id,1)}">
				<input type="button" class="crmButton save small" value="{$MOD.LBL_ACTIVATE_ALL_BUTTON_LABEL}" id='approval'/>
			</a>
			<a href="{$module->activatedeactivateTaskUrl($workflow->id,0)}">
				<input type="button" class="crmButton save small" value="{$MOD.LBL_DIACTIVATE_ALL_BUTTON_LABEL}" id='close' />
			</a>
			<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_TASK_BUTTON_LABEL}" id='new_task' style="display:none;" />
		</td>
	</tr>
</table>
{include file='com_vtiger_workflow/TaskSummaryList.tpl'}