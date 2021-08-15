{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{assign var="MODULEICON" value='currency'}
{assign var="MODULESECTION" value=$MOD.LBL_VIEWING|cat:" "|cat:$CURRENCY_NAME}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_CURRENCY_DESCRIPTION}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none slds-card">
<div align=center>
	<form action="index.php" method="post" name="index" id="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="record" value="{$ID}">

	<div class="slds-grid slds-gutters">
		<div class="slds-col slds-size_2-of-12">
			<br>
			{include file='Components/PageSubTitle.tpl' PAGESUBTITLE='LBL_SETTINGS'|@getTranslatedString|cat:" "|cat:$APP.LBL_FOR|cat:" "|cat:$CURRENCY_NAME|@getTranslatedCurrencyString}
			<br>
		</div>
		<div class="slds-col slds-size_4-of-12 slds-p-bottom_large"></div>
		<div class="slds-col slds-size_11-of-12 slds-p-bottom_large">
			<br>
			<button type="submit" class="slds-button slds-button_success edit" onclick="this.form.action.value='CurrencyEditView'; this.form.record.value='{$ID}'">
			<svg class="slds-button__icon slds-icon-text-success slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use> </svg>
			&nbsp;Edit
			</button>
		</div>
	</div>
	<div class="slds-col">
		<table class="slds-table slds-table_cell-buffer slds-no-row-hover slds-table_bordered">
			<tr>
				<td width="20%" height="40px" nowrap >
					<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
					<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_NAME}">* </abbr>
					{$MOD.LBL_CURRENCY_NAME}
					</label>
				</td>
				<td width="80%" >
				{$CURRENCY_NAME|@getTranslatedCurrencyString}
				</td>
			</tr>
			<tr>
				<td nowrap  height="40px">
					<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
					<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_CODE}">* </abbr>
					{$MOD.LBL_CURRENCY_CODE}
					</label>
				</td>
				<td >{$CURRENCY_CODE}</td>
			</tr>
			<tr>
				<td nowrap  height="40px">
					<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
					<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_SYMBOL}">* </abbr>
					{$MOD.LBL_CURRENCY_SYMBOL}
					</label>
				</td>
				<td >{$CURRENCY_SYMBOL}</td>
			</tr>
			<tr>
				<td nowrap  height="40px">
					<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
					<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_CRATE}">* </abbr>
					{$MOD.LBL_CURRENCY_CRATE}<br>({$MOD.LBL_BASE_CURRENCY}{$MASTER_CURRENCY|@getTranslatedCurrencyString})
					</label>
				</td>
				<td >{$CONVERSION_RATE}</td>
			</tr>
			<tr>
				<td nowrap  height="40px">
					<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
					<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_STATUS}">* </abbr>
					{$MOD.LBL_CURRENCY_STATUS}
					</label>
				</td>
				<td >{$CURRENCY_STATUS}</td>
			</tr>
		</table>
	</div>
	</form>
</div>
</div>
</section>