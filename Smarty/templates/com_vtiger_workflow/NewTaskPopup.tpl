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

<!-- A popup to create a new task-->
<div id="new_task_popup" class='layerPopup' style="display:none;">
	<table class="slds-table slds-no-row-hover" width="100%" style="border-bottom: 1px solid #d4d4d4;">
		<tr class="slds-text-title--header">
			<th scope="col">
				<div class="slds-truncate moduleName">
					<b>{$MOD.LBL_CREATE_TASK}</b>
				</div>
			</th>
			<th scope="col" style="padding: .5rem;text-align: right;">
				<div class="slds-truncate">
					<a href="javascript:;" id="new_task_popup_close">
						<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
					</a>
				</div>
			</th>
		</tr>
	</table>

	<form action="index.php" method="get" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
		<div class="popup_content" align="left">
			{$MOD.LBL_CREATE_TASK_OF_TYPE}
			<select name="task_type" class="slds-select">
				{foreach item=taskType from=$taskTypes}
					<option value='{$taskType}'>
						{$taskType|@getTranslatedString:$module->name}
					</option>
				{/foreach}
			</select>
			<input type="hidden" name="module_name" value="{$workflow->moduleName}">
			<input type="hidden" name="save_type" value="new" id="save_type_new">
			<input type="hidden" name="module" value="{$module->name}" id="save_module">
			<input type="hidden" name="action" value="edittask" id="save_action">
			<input type="hidden" name="return_url" value="{$newTaskReturnUrl}" id="save_return_url">
			<input type="hidden" name="workflow_id" value="{$workflow->id}">
		</div>
		<!-- Buttons -->
		<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<tr>
				<td align="center">
					<input type="submit" class="slds-button_success slds-button--small slds-button" value="{$APP.LBL_CREATE_BUTTON_LABEL}" name="save" id='new_task_popup_save'/>
					<input type="button" class="slds-button--small slds-button slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " name="cancel" id='new_task_popup_cancel'/>
				</td>
			</tr>
		</table>
	</form>
</div>