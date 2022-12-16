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
					<input id="filename" class="slds-input" name="imagesvalue" value="{$task->imagesvalue}" onfocus="wfeditexptype($(this), { 'name':'string' })" onchange="setwfexptype()" style="border: 1px solid #bababa">
					<input type="hidden" name="exptype" id="exptype" value="{$task->exptype}"/>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label">{'Position X'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					<input type="text" name="imagesx" id="imagesx" class="slds slds-input" value="{$task->imagesx}">
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
				<label class="slds-form-element__label">{'Position Y'|@getTranslatedString}</label>
				<div class="slds-form-element__control slds-input-has-fixed-addon">
					<input type="text" name="imagesy" id="imagesy" class="slds slds-input" value="{$task->imagesy}">
				</div>
			</div>
		</div>
	</div>
</div>