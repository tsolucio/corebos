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

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
var taskStatus = '{$task->status}';
var taskPriority = '{$task->priority}';
</script>

<script src="modules/com_vtiger_workflow/resources/createtodotaskscript.js" type="text/javascript" charset="utf-8"></script>

<div id="view">
	<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> {$MOD.LBL_TODO}</b></td>
		<td class='dvtCellInfo'><input type="text" name="todo" value="{$task->todo}" id="workflow_todo" class="form_input"></td>
	</tr>
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{$MOD.LBL_DESCRIPTION}</b></td>
		<td class='dvtCellInfo'><textarea name="description" rows="8" cols="40" class='detailedViewTextBox'>{$task->description}</textarea></td>
	</tr>
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{$MOD.LBL_STATUS}</b></td>
		<td class='dvtCellLabel'>
			<span id="task_status_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task_status" value="{$task->status}" name="status" class="small" style="display: none;"></select>
		</td>
	</tr> 
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{$MOD.Priority}</b></td>
		<td class='dvtCellLabel'>
			<span id="task_priority_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task_priority" value="{$task->priority}" name="priority" class="small" style="display: none;"></select>
		</td>
	</tr>
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{'LBL_ASSIGNED_TO'|@getTranslatedString}</b></td>
		<td class='dvtCellLabel'>
			<select id="assigned_user_id" name="assigned_user_id" class="small">
				<option value="">{'LBL_SELECT'|@getTranslatedString}</option>
				<optgroup label="{'LBL_USER'|@getTranslatedString}">
				{foreach from=$ASSIGNED_TO.user item=ASSIGNED_USER key=ASSIGNED_USER_KEY}
				 {if $ASSIGNED_USER != ''}
					<option value="{$ASSIGNED_USER_KEY}" {if $ASSIGNED_USER_KEY eq $task->assigned_user_id} selected="" {/if}>{$ASSIGNED_USER}</option>
				 {/if}
				{/foreach}
				</optgroup>
				<optgroup label="{'LBL_GROUP'|@getTranslatedString}">
				{foreach from=$ASSIGNED_TO.group item=ASSIGNED_USER key=ASSIGNED_USER_KEY}
				 {if $ASSIGNED_USER != ''}
					<option value="{$ASSIGNED_USER_KEY}" {if $ASSIGNED_USER_KEY eq $task->assigned_user_id} selected="" {/if}>{$ASSIGNED_USER}</option>
				 {/if}
				{/foreach}
				</optgroup>
				<optgroup label="{'LBL_SPECIAL_OPTIONS'|@getTranslatedString}">
					<option value="copyParentOwner" {if $task->assigned_user_id eq 'copyParentOwner'} selected="" {/if}>{'LBL_PARENT_OWNER'|@getTranslatedString}</option>
				</optgroup>
			</select>
		</td>
	</tr>
	<tr><td colspan="2"><hr size="1" noshade="noshade" /></td></tr>
	<tr>
		<td align="right"><b>{$MOD.LBL_TIME}</b></td>
		{if $task->time neq ''} 
			{assign var=now value=$task->time}
		{else}
			{assign var=now value=$USER_TIME}
		{/if}
		<td><input type="hidden" name="time" value="{$now}" id="workflow_time" style="width:60px" class="time_field"></td>
	</tr>
	<tr>
		<td align="right"><b>{$MOD.LBL_START_DATE}<br>{$MOD.LBL_DUE_DATE}</b></td>
		<td>
			<input type="text" name="days" value="{$task->days}" id="days" style="width:30px" class="small"> {$MOD.LBL_DAYS} 
			<select name="direction" class="small">
				<option {if $task->direction eq 'After'}selected{/if} value="After">{$MOD.LBL_AFTER}</option>
				<option {if $task->direction eq 'Before'}selected{/if} value="Before">{$MOD.LBL_BEFORE}</option>
			</select>
			<select name="datefield" value="{$task->datefield}" class="small">
			{foreach key=name item=label from=$dateFields}
				<option value='{$name}' {if $task->datefield eq $name}selected{/if}>
					{$label}
				</option>
			{/foreach}
			</select></td>
		</tr>
		<tr valign="top">
			<td align="right"><b>{$MOD.LBL_SENDNOTIFICATION}</b></td>
			<td><input type="checkbox" name="sendNotification" value="true" id="sendNotification" {if $task->sendNotification}checked{/if}></td>
		</tr>
	</table>
</div>
