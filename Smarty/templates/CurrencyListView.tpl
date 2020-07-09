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
							<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_CURRENCY_LIST}">
								<svg class="slds-button__icon slds-icon-text-success slds-icon_large slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#currency"></use> </svg>
								&nbsp;{$MOD.LBL_CURRENCY_LIST}
								<p valign=top class="small cblds-p-v_none">&nbsp;&nbsp;&nbsp;&nbsp;{$MOD.LBL_CURRENCY_DESCRIPTION}</p>
							</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div align=center>
	<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="module" value="Settings">
		<input type="hidden" name="action" value="CurrencyEditView">
		<div align="right" class="slds-col slds-p-right_xx-large slds-p-top_large">
			<button type="submit" class="slds-button slds-button_brand">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use> </svg>
			&nbsp;{$MOD.LBL_NEW_CURRENCY}
			</button>
		</div>
		<div>
			{include file="CurrencyListViewEntries.tpl"}
		</div>
	</form>
</div>
	<div id="currencydiv" style="display:block;position:absolute;width:250px;"></div>
</div>
</section>
{literal}
<script>
	function deleteCurrency(currid){
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url:'index.php?action=SettingsAjax&file=CurrencyDeleteStep1&return_action=CurrencyListView&return_module=Settings&module=Settings&parenttab=Settings&id='+currid,
		}).done(function(response) {
			jQuery('#status').hide();
			jQuery('#currencydiv').html(response);
		});
	}

	function transferCurrency(del_currencyid){
		document.getElementById('status').style.display='inline';
		jQuery('#CurrencyDeleteLay').hide();
		var trans_currencyid=jQuery('#transfer_currency_id').val();
		jQuery.ajax({
			method: 'POST',
			url:'index.php?module=Settings&action=SettingsAjax&file=CurrencyDelete&ajax=true&delete_currency_id='+del_currencyid+'&transfer_currency_id='+trans_currencyid,
		}).done(function(response) {
			jQuery('#status').hide();
			jQuery('#CurrencyListViewContents').html(response);
			document.getElementById(`currency-${del_currencyid}`).remove();
		});
	}
</script>
{/literal}