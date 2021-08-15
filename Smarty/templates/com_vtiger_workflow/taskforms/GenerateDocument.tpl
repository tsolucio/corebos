{*<!--
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author       : ATConsulting, Mikel Kasneci.
 *************************************************************************************************//
-->*}
<script type='text/javascript' src='include/Webservices/WSClientp.js'></script>
<script type='text/javascript' charset='utf-8'>
var moduleName = '{$entityName}';
</script>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_1-of-1 slds-p-around_x-small">
		<div class="slds-media__body">
			<div class="slds-page-header__name">
				<div class="slds-page-header__name-title">
					<h1>
					<span class="slds-page-header__title slds-truncate" title="{'LBL_ACTION'|@getTranslatedString:'com_vtiger_workflow'}">{'LBL_ACTION'|@getTranslatedString:'com_vtiger_workflow'}</span>
					</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_1-of-8 slds-p-around_x-small">
		<span>
			<input type="radio" name="gdformat" value="1" {if isset($task->gdformat) && $task->gdformat eq 1}checked{/if}> ODT
		</span>
	</div>
	<div class="slds-col slds-size_1-of-8 slds-p-around_x-small">
		<span>
			<input type="radio" name="gdformat" value="0" {if isset($task->gdformat) && $task->gdformat eq 0}checked{/if}> PDF
		</span>
	</div>
	<div class="slds-col slds-size_2-of-8 slds-p-around_x-small">
		<span>
			<div class="slds-form-element">
				<label class="slds-checkbox_toggle slds-grid">
					<span class="slds-form-element__label slds-m-bottom_none"><b>{'UpdatePrevious'|@getTranslatedString:'evvtgendoc'}</b></span>
						<input type="checkbox" id="updateOnChange" name="updateOnChange" value="true" {if $task->updateOnChange}checked{/if}>
							<span id="spanupdateOnChange" class="slds-checkbox_faux_container" aria-live="assertive">
							<span class="slds-checkbox_faux"></span>
							<span class="slds-checkbox_on"></span>
							<span class="slds-checkbox_off"></span>
						</span>
				</label>
			</div>
		</span>
	</div>
	<div class="slds-col slds-size_2-of-8 slds-p-around_x-small">
		<span>
			<div class="slds-form-element">
				<label class="slds-checkbox_toggle slds-grid">
					<span class="slds-form-element__label slds-m-bottom_none"><b>{'shareOnCreate'|@getTranslatedString:'evvtgendoc'}</b></span>
						<input type="checkbox" id="shareOnCreate" name="shareOnCreate" value="true" {if $task->shareOnCreate}checked{/if}>
							<span id="spanshareOnCreate" class="slds-checkbox_faux_container" aria-live="assertive">
							<span class="slds-checkbox_faux"></span>
							<span class="slds-checkbox_on"></span>
							<span class="slds-checkbox_off"></span>
						</span>
				</label>
			</div>
		</span>
	</div>
	<div class="slds-col slds-size_1-of-8 slds-p-around_x-small"></div>
</div>
<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_1-of-1 slds-p-around_x-small">
		<div class="slds-media__body">
			<div class="slds-page-header__name">
				<div class="slds-page-header__name-title">
					<h1>
					<span class="slds-page-header__title slds-truncate" title="{'Templates'|@getTranslatedString:'com_vtiger_workflow'}">{'Templates'|@getTranslatedString:'com_vtiger_workflow'}</span>
					</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_1-of-1 slds-p-around_x-small">
		<input type="hidden" id="gendoctemplate" name="gendoctemplate" value="{if !empty($task->gendoctemplate)}{$task->gendoctemplate}{/if}">
		<select class="slds-select" size="12" style="width:540px;overflow:auto;" id="gdtplsel" name="gdtplsel" multiple onchange="fillGenDocTemplate(this);"></select>
	</div>
</div>
<script type="text/javascript" charset="utf-8">
function fillGenDocTemplate(select) {
	document.getElementById('gendoctemplate').value = [...select.options].filter(option => option.selected).map(option => option.value).join(';');
}
var cbws=new cbWSClient('');
cbws.extendSession()
.then(function () {
	let selvalues = {if !empty($task->gendoctemplate)}'{$task->gendoctemplate}'.split(';'){else}[]{/if};
	cbws.doQuery("select id,notes_title from Documents where template=1 and template_for='{$entityName}' order by notes_title")
	.then(function (tpls) {
		let ts = document.getElementById('gdtplsel');
		for(var i = 0; i < tpls.length; i++) {
			var opt = document.createElement('option');
			opt.innerHTML = tpls[i].notes_title;
			let wsid = tpls[i].id.split('x');
			opt.value = wsid[1];
			opt.title = tpls[i].notes_title;
			opt.selected = (selvalues.indexOf(wsid[1])!=-1);
			ts.appendChild(opt);
		}
	});
})
</script>
