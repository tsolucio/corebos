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
	{if $MODULE eq 'SalesOrder'}
	<!-- SO Actions starts -->
		<tr>
		<td align="left" style="padding-left:10px;">
			<a href="javascript: document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='SalesOrder'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='{$ID}'; document.DetailView.record.value='{$ID}'; document.DetailView.convertmode.value='sotoinvoice'; document.DetailView.submit();" class="webMnu"><img src="{'actionGenerateInvoice.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
			<a href="javascript: document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='SalesOrder'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='{$ID}'; document.DetailView.record.value='{$ID}'; document.DetailView.convertmode.value='sotoinvoice'; document.DetailView.submit();" class="webMnu">{$APP.LBL_CREATE_BUTTON_LABEL} {$APP.Invoice}</a>
		</td>
		</tr>
	{elseif $MODULE eq 'Quotes'}
	<!-- Quotes Actions starts -->
		<tr>
		<td align="left" style="padding-left:10px;">
			<a href="javascript: document.DetailView.return_module.value='{$MODULE}'; document.DetailView.return_action.value='DetailView'; document.DetailView.convertmode.value='{$CONVERTMODE}'; document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_id.value='{$ID}'; document.DetailView.submit();" class="webMnu"><img src="{'actionGenerateInvoice.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a> 
			<a href="javascript: document.DetailView.return_module.value='{$MODULE}'; document.DetailView.return_action.value='DetailView'; document.DetailView.convertmode.value='{$CONVERTMODE}'; document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_id.value='{$ID}'; document.DetailView.submit();" class="webMnu">{$APP.LBL_GENERATE} {$APP.Invoice}</a>
		</td>
		</tr>
		<tr>
		<td align="left" style="padding-left:10px;border-bottom:1px dotted #CCCCCC;">
			<a href="javascript: document.DetailView.return_module.value='{$MODULE}'; document.DetailView.return_action.value='DetailView'; document.DetailView.convertmode.value='quotetoso'; document.DetailView.module.value='SalesOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_id.value='{$ID}'; document.DetailView.submit();" class="webMnu"><img src="{'actionGenerateSalesOrder.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
			<a href="javascript: document.DetailView.return_module.value='{$MODULE}'; document.DetailView.return_action.value='DetailView'; document.DetailView.convertmode.value='quotetoso'; document.DetailView.module.value='SalesOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_id.value='{$ID}'; document.DetailView.submit();" class="webMnu">{$APP.LBL_GENERATE} {$APP.SalesOrder}</a>
		</td>
		</tr>
	{/if}

	<!-- To display the Export To PDF link for PO, SO, Quotes and Invoice - starts -->
	{if $MODULE eq 'SalesOrder'}
		{assign var=export_pdf_action value='CreateSOPDF'}
	{else}
		{assign var=export_pdf_action value='CreatePDF'}
	{/if}

	<tr>
	<td align="left" style="padding-left:10px;">
		<a href="index.php?module={$MODULE}&action={$export_pdf_action}&return_module={$MODULE}&return_action=DetailView&record={$ID}&return_id={$ID}" class="webMnu"><img src="{'actionGeneratePDF.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
		<a href="index.php?module={$MODULE}&action={$export_pdf_action}&return_module={$MODULE}&return_action=DetailView&record={$ID}&return_id={$ID}" class="webMnu">{$APP.LBL_EXPORT_TO_PDF}</a>
	</td>
	</tr>

	<!-- send Invoice PDF through mail -->
	<tr>
	<td align="left" style="padding-left:10px;">
		<a href="javascript: document.DetailView.return_module.value='{$MODULE}'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='{$MODULE}'; document.DetailView.action.value='SendPDFMail'; document.DetailView.record.value='{$ID}'; document.DetailView.return_id.value='{$ID}'; sendpdf_submit();" class="webMnu"><img src="{'PDFMail.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
		<a href="javascript: document.DetailView.return_module.value='{$MODULE}'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='{$MODULE}'; document.DetailView.action.value='SendPDFMail'; document.DetailView.record.value='{$ID}'; document.DetailView.return_id.value='{$ID}'; sendpdf_submit();" class="webMnu">{$APP.LBL_SEND_EMAIL_PDF}</a> 
	</td>
	</tr>

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
