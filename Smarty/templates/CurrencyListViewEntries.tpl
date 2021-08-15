{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
-->
*}
<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-p-around_small slds-card">
<thead>
	<tr class="slds-line-height_reset">
		<th scope="col"  width="3%"><div class="slds-truncate">#</div></th>
		<th scope="col"  width="9%"><div class="slds-truncate">{$MOD.LBL_CURRENCY_TOOL}</div></th>
		<th scope="col"  width="23%"><div class="slds-truncate">{$MOD.LBL_CURRENCY_NAME}</div></th>
		<th scope="col"  width="15%"><div class="slds-truncate">{$MOD.LBL_CURRENCY_CODE}</div></th>
		<th scope="col"  width="10%"><div class="slds-truncate">{$MOD.LBL_CURRENCY_SYMBOL}</div></th>
		<th scope="col"  width="16%"><div class="slds-truncate">{'Symbol Placement'|@getTranslatedString:'Users'}</div></th>
		<th scope="col"  width="16%"><div class="slds-truncate">{$MOD.LBL_CURRENCY_CRATE}</div></th>
		<th scope="col"  width="8%"><div class="slds-truncate">{$MOD.LBL_CURRENCY_STATUS}</div></th>
	</tr>
</thead>
<tbody>
	{foreach item=currencyvalues name=currlist key=id from=$CURRENCY_LIST}
	<tr class="slds-hint-parent" id="currency-{$currencyvalues.id}">
		<td><div class="slds-truncate">{$smarty.foreach.currlist.iteration}</div></td>
		<td><div style="height:30px;" class="slds-truncate">{$currencyvalues.tool}</div></td>
		<td><div class="slds-truncate"><b>{$currencyvalues.name|@getTranslatedCurrencyString}</b></div></td>
		<td><div class="slds-truncate">{$currencyvalues.code}</div></td>
		<td><div class="slds-truncate">{$currencyvalues.symbol}</div></td>
		<td><div class="slds-truncate">{$currencyvalues.position}</div></td>
		<td><div class="slds-truncate">{$currencyvalues.crate}</div></td>
		{if $currencyvalues.status eq 'Active'}
			<td><div class="slds-truncate">{$currencyvalues.status|@getTranslatedString}</div></td>
		{else}
			<td><div class="slds-truncate">{$currencyvalues.status|@getTranslatedString}</div></td>
		{/if}
	</tr>
	{/foreach}
</tbody>
</table>
