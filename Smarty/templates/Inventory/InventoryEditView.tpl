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
<script type="text/javascript" src="modules/Services/Services.js"></script>
{if $FIELD_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="include/js/FieldDepFunc.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim} (new FieldDependencies({$FIELD_DEPENDENCY_DATASOURCE})).init() {rdelim});
	var Inventory_ListPrice_ReadOnly = '{if isset($Inventory_ListPrice_ReadOnly)}{$Inventory_ListPrice_ReadOnly}{/if}';
	var Inventory_Comment_Style = '{if isset($Inventory_Comment_Style)}{$Inventory_Comment_Style}{else}width:70%;height:40px;{/if}';
</script>
{/if}
{if vt_hasRTE()}
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
{if vt_hasRTESpellcheck()}
<script type="text/javascript" src="include/ckeditor/config_spellcheck.js"></script>
{/if}
{/if}

{include file='Buttons_List.tpl' isEditView=true}

{*<!-- Contents -->*}
<table class="slds-m-around_medium" style="width: 98%;">
	<tr>
	<td class="showPanelBg" valign=top width=100%>
		{*<!-- PUBLIC CONTENTS STARTS-->*}
		<div class="small" style="padding:20px">
			{include file='EditViewHidden.tpl'}

			{*<!-- Account details tabs -->*}
			<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
				<tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
					<tr>
						<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
						<td class="dvtSelectedCell" align=center nowrap> {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>
						<td class="dvtTabCache" style="width:10px">&nbsp;</td>
						<td class="dvtTabCache" style="width:100%">&nbsp;</td>
					</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td valign=top align=left >
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
					<tr>
						<td align=left>
							{*<!-- content cache -->*}
							<table border=0 cellspacing=0 cellpadding=0 width=100%>
							<tr>
								<td style="padding:10px">
									<!-- General details -->
									<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small createview_table">
										<!-- included to handle the edit fields based on ui types -->
										{foreach key=header item=data from=$BLOCKS name=BLOCKS}
										<tr id="tbl{$header|replace:' ':''}Head">
										{if isset($MOD.LBL_ADDRESS_INFORMATION) && $header==$MOD.LBL_ADDRESS_INFORMATION && ($MODULE == 'Accounts' || $MODULE == 'Contacts' || $MODULE == 'Quotes' || $MODULE == 'PurchaseOrder' || $MODULE == 'SalesOrder'|| $MODULE == 'Invoice') && $SHOW_COPY_ADDRESS eq 1}
											<td colspan=2 class="detailedViewHeader">
											<b>{$header}</b></td>
											<td class="detailedViewHeader">
											<input name="cpy" onclick="return copyAddressLeft(EditView)" type="radio"><b>{$APP.LBL_RCPY_ADDRESS}</b></td>
											<td class="detailedViewHeader">
											<input name="cpy" onclick="return copyAddressRight(EditView)" type="radio"><b>{$APP.LBL_LCPY_ADDRESS}</b></td>
										{else}
											<td colspan=4 class="detailedViewHeader">
											<b>{$header}</b>
										</td>
										{/if}
										</tr>

										{if $CUSTOMBLOCKS.$header.custom}
											{include file=$CUSTOMBLOCKS.$header.tpl}
										{else}
											<!-- Handle the ui types display -->
											{include file="DisplayFields.tpl"}
											{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.EDITVIEWWIDGET)}
												{* Embed EditViewWidget block:// type if any *}
												{foreach item=CUSTOM_LINK_EDITVIEWWIDGET from=$CUSTOM_LINKS.EDITVIEWWIDGET}
													{if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_EDITVIEWWIDGET->linkurl)}
													{if ($smarty.foreach.BLOCKS.first && $CUSTOM_LINK_EDITVIEWWIDGET->sequence <= 1)
														|| ($CUSTOM_LINK_EDITVIEWWIDGET->sequence == $smarty.foreach.BLOCKS.iteration + 1)
														|| ($smarty.foreach.BLOCKS.last && $CUSTOM_LINK_EDITVIEWWIDGET->sequence >= $smarty.foreach.BLOCKS.iteration + 1)}
														<tr>
															<td colspan=4 class="dvInnerHeader">
																<b>{$CUSTOM_LINK_EDITVIEWWIDGET->linklabel|@getTranslatedString:$MODULE}</b>
															</td>
														</tr>
														<tr name="tbl{$CUSTOM_LINK_EDITVIEWWIDGET->linklabel|replace:' ':''}Content" class="createview_field_row">
															<td colspan="4" style="padding:5px;">{process_widget widgetLinkInfo=$CUSTOM_LINK_EDITVIEWWIDGET}</td>
														</tr>
													{/if}
													{/if}
												{/foreach}
											{/if}
										{/if}

										<tr style="height:25px"><td>&nbsp;</td></tr>

										{/foreach}

										<!-- Added to display the Product Details in Inventory-->
										{if in_array($MODULE, getInventoryModules()) && $ShowInventoryLines}
										<tr>
										<td colspan=4>
											{if isset($AVAILABLE_PRODUCTS) && $AVAILABLE_PRODUCTS eq 'true'}
												{include file="Inventory/ProductDetailsEditView.tpl"}
											{else}
												{include file="Inventory/ProductDetails.tpl"}
											{/if}
										</td>
										</tr>
										{/if}
									</table>
								</td>
							</tr>
							</table>
						</td>
						<!-- Inventory Actions - ends -->
					</tr>
					</table>
				</td>
				</tr>
			</table>
		</div>
	</td>
	</tr>
</table>
</form>

<!-- This div is added to get the left and top values to show the tax details-->
<div id="tax_container" style="display:none; position:absolute; z-index:1px;"></div>

<script>
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
	var product_labelarr = {ldelim}
		CLEAR_COMMENT:'{$APP.LBL_CLEAR_COMMENT}',
		DISCOUNT:'{$APP.LBL_DISCOUNT}',
		TOTAL_AFTER_DISCOUNT:'{$APP.LBL_TOTAL_AFTER_DISCOUNT}',
		TAX:'{$APP.LBL_TAX}',
		ZERO_DISCOUNT:'{$APP.LBL_ZERO_DISCOUNT}',
		PERCENT_OF_PRICE:'{$APP.LBL_OF_PRICE}',
		DIRECT_PRICE_REDUCTION:'{$APP.LBL_DIRECT_PRICE_REDUCTION}'
	{rdelim};
	{if !empty($smarty.request.MDGridInfo)}
	{assign var='mdgridinfo' value=$smarty.request.MDGridInfo|json_decode:true}
	function windowopenermasterdetailworksave() {
		window.opener.masterdetailwork.save('{$mdgridinfo.name|vtlib_purify}', '{$mdgridinfo.module|vtlib_purify}');
	}
	corebosjshook.after(window, 'corebosjshook_submitFormForAction', windowopenermasterdetailworksave);
	{/if}
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
