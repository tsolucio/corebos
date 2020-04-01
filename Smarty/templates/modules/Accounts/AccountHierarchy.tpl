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
<script type="text/javascript" src="include/js/{$LANGUAGE}.lang.js"></script>
{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center" style="text-align: unset;">
		<h2 id="header43" class="slds-text-heading_medium slds-col slds-size_1-of-2">
			<div class="slds-media__figure slds-col">
				<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
					<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#hierarchy"></use>
				</svg>
				{$APP.LBL_ACCOUNT_HIERARCHY}
			</div>
		</h2>
		<div class="slds-media__figure slds-col slds-size_1-of-2 cblds-t-align_right">
			<button class="slds-button slds-button_brand" onclick="window.history.back();">{$APP.LBL_BACK}</button>
		</div>
	</header>
	<div id="ListViewContents">
		{foreach key=header item=detail from=$ACCOUNT_HIERARCHY}
			{if $header eq 'header'}
				<table class="slds-table slds-table_cell-buffer slds-table_bordered">
				<thead>
					<tr class="slds-line-height_reset">
					{foreach key=header item=headerfields from=$detail}
						<th scope="col">{$headerfields}</th>
					{/foreach}
					</tr>
				</thead>
				<tbody>
			{elseif $header eq 'entries'}
				{foreach key=header item=entriesfields from=$detail}
				<tr class="slds-hint-parent">
					{foreach key=header item=listfields from=$entriesfields}
					<td scope="row">{$listfields}</td>
					{/foreach}
				</tr>
				{/foreach}
			</table>
			{/if}
		{/foreach}
	</div>
</div>
</section>