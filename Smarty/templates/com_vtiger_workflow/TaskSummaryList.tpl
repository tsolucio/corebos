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

<table class="listTable slds-table slds-table--bordered" id='expressionlist'>
	<thead>
		<tr>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate">#</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate">{$MOD.LBL_TASK}</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate">{$MOD.LBL_STATUS}</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate">{$MOD.LBL_LIST_TOOLS}</span>
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach item=task from=$tasks name=wftasks}
		<tr class="slds-hint-parent slds-line-height--reset">
			<th scope="row">
				<div class="slds-truncate">
			{$task->executionorder}
			{if not $smarty.foreach.wftasks.first}
				&nbsp;
				<a href="javascript:moveWorkflowTaskUpDown('UP','{$task->id}')" title="{'LBL_MOVE'|@getTranslatedString:'Settings'} {'LBL_UP'|@getTranslatedString:'Settings'}">
					<img src="{'up_layout.gif'|@vtiger_imageurl:$THEME}" border="0">
				</a>
			{/if}
			{if not $smarty.foreach.wftasks.last}
				&nbsp;
				<a href="javascript:moveWorkflowTaskUpDown('DOWN','{$task->id}')" title="{'LBL_MOVE'|@getTranslatedString:'Settings'} {'LBL_DOWN'|@getTranslatedString:'Settings'}">
					<img src="{'down_layout.gif'|@vtiger_imageurl:$THEME}" border="0" >
				</a>
			{/if}
				</div>
			</th>
			<th scope="row">
				<div class="slds-truncate">{$task->summary|@to_html}</div>
			</th>
			<th scope="row">
				<div class="slds-truncate">{if $task->active}{'Active'|@getTranslatedString:$MODULE_NAME}{else}{'Inactive'|@getTranslatedString:$MODULE_NAME}{/if}</div>
			</th>
			<th scope="row">
				<div class="slds-truncate">
					<a href="{$module->editTaskUrl($task->id)}">
						<img border="0" title="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}" alt="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}" \  id="expressionlist_editlink_{$task->id}" \ src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
					</a>
					<a href="{$module->deleteTaskUrl($task->id)}" onclick="return confirm('{$APP.SURE_TO_DELETE}');">
						<img border="0" title="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}" alt="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}"\ src="{'delete.gif'|@vtiger_imageurl:$THEME}" \ id="expressionlist_deletelink_{$task->id}"/>
					</a>
				</div>
			</th>
		</tr>
		{/foreach}
	</tbody>
</table>