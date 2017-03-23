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
		<td class="colHeader small" width="6%"></td>
		<td class="colHeader small" width="70%">
			{$MOD.LBL_TASK}
		</td>
		<td class="colHeader small" width="12%">
			{$MOD.LBL_STATUS}
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
		<td class="listTableRow small">{if $task->active}{'Active'|@getTranslatedString:$MODULE_NAME}{else}{'Inactive'|@getTranslatedString:$MODULE_NAME}{/if}</td>
		<td class="listTableRow small">
			<a href="{$module->editTaskUrl($task->id)}">
				<img border="0" title="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}" alt="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}" \
					style="cursor: pointer;" id="expressionlist_editlink_{$task->id}" \
					src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
			</a>
			<a href="{$module->deleteTaskUrl($task->id)}" onclick="return confirm('{$APP.SURE_TO_DELETE}');">
				<img border="0" title="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}" alt="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}"\
					src="{'delete.gif'|@vtiger_imageurl:$THEME}" \
					style="cursor: pointer;" id="expressionlist_deletelink_{$task->id}"/>
			</a>
		</td>
	</tr>
	{/foreach}
</table>