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
 *************************************************************************************************/
-->*}
{assign var="slds_role" value=""}
<div id="setfieldsdiv" class="slds-m-top_x-small slds-m-bottom_x-small">
    {if !empty($LAYOUT_DATA.fields)}
		<div class="slds-align_absolute-center"><b>{'Detail View Layout Details'|@getTranslatedString:'cbMap'}</b></div>
		{if {$LAYOUT_DATA.type} eq "FieldList"}
			{foreach item=field from=$LAYOUT_DATA.fields}
				<section class="slds-card">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media_center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2 class="slds-card__header-title">
								</h2>
							</div>
						</header>
					</div>
					<div class="slds-card__body slds-card__body_inner">
						<ul class="slds-has-dividers_top">	
							{foreach item=fieldvalue from=$field}
								<li>{$fieldvalue}</li>
							{/foreach}
						</ul>
					</div>
				<footer class="slds-card__footer"></footer>
				</section>
			{/foreach}
		{/if}

		{if {$LAYOUT_DATA.type} eq "ApplicationFields"}
			{foreach item=field from=$LAYOUT_DATA.fields}
				<section class="slds-card">
					<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
						<thead>
							<tr class="slds-line-height_reset">
							<th class="" scope="col">
								<div class="slds-truncate" title="{'Block Feature'|@getTranslatedString:'cbMap'}">{'Block Feature'|@getTranslatedString:'cbMap'}</div>
							</th>
							<th class="" scope="col">
								<div class="slds-truncate" title="{'Value'|@getTranslatedString:'cbMap'}">{'Value'|@getTranslatedString:'cbMap'}</div>
							</th>
							</tr>
						</thead>
						<tbody>
							{foreach item=val key=keyval from=$field}
								<tr class="slds-hint-parent">
									<td data-label="{$keyval}">
										<div class="slds-truncate" title="{$keyval}">{$keyval}</div>
									</td>
									<td data-label="{$val}">
										<div class="slds-truncate" title="{$val}">{$val}</div>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
					<footer class="slds-card__footer"></footer>
				</section>
			{/foreach}
		{/if}

		{if {$LAYOUT_DATA.type} eq "RelatedList"}
				<section class="slds-card">
					<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
						<thead>
							<tr class="slds-line-height_reset">
							<th class="" scope="col">
								<div class="slds-truncate" title="{'Key Name'|@getTranslatedString:'cbMap'}">{'Key Name'|@getTranslatedString:'cbMap'}</div>
							</th>
							<th class="" scope="col">
								<div class="slds-truncate" title="{'Value'|@getTranslatedString:'cbMap'}">{'Value'|@getTranslatedString:'cbMap'}</div>
							</th>
							</tr>
						</thead>
						<tbody>
							{foreach item=val key=keyval from=$LAYOUT_DATA.fields}
								<tr class="slds-hint-parent">
									<td data-label="{$keyval}">
										<div class="slds-truncate" title="{$keyval}">{$keyval}</div>
									</td>
									<td data-label="{$val}">
										<div class="slds-truncate" title="{$val}">{$val}</div>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
					<footer class="slds-card__footer"></footer>
				</section>
		{/if}

		{if {$LAYOUT_DATA.type} eq "Widget" || {$LAYOUT_DATA.type} eq "CodeWithHeader" || {$LAYOUT_DATA.type} eq "CodeWithoutHeader"}
				<section class="slds-card">
					<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
						<thead>
							<tr class="slds-line-height_reset">
							<th class="" scope="col">
								<div class="slds-truncate" title="{'Key Name'|@getTranslatedString:'cbMap'}">{'Key Name'|@getTranslatedString:'cbMap'}</div>
							</th>
							<th class="" scope="col">
								<div class="slds-truncate" title="{'Values'|@getTranslatedString:'cbMap'}">{'Values'|@getTranslatedString:'cbMap'}</div>
							</th>
							</tr>
						</thead>
						<tbody>
							{foreach item=val key=keyval from=$LAYOUT_DATA.fields}
								<tr class="slds-hint-parent">
									<td data-label="{$keyval}">
										<div class="slds-truncate" title="{$keyval}">{$keyval}</div>
									</td>
									<td data-label="{$val}">
										<div class="slds-truncate" title="{$val}">{$val}</div>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
					<footer class="slds-card__footer"></footer>
				</section>
		{/if}
	{/if}
</div>