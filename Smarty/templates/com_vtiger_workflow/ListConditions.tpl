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
<div class="slds-grid slds-gutters">
{if $RecordSetTab}
<div class="slds-col slds-size_1-of-2">
{else}
<div class="slds-col slds-size_1-of-1">
{/if}
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_CONDITIONS}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{$MOD.LBL_CONDITIONS}">
						<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#rules"></use>
						</svg>
						</span>
					</span>
					{$MOD.LBL_CONDITIONS}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
{if $RecordSetTab}
<div class="slds-col slds-size_1-of-2">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{'Record Set'|@getTranslatedString:$MODULE_NAME}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{'Record Set'|@getTranslatedString:$MODULE_NAME}">
						<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#dataset"></use>
						</svg>
						</span>
					</span>
					{'Record Set'|@getTranslatedString:$MODULE_NAME}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
{/if}
</div>
</div>
<div class="slds-grid slds-gutters">
	{if $RecordSetTab}
	<div class="slds-col slds-size_1-of-2 slds-page-header__meta-text">
	{else}
	<div class="slds-col slds-size_1-of-1 slds-page-header__meta-text">
	{/if}
		<div id="workflow_loading" class="slds-align_absolute-center" style="height:5rem;">
		<b>{$MOD.LBL_LOADING}</b>
		</div>
		<div id="startwhennoconditions" class="slds-align_absolute-center" style="height:5rem;display:none;">
			<button class="slds-button slds-button_neutral" type="button" id="startwhennoconditionsbutton">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
				</svg>
				{$MOD.LBL_NEW_CONDITION_GROUP_BUTTON_LABEL}
			</button>
		</div>
		<div id="save_conditions"></div>
		<br>
		{include file="com_vtiger_workflow/FieldExpressions.tpl"}
	</div>
{if $RecordSetTab}
	<div class="slds-col slds-size_1-of-2 slds-page-header__meta-text">
		<fieldset class="slds-form-element" id="fsmalaunch" style="{if $workflow->executionCondition eq 6 or $workflow->executionCondition eq 7}display:block;{else}display:none;{/if}">
			<legend class="slds-form-element__legend slds-form-element__label">{'Select where to get the records from'|@getTranslatedString:$MODULE_NAME}</legend>
			<div class="slds-form-element__control">
			<span class="slds-radio slds-p-top_xx-small">
				<input type="radio" id="radio-4" value="conditions" name="options" {if $workflow->options eq 'conditions' || $workflow->options eq ''}checked="checked"{/if} />
				<label class="slds-radio__label" for="radio-4">
				<span class="slds-radio_faux"></span>
				<span class="slds-form-element__label">
					<span style="width:150px;display:inline-block;">{$MOD.LBL_CONDITIONS}</span>
				</label>
			</span>
			<span class="slds-radio slds-p-top_xx-small">
			<input type="radio" id="radio-5" value="cbquestion" name="options" {if $workflow->options eq 'cbquestion'}checked="checked"{/if} />
			<label class="slds-radio__label" for="radio-5">
			<span class="slds-radio_faux"></span>
			<span class="slds-form-element__label">
				<span style="width:150px;display:inline-block;">{'cbQuestion'|@getTranslatedString:'cbQuestion'}</span>
				<input type='hidden' class='small' name="cbquestion_type" id="cbquestion_type" value="cbQuestion">
				<input id="cbquestion" name="cbquestion" type="hidden" value="{$workflow->cbquestion}">
				<input
					id="cbquestion_display"
					name="cbquestion_display"
					readonly
					type="text"
					style="border:1px solid #bababa;"
					onclick='return vtlib_open_popup_window("", "cbquestion", "cbQuestion", "{$workflow->cbquestion}");'
					value="{$workflow->cbquestiondisplay}">&nbsp;
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick='return vtlib_open_popup_window("", "cbquestion", "cbQuestion", "");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_ADD_ITEM'|getTranslatedString}" onclick='return window.open("index.php?module=cbQuestion&action=EditView");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('cbquestion').value=''; document.getElementById('cbquestion_display').value=''; return false;">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
				</svg>
				</span>
			</label>
			</span>
			<span class="slds-radio slds-p-top_xx-small">
			<input type="radio" id="radio-6" value="recordset" name="options" {if $workflow->options eq 'recordset'}checked="checked"{/if} />
			<label class="slds-radio__label" for="radio-6">
			<span class="slds-radio_faux"></span>
			<span class="slds-form-element__label">
				<span style="width:150px;display:inline-block;">{'Record Set Mapping'|@getTranslatedString:'cbMap'}</span>
				<input type='hidden' class='small' name="recordset_type" id="recordset_type" value="cbMap">
				<input id="recordset" name="recordset" type="hidden" value="{$workflow->recordset}">
				<input
					id="recordset_display"
					name="recordset_display"
					readonly
					type="text"
					style="border:1px solid #bababa;"
					onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=EditView&forfield=recordset&query=true&search=true&searchtype=BasicSearch&search_field=maptype&search_text=Record%20Set%20Mapping', '', cbPopupWindowSettings);"
					value="{$workflow->recordsetdisplay}">&nbsp;
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=EditView&forfield=recordset&query=true&search=true&searchtype=BasicSearch&search_field=maptype&search_text=Record%20Set%20Mapping', '', cbPopupWindowSettings);">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_ADD_ITEM'|getTranslatedString}" onclick='return window.open("index.php?module=cbMap&action=EditView");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('recordset').value=''; document.getElementById('recordset_display').value=''; return false;">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
				</svg>
				</span>
			</span>
			</label>
			</span>
			<span class="slds-radio slds-p-top_xx-small">
			<input type="radio" id="radio-7" value="onerecord" name="options"  {if $workflow->options eq 'onerecord'}checked="checked"{/if}  />
			<label class="slds-radio__label" for="radio-7">
			<span class="slds-radio_faux"></span>
			<span class="slds-form-element__label">
				<span style="width:150px;display:inline-block;">{'record'|@getTranslatedString}</span>
				<input type='hidden' class='small' name="onerecord_type" id="onerecord_type" value={$workflow->moduleName}>
				<input id="onerecord" name="onerecord" type="hidden" value="{$workflow->onerecord}">
				<input
					id="onerecord_display"
					name="onerecord_display"
					readonly
					type="text"
					style="border:1px solid #bababa;"
					onclick='return vtlib_open_popup_window("", "onerecord", "{$workflow->moduleName}", "{$workflow->onerecord}");'
					value="{$workflow->onerecorddisplay}">&nbsp;
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick='return vtlib_open_popup_window("", "onerecord", "{$workflow->moduleName}", "{$workflow->onerecord}");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_ADD_ITEM'|getTranslatedString}" onclick='return window.open("index.php?module={$workflow->moduleName}&action=EditView");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('onerecord').value=''; document.getElementById('onerecord_display').value=''; return false;">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
				</svg>
				</span>
			</span>
			</label>
			</span>
			</div>
			<span class="slds-p-top_small slds-align_absolute-center">
			<button class="slds-button slds-button_success" id="btnmalaunch" name="btnmalaunch" onclick="editWFSaveConditions();">{'Launch Now'|getTranslatedString:'com_vtiger_workflow'}</button>
			</span>
		</fieldset>
	</div>
{/if}
</div>
