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
<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    var moduleName = '{$entityName}';
	var moduleLabel = '{$entityName|@getTranslatedString:$entityName}';
    {if $task->field_value_mapping}
        var fieldvaluemapping = JSON.parse('{$task->field_value_mapping|escape:'quotes'}');
    {else}
        var fieldvaluemapping = null;
    {/if}
	var selectedEntityType = '{$task->entity_type}';
	var createEntityHeaderTemplate = '<input type="button" class="slds-button slds-button_success" value="'+"{'LBL_ADD_FIELD'|@getTranslatedString:$MODULE_NAME}"+ '" id="save_fieldvaluemapping_add" />';
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/createentitytaskscript.js" type="text/javascript" charset="utf-8"></script>

<div class="slds-grid slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span> <b> {'LBL_ENTITY_TYPE'|@getTranslatedString:$MODULE_NAME} </b> </span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="hidden" value='{$task->reference_field}' name='reference_field' id='reference_field' />
			</div>
			<span id="entity_type-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" ></span>
				<div class="slds-form-element__control">
					<div class="slds-select_container">
						<select name="entity_type" id="entity_type" class="slds-select slds-page-header__meta-text" style="display:none;">
							<option value=''>{'LBL_SELECT_ENTITY_TYPE'|@getTranslatedString:$MODULE_NAME}</option>
						</select>
					</div>
				</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-grid_vertical-align-center slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		<span id="workflow_loading" style="display:none">
			<b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" >
		</span>
		<span id="save_fieldvaluemapping_add-busyicon" style="display:none"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" ></span>
		<span id="save_fieldvaluemapping_add_wrapper"></span>
	</div>
</div>
<div class="slds-grid slds-grid_vertical-align-center slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		{include file="com_vtiger_workflow/FieldExpressions.tpl"}
			<input type="hidden" class="slds-input" name="field_value_mapping" value="" id="save_fieldvaluemapping_json"/>
				<div id="dump" style="display:none;"></div>
					<div id="save_fieldvaluemapping">
						<div class="slds-grid slds-grid_vertical-align-center">
							<div class="slds-col slds-p-around_x-small">
								<img width="61" height="60" src="{'empty.jpg'|@vtiger_imageUrl:$THEME}">
							</div>
							<div class="slds-col slds-p-around_x-small">
								<span class="genHeaderSmall">{'LBL_NO_ENTITIES_FOUND'|@getTranslatedString:$MODULE_NAME}</span>
							</div>
						</div>
					</div>
	</div>
</div>
<div class="slds-grid slds-grid_vertical-align-center">
	<div class="slds-col slds-p-around_x-small">
		<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_warning" role="alert">
			<h2> {'LBL_CREATE_ENTITY_NOTE_BUSINESSMAPS'|@getTranslatedString:$MODULE_NAME} </h2>
		</div>
		<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_warning" role="alert">
			<h2> {'LBL_CREATE_ENTITY_NOTE_ORDER_MATTERS'|@getTranslatedString:$MODULE_NAME} </h2>
		</div>
	</div>
</div>