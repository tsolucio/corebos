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

<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	var moduleName = '{$entityName}';
	{if isset($task->field_value_mapping)}
		{assign var="fvmdoubleslashes" value=$task->field_value_mapping|replace:'\"':'\\\\"'}
		{if empty($fvmdoubleslashes)}
			var fieldvaluemapping = null;
		{else}
			var fieldvaluemapping = JSON.parse('{$fvmdoubleslashes|escape:'quotes'}');
		{/if}
	{else}
		var fieldvaluemapping = null;
	{/if}
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/updatefieldstaskscript.js" type="text/javascript" charset="utf-8"></script>

<div class="slds-grid slds-p-horizontal_x-large slds-grid_vertical-align-center slds-border_top slds-border_bottom">
	<div class="slds-col slds-size_1-of-2 slds-p-around_x-small">
		<span>
			<strong>{$MOD.LBL_SET_FIELD_VALUES}</strong>
		</span>
	</div>
	<div class="slds-col slds-size_1-of-2 slds-p-around_x-small slds-text-align_right">
		<span id="workflow_loading" style="display:none">
			<b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
		</span>
		<span id="save_fieldvaluemapping_add-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
		<input type="button" class="slds-button slds-button_success" value="{$MOD.LBL_ADD_FIELD}" id="save_fieldvaluemapping_add" style='display: none;'/>
	</div>
</div>
{include file="com_vtiger_workflow/FieldExpressions.tpl"}
<div class="slds-grid slds-gutters slds-p-horizontal_x-large slds-grid_vertical-align-center">
	<div class="slds-col slds-size_1-of-1 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control slds-m-top_medium">
				<div class="slds-checkbox">
					<input type="checkbox" name="launchrelwf" id="launchrelwf" {if $task->launchrelwf}checked{/if}/>
					<label class="slds-checkbox__label" for="launchrelwf">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-form-element__label">{$MOD.launchrelwf}</span>
					</label>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<input type="hidden" name="field_value_mapping" value="" id="save_fieldvaluemapping_json"/>
<div id="dump" style="display:none;"></div>
<div id="save_fieldvaluemapping"></div>