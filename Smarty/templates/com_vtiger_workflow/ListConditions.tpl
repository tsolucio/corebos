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
<script>
function setTabToggle(h, s) {
	//allow switching tabs only if seleted wf trigger is `Schedule` or `System` otherwise `Record Set` tab stays disabled
	var selectedTrigger = $('input[name="execution_condition"]:checked').val();
	if(selectedTrigger=='ON_SCHEDULE' || selectedTrigger=='MANUAL') {
		document.getElementById(h).classList.add('slds-hide');
		document.getElementById(h).classList.remove('slds-show');
		document.getElementById(h+'-tab').classList.remove('slds-is-active');
		document.getElementById(s).classList.add('slds-show');
		document.getElementById(s).classList.remove('slds-hide');
		document.getElementById(s+'-tab').classList.add('slds-is-active');
	}
}

//switch to conditions tab if tab-records is shown while changing selected trigger
$('input[name="execution_condition"]').click(function(){
	if($(this).val()!='ON_SCHEDULE' && $(this).val()!='MANUAL') {
		document.getElementById('tab-records').classList.add('slds-hide');
		document.getElementById('tab-records').classList.remove('slds-show');
		document.getElementById('tab-records-tab').classList.remove('slds-is-active');
		document.getElementById('tab-records-tab').classList.add('disabled');
		document.getElementById('tab-conditions').classList.add('slds-show');
		document.getElementById('tab-conditions').classList.remove('slds-hide');
		document.getElementById('tab-conditions-tab').classList.add('slds-is-active');
	} else {
		document.getElementById('tab-records-tab').classList.remove('disabled');
		//enable button "Launch Now" only if System trigger is selected
		if($(this).val()!='MANUAL') {
			$('.btn-launch_now').attr("disabled", true);
		}
		else {
			$('.btn-launch_now').attr("disabled", false);
		}
	}
});
</script>

