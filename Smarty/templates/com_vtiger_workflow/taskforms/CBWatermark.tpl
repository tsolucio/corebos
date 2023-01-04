{*<!--
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	var moduleName = '{$entityName}';
	var exptype = {$task->exptype|json_encode}
</script>
<script src="modules/com_vtiger_workflow/resources/wfWatermark.js" type="text/javascript" charset="utf-8"></script>
<div class="slds-grid slds-gutters slds-p-horizontal_x-large slds-grid_vertical-align-center">
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label">{'Watermark Value'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					<input id="filename" class="slds-input" name="wmImageValue" value="{$task->wmImageValue}" onfocus="wfeditexptype($(this), { 'name':'string' })" onchange="setwfexptype()" style="border: 1px solid #bababa">
					<input type="hidden" name="exptype" id="exptype" value="{$task->exptype}"/>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label">{'image field name'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					<input type="text" name="imagefieldName" id="imagefieldName" class="slds slds-input" value="{$task->imagefieldName}">
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label">{'Water Mark Size'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					<input type="text" name="wmSize" id="wmSize" class="slds slds-input" value="{$task->wmSize}">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-gutters slds-p-horizontal_x-large slds-grid_vertical-align-center">
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label">{'Water Mark Position'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					{* <input type="text" name="wmPosition" id="wmPosition" class="slds slds-input" value="{$task->wmPosition}"> *}
					<select name="wmPosition" id="wmPosition" class="slds slds-input" value="{$task->wmPosition}">
						<option value="center">{'Center'|@getTranslatedString}</option>
						<option value="top">{'Top'|@getTranslatedString}</option>
						<option value="bottom">{'Bottom'|@getTranslatedString}</option>
						<option value="right">{'Right'|@getTranslatedString}</option>
						<option value="left">{'Left'|@getTranslatedString}</option>
						<option value="topright">{'Top Right'|@getTranslatedString}</option>
						<option value="topleft">{'Top Left'|@getTranslatedString}</option>
						<option value="bottomleft">{'Bottom Left'|@getTranslatedString}</option>
						<option value="bottomright">{'Bottom Right'|@getTranslatedString}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>