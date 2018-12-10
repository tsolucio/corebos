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
			<strong>{$MOD.LBL_CONDITIONS}</strong>
		</td>
		<td class="small cblds-t-align--right" align="right">
			<span id="workflow_loading" style="display:none">
			<b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
			</span>
			<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_CONDITION_GROUP_BUTTON_LABEL}" id="save_conditions_add" style='display: none;'/>
		</td>
	</tr>
</table>
{if $showreeval eq 'true'}
<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
	<td colspan=2><input type="checkbox" name="reevaluate" id="reevaluate" {if !$edit || !isset($task->reevaluate) || $task->reevaluate eq 1}checked{/if}>&nbsp;{$MOD.LBL_REEVALCONDITIONS}</td>
	</tr>
</table>
{/if}
<br>
<div id="save_conditions"></div>
<br>
{include file="com_vtiger_workflow/FieldExpressions.tpl"}
