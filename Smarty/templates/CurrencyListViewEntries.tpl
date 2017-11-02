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
<table class="slds-table slds-table--bordered  slds-table--cell-buffer listTable currencies-table">
	<thead>
		<tr>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">#</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD.LBL_CURRENCY_TOOL}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD.LBL_CURRENCY_CODE}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD.LBL_CURRENCY_SYMBOL}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{'Symbol Placement'|@getTranslatedString:'Users'}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD.LBL_CURRENCY_CRATE}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD.LBL_CURRENCY_STATUS}</span></th>
		</tr>
	</thead>
	<tbody>
		{foreach item=currencyvalues name=currlist key=id from=$CURRENCY_LIST}
		<tr class="slds-hint-parent slds-line-height--reset">
			<th scope="row"><div class="slds-truncate">{$smarty.foreach.currlist.iteration}</div></th>
			<th scope="row"><div class="slds-truncate">{$currencyvalues.tool}</div></th>
			<th scope="row"><div class="slds-truncate"><b>{$currencyvalues.name|@getTranslatedCurrencyString}</b></div></th>
			<th scope="row"><div class="slds-truncate">{$currencyvalues.code}</div></th>
			<th scope="row"><div class="slds-truncate">{$currencyvalues.symbol}</div></th>
			<th scope="row"><div class="slds-truncate">{$currencyvalues.position}</div></th>
			<th scope="row"><div class="slds-truncate">{$currencyvalues.crate}</div></th>
			{if $currencyvalues.status eq 'Active'}
				<th scope="row" class="active"><div class="slds-truncate">{$currencyvalues.status|@getTranslatedString}</div></th>
			{else}
				<th scope="row" class="inactive"><div class="slds-truncate">{$currencyvalues.status|@getTranslatedString}</div></th>
			{/if}
		</tr>
		{/foreach}
	</tbody>
</table>
