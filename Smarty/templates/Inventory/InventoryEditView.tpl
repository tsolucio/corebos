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
</script>
{/if}
{if vt_hasRTE()}
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
{/if}

{include file='Buttons_List.tpl'}

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
   <tr>
	<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>

	<td class="showPanelBg" valign=top width=100%>
		{*<!-- PUBLIC CONTENTS STARTS-->*}
		<div class="small" style="padding:20px">
			{if $OP_MODE eq 'edit_view'}
				{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
				{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
				<span class="lvtHeaderText"><font color="purple">[ {$USE_ID_VALUE} ] </font>{$NAME} - {$APP.LBL_EDITING} {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</span> <br>
				{$UPDATEINFO}
			{/if}
			{if $OP_MODE eq 'create_view'}
				{if $DUPLICATE neq 'true'}
					<span class="lvtHeaderText">{$APP.LBL_CREATING} {$SINGLE_MOD|@getTranslatedString:$MODULE}</span> <br>
				{else}
					<span class="lvtHeaderText">{$APP.LBL_DUPLICATING} "{$NAME}" </span> <br>
				{/if}
			{/if}

			<hr noshade size=1>
			<br>
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
									   <tr>
										<td colspan=4 style="padding:5px">
											<div align="center">
												<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='Save'; displaydeleted(); return validateInventory('{$MODULE}')" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
												{if isset($SandRActive) && $SandRActive!=0 && (!isset($MED1x1MODE) || $MED1x1MODE==0)}
												<input title="{$APP.LBL_SAVEREPEAT_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVEREPEAT_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.saverepeat.value='1';this.form.action.value='Save'; displaydeleted(); return formValidate();" type="submit" name="button" value="  {$APP.LBL_SAVEREPEAT_BUTTON_LABEL}  ">
												{/if}
												{if isset($MED1x1MODE) && $MED1x1MODE!=0}
												<input title="{$APP.LBL_SKIP_BUTTON_TITLE}" accessKey="{$APP.LBL_SKIP_BUTTON_KEY}" class="crmbutton small cancel" onclick="this.form.saverepeat.value='skip';this.form.action.value='Save'; displaydeleted();" type="submit" name="button" value="  {$APP.LBL_SKIP_BUTTON_LABEL}  ">
												{/if}
												<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="{if isset($smarty.request.Module_Popup_Edit)}window.close(){elseif isset($CANCELGO)}window.location.href='{$CANCELGO}'{else}window.history.back(){/if};" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
											</div>
										</td>
									   </tr>

									   <!-- included to handle the edit fields based on ui types -->
									   {foreach key=header item=data from=$BLOCKS}
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
										{/if}

										<tr style="height:25px"><td>&nbsp;</td></tr>

									   {/foreach}


									   <!-- Added to display the Product Details in Inventory-->
									   {if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice' || $MODULE eq 'Issuecards' || $MODULE eq 'Receiptcards'}
									   <tr>
										<td colspan=4>
										{if $OP_MODE eq 'create_view'}
											{if isset($AVAILABLE_PRODUCTS) && $AVAILABLE_PRODUCTS eq 'true'}
												{include file="Inventory/ProductDetailsEditView.tpl"}
											{else}
												{include file="Inventory/ProductDetails.tpl"}
											{/if}
										{else}
											{include file="Inventory/ProductDetailsEditView.tpl"}
										{/if}
										</td>
									   </tr>
									   {/if}

									   <tr>
										<td  colspan=4 style="padding:5px">
											<div align="center">
												<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='Save';  displaydeleted();return validateInventory('{$MODULE}')" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
												{if isset($SandRActive) && $SandRActive!=0 && (!isset($MED1x1MODE) || $MED1x1MODE==0)}
												<input title="{$APP.LBL_SAVEREPEAT_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVEREPEAT_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.saverepeat.value='1';this.form.action.value='Save'; displaydeleted(); return formValidate();" type="submit" name="button" value="  {$APP.LBL_SAVEREPEAT_BUTTON_LABEL}  ">
												{/if}
												{if isset($MED1x1MODE) && $MED1x1MODE!=0}
												<input title="{$APP.LBL_SKIP_BUTTON_TITLE}" accessKey="{$APP.LBL_SKIP_BUTTON_KEY}" class="crmbutton small cancel" onclick="this.form.saverepeat.value='skip';this.form.action.value='Save'; displaydeleted();" type="submit" name="button" value="  {$APP.LBL_SKIP_BUTTON_LABEL}  ">
												{/if}
												<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="{if isset($smarty.request.Module_Popup_Edit)}window.close(){elseif isset($CANCELGO)}window.location.href='{$CANCELGO}'{else}window.history.back(){/if};" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
											</div>
										</td>
									   </tr>
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
