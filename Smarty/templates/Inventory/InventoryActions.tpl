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

<!-- display the Inventory Actions based on the Inventory Modules -->
{if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice'}
{literal}
<script type='text/javascript'>
function sendpdf_submit() {
	// Submit the form to get the attachment ready for submission
	document.DetailView.submit();
{/literal}
	{if $MODULE eq 'Invoice'}
	OpenCompose('{$INV_NO}', 'Invoice:{'SINGLE_Invoice'|@getTranslatedString:$MODULE}', {$ID});
	{elseif $MODULE eq 'Quotes'}
	OpenCompose('{$QUO_NO}', 'Quote:{'SINGLE_Quotes'|@getTranslatedString:$MODULE}', {$ID});
	{elseif $MODULE eq 'PurchaseOrder'}
	OpenCompose('{$PO_NO}', 'PurchaseOrder:{'SINGLE_PurchaseOrder'|@getTranslatedString:$MODULE}', {$ID});
	{elseif $MODULE eq 'SalesOrder'}
	OpenCompose('{$SO_NO}', 'SalesOrder:{'SINGLE_SalesOrder'|@getTranslatedString:$MODULE}', {$ID});
	{/if}
{literal}
}
{/literal}
</script>
{/if}
