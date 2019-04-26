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

<table class="listTable" width="100%" border="0" cellspacing="1" cellpadding="5" id='expressionlist'>
	<tr>
		<td class="colHeader small" width="5%"></td>
		<td class="colHeader small" width="32%">
			{$MOD.LBL_TASK}
		</td>
		<td class="colHeader small" width="24%">
			{$MOD.LBL_TYPE}
		</td>
		<td class="colHeader small" width="9%">
			{$MOD.LBL_STATUS}
		</td>
		<td class="colHeader small" width="9%">
			{$MOD.LBL_CONDITIONS}
		</td>
		<td class="colHeader small" width="9%">
			{$MOD.LBL_DELAY}
		</td>
		<td class="colHeader small" width="12%">
			{$MOD.LBL_LIST_TOOLS}
		</td>
	</tr>
	{foreach item=task from=$tasks name=wftasks}
	<tr>
		<td class="listTableRow small">{$task->executionorder}
		{if not $smarty.foreach.wftasks.first}
			&nbsp;<a href="javascript:moveWorkflowTaskUpDown('UP','{$task->id}')" title="{'LBL_MOVE'|@getTranslatedString:'Settings'} {'LBL_UP'|@getTranslatedString:'Settings'}"><img src="{'up_layout.gif'|@vtiger_imageurl:$THEME}" border="0"></a>
		{/if}
		{if not $smarty.foreach.wftasks.last}
			&nbsp;<a href="javascript:moveWorkflowTaskUpDown('DOWN','{$task->id}')" title="{'LBL_MOVE'|@getTranslatedString:'Settings'} {'LBL_DOWN'|@getTranslatedString:'Settings'}"><img src="{'down_layout.gif'|@vtiger_imageurl:$THEME}" border="0" ></a>
		{/if}
		</td>
		<td class="listTableRow small">{$task->summary|@to_html}</td>

		<td class="listTableRow small">{get_class($task)|@getTranslatedString:$MODULE_NAME}</td>
		<td class="listTableRow small">{if $task->active}{'Active'|@getTranslatedString:$MODULE_NAME}{else}{'Inactive'|@getTranslatedString:$MODULE_NAME}{/if}</td>
		<td class="listTableRow small"> {if empty($task->test)}{'LBL_NO'|@getTranslatedString:$MODULE_NAME}{else}{'LBL_YES'|@getTranslatedString:$MODULE_NAME}{/if}</td>
		<td class="listTableRow small">
			{if empty($task->trigger)}
				0
			{else}
				{if $task->trigger['days'] eq abs($task->trigger['days'])}
					{abs($task->trigger['days'])} {$MOD.LBL_DAYS} {'LBL_AFTER'|@getTranslatedString:$MODULE_NAME} {$task->trigger['field']}
				{else}
					{abs($task->trigger['days'])} {$MOD.LBL_DAYS} {'LBL_BEFORE'|@getTranslatedString:$MODULE_NAME}   {$task->trigger['field']}
				{/if}
			{/if}
		</td>
		<td class="listTableRow small">
			<a href="{$module->editTaskUrl($task->id)}">
				<span class="slds-icon_container slds-icon_container_circle slds-icon-action-edit" title="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}">
					<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}</span>
				</span>
			</a>
			{if empty($task->active)}
			<a href="{$module->onoffTaskUrl($task->id)}">
				<span class="slds-icon_container slds-icon_container_circle slds-icon-action-approval" title="{'LBL_ACTIVATE'|@getTranslatedString:$MODULE_NAME}">
					<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#approval"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_ACTIVATE'|@getTranslatedString:$MODULE_NAME}</span>
				</span>
			</a>
			{elseif $task->active eq 1}
			<a href="{$module->onoffTaskUrl($task->id, $task->active)}">
				<span class="slds-icon_container slds-icon_container_circle slds-icon-action-close" title="{'LBL_DEACTIVATE'|@getTranslatedString:$MODULE_NAME}">
					<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_DEACTIVATE'|@getTranslatedString:$MODULE_NAME}</span>
				</span>
			</a>
			{/if}
			<a href="{$module->deleteTaskUrl($task->id)}" onclick="return confirm('{$APP.SURE_TO_DELETE}');">
				<span class="slds-icon_container slds-icon_container_circle slds-icon-action-delete" title="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}">
					<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}</span>
				</span>
			</a>

		</td>
	</tr>
	{/foreach}
</table>
