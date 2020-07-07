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
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none slds-card">
<div class="slds-page-header"> 
	<div class="slds-page-header__row"> 
		<div class="slds-page-header__col-title"> 
			<div class="slds-media"> 
				<div class="slds-media__body"> 
					<div class="slds-page-header__name"> 
						<div class="slds-page-header__name-title"> 
							<h1> 
							<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_NEW_CURRENCY}">
							<h1>
							<svg class="slds-button__icon slds-icon-text-success slds-icon_large slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#currency"></use> </svg>
							&nbsp;{$MOD.LBL_VIEWING} &quot;{$CURRENCY_NAME}&quot; 
							<p valign=top class="small cblds-p-v_none">&nbsp;&nbsp;&nbsp;&nbsp;{$MOD.LBL_CURRENCY_DESCRIPTION}</p>
							</h1>
							</span>
						</div> 
					</div> 
				</div> 
			</div> 
		</div> 
	</div> 
</div>
<div align=center>
			<!-- DISPLAY -->
	<form action="index.php" method="post" name="index" id="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="parenttab" value="{$PARENTTAB}">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="record" value="{$ID}">
		
	<div class="slds-grid slds-gutters">
		<div class="slds-col slds-size_5-of-6">
			<br>
			<h2 align="left" class="slds-p-left_x-small"><strong>{'LBL_SETTINGS'|@getTranslatedString} {$APP.LBL_FOR} &quot;{$CURRENCY_NAME|@getTranslatedCurrencyString}&quot;  </strong></h2>
			<br>
		</div>
		<div class="slds-col slds-size_1-of-6 slds-p-bottom_large">
			<br>
			<button type="submit" class="slds-button slds-button_success edit" onclick="this.form.action.value='CurrencyEditView'; this.form.parenttab.value='Settings'; this.form.record.value='{$ID}'">
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