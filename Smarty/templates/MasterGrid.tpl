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
 *************************************************************************************************/
-->*}
<script>
	if (MasterGridData === undefined) {
		var MasterGridData = Array();
	}
	if (MasterGridInsances === undefined) {
		var MasterGridInsances = Array();
	}
	MasterGridInsances.push({$linkid});
	mg[{$linkid}] = new MasterGrid();
	mg[{$linkid}].id = {$linkid};
	mg[{$linkid}].module = '{$module}';
	mg[{$linkid}].relatedfield = '{$relatedfield}';
	mg[{$linkid}].fields = '{$GridFields|json_encode}';
	mg[{$linkid}].data = '{$GridData}';
	window.addEventListener('DOMContentLoaded', (event) => {
		mg[{$linkid}].Init();
	});
</script>
<form id="fake__form"></form>
<form id="mastergridform__{$linkid}">
	<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped">
		<thead>
			<tr class="slds-line-height_reset">
				{foreach from=$GridFields item=$i}
				<th data-name="{$i.name}">
					{$i.label} {if $i.mandatory}<span class="slds-required">*</span>{/if}
				</th>
				{/foreach}
				<th data-name="gridaction">{$APP.LBL_ACTIONS}</th>
			</tr>
		</thead>
		<tbody id="mastergrid-{$linkid}"></tbody>
	</table>
</form>
<button type="button" data-id="{$linkid}" class="slds-button slds-button_brand slds-float_right slds-m-top_x-small" onclick="mg[{$linkid}].EmptyRow()">
	<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
		<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
	</svg>
	{$APP.LBL_NEW_BUTTON_LABEL} {$module_label}
</button>