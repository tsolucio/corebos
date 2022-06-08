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

<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped" style="width:98%;margin: 0.5rem auto;" id='expressionlist'>
<thead>
	<tr class="slds-line-height_reset">
		<th scope="col" width="5%"></th>
		<th scope="col" width="32%">
			{$MOD.LBL_TASK}
		</th>
		<th scope="col" width="24%">
			{$MOD.LBL_TYPE}
		</th>
		<th scope="col" width="9%">
			{$MOD.LBL_STATUS}
		</th>
		<th scope="col" width="9%">
			{$MOD.LBL_CONDITIONS}
		</th>
		<th scope="col" width="9%">
			{$MOD.LBL_DELAY}
		</th>
		<th scope="col" width="12%">
			{$MOD.LBL_LIST_TOOLS}
		</th>
	</tr>
</thead>
<tbody>
	{foreach item=task from=$tasks name=wftasks}
	<tr class="slds-hint-parent">
		<td>{$task->executionorder}
		{if not $smarty.foreach.wftasks.first}
			&nbsp;<a href="javascript:moveWorkflowTaskUpDown('UP','{$task->id}')" title="{'LBL_MOVE'|@getTranslatedString:'Settings'} {'LBL_UP'|@getTranslatedString:'Settings'}"><img src="{'up_layout.gif'|@vtiger_imageurl:$THEME}" border="0"></a>
		{/if}
		{if not $smarty.foreach.wftasks.last}
			&nbsp;<a href="javascript:moveWorkflowTaskUpDown('DOWN','{$task->id}')" title="{'LBL_MOVE'|@getTranslatedString:'Settings'} {'LBL_DOWN'|@getTranslatedString:'Settings'}"><img src="{'down_layout.gif'|@vtiger_imageurl:$THEME}" border="0" ></a>
		{/if}
		</td>
		<td>{$task->summary|@to_html}</td>

		<td>{get_class($task)|@getTranslatedString:$MODULE_NAME}</td>
		<td>{if $task->active}{'Active'|@getTranslatedString:$MODULE_NAME}{else}{'Inactive'|@getTranslatedString:$MODULE_NAME}{/if}</td>
		<td> {if empty($task->test)}{'LBL_NO'|@getTranslatedString:$MODULE_NAME}{else}{'LBL_YES'|@getTranslatedString:$MODULE_NAME}{/if}</td>
		<td>
			{if empty($task->trigger)}
				0
			{else}
				{if isset($task->trigger['days'])}
					{if $task->trigger['days'] eq abs($task->trigger['days'])}
						{abs($task->trigger['days'])} {$MOD.LBL_DAYS} {'LBL_AFTER'|@getTranslatedString:$MODULE_NAME} {$task->trigger['field']}
					{else}
						{abs($task->trigger['days'])} {$MOD.LBL_DAYS} {'LBL_BEFORE'|@getTranslatedString:$MODULE_NAME} {$task->trigger['field']}
					{/if}
				{/if}
				{if isset($task->trigger['hours'])}
					{if $task->trigger['hours'] eq abs($task->trigger['hours'])}
						{abs($task->trigger['hours'])} {$MOD.LBL_HOURS} {'LBL_AFTER'|@getTranslatedString:$MODULE_NAME} {$task->trigger['field']}
					{else}
						{abs($task->trigger['hours'])} {$MOD.LBL_HOURS} {'LBL_BEFORE'|@getTranslatedString:$MODULE_NAME} {$task->trigger['field']}
					{/if}
				{/if}
				{if isset($task->trigger['mins'])}
					{if $task->trigger['mins'] eq abs($task->trigger['mins'])}
						{abs($task->trigger['mins'])} {'LBL_MINUTES'|@getTranslatedString:'CronTasks'} {'LBL_AFTER'|@getTranslatedString:$MODULE_NAME} {$task->trigger['field']}
					{else}
						{abs($task->trigger['mins'])} {'LBL_MINUTES'|@getTranslatedString:'CronTasks'} {'LBL_BEFORE'|@getTranslatedString:$MODULE_NAME} {$task->trigger['field']}
					{/if}
				{/if}
			{/if}
		</td>
		<td>
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
</tbody>
</table>