<div class="slds-tabs_default" style="height:12rem">
	<div class="slds-tabs_default">
		<ul class="slds-tabs_default__nav" role="tablist">
		<li id="tab-conditions-tab" class="slds-tabs_default__item slds-is-active" title="{$MOD.LBL_CONDITIONS}" role="presentation">
			<a class="slds-tabs_default__link" href="javascript:setTabToggle('tab-records', 'tab-conditions');" role="tab" tabindex="0" aria-selected="true" aria-controls="tab-default-1" id="tab-default-1__item">
			<span class="slds-tabs__left-icon">
				<span class="slds-icon_container slds-icon-standard-flow" title="{$MOD.LBL_CONDITIONS}">
				<svg class="slds-icon slds-icon_small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#rules"></use>
				</svg>
				</span>
			</span>{$MOD.LBL_CONDITIONS}</a>
		</li>
		<li id="tab-records-tab" class="slds-tabs_default__item" title="{'Record Set'|@getTranslatedString:$MODULE_NAME}" role="presentation">
			<a class="slds-tabs_default__link" href="javascript:setTabToggle('tab-conditions', 'tab-records');" role="tab" tabindex="-1" aria-selected="false" aria-controls="tab-default-2" id="tab-default-2__item">
			<span class="slds-tabs__left-icon">
				<span class="slds-icon_container slds-icon-standard-case" title="{'Record Set'|@getTranslatedString:$MODULE_NAME}">
				<svg class="slds-icon slds-icon_small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#tabset"></use>
				</svg>
				</span>
			</span>{'Record Set'|@getTranslatedString:$MODULE_NAME}</a>
		</li>
		</ul>
		<div id="tab-conditions" class="slds-tabs_default__content slds-show" role="tabpanel" aria-labelledby="tab-default-1__item">
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="small cblds-t-align_right" align="right">
					<span id="workflow_loading" style="display:none">
					<b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
					</span>
					<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_CONDITION_GROUP_BUTTON_LABEL}" id="save_conditions_add" style='display: none;'/>
				</td>
			</tr>
		</table>
		<br>
		<div id="save_conditions"></div>
		<br>
		{include file="com_vtiger_workflow/FieldExpressions.tpl"}
		</div>
		<div id="tab-records" class="slds-tabs_default__content slds-hide" role="tabpanel" aria-labelledby="tab-default-2__item">
			<fieldset class="slds-form-element">
			<legend class="slds-form-element__legend slds-form-element__label">{'Select where to get the records from'|@getTranslatedString:$MODULE_NAME}</legend>
			<div class="slds-form-element__control">
			<span class="slds-radio slds-p-top_xx-small">
			<input type="radio" id="radio-5" value="radio-5" name="options" checked="" />
			<label class="slds-radio__label" for="radio-5">
			<span class="slds-radio_faux"></span>
			<span class="slds-form-element__label">
				<span style="width:150px;display:inline-block;">{'cbQuestion'|@getTranslatedString:'cbQuestion'}</span>
				<input type='hidden' class='small' name="cbquestion_type" id="cbquestion_type" value="cbQuestion">
				<input id="cbquestion" name="cbquestion" type="hidden" value="">
				<input
					id="cbquestion_display"
					name="cbquestion_display"
					readonly
					type="text"
					style="border:1px solid #bababa;"
					onclick='return vtlib_open_popup_window("","cbquestion", "cbQuestion","");'
					value="">&nbsp;
				<span class="slds-icon_container slds-icon-standard-choice" title="choose" onclick='return vtlib_open_popup_window("","cbquestion", "cbQuestion","");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="add" onclick='return window.open("index.php?module=cbQuestion&action=EditView");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="clear" onclick="document.getElementById('cbquestion').value = ''; document.getElementById('cbquestion_display').value = ''; return false;">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
				</svg>
				</span>
			</label>
			</span>
			<span class="slds-radio slds-p-top_xx-small">
			<input type="radio" id="radio-6" value="radio-6" name="options" />
			<label class="slds-radio__label" for="radio-6">
			<span class="slds-radio_faux"></span>
			<span class="slds-form-element__label">
				<span style="width:150px;display:inline-block;">{'Record Set Mapping'|@getTranslatedString:'cbMap'}</span>
				<input type='hidden' class='small' name="recordset_type" id="recordset_type" value="cbMap">
				<input id="recordset" name="recordset" type="hidden" value="">
				<input
					id="recordset_display"
					name="recordset_display"
					readonly
					type="text"
					style="border:1px solid #bababa;"
					onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=EditView&forfield=recordset&query=true&search=true&searchtype=BasicSearch&search_field=maptype&search_text=Record%20Set%20Mapping', '', 'width=640,height=602,resizable=0,scrollbars=0');"
					value="">&nbsp;
				<span class="slds-icon_container slds-icon-standard-choice" title="choose" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=EditView&forfield=recordset&query=true&search=true&searchtype=BasicSearch&search_field=maptype&search_text=Record%20Set%20Mapping', '', 'width=640,height=602,resizable=0,scrollbars=0');">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="add" onclick='return window.open("index.php?module=cbMap&action=EditView");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="clear" onclick="document.getElementById('recordset').value = ''; document.getElementById('recordset_display').value = ''; return false;">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
				</svg>
				</span>
			</span>
			</label>
			</span>
			<span class="slds-radio slds-p-top_xx-small">
			<input type="radio" id="radio-7" value="radio-7" name="options" />
			<label class="slds-radio__label" for="radio-7">
			<span class="slds-radio_faux"></span>
			<span class="slds-form-element__label">
				<span style="width:150px;display:inline-block;">{'Record'|@getTranslatedString}</span>
				<input type='hidden' class='small' name="onerecord_type" id="onerecord_type" value={$workflow->moduleName}>
				<input id="onerecord" name="onerecord" type="hidden" value="">
				<input
					id="onerecord_display"
					name="onerecord_display"
					readonly
					type="text"
					style="border:1px solid #bababa;"
					onclick='return vtlib_open_popup_window("","onerecord","{$workflow->moduleName}","");'
					value="">&nbsp;
				<span class="slds-icon_container slds-icon-standard-choice" title="choose" onclick='return vtlib_open_popup_window("","onerecord","{$workflow->moduleName}","");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="add" onclick='return window.open("index.php?module={$workflow->moduleName}&action=EditView");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
				</svg>
				</span>
				<span class="slds-icon_container slds-icon-standard-choice" title="clear" onclick="document.getElementById('onerecord').value = ''; document.getElementById('onerecord_display').value = ''; return false;">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
				</svg>
				</span>
			</span>
			</label>
			</span>
			</div>
			<span class="slds-p-top_small slds-align_absolute-center">
			<button class="slds-button slds-button_success btn-launch_now">Launch Now</button>
			</span>
			</fieldset>
		</div>
	</div>
</div>
 <br>
