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
<script type="text/javascript" src="include/js/Inventory.js"></script>
{literal}
<style>
	.tax_delete{
		text-decoration:none;
	}
	.tax_delete td{
	}
</style>
{/literal}
{assign var="MODULEICON" value='money'}
{assign var="MODULESECTION" value=$MOD.LBL_TAX_SETTINGS}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_TAX_DESC}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none slds-card">
<div align=center>
<!-- DISPLAY -->
	{if $EDIT_MODE eq 'true'}
		{assign var=formname value='EditTax'}
		{assign var=shformname value='SHEditTax'}
	{else}
		{assign var=formname value='ListTax'}
		{assign var=shformname value='SHListTax'}
	{/if}
	<br>
	<div width=100% class="slds-form-element">
		<!-- if EDIT_MODE is true then Textbox will be displayed else the value will be displayed-->
		<form name="{$formname}" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="module" value="Settings" class="slds-input">
		<input type="hidden" name="action" value="" class="slds-input">
		<input type="hidden" name="save_tax" value="" class="slds-input">
		<input type="hidden" name="edit_tax" value="" class="slds-input">
		<input type="hidden" name="add_tax_type" value="" class="slds-input">
			<!-- Table to display the Product Tax Add and Edit Buttons - Starts -->
		<div class="slds-grid slds-gutters">
			<div class="slds-col slds-size_2-of-12">
				<p class="big" colspan="3"><strong>{$MOD.LBL_PRODUCT_TAX_SETTINGS} </strong></p>
			</div>
			<div class="slds-col slds-size_10-of-12 slds-p-vertical_medium slds-p-right_xx-large"  width="80%">
				<div id="td_add_tax" align="right">
					{if $EDIT_MODE neq 'true'}
						<button title="{$MOD.LBL_ADD_TAX_BUTTON}" accessKey="{$MOD.LBL_ADD_TAX_BUTTON}" onclick="fnAddTaxConfigRow('');" type="button" name="button" class="slds-button slds-button_success">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use> </svg>
						&nbsp;{$MOD.LBL_ADD_TAX_BUTTON}
						</button>
					{/if}
					{if $EDIT_MODE eq 'true'}
						<button class="slds-button slds-button_success save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.save_tax.value='true'; return validateTaxes('tax_count');" type="submit" name="button2">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use> </svg>
						&nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}
						</button>
						<button class="slds-button slds-button_destructive cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.module.value='Settings'; this.form.save_tax.value='false';" type="submit" name="button22">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use> </svg>
						{$APP.LBL_CANCEL_BUTTON_LABEL}
						</button>&nbsp;&nbsp;&nbsp;&nbsp;
					{elseif $TAX_COUNT > 0}
						<button title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.add_tax_type.value=''; this.form.edit_tax.value='true';" type="submit" name="button" class="slds-button slds-button_success edit">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use> </svg>
						{$APP.LBL_EDIT_BUTTON_LABEL}
						</button>&nbsp;&nbsp;&nbsp;&nbsp;
					{/if}
				</div>
			</div>
		</div>
			<!-- Table to display the Product Tax Add and Edit Buttons - Ends -->
			<!-- Table to display the List of Product Tax values - Starts -->
		<table id="add_tax" border=0 cellspacing=0 cellpadding=5 width=100% class="slds-table slds-table_cell-buffer slds-table_bordered">
		{if $TAX_COUNT eq 0}
			<tr><td>{$MOD.LBL_NO_TAXES_AVAILABLE}. {$MOD.LBL_PLEASE} {$MOD.LBL_ADD_TAX_BUTTON}.</td></tr>
		{else}
			<thead>
				<tr class="slds-line-height_reset" height="40px">
				<th scope="col" ><div class="slds-truncate">{$MOD.LBL_TAXCLASS}</div></th>
				<th scope="col" ><div class="slds-truncate">{$MOD.LBL_PERCENTAGE}</div></th>
				<th scope="col" ><div class="slds-truncate">{$MOD.LBL_RETENTION}</div></th>
				<th scope="col" ><div class="slds-truncate">{$MOD.LBL_DEFAULT}</div></th>
				<th scope="col" ><div class="slds-truncate">{$MOD.LBL_QUICK_CREATE}</div></th>
				<th scope="col" ><div class="slds-truncate">{$MOD.LBL_ENABLED}</div></th>
			</tr>
			</thead>
			{foreach item=tax key=count from=$TAX_VALUES}
				<!-- To set the color coding for the taxes which are active and inactive-->
				{if $tax.deleted eq 0}
					<tr><!-- set color to taxes which are active now-->
				{else}
					<tr><!-- set color to taxes which are disabled now-->
				{/if}
				<!--assinging tax label name for javascript validation-->
				{assign var=tax_label value="taxlabel_"|cat:$tax.taxname}
				<td>
					{if $EDIT_MODE eq 'true'}
						{assign var=pstax value=$tax.taxlabel}
						<input name="{$pstax|bin2hex}" id={$tax_label} type="text" value="{$tax.taxlabel}" class="slds-input">
					{else}
						{$tax.taxlabel}
					{/if}
				</td>
				<td>
					{if $EDIT_MODE eq 'true'}
						<input name="{$tax.taxname}" id="{$tax.taxname}" type="number" step='0.001' value="{$tax.percentage}" style="width:65%" class="slds-input">&nbsp;%
					{else}
						{$tax.percentage}&nbsp;%
					{/if}
				</td>
				<td>
					{if $EDIT_MODE eq 'true'}
						<input name="{$tax.taxname}retention" id="{$tax.taxname}retention" class="slds-checkbox" type="checkbox" {if $tax.retention neq 0}checked{/if}>
					{else}
						{if $tax.retention eq 0}{$APP.LBL_NO}{else}{$APP.LBL_YES}{/if}
					{/if}
				</td>
				<td>
					{if $EDIT_MODE eq 'true'}
						<input name="{$tax.taxname}default" id="{$tax.taxname}default" type="checkbox" {if $tax.default neq 0}checked{/if}>
					{else}
						{if $tax.default eq 0}{$APP.LBL_NO}{else}{$APP.LBL_YES}{/if}
					{/if}
				</td>
				<td >
					{if $EDIT_MODE eq 'true'}
						<input name="{$tax.taxname}qcreate" id="{$tax.taxname}qcreate" type="checkbox" {if $tax.qcreate neq 0}checked{/if}>
					{else}
						{if $tax.qcreate eq 0}{$APP.LBL_NO}{else}{$APP.LBL_YES}{/if}
					{/if}
				</td>
				<td >
					{if $tax.deleted eq 0}
						<a href="index.php?module=Settings&action=TaxConfig&disable=true&taxname={$tax.taxname}&taxid={$tax.taxid}">
							<span class="slds-icon_container slds-icon_container_circle slds-icon-action-approval" title="{$MOD.LBL_ENABLED}">
								<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#approval"></use>
								</svg>
								<span class="slds-assistive-text">{$MOD.LBL_ENABLED}</span>
							</span>
						</a>
					{else}
						<a href="index.php?module=Settings&action=TaxConfig&enable=true&taxname={$tax.taxname}&taxid={$tax.taxid}">
							<span class="slds-icon_container slds-icon_container_circle slds-icon-action-close" title="{$MOD.LBL_DISABLED}">
								<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use>
								</svg>
								<span class="slds-assistive-text">{$MOD.LBL_DISABLED}</span>
							</span>
						</a>
					{/if}
				</td>
				</tr>
			{/foreach}
			{if $EDIT_MODE eq 'true'}
				<input type="hidden" id="tax_count" value="{$count}">
			{/if}
		{/if}
		</table>
		<!-- Table to display the List of Product Tax values - Ends -->
		</form>
		<!-- Shipping Tax Config Table Starts Here -->
		<form name="{$shformname}" method="POST" action="index.php">
		<input type="hidden" name="module" value="Settings">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="sh_save_tax" value="">
		<input type="hidden" name="sh_edit_tax" value="">
		<input type="hidden" name="sh_add_tax_type" value="">
		<!-- Table to display the S&H Tax Add and Edit Buttons - Starts -->
		<div class="slds-grid slds-gutters">
			<div class="slds-col slds-size_2-of-12 slds-p-top_large">
				<p class="big" colspan="3"><strong>{$MOD.LBL_SHIPPING_HANDLING_TAX_SETTINGS}</strong></p>
			</div>
			<div class="slds-col slds-size_10-of-12 slds-p-vertical_medium slds-p-right_xx-large"  width="80%">
				<div id="td_sh_add_tax" align="right">
					{if $SH_EDIT_MODE neq 'true'}
						<button title="{$MOD.LBL_ADD_TAX_BUTTON}" accessKey="{$MOD.LBL_ADD_TAX_BUTTON}" onclick="fnAddTaxConfigRow('sh');" type="button" name="button"  class="slds-button slds-button_success ">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use> </svg>
						{$MOD.LBL_ADD_TAX_BUTTON}
						</button>
					{/if}
					{if $SH_EDIT_MODE eq 'true'}
						<button class="slds-button slds-button_success save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.sh_save_tax.value='true'; return validateTaxes('sh_tax_count');" type="submit" name="button2" >
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use> </svg>
						{$APP.LBL_SAVE_BUTTON_LABEL}
						</button>
						<button class="slds-button slds-button_destructive cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.module.value='Settings'; this.form.sh_save_tax.value='false';" type="submit" name="button22">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use> </svg>
						{$APP.LBL_CANCEL_BUTTON_LABEL}
						</button>&nbsp;&nbsp;&nbsp;
					{elseif $SH_TAX_COUNT > 0}
						<button title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.sh_add_tax_type.value=''; this.form.sh_edit_tax.value='true';" type="submit" name="button" class="slds-button slds-button_success edit">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use> </svg>
							{$APP.LBL_EDIT_BUTTON_LABEL}
						</button>&nbsp;&nbsp;&nbsp;
					{/if}
				</div>
			</div>
		</div>
			<!-- Table to display the S&H Tax Add and Edit Buttons - Ends -->
			<!-- Table to display the List of S&H Tax Values - Starts -->
		<table id="sh_add_tax" border=0 cellspacing=0 cellpadding=5 width=100% class="slds-table slds-table_cell-buffer slds-table_bordered">
		{if $SH_TAX_COUNT eq 0}
			<tr><td>{$MOD.LBL_NO_TAXES_AVAILABLE}. {$MOD.LBL_PLEASE} {$MOD.LBL_ADD_TAX_BUTTON}.</td></tr>
		{else}
			{foreach item=tax key=count from=$SH_TAX_VALUES}
			<!-- To set the color coding for the taxes which are active and inactive-->
			{if $tax.deleted eq 0}
				<tr><!-- set color to taxes which are active now-->
			{else}
				<tr><!-- set color to taxes which are disabled now-->
			{/if}
			{assign var=tax_label value="taxlabel_"|cat:$tax.taxname}
			<td width=33% >
				{if $SH_EDIT_MODE eq 'true'}
					{assign var = shtax value = $tax.taxlabel}
					<input name="{$shtax|bin2hex}" id="{$tax_label}" type="text" value="{$tax.taxlabel}" class="slds-input">
				{else}
					{$tax.taxlabel}
				{/if}
			</td>
			<td width=50% >
				{if $SH_EDIT_MODE eq 'true'}
					<input name="{$tax.taxname}" id="{$tax.taxname}" type="number" step='0.001' value="{$tax.percentage}" style="width:55%" class="slds-input">
					%&nbsp;
				{else}
					{$tax.percentage}&nbsp;%
				{/if}
			</td>
			<td width=17% >
				{if $tax.deleted eq 0}
					<a href="index.php?module=Settings&action=TaxConfig&sh_disable=true&sh_taxname={$tax.taxname}&taxid={$tax.taxid}">
						<span class="slds-icon_container slds-icon_container_circle slds-icon-action-approval" title="{$MOD.LBL_ENABLED}">
							<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#approval"></use>
							</svg>
							<span class="slds-assistive-text">{$MOD.LBL_ENABLED}</span>
						</span>
					</a>
				{else}
					<a href="index.php?module=Settings&action=TaxConfig&sh_enable=true&sh_taxname={$tax.taxname}&taxid={$tax.taxid}">
						<span class="slds-icon_container slds-icon_container_circle slds-icon-action-close" title="{$MOD.LBL_DISABLED}">
							<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{$MOD.LBL_DISABLED}</span>
						</span>
					</a>
				{/if}
			</td>
			</tr>
			{/foreach}
			{if $SH_EDIT_MODE eq 'true'}
			<input class="slds-input" type="hidden" id="sh_tax_count" value="{$count}">
			{/if}
		{/if}
		</table>
		<!-- Table to display the List of S&H Tax Values - Ends -->
	</form>
		<!-- Shipping Tax Ends Here -->
</div>
</div>
</div>
</section>
<script>
var tax_labelarr = {ldelim}
	SAVE_BUTTON:'{$APP.LBL_SAVE_BUTTON_LABEL}',
	CANCEL_BUTTON:'{$APP.LBL_CANCEL_BUTTON_LABEL}',
	TAX_NAME:'{$APP.LBL_TAX_NAME}',
	TAX_VALUE:'{$APP.LBL_TAX_VALUE}',
	TAX_RETENTION:'{$MOD.LBL_RETENTION}',
	TAX_DEFAULT:'{$MOD.LBL_DEFAULT}',
	TAX_QCREATE:'{$MOD.LBL_QUICK_CREATE}',
{rdelim};
</script>
