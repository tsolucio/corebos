{*<!--
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
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
{literal}
	var searchConditions = [
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Condition Expression",
		 "columncondition":"or"},
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Condition Query",
		 "columncondition":""}
	];
	var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
	var SpecialSearch = encodeURI(advSearch);
{/literal}
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/upserttask.js" type="text/javascript" charset="utf-8"></script>
<div class="slds-grid slds-gutters slds-p-horizontal_x-large slds-grid_vertical-align-center">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label" for="form-element-01">{'Select Module To Upsert'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					<select class="slds-select" id="upsert_module" name="upsert_module"></select>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label" for="form-element-01">{'Select Condition'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					<input id="bmapid" name="bmapid" type="hidden" class="slds-input" value="{$task->bmapid}">
					<input id="bmapid_display" name="bmapid_display" readonly="" class="slds-input" style="border:1px solid #bababa;" type="text" value="{$task->bmapid_display}" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch, 'vtlibui10wf', cbPopupWindowSettings);" style="cursor:hand;cursor:pointer">
					<span class="slds-form-element__addon" id="fixed-text-addon-post">
						<button type="image" class="slds-button" alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
							<svg class="slds-icon slds-icon_small slds-icon-text-light" aria-hidden="true" >
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
							</svg>
						</button>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>

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
		<button class="slds-button slds-button_success" id="save_fieldvaluemapping_add" style='display: none;'> {$MOD.LBL_ADD_FIELD} </button>
	</div>
</div>

{include file="com_vtiger_workflow/FieldExpressions.tpl"}
<br>
<input type="hidden" name="field_value_mapping" value="" id="save_fieldvaluemapping_json"/>
<div id="dump" style="display:none;"></div>
<div id="save_fieldvaluemapping"></div>