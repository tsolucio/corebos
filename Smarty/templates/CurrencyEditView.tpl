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
{if $ID neq ''}
	{assign var="MODULESECTION" value='LBL_SETTINGS'|@getTranslatedString|cat:" "|cat:$APP.LBL_FOR|cat:" "|cat:$CURRENCY_NAME|@getTranslatedCurrencyString}
	{assign var="MODULESECTIONDESC" value=$MOD.LBL_CURRENCY_DESCRIPTION}
{else}
	{assign var="MODULESECTION" value=$MOD.LBL_NEW_CURRENCY}
	{assign var="MODULESECTIONDESC" value=$MOD.LBL_CURRENCY_DESCRIPTION}
{/if}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none slds-card">
<div align=center>
			<!-- DISPLAY -->
	<form action="index.php" method="post" name="index" id="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="record" value="{$ID}">
	<div class="slds-grid slds-gutters slds-p-right_small">
		<div align="right" class="slds-col slds-size_11-of-12 slds-p-right_none">
			<br>
			<button title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success" onclick="this.form.action.value='SaveCurrencyInfo'; return validate()" type="submit" name="button">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use> </svg>
			{$APP.LBL_SAVE_BUTTON_LABEL}</button>
		</div>
		<div id="CurrencyEditLay" style="display:none;">
			<section role="dialog" tabindex="-1" aria-labelledby="modal-heading-tcur" aria-modal="true" aria-describedby="modal-content-id-tcur" class="slds-modal slds-fade-in-open slds-modal_small">
				<div class="slds-modal__container custom-width-350">
					<header class="slds-modal__header">
						<a class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="{$APP.LBL_CLOSE}" onClick="document.getElementById('CurrencyEditLay').style.display='none'";>
							<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
						</a>
						<h2 id="modal-heading-tcur" class="slds-modal__title slds-hyphenate">{$MOD.LBL_TRANSFER_CURRENCY}</h2>
					</header>
					<div class="slds-modal__content slds-p-around_medium" id="modal-content-id-tcur">
						<div class="slds-grid slds-gutters slds-grid_vertical-align-center slds-p-around_xx-small">
							<div class="slds-col slds-size_1-of-2 slds-text-align_left">
								<span class="slds-truncate" title="{$MOD.LBL_CURRENT_CURRENCY}"> <strong> {$MOD.LBL_CURRENT_CURRENCY} </strong> </span>
							</div>
							<div class="slds-col slds-size_1-of-2 slds-text-align_left">
								<span class="slds-truncate" title="{$CURRENCY_NAME|@getTranslatedCurrencyString}">{$CURRENCY_NAME|@getTranslatedCurrencyString}</span>
							</div>
						</div>
						<div class="slds-grid slds-gutters slds-grid_vertical-align-center slds-p-around_xx-small">
							<div class="slds-col slds-size_1-of-2 slds-text-align_left">
								<span class="slds-truncate" title="{$MOD.LBL_TRANSCURR}"> <strong> {$MOD.LBL_TRANSCURR} </strong></span>
							</div>
							<div class="slds-col slds-size_1-of-2">
								<select class="slds-select" name="transfer_currency_id" id="transfer_currency_id">
									{foreach key=cur_id item=cur_name from=$OTHER_CURRENCIES}
										<option value="{$cur_id}">{$cur_name|@getTranslatedCurrencyString}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
					<div class="slds-modal__footer">
						<input type="button" onclick="form.submit();" name="Update" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="slds-button slds-button_brand">
					</div>
				</div>
			</section>
			<div class="slds-backdrop slds-backdrop_open"></div>
		</div>
		<div class="slds-col slds-size_1-of-12 slds-p-left_none">
			<br>
			<button title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button_destructive" onclick="window.history.back()" type="button" name="button">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use> </svg>
			{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
		</div>
	</div>
	<table border=0 cellspacing=0 cellpadding=0 width=100% class="slds-table slds-table_cell-buffer slds-no-row-hover slds-no-table_bordered">
		<tr class="slds-line-height_reset">
			<td class="small" valign=top >
				<table width="100%" border="0" cellspacing="0" cellpadding="5" class="slds-table slds-table_cell-buffer slds-table_bordered">
					<tr class="slds-line-height_reset">
						<td width="20%" >
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_NAME}">* </abbr>
							{$MOD.LBL_CURRENCY_NAME}
							</label>
						</td>
						<td width="80%" >
							<div class="slds-truncate">
							<!-- input type="hidden" class="detailedViewTextBox small" value="" name="currency_name" -->
								<div class="slds-form-element">
									<div class="slds-form-element__control">
										<div class="slds-select_container">
											<select name="currency_name" id="currency_name" class="slds-select" onChange='updateSymbolAndCode();'>
											{foreach key=header item=currency from=$CURRENCIES}
												{if $header eq $CURRENCY_NAME}
													<option value="{$header}" selected>{$header|@getTranslatedCurrencyString}({$currency.1})</option>
												{else}
													<option value="{$header}" >{$header|@getTranslatedCurrencyString}({$currency.1})</option>
												{/if}
											{/foreach}
											</select>
										</div>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr class="slds-line-height_reset">
						<td width="20%" >
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_CODE}">* </abbr>
							{$MOD.LBL_CURRENCY_CODE}
							</label>
						</td>
						<td >
							<div class="slds-form-element">
								<div class="slds-form-element__control">
									<input type="text" readonly class="slds-input" value="{$CURRENCY_CODE}" name="currency_code" id="currency_code">
								</div>
							</div>
						</td>
					</tr>
					<tr class="slds-line-height_reset">
					<td width="20%" >
						<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
						<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_SYMBOL}">* </abbr>
						{$MOD.LBL_CURRENCY_SYMBOL}
						</label>
					</td>
					<td >
						<div class="slds-form-element">
							<div class="slds-form-element__control">
								<input type="text" readonly class="slds-input" value="{$CURRENCY_SYMBOL}" name="currency_symbol" id="currency_symbol" />
							</div>
						</div>
					</td>
					</tr>
					<tr class="slds-line-height_reset">
						<td width="20%" >
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="{'Symbol Placement'|@getTranslatedString:'Users'}">* </abbr>
							{'Symbol Placement'|@getTranslatedString:'Users'}
							</label>
						</td>
						<td >
							<div class="slds-form-element">
								<div class="slds-form-element__control">
									<div class="slds-select_container">
										<select name="currency_position" class="slds-select">
											{html_options options=$symbolpositionvalues selected=$CURRENCY_POSITION}
										</select>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr class="slds-line-height_reset">
						<td width="20%" >
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_CRATE}">* </abbr>
							{$MOD.LBL_CURRENCY_CRATE}<br>({$MOD.LBL_BASE_CURRENCY}{$MASTER_CURRENCY|@getTranslatedCurrencyString})
							</label>
						</td>
						<td>
							<div class="slds-form-element">
								<div class="slds-form-element__control">
									<input type="text" class="slds-input" value="{$CONVERSION_RATE}" name="conversion_rate">
								</div>
							</div>
						</td>
					</tr>
					<tr class="slds-line-height_reset">
						<td>
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="{$MOD.LBL_CURRENCY_STATUS}"> </abbr>
							{$MOD.LBL_CURRENCY_STATUS}
							</label>
						<td>
							<input type="hidden" value="{$CURRENCY_STATUS}" id="old_currency_status" />
							<div class="slds-form-element">
								<div class="slds-form-element__control">
									<div class="slds-select_container">
										<select name="currency_status" {$STATUS_DISABLE} class="slds-select">
											<option value="Active" {$ACTSELECT}>{$MOD.LBL_ACTIVE}</option>
											<option value="Inactive" {$INACTSELECT}>{$MOD.LBL_INACTIVE}</option>
										</select>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			</td>
		</tr>
	</table>
	</form>
