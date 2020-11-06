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
 *************************************************************************************************/
-->*}

<script src="modules/cbQuestion/resources/appendcontext.js" type="text/javascript" charset="utf-8"></script>
<div style="width:100%;">
	<div class="slds-col slds-size_12-of-12">
		<fieldset class="slds-form-element">
			<legend class="slds-form-element__legend slds-form-element__label">{'Must match'|@getTranslatedString:'cbQuestion'}:</legend>
			<div class="slds-form-element__control">
				<span class="slds-radio">
					<input type="radio" name="conditions" id="all" value="all" checked>
					<label class="slds-radio__label" for="all">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label">{'all conditions'|@getTranslatedString:'cbQuestion'}</span>
					</label>
				</span>
				<span class="slds-radio">
					<input type="radio" name="conditions" id="any" value="any">
					<label class="slds-radio__label" for="any">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label">{'any condition'|@getTranslatedString:'cbQuestion'}</span>
					</label>
				</span>
			</div>
		</fieldset>
	</div>
	<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
		<thead>
			<tr class="slds-line-height_reset">
				<th scope="col">
					<div class="slds-truncate" title="{'Variable'|@getTranslatedString:$module->name}">
						{'Variable'|@getTranslatedString:$module->name}
					</div>
				</th>
				<th scope="col">
					<div title="{'Variable'|@getTranslatedString:$module->name}">
						{'Operator'|@getTranslatedString:$module->name}
					</div>
				</th>
				<th scope="col">
					<div title="{'Variable'|@getTranslatedString:$module->name}">
						{'Value'|@getTranslatedString:$module->name}
					</div>
				</th>
				<th scope="col" style="width:5%;">
					<div title="{'LBL_DELETE'|@getTranslatedString:$module->name}">
						{'LBL_DELETE'|@getTranslatedString:$module->name}
					</div>
				</th>
			</tr>
		</thead>
		<tbody id="context_rows">
		</tbody>
	</table>
</div>