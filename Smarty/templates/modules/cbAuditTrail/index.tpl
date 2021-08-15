{*<!--
/*********************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 ********************************************************************************/
-->*}
<script src="modules/cbAuditTrail/cbAuditTrail.js"></script>
<style>
{literal}
	.arrow-up:after,
	.arrow-down:after {
		margin-left: 5px;
		margin-top: -5px;
		background-size: cover;
		position: relative;
		display: inline-block;
		top: 6px;
		width: 16px;
		height: 16px;
	}

	.arrow-up:after {
		content: " ";
		background-image: url('include/LD/assets/icons/utility/chevronup_60.png');
	}

	.arrow-down:after {
		content: " ";
		background-image: url('include/LD/assets/icons/utility/chevrondown_60.png');
	}
{/literal}
</style>
<div class="slds-page-header" role="banner">
	<div class="slds-grid">
		<div class="slds-col slds-has-flexi-truncate">
			<div class="slds-media slds-no-space slds-grow">
				<div class="slds-media__figure">
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#trail"></use>
					</svg>
				</div>
				<div class="slds-media__body">
					<h1 class="slds-page-header__title slds-m-right_small slds-align-middle slds-truncate"
						title="{$MOD.LBL_LOGIN_HISTORY_DETAILS}">{$MOD.LBL_AUDIT_TRAIL}</h1>
				</div>
			</div>
		</div>
		{if $ATENABLED != 'true'}
		<span class="slds-badge" style="margin:auto;margin-right:20px;">{'AuditTrailDisabled'|@getTranslatedString:'Settings'}</span>
		<div class="slds-col slds-no-flex slds-grid slds-align-top">
			<button class="slds-button slds-button_neutral slds-not-selected" aria-live="assertive" onclick="auditenable();">
				<svg aria-hidden="true" class="slds-button__icon_stateful slds-button__icon_left">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
				</svg>{'LBL_ENABLE'|@getTranslatedString:'Settings'}
			</button>
		</div>
		{/if}
		{if $ATENABLED == 'true'}
		<span class="slds-badge" style="margin:auto;margin-right:20px;">{'AuditTrailEnabled'|@getTranslatedString:'Settings'}</span>
		<div class="slds-col slds-no-flex slds-grid slds-align-top">
			<button class="slds-button slds-button_neutral slds-not-selected" aria-live="assertive" onclick="auditdisable();">
				<svg aria-hidden="true" class="slds-button__icon_stateful slds-button__icon_left">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>{'LBL_DISABLE'|@getTranslatedString:'Settings'}
			</button>
		</div>
		{/if}
	</div>
</div>
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher">
<div class="slds-m-around_small slds-card">
	<div class="slds-grid slds-gutters slds-m-around_small">
		<div class="slds-col slds-size_2-of-6">
			<div class="slds-form-element slds-lookup" data-select="single" style="width: 400px;">
				<label class="slds-form-element__label" for="lookup-339">{'LBL_SEARCH_FORM_TITLE'|getTranslatedString:'Users'}</label>
				<div class="slds-form-element__control slds-grid slds-box_border">
					<div class="slds-dropdown_trigger slds-dropdown-trigger_click slds-align-middle slds-m-left_xx-small slds-shrink-none">
						<svg aria-hidden="true" class="slds-icon slds-icon-standard-account slds-icon_small">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#user"></use>
						</svg>
					</div>
					<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
						<svg aria-hidden="true" class="slds-input__icon">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
						</svg>
						<select name="user_list" id="user_list" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
							aria-owns="user_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list"
							onchange="reloadgriddata();">
							<option value="none" selected="true">{$APP.LBL_NONE}</option>
							{$USERLIST}
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-col slds-size_2-of-6">
			<div class="slds-form-element" style="width: 160px;">
				<label class="slds-form-element__label" for="text-input-id-1">
				{'LBL_ACTION'|getTranslatedString:'Reports'} {'LBL_Search'|getTranslatedString:'MailManager'}
				</label>
				<div class="slds-form-element__control">
					<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
						<svg aria-hidden="true" class="slds-input__icon">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
						</svg>
						<input type="text" name="action_search" id="action_search" class="slds-input" style="height: 30px;" onchange="reloadgriddata();"/>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-col slds-size_2-of-6"></div>
	</div>
	<div id="atgrid" class="rptContainer" style="width:96%;margin:auto;"></div>
</div>
</section>