</div>
</div>
</section>
{literal}
<script>
function validate() {
	if (!emptyCheck('currency_name', 'Currency Name', 'text')) {
		return false;
	}
	if (!emptyCheck('currency_code', 'Currency Code', 'text')) {
		return false;
	}
	if (!emptyCheck('currency_symbol', 'Currency Symbol', 'text')) {
		return false;
	}
	if (!emptyCheck('conversion_rate', 'Conversion Rate', 'text')) {
		return false;
	}
	if (!emptyCheck('currency_status', 'Currency Status', 'text')) {
		return false;
	}
	if (isNaN(getObj('conversion_rate').value) || eval(getObj('conversion_rate').value) <= 0) {
{/literal}
		alert("{$APP.ENTER_VALID_CONVERSION_RATE}")
		return false
{literal}
	}
	if (getObj("currency_status") != null && getObj("currency_status").value == "Inactive"
			&& getObj("old_currency_status") != null && getObj("old_currency_status").value == "Active")
	{
		if (getObj("CurrencyEditLay") != null) {
			getObj("CurrencyEditLay").style.display = "block";
		}
		return false;
	} else {
		return true;
	}
}
{/literal}
var currency_array = {$CURRENCIES_ARRAY}
{literal}
updateSymbolAndCode();
function updateSymbolAndCode(){
	selected_curr = document.getElementById('currency_name').value;
	getObj('currency_code').value = currency_array[selected_curr][0];
	getObj('currency_symbol').value = currency_array[selected_curr][1];
}
</script>
{/literal}