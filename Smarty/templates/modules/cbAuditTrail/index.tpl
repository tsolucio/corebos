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
<br>
<link rel="stylesheet" href="include/bunnyjs/css/svg-icons.css">
<script src="include/bunnyjs/utils-dom.min.js"></script>
<script src="include/bunnyjs/ajax.min.js"></script>
<script src="include/bunnyjs/template.min.js"></script>
<script src="include/bunnyjs/pagination.min.js"></script>
<script src="include/bunnyjs/url.min.js"></script>
<script src="include/bunnyjs/utils-svg.min.js"></script>
<script src="include/bunnyjs/spinner.min.js"></script>
<script src="include/bunnyjs/datatable.min.js"></script>
<script src="include/bunnyjs/datatable.icons.min.js"></script>
<script src="include/bunnyjs/element.min.js"></script>
<script src="include/bunnyjs/datatable.scrolltop.min.js"></script>
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
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#people"></use>
					</svg>
				</div>
				<div class="slds-media__body">
					<h1 class="slds-page-header__title slds-m-right--small slds-align-middle slds-truncate"
						title="{$MOD.LBL_LOGIN_HISTORY_DETAILS}">{$MOD.LBL_AUDIT_TRAIL}</h1>
				</div>
			</div>
		</div>
		{if $ATENABLED != 'true'}
		<span class="slds-badge" style="margin:auto;margin-right:20px;">{'AuditTrailDisabled'|@getTranslatedString:'Settings'}</span>
		<div class="slds-col slds-no-flex slds-grid slds-align-top">
			<button class="slds-button slds-button--neutral slds-not-selected" aria-live="assertive" onclick="auditenable();">
				<span class="slds-text-not-selected">
					<svg aria-hidden="true" class="slds-button__icon--stateful slds-button__icon--left">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
					</svg>{'LBL_ENABLE'|@getTranslatedString:'Settings'}</span>
			</button>
		</div>
		{/if}
		{if $ATENABLED == 'true'}
		<span class="slds-badge" style="margin:auto;margin-right:20px;">{'AuditTrailEnabled'|@getTranslatedString:'Settings'}</span>
		<div class="slds-col slds-no-flex slds-grid slds-align-top">
			<button class="slds-button slds-button--neutral slds-not-selected" aria-live="assertive" onclick="auditdisable();">
				<span class="slds-text-not-selected">
					<svg aria-hidden="true" class="slds-button__icon--stateful slds-button__icon--left">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>{'LBL_DISABLE'|@getTranslatedString:'Settings'}</span>
			</button>
		</div>
		{/if}
	</div>
</div>
<div class="rptContainer" style="width:94%;margin:auto;">
	<datatable url="index.php?module=cbAuditTrail&action=cbAuditTrailAjax&file=getJSON" template="report_row_template">
		<header>
			<div class="slds-form-element slds-lookup" data-select="single" style="width: 400px; margin-bottom: 6px;">
				<label class="slds-form-element__label" for="lookup-339">{'LBL_SEARCH_FORM_TITLE'|getTranslatedString:'Users'}</label>
				<div class="slds-form-element__control slds-grid slds-box--border">
					<div class="slds-dropdown--trigger slds-dropdown-trigger--click slds-align-middle slds-m-left--xx-small slds-shrink-none">
						<svg aria-hidden="true" class="slds-icon slds-icon-standard-account slds-icon--small">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#user"></use>
						</svg>
					</div>
					<div class="slds-input-has-icon slds-input-has-icon--right slds-grow">
						<svg aria-hidden="true" class="slds-input__icon">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
						</svg>
						<select name="user_list" id="user_list" class="slds-lookup__search-input slds-input--bare" type="search"
							aria-owns="user_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
							<option value="none" selected="true">{$APP.LBL_NONE}</option>
							{$USERLIST}
						</select>
					</div>
				</div>
			</div>
		</header>
		<footer>
			<pagination limit="12" outer></pagination>
			<stats></stats>
		</footer>
		<table class="rptTable">
			<tr>
			{foreach key=dtkey item=dtheader from=$LIST_HEADER}
				<th pid="{$dtkey}" class="rptCellLabel">{$dtheader}</th>
			{/foreach}
			</tr>
		</table>
	</datatable>
</div>
<table id="report_row_template" hidden>
	<tr>
		{foreach key=dtkey item=dtheader from=$LIST_FIELDS}
			{if $dtheader eq 'recordid'}
			<td class="rptData"><a av="href:Record"><span v="RecordDetail"></span></a></td>
			{else}
			<td v="{$dtkey}" class="rptData"></td>
			{/if}
		{/foreach}
	</tr>
</table>
<script type="text/javascript">
{literal}
Template.define('report_row_template', {});
{/literal}
Pagination._config.langFirst = "{$APP.LNK_LIST_START}";
Pagination._config.langLast = "{$APP.LNK_LIST_END}";
Pagination._config.langPrevious = "< {$APP.LNK_LIST_PREVIOUS}";
Pagination._config.langNext = "{$APP.LNK_LIST_NEXT} >";
{literal}
Pagination._config.langStats = "{from}-{to} {/literal}{$APP.LBL_LIST_OF}{literal} {total} ({/literal}{$APP.Page}{literal} {currentPage} {/literal}{$APP.LBL_LIST_OF}{literal} {lastPage})";
//DataTableConfig.loadingImg = 'themes/images/loading.svg';
DataTableConfig.searchInputName = 'user_list';
</script>
{/literal}
