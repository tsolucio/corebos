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
{assign var="MODULESECTION" value=$MOD.LBL_CURRENCY_LIST}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_CURRENCY_DESCRIPTION}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="currencies list">
<div class="slds-modal__container slds-p-around_none slds-card" style="min-height:400px;">
<div class="slds-align-content-center" style="align-self:normal;">
	<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="module" value="Settings">
		<input type="hidden" name="action" value="CurrencyEditView">
		<div align="right" class="slds-col slds-p-right_xx-large slds-p-top_large">
			<button type="submit" class="slds-button slds-button_brand">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use> </svg>
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
			url:'index.php?action=SettingsAjax&file=CurrencyDeleteStep1&return_action=CurrencyListView&return_module=Settings&module=Settings&id='+currid,
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