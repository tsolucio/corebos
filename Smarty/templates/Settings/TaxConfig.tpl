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
<script type="text/javascript" src="include/js/Inventory.js"></script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					{include file='SetMenu.tpl'}
						<!-- DISPLAY Tax Calculations-->
						{if $EDIT_MODE eq 'true'}
							{assign var=formname value='EditTax'}
							{assign var=shformname value='SHEditTax'}
						{else}
							{assign var=formname value='ListTax'}
							{assign var=shformname value='SHListTax'}
						{/if}

						<!-- This table is used to display the Tax Configuration values-->
						<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
							<tr class="slds-text-title--caps">
								<td style="padding: 0;">
									<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
										<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
											<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
												<!-- Image -->
												<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
													<div class="slds-media__figure slds-icon forceEntityIcon">
														<span class="photoContainer forceSocialPhoto">
															<div class="small roundedSquare forceEntityIcon">
																<span class="uiImage">
																	<img src="{'taxConfiguration.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}">
																</span>
															</div>
														</span>
													</div>
												</div>
												<!-- Title and help text -->
												<div class="slds-media__body">
													<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
														<span class="uiOutputText" style="width: 100%;">
															<b>
																<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > 
																<strong>
																	{if $EDIT_MODE eq 'true'}
																		{$MOD.LBL_EDIT} {$MOD.LBL_TAX_SETTINGS}
																	{else}
																		{$MOD.LBL_TAX_SETTINGS}
																	{/if}
																</strong>
															</b>
														</span>
														<span class="small">{$MOD.LBL_TAX_DESC}</span>
													</h1>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</table>

						<table border=0 cellspacing=0 cellpadding=10 width=100%>
							<tr>
								<td style="border-right:1px dotted #CCCCCC;" valign="top">
									<!-- if EDIT_MODE is true then Textbox will be displayed else the value will be displayed-->
									<form name="{$formname}" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
										<input type="hidden" name="module" value="Settings">
										<input type="hidden" name="action" value="">
										<input type="hidden" name="parenttab" value="Settings">
										<input type="hidden" name="save_tax" value="">
										<input type="hidden" name="edit_tax" value="">
										<input type="hidden" name="add_tax_type" value="">

										<!-- Table to display the Product Tax Add and Edit Buttons - Starts -->
										<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
											<tr>
												<td class="big" colspan="3">

													<div class="forceRelatedListSingleContainer">
														<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
															<div class="slds-card__header slds-grid">
																<header class="slds-media slds-media--center slds-has-flexi-truncate">
																	<div class="slds-media__body">
																		<h2>
																			<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																				<strong>{$MOD.LBL_PRODUCT_TAX_SETTINGS} </strong>
																			</span>
																		</h2>
																	</div>
																</header>
																<div class="slds-no-flex">
																	<span id="td_add_tax">
																	{if $EDIT_MODE neq 'true'}
																		<input title="{$MOD.LBL_ADD_TAX_BUTTON}" accessKey="{$MOD.LBL_ADD_TAX_BUTTON}" onclick="fnAddTaxConfigRow('');" type="button" name="button" value="{$MOD.LBL_ADD_TAX_BUTTON}" class="slds-button slds-button--small slds-button_success">
																	{/if}
																	</span>

																	{if $EDIT_MODE eq 'true'}
																		<input class="slds-button slds-button--small slds-button_success" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.save_tax.value='true'; this.form.parenttab.value='Settings'; return validateTaxes('tax_count');" type="submit" name="button2" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">&nbsp;
																		<input class="slds-button slds-button--small slds-button--destructive" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.module.value='Settings'; this.form.save_tax.value='false'; this.form.parenttab.value='Settings';" type="submit" name="button22" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
																	{elseif $TAX_COUNT > 0}
																		<input class="slds-button slds-button--small slds-button--brand" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.add_tax_type.value=''; this.form.edit_tax.value='true'; this.form.parenttab.value='Settings';" type="submit" name="button" value="  {$APP.LBL_EDIT_BUTTON_LABEL}  " >
																	{/if}
																</div>
															</div>
														</article>
													</div>

													<!-- Table to display the List of Product Tax values - Starts -->
													<table id="add_tax" border=0 cellspacing=0 cellpadding=5 width=100% class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table listRow">
														{if $TAX_COUNT eq 0}
															<tr><td>{$MOD.LBL_NO_TAXES_AVAILABLE}. {$MOD.LBL_PLEASE} {$MOD.LBL_ADD_TAX_BUTTON}.</td></tr>
														{else}
															{foreach item=tax key=count from=$TAX_VALUES}
																<!-- To set the color coding for the taxes which are active and inactive-->
																{if $tax.deleted eq 0}
																	<tr><!-- set color to taxes which are active now-->
																{else}
																	<tr><!-- set color to taxes which are disabled now-->
																{/if}
																	<!--assinging tax label name for javascript validation-->
																	{assign var=tax_label value="taxlabel_"|cat:$tax.taxname}
																	<td width=35% class="dvtCellLabel small" >
																		{if $EDIT_MODE eq 'true'}
																			{assign var = pstax value = $tax.taxlabel}
																			<input name="{$pstax|bin2hex}" id={$tax_label} type="text" value="{$tax.taxlabel}" class="slds-input small">
																		{else}
																			{$tax.taxlabel}
																		{/if}
																	</td>
																	<td width=55% class="dvtCellInfo small">
																		{if $EDIT_MODE eq 'true'}
																			<input name="{$tax.taxname}" id="{$tax.taxname}" type="text" value="{$tax.percentage}" class="slds-input small">&nbsp;%
																		{else}
																			{$tax.percentage}&nbsp;%
																		{/if}
																	</td>
																	<td width=10% class="dvtCellInfo small">
																		{if $tax.deleted eq 0}
																			<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&disable=true&taxname={$tax.taxname}">
																				<img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLED}" title="{$MOD.LBL_ENABLED}">
																			</a>
																		{else}
																			<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&enable=true&taxname={$tax.taxname}">
																				<img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLED}" title="{$MOD.LBL_DISABLED}">
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

												</td>
											</tr>
										</table>
										<!-- Table to display the Product Tax Add and Edit Buttons - Ends -->

									</form>
								</td>
								<!-- Shipping Tax Config Table Starts Here -->
								<td width="50%" valign="top">
									<form name="{$shformname}" method="POST" action="index.php">
										<input type="hidden" name="module" value="Settings">
										<input type="hidden" name="action" value="">
										<input type="hidden" name="parenttab" value="Settings">
										<input type="hidden" name="sh_save_tax" value="">
										<input type="hidden" name="sh_edit_tax" value="">
										<input type="hidden" name="sh_add_tax_type" value="">

										<!-- Table to display the S&H Tax Add and Edit Buttons - Starts -->
										<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
											<tr>
												<td class="big" colspan="3">

													<div class="forceRelatedListSingleContainer">
														<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
															<div class="slds-card__header slds-grid">
																<header class="slds-media slds-media--center slds-has-flexi-truncate">
																	<div class="slds-media__body">
																		<h2>
																			<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																				<strong>{$MOD.LBL_SHIPPING_HANDLING_TAX_SETTINGS}</strong>
																			</span>
																		</h2>
																	</div>
																</header>
																<div class="slds-no-flex">
																	<span id="td_sh_add_tax">
																		{if $SH_EDIT_MODE neq 'true'}
																			<input title="{$MOD.LBL_ADD_TAX_BUTTON}" accessKey="{$MOD.LBL_ADD_TAX_BUTTON}" onclick="fnAddTaxConfigRow('sh');" type="button" name="button" value="  {$MOD.LBL_ADD_TAX_BUTTON}  " class="slds-button--small slds-button slds-button_success">
																		{/if}
																	</span>
																	{if $SH_EDIT_MODE eq 'true'}
																		<input class="slds-button slds-button--small slds-button_success" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.sh_save_tax.value='true'; this.form.parenttab.value='Settings'; return validateTaxes('sh_tax_count');" type="submit" name="button2" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
																		&nbsp;
																		<input class="slds-button slds-button--small slds-button--destructive" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.module.value='Settings'; this.form.sh_save_tax.value='false'; this.form.parenttab.value='Settings';" type="submit" name="button22" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
																	{elseif $SH_TAX_COUNT > 0}
																		<input class="slds-button slds-button--small slds-button--brand" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.sh_add_tax_type.value=''; this.form.sh_edit_tax.value='true'; this.form.parenttab.value='Settings';" type="submit" name="button" value="  {$APP.LBL_EDIT_BUTTON_LABEL}  ">
																	{/if}
																</div>
															</div>
														</article>
													</div>

													<!-- Table to display the List of S&H Tax Values - Starts -->
													<table id="sh_add_tax" class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table listRow">
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
																		<td width=35% class="dvtCellLabel small">
																			{if $SH_EDIT_MODE eq 'true'}
																				{assign var = shtax value = $tax.taxlabel}
																				<input name="{$shtax|bin2hex}" id="{$tax_label}" type="text" value="{$tax.taxlabel}" class="slds-input small">
																			{else}
																				{$tax.taxlabel}
																			{/if}
																		</td>
																		<td width=55% class="dvtCellInfo small">
																			{if $SH_EDIT_MODE eq 'true'}
																				<input name="{$tax.taxname}" id="{$tax.taxname}" type="text" value="{$tax.percentage}" class="slds-input small">
																				&nbsp;%
																			{else}
																				{$tax.percentage}&nbsp;%
																			{/if}
																		</td>
																		<td width=10% class="dvtCellInfo small">
																			{if $tax.deleted eq 0}
																				<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&sh_disable=true&sh_taxname={$tax.taxname}">
																					<img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE}" title="{$MOD.LBL_ENABLE}">
																				</a>
																			{else}
																				<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&sh_enable=true&sh_taxname={$tax.taxname}">
																					<img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLE}" title="{$MOD.LBL_DISABLE}">
																				</a>
																			{/if}
																		</td>
																	</tr>
															{/foreach}
															{if $SH_EDIT_MODE eq 'true'}
															<input type="hidden" id="sh_tax_count" value="{$count}">
															{/if}
														{/if}
													</table>
													<!-- Table to display the List of S&H Tax Values - Ends -->
												</td>
											</tr>
										</table>
										<!-- Table to display the S&H Tax Add and Edit Buttons - Ends -->
									</form>
								</td>
								<!-- Shipping Tax Ends Here -->
							</tr>
						</table>

						<!-- <table border=0 cellspacing=0 cellpadding=5 width=100% >
							<tr>
								<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
							</tr>
						</table> -->

					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>


<script>
var tax_labelarr = {ldelim}SAVE_BUTTON:'{$APP.LBL_SAVE_BUTTON_LABEL}',
 CANCEL_BUTTON:'{$APP.LBL_CANCEL_BUTTON_LABEL}',
 TAX_NAME:'{$APP.LBL_TAX_NAME}',
 TAX_VALUE:'{$APP.LBL_TAX_VALUE}'{rdelim};
</script>
