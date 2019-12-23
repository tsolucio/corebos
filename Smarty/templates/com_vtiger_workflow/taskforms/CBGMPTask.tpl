{*<!--
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L.
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
 *************************************************************************************************//
-->*}
<script src='modules/com_vtiger_workflow/resources/vtigerwebservices.js' charset="utf-8"></script>

<div class="slds-form-element">
	<label class="slds-form-element__label" for="url_query">{'url_query'|@getTranslatedString:'com_vtiger_workflow'}</label>
	<div class="slds-form-element__control">
		<input type="text" class="slds-input" name="url_query" id="url_query" value="{$task->url_query}" />
	</div>
</div>
<div class="slds-grid slds-gutters slds-m-around_x-small">
	<div class="slds-col slds-size_3-of-12">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<div class="slds-combobox_container">
					<select id="modulefields" onchange="gmpaddVar(this);">
						<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_9-of-12">
		<div >
			<div class="slds-popover slds-popover_tooltip slds-nubbin_top-left" role="tooltip" id="help" style="max-width: unset;">
				<div class="slds-popover__body">{'gmp_url_explanation'|@getTranslatedString:'com_vtiger_workflow'}</div>
			</div>
		</div>
	</div>
</div>
<script>
var moduleName = '{$entityName}';
function gmpaddVar(obj) {
	let urlf = document.getElementById('url_query');
	let mf = document.getElementById('modulefields').value;
	let pieces = mf.split('-');
	urlf.value = urlf.value+'&'+pieces[1].substring(0, pieces[1].length-1)+'='+mf;
}
jQuery.ajax({
	method:'POST',
	url:'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getEmailTemplateVariables&module_from={$entityName}',
}).done(function(response) {
	var resp = jQuery.parseJSON(response);
	jQuery.each(resp,function(index,item){
		var option = new Option(item[0], item[1]);
		$('#modulefields').append($(option));
	});
});
</script>
