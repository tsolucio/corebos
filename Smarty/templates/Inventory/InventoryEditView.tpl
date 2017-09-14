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
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="include/js/Inventory.js"></script>
<script type="text/javascript" src="modules/Services/Services.js"></script>
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
{if $PICKIST_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim} (new FieldDependencies({$PICKIST_DEPENDENCY_DATASOURCE})).init() {rdelim});
	var Inventory_ListPrice_ReadOnly = '{if isset($Inventory_ListPrice_ReadOnly)}{$Inventory_ListPrice_ReadOnly}{/if}';
</script>
{/if}
{if vt_hasRTE()}
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
{/if}

{include file='Buttons_List.tpl'}

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
		<td>
			{*<!-- PUBLIC CONTENTS STARTS-->*}
			<div class="slds-truncate">
				<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
					<tr class="slds-text-title--caps">
						<td style="padding: 0;">
							<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilDesktop" style="height: 70px;">
								<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
									<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
										<div class="profilePicWrapper slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
											<div class="slds-media__figure slds-icon forceEntityIcon">
												<span class="photoContainer forceSocialPhoto">
													<div class="small roundedSquare forceEntityIcon img-background">
														<span class="uiImage">
															{if $MODULE eq 'Quotes'}
																<img src="{'quotes_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Quotes" title="Quotes">
															{elseif $MODULE eq 'SalesOrder'}
																<img src="{'salesorder_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="SalesOrder" title="SalesOrder">
															{elseif $MODULE eq 'Invoice'}
																<img src="{'invoice_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Invoice" title="Invoice" style="height:1.8rem; padding-top: 1px;">
															{elseif $MODULE eq 'PriceBooks'}
																<img src="{'pricebook_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="PriceBooks" title="PriceBooks">
															{elseif $MODULE eq 'Services'}
																<img src="{'custom_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Services" title="Services">
															{elseif $MODULE eq 'Products'}
																<img src="{'product_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="ProductsProducts" title="Products">
															{elseif $MODULE eq 'Vendors'}
																<img src="{'vendors_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Vendors" title="Vendors">
															{elseif $MODULE eq 'PurchaseOrder'}
																<img src="{'purchase_order_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="PurchaseOrder" title="PurchaseOrder" >
															{/if}
														</span>
													</div>
												</span>
											</div>
										</div>
										<div class="slds-media__body">
										{if $OP_MODE eq 'edit_view'}
											{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
											{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
											<p class="slds-text-heading--label slds-line-height--reset">{$APP.LBL_EDITING} {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</p>
											<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
												<span class="uiOutputText"><font color="purple">[ {$USE_ID_VALUE} ] </font>{$NAME}</span>
												<span class="small" style="text-transform: capitalize;">{$UPDATEINFO}</span>
											</h1>
										{/if}

										{if $OP_MODE eq 'create_view'}
											{if $DUPLICATE neq 'true'}
												<p class="slds-text-heading--label slds-line-height--reset" style="opacity: 1;">{$APP.LBL_CREATING} {$SINGLE_MOD|@getTranslatedString:$MODULE}</p>
											{else}
												<p class="slds-text-heading--label slds-line-height--reset" style="opacity: 1;">{$APP.LBL_DUPLICATING} "{$NAME}" </p>
											{/if}
										{/if}
										</div>
									</div>
								</div> {*/primaryFieldRow*}
							</div> {*/forceHighlightsStencilDesktop*}
						</td>
					</tr>
				</table>
				<br>
				{include file='EditViewHidden.tpl'}

				{if $OP_MODE eq 'create_view'}
					<input type="hidden" name="convert_from" value="{$CONVERT_MODE}">
					<input type="hidden" name="duplicate_from" value="{if isset($DUPLICATE_FROM)}{$DUPLICATE_FROM}{/if}">
				{/if}
				<input name='search_url' id="search_url" type='hidden' value='{$SEARCH}'>

				{*<!-- Account details tabs -->*}
				<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
					<tr>
						<td valign=top align=left >
							<!-- General details -->
							<table class="slds-table slds-no-row-hover slds-table-moz dvtContentSpace">
								<!-- Top buttons -->
								<tr>
									<td colspan=4 style="padding:5px">
										<div align="center">
											{if $MODULE eq 'Webmails'}
												<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" onclick="this.form.action.value='Save';this.form.module.value='Webmails';this.form.send_mail.value='true';this.form.record.value='{$ID}'" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
											{else}
												<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" onclick="this.form.action.value='Save'; displaydeleted(); return formValidate();" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
											{/if}
												<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--destructive slds-button--small" onclick="{if isset($smarty.request.Module_Popup_Edit)}window.close(){else}window.history.back(){/if};" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
										</div>
									</td>
								</tr>
								<!-- End Top buttons -->
								<!-- Main Content -->
								<tr>
									<td valign="top" style="padding: 0;">
										<div class="slds-tabs--scoped">
											<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
												<li class="slds-tabs--scoped__item active" title="{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}" role="presentation">
													<a class="slds-tabs--scoped__link "  href="javascript:void(0);"  role="tab" tabindex="0" aria-selected="true" aria-controls="tab--scoped-1" id="tab--scoped--1__item">{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</a>
												</li>
											</ul>

											<div id="tab--scoped-1" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
												<!-- Content here -->
												<table class="slds-table slds-no-row-hover slds-table-moz" style="border-collapse: separate;border-spacing: 1rem 2rem;">
													<!-- included to handle the edit fields based on ui types -->
													{foreach key=header item=data from=$BLOCKS}
														<tr class="blockStyleCss">
															<td class="detailViewContainer">
																<div id="tbl{$header|replace:' ':''}Head">
																	{if isset($MOD.LBL_ADDRESS_INFORMATION) && $header==$MOD.LBL_ADDRESS_INFORMATION && ($MODULE == 'Accounts' || $MODULE == 'Contacts' || $MODULE == 'Quotes' || $MODULE == 'PurchaseOrder' || $MODULE == 'SalesOrder'|| $MODULE == 'Invoice') && $SHOW_COPY_ADDRESS eq 1}
																		<div class="forceRelatedListSingleContainer">
																			<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																				<div class="slds-card__header slds-grid">
																					<header class="slds-media slds-media--center slds-has-flexi-truncate">
																						<div class="slds-media__body">
																							<h2>
																								<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																									<b>{$header}</b>
																								</span>
																							</h2>
																						</div>
																					</header>
																					<div class="slds-no-flex" data-aura-rendered-by="1224:0">
																						<div class="forceRelatedListSingleContainer">
																							<span class="slds-radio">
																								<input name="cpy" onclick="return copyAddressLeft(EditView)" id="shipping-address" type="radio">
																								<label class="slds-radio__label" for="shipping-address">
																									<span class="slds-radio--faux"></span>
																									<span class="slds-form-element__label"><b>{$APP.LBL_RCPY_ADDRESS}</b></span>
																								</label>
																							</span>
																						</div>
																						<div class="forceRelatedListSingleContainer">
																							<span class="slds-radio">
																								<input name="cpy" onclick="return copyAddressRight(EditView)" id="billing-address" type="radio">
																								<label class="slds-radio__label" for="billing-address">
																									<span class="slds-radio--faux"></span>
																									<span class="slds-form-element__label"><b>{$APP.LBL_LCPY_ADDRESS}</b></span>
																								</label>
																							</span>
																						</div>
																					</div>
																				</div>
																			</article>
																		</div>
																	{else}
																		<div class="forceRelatedListSingleContainer">
																			<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																				<div class="slds-card__header slds-grid">
																					<header class="slds-media slds-media--center slds-has-flexi-truncate">
																						<div class="slds-media__body">
																							<h2>
																								<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																									<b>{$header}</b>
																								</span>
																							</h2>
																						</div>
																					</header>
																				</div>
																			</article>
																		</div>
																	{/if}
																</div>

																{if $CUSTOMBLOCKS.$header.custom}
																	{include file=$CUSTOMBLOCKS.$header.tpl}
																{else}
																<!-- Handle the ui types display -->
																{include file="DisplayFields.tpl"}
																{/if}
															</td>
														</tr>
													{/foreach}

													<!-- Added to display the Product Details in Inventory-->
													{if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice' || $MODULE eq 'Issuecards'}
														<div>
															{if $OP_MODE eq 'create_view'}
																{if isset($AVAILABLE_PRODUCTS) && $AVAILABLE_PRODUCTS eq 'true'}
																	{include file="Inventory/ProductDetailsEditView.tpl"}
																{else}
																	{include file="Inventory/ProductDetails.tpl"}
																{/if}
															{else}
																{include file="Inventory/ProductDetailsEditView.tpl"}
															{/if}
														</div>
													{/if}
												</table>
												<!-- Content here -->
											</div>
										</div>
									</td>
								</tr>
								<!-- End Main Content -->
								<!-- Bottom buttons -->
								<tr>
									<td colspan=4 style="padding:5px">
										<div align="center">
											<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" onclick="this.form.action.value='Save';  displaydeleted();return validateInventory('{$MODULE}')" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
											<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--destructive slds-button--small" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<!-- Inventory Actions - ends -->
		</td>
	</tr>
</table>
</form>

<!-- This div is added to get the left and top values to show the tax details-->
<div id="tax_container" style="display:none; position:absolute; z-index:1px;"></div>

<script>
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME})
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL})
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE})
	var product_labelarr = {ldelim}CLEAR_COMMENT:'{$APP.LBL_CLEAR_COMMENT}',
				DISCOUNT:'{$APP.LBL_DISCOUNT}',
				TOTAL_AFTER_DISCOUNT:'{$APP.LBL_TOTAL_AFTER_DISCOUNT}',
				TAX:'{$APP.LBL_TAX}',
				ZERO_DISCOUNT:'{$APP.LBL_ZERO_DISCOUNT}',
				PERCENT_OF_PRICE:'{$APP.LBL_OF_PRICE}',
				DIRECT_PRICE_REDUCTION:'{$APP.LBL_DIRECT_PRICE_REDUCTION}'{rdelim};
	var ProductImages=new Array();
	var count=0;
	function delRowEmt(imagename)
	{ldelim}
		ProductImages[count++]=imagename;
		multi_selector.current_element.disabled = false;
		multi_selector.count--;
	{rdelim}
	function displaydeleted()
	{ldelim}
		if(ProductImages.length > 0)
			document.EditView.del_file_list.value=ProductImages.join('###');
	{rdelim}
</script>

<!-- vtlib customization: Help information assocaited with the fields -->
{if $FIELDHELPINFO}
<script type='text/javascript'>
{literal}var fieldhelpinfo = {}; {/literal}
{foreach item=FIELDHELPVAL key=FIELDHELPKEY from=$FIELDHELPINFO}
	fieldhelpinfo["{$FIELDHELPKEY}"] = "{$FIELDHELPVAL}";
{/foreach}
</script>
{/if}
<!-- END -->
