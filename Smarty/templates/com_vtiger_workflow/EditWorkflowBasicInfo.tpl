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

<div class="slds-page-header">
<div class="slds-page-header__row">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_SUMMARY}">
				{$MOD.LBL_SUMMARY}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
</div>
<div class="slds-grid slds-gutters">
<div class="slds-col slds-size_1-of-2">
<fieldset class="slds-form-element slds-form-element_compound">
	<div class="slds-form-element slds-form-element_horizontal" id="account_block" style="margin-left:0.5rem;" >
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
		<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>
		{$APP.LBL_UPD_DESC}
		</label>
		<div class="slds-form-element__control slds-m-top_x-small">
			<input type="text" name="description" id="save_description" class="slds-input slds-page-header__meta-text" value="{$workflow->description}">
			<input type="hidden" name="hidden_description" id="hidden_description" value="{$workflow->description}">
		</div>
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small">
		&nbsp;&nbsp;{$APP.LBL_MODULE}
		</label>
		<div class="slds-form-element__control slds-m-top_x-small slds-page-header__meta-text">
			<input type="text" readonly class="slds-input" value="{$workflow->moduleName|@getTranslatedString:$workflow->moduleName}">
		</div>
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="purpose">
		&nbsp;&nbsp;{'LBL_WFPURPOSE'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control slds-m-top_x-small">
			<textarea id='purpose' name='purpose' class="slds-textarea slds-page-header__meta-text">{$workflow->purpose}</textarea>
		</div>
	</div>
</fieldset>
</div>
<div class="slds-col slds-size_1-of-2">
<fieldset class="slds-form-element slds-form-element_compound">
	<div class="slds-form-element slds-form-element_horizontal" id="account_block" style="margin-left:0.5rem;" >
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
		{'LBL_START_DATE'|@getTranslatedString:'com_vtiger_workflow'}&nbsp;
		{include file='com_vtiger_workflow/WorkflowDateRangeHelp.tpl'}
		</label>
		<div class="slds-form-element__control slds-m-top_x-small">
			<input name="wfstarton" id="jscal_field_wfstarton" type="text" class="slds-input slds-page-header__meta-text" size="16" maxlength="16" value="{$workflow->wfstarton}" style="width:50%">
			<input name="timefmt_wfstarton" id="inputtimefmt_wfstarton" type="hidden" value="24">
			{include file='Components/DateButton.tpl' fldname='wfstarton'}
			<script type="text/javascript">
				Calendar.setup ({
					inputField : "jscal_field_wfstarton", ifFormat : "%Y-%m-%d %H:%M", inputTimeFormat : "24",
					showsTime : true, timeFormat : "24",
					button : "jscal_trigger_wfstarton", singleClick : true, step : 1
				});
			</script>
		</div>
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small">
		{'LBL_END_DATE'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control slds-m-top_x-small slds-page-header__meta-text">
			<input name="wfendon" id="jscal_field_wfendon" type="text" class="slds-input slds-page-header__meta-text" size="16" maxlength="16" value="{$workflow->wfendon}" style="width:50%">
			<input name="timefmt_wfendon" id="inputtimefmt_wfendon" type="hidden" value="24">
			{include file='Components/DateButton.tpl' fldname='wfendon'}
			<script type="text/javascript">
				Calendar.setup ({
					inputField : "jscal_field_wfendon", ifFormat : "%Y-%m-%d %H:%M", inputTimeFormat : "24",
					showsTime : true, timeFormat : "24",
					button : "jscal_trigger_wfendon", singleClick : true, step : 1
				});
			</script>
		</div>
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="purpose">
		{'LBL_STATUS'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control slds-m-top_x-small">
			<select name="active" class="slds-select slds-page-header__meta-text" style="width:50%">
				<option value="true" {$selected_active}>{$MOD.LBL_ACTIVE}</option>
				<option value="false" {$selected_inactive}>{$MOD.LBL_INACTIVE}</option>
			</select>
		</div>
	</div>
</fieldset>
</div>
</div>
