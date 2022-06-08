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
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-p-around_small slds-card">
<thead>
	<tr class="slds-line-height_reset">
	<th scope="col" width="5%">#</th>
	<th scope="col" width="20%">{'Cron Job'|@getTranslatedString:'CronTasks'}</th>
	<th scope="col" width="11%">{$MOD.LBL_FREQUENCY}{$MOD.LBL_HOURMIN}</th>
	<th scope="col" width="9%">{$CMOD.LBL_STATUS}</th>
	<th scope="col" width="20%">{$MOD.LAST_START}</th>
	<th scope="col" width="20%">{$MOD.LAST_END}</th>
	<th scope="col" width='10%'>{$MOD.LBL_NTT}</th>
	<th scope="col" width='10%'>{$MOD.LBL_SEQUENCE}</th>
	<th scope="col" width="5%" class="slds-align_absolute-center">{$MOD.LBL_TOOLS}</th>
	</tr>
<thead>
<tbody>
	{foreach name=cronlist item=elements from=$CRON}
	<tr class="slds-hint-parent">
	<td>{$smarty.foreach.cronlist.iteration}</td>
	<td>{$elements.cronname}</td>
	<td>{$elements.hours}:{$elements.mins}</td>
	{if $elements.status eq 'Active'|@getTranslatedString:'CronTasks'}
		<td class="active">{$elements.status}</td>
	{elseif $elements.status eq 'LBL_RUNNING'|@getTranslatedString:'CronTasks'}
		<td style="color: red">{$elements.status}</td>
	{else}
		<td class="inactive">{$elements.status}</td>
	{/if}
	<td>{$elements.laststart}</td>
	<td>{$elements.lastend}</td>
	<td>{$elements.ntt}</td>
	{if $smarty.foreach.cronlist.first neq true}
		<td>
			<a href="javascript:move_module('{$elements.id}','Up');">
			<span class="slds-icon_container slds-icon_container_circle slds-icon-action-sort" title="{$APP.LBL_EDIT}">
				<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowup"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_EDIT}</span>
			</span>
			</a>
	{/if}
	{if $smarty.foreach.cronlist.first eq true}
		<td>
			<a href="javascript:move_module('{$elements.id}','Down');">
			<span class="slds-icon_container slds-icon_container_circle slds-icon-action-sort" title="{$APP.LBL_EDIT}">
				<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowdown"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_EDIT}</span>
			</span>
			</a>
		</td>
	{/if}

	{if $smarty.foreach.cronlist.last neq true && $smarty.foreach.cronlist.first neq true}
		<a href="javascript:move_module('{$elements.id}','Down');">
			<span class="slds-icon_container slds-icon_container_circle slds-icon-action-sort slds-m-left_xx-small" title="{$APP.LBL_EDIT}">
				<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowdown"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_EDIT}</span>
			</span>
		</a>
		</td>
	{/if}
	<td class="slds-align_absolute-center">
		<a href="javascript:void(0);" onClick="fnvshobj(this, 'editdiv');fetchEditCron('{$elements.id}'); return false;">
			<span class="slds-icon_container slds-icon_container_circle slds-icon-action-edit slds-m-left_xx-small" title="{$APP.LBL_EDIT}">
				<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_EDIT}</span>
			</span>
		</a>
	</td>
	</tr>
	{/foreach}
</tbody>
</table>
</div>
</section>