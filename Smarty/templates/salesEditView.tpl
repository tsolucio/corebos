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
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
{if $PICKIST_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim} (new FieldDependencies({$PICKIST_DEPENDENCY_DATASOURCE})).init() {rdelim});
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
															<img src="{$MODULEICON|@vtiger_imageurl:$THEME}" class="icon" alt="{$MODULE}" title="{$MODULE}">
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

			{*<!-- Account details tabs -->*}
			<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
				<tr>
					<td valign=top align=left >
						<!-- General details -->
						<table class="slds-table slds-no-row-hover slds-table-moz dvtContentSpace" >
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
											<table class="slds-table slds-no-row-hover slds-table-moz" style="border-collapse: separate;border-spacing: 1rem 2rem;" ng-controller="editViewng">
												<!-- included to handle the edit fields based on ui types -->
												{foreach key=header item=data from=$BLOCKS}
												<tr class="blockStyleCss">
													<td class="detailViewContainer" valign="top">
														<!-- This is added to display the existing comments -->
														{if $header eq $APP.LBL_COMMENTS || (isset($MOD.LBL_COMMENT_INFORMATION) && $header eq $MOD.LBL_COMMENT_INFORMATION)}
														<div class="flexipageComponent" style="background-color: #fff;">
															<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
																<div class="slds-card__header slds-grid">
																	<header class="slds-media slds-media--center slds-has-flexi-truncate">
																		<div class="slds-media__body">
																			<h2 class="header-title-container" >
																				<span class="slds-text-heading--small slds-truncate actionLabel">
																					<b>{if isset($MOD.LBL_COMMENT_INFORMATION)}{$MOD.LBL_COMMENT_INFORMATION}{else}{$APP.LBL_COMMENTS}{/if}</b>
																				</span>
																			</h2>
																		</div>
																	</header>
																</div>
																<div class="slds-card__body slds-card__body--inner">
																	<div class="commentData">{$COMMENT_BLOCK}</div>
																</div>
															</article>
														</div>
														{/if}

														<div class="slds-truncate" id="tbl{$header|replace:' ':''}Head">
															{if isset($MOD.LBL_ADDRESS_INFORMATION) && $header==$MOD.LBL_ADDRESS_INFORMATION && ($MODULE == 'Accounts' || $MODULE == 'Quotes' || $MODULE == 'PurchaseOrder' || $MODULE == 'SalesOrder'|| $MODULE == 'Invoice') && $SHOW_COPY_ADDRESS eq 1}
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
															{elseif isset($MOD.LBL_ADDRESS_INFORMATION) && $header== $MOD.LBL_ADDRESS_INFORMATION && $MODULE == 'Contacts' && $SHOW_COPY_ADDRESS eq 1}
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
																						<input name="cpy" onclick="return copyAddressLeft(EditView)" id="shipping-address1" type="radio">
																						<label class="slds-radio__label" for="shipping-address1">
																							<span class="slds-radio--faux"></span>
																							<span class="slds-form-element__label"><b>{$APP.LBL_CPY_OTHER_ADDRESS}</b></span>
																						</label>
																					</span>
																				</div>
																				<div class="forceRelatedListSingleContainer">
																					<span class="slds-radio">
																						<input name="cpy" onclick="return copyAddressRight(EditView)" id="billing-address2" type="radio">
																						<label class="slds-radio__label" for="billing-address2">
																							<span class="slds-radio--faux"></span>
																							<span class="slds-form-element__label"><b>{$APP.LBL_CPY_MAILING_ADDRESS}</b></span>
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
														<!-- Added to display the Product Details in Inventory-->
														{if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice'}
														<div>
															{include file="ProductDetailsEditView.tpl"}
														</div>
														{/if}
													</td>
												</tr>
												{/foreach}
											</table>
											<!-- Content here -->
										</div>
									</div>
								</td>
							</tr>
							<!-- End Main Content -->
							<!-- Bottom buttons -->
							<tr>
								<td  colspan=4 style="padding:5px">
									<div align="center">
										{if $MODULE eq 'Emails'}
											<input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small create" onclick="window.open('index.php?module=Users&action=lookupemailtemplates&entityid={$ENTITY_ID}&entity={$ENTITY_TYPE}','emailtemplate','top=100,left=200,height=400,width=300,menubar=no,addressbar=no,status=yes')" type="button" name="button" value="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}">
											<input title="{$MOD.LBL_SEND}" accessKey="{$MOD.LBL_SEND}" class="crmbutton small save" onclick="this.form.action.value='Save';this.form.send_mail.value='true'; return formValidate()" type="submit" name="button" value="  {$MOD.LBL_SEND}  " >
										{/if}
										{if $MODULE eq 'Webmails'}
											<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" onclick="this.form.action.value='Save';this.form.module.value='Webmails';this.form.send_mail.value='true';this.form.record.value='{$ID}'" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
										{else}
											<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" onclick="this.form.action.value='Save';  displaydeleted();return formValidate();" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
										{/if}
											<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--destructive slds-button--small" onclick="{if isset($smarty.request.Module_Popup_Edit)}window.close(){else}window.history.back(){/if};" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
									</div>
								</td>
							</tr>
							<!-- End Bottom buttons -->
						</table>
					</td>
				</tr>
			</table>
		</div>
	</td>
	</tr>
</table>
<!--added to fix 4600-->
<input name='search_url' id="search_url" type='hidden' value='{$SEARCH}'>
</form>

<script>
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});

	var ProductImages=new Array();
	var count=0;
	function delRowEmt(imagename)
	{ldelim}
		ProductImages[count++]=imagename;
	{rdelim}
	function displaydeleted()
	{ldelim}
		var imagelists='';
		for(var x = 0; x < ProductImages.length; x++)
		{ldelim}
			imagelists+=ProductImages[x]+'###';
		{rdelim}

		if(imagelists != '')
			document.EditView.imagelist.value=imagelists
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
