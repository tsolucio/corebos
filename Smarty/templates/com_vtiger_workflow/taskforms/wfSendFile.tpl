{*<!--
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
var credentialid_display = {$task->credentialid_display|json_encode}
var credentialid = {$task->credentialid|json_encode}
var filename = {$task->filename|json_encode}
var exptype = {$task->exptype|json_encode}
var wfexeexppressions = null;
</script>
<script src="modules/com_vtiger_workflow/resources/wfSendFile.js" type="text/javascript" charset="utf-8"></script>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b> {$MOD.LBL_SELECT_CREDENTIAL}: </b></span>
	</div>
	<div class="slds-col slds-size_5-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
			<input id="credentialid" name="credentialid" type="hidden">
			<input id="credentialid_display" class="slds-input" name="credentialid_display" readonly="" style="border:1px solid #bababa;" type="text" value="" onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=credentialid&srcmodule=GlobalVariable', 'vtlibui10wf', cbPopupWindowSettings);">
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_1-of-12 slds-text-align_right slds-p-around_x-small" style="margin-left: -50px;">
		<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|getTranslatedString}" type="button" onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=credentialid&srcmodule=GlobalVariable', 'vtlibui10wf', cbPopupWindowSettings);">
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
			</svg>
			<span class="slds-assistive-text">{'LBL_SELECT'|getTranslatedString}</span>
		</button>
		<button class="slds-button slds-button_icon" title="{'LBL_CLEAR'|getTranslatedString}" type="button" onClick="this.form.credentialid.value=''; this.form.credentialid_display.value=''; return false;">
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
			</svg>
			<span class="slds-assistive-text">{'LBL_CLEAR'|getTranslatedString}</span>
		</button>
	</div>
</div>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b> {$MOD.FILENAME}: </b></span>
	</div>
	<div class="slds-col slds-size_5-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
			<input id="filename" class="slds-input" name="filename" value="{$task->filename}" onfocus="wfeditexptype($(this), { 'name':'string' })" onchange="setwfexptype()" style="border: 1px solid #bababa">
			<input type="hidden" name="exptype" id="exptype" value="{$task->exptype}"/>
			</div>
		</div>
	</div>
</div>