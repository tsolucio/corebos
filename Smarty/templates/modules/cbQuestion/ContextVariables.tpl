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
	<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
		<thead>
			<tr class="slds-line-height_reset">
				<th scope="col">
					<div class="slds-truncate" title="{'Variable'|@getTranslatedString:$MODULE}">
						{'Variable'|@getTranslatedString:$MODULE}
					</div>
				</th>
				<th scope="col">
					<div title="{'Variable'|@getTranslatedString:$MODULE}">
						{'Value'|@getTranslatedString:$MODULE}
					</div>
				</th>
				<th scope="col" style="width:5%;">
					<div title="{'LBL_DELETE'|@getTranslatedString:$MODULE}">
						{'LBL_DELETE'|@getTranslatedString:$MODULE}
					</div>
				</th>
			</tr>
		</thead>
		<tbody id="context_rows">
		</tbody>
	</table>
</div>