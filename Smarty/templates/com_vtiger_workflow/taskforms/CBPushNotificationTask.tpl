{*<!--
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.
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

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="url_query">{'pushnoturl'|@getTranslatedString:'com_vtiger_workflow'}</label>
			<div class="slds-form-element__control">
				<input type="text" class="slds-input slds-page-header__meta-text" name="url_query" id="url_query" value="{$task->url_query}" onchange="pushurlchange()" />
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<div class="slds-combobox_container">
					<select id="modulefields" onchange="gmpaddVar(this);" class="slds-select slds-page-header__meta-text">
						<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<div class="slds-form-element__control slds-input-has-fixed-addon">
			<input id="evalurlid" name="evalurlid" type="hidden" value="" onchange="pushurlchange()">
			<input id="evalurlid_type" name="evalurlid_type" type="hidden" value="{if isset($workflow)}{$workflow->moduleName}{/if}">
			<input id="evalurlid_display" name="evalurlid_display" readonly type="text" style="border:1px solid #bababa;background-color:white;" class="slds-input" value=""  onClick='return vtlib_open_popup_window("new_task","evalurlid","com_vtiger_workflow","");'>
				<span class="slds-form-element__addon" onClick='return vtlib_open_popup_window("new_task","evalurlid","com_vtiger_workflow","");'>
					<svg class="slds-icon slds-icon slds-icon_small slds-icon-text-default" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
					</svg>
				</span>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_1-of-1 slds-p-around_small" id="evaluateurlresult"></div>
</div>

<script>
var moduleName = '{$entityName}';
function gmpaddVar(obj) {
	let urlf = document.getElementById('url_query');
	let mf = document.getElementById('modulefields').value;
	let pieces = mf.split('-');
	urlf.value = urlf.value+'&'+pieces[1].substring(0, pieces[1].length-1)+'='+mf;
	urlf.dispatchEvent(new Event('change'));
}
function pushurlchange() {
	let ev = document.getElementById('evalurlid').value;
	if (ev != '' && ev != 0) {
		let urlf = document.getElementById('url_query').value;
		{literal}
		params = `&template=${encodeURIComponent(urlf)}&${csrfMagicName}=${csrfMagicToken}`;
		{/literal}
		fetch(
			'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getMergedDescription&crmid='+ev,
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: params
			}
		).then(response => response.json().then(url => {
			document.getElementById('evaluateurlresult').innerHTML = url.replace(/\s+/g, '+');
		}));
	}
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
