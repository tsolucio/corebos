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

<script>
if(typeof(e) != 'undefined')
	window.captureEvents(Event.MOUSEMOVE);

//  window.onmousemove= displayCoords;
//  window.onclick = fnRevert;
function displayCoords(currObj,obj,mode,curr_row) 
{ldelim}
	if(mode != 'discount_final' && mode != 'sh_tax_div_title' && mode != 'group_tax_div_title')
	{ldelim}
		var curr_productid = document.getElementById("hdnProductId"+curr_row).value;
		if(curr_productid == '')
		{ldelim}
			alert("{$APP.PLEASE_SELECT_LINE_ITEM}");
			return false;
		{rdelim}
		var curr_quantity = document.getElementById("qty"+curr_row).value;
		if(curr_quantity == '')
		{ldelim}
			alert("{$APP.PLEASE_FILL_QUANTITY}");
			return false;
		{rdelim}
	{rdelim}

	//Set the Header value for Discount
	if(mode == 'discount')
	{ldelim}
		document.getElementById("discount_div_title"+curr_row).innerHTML = '<b>{$APP.LABEL_SET_DISCOUNT_FOR_COLON} '+document.getElementById("productTotal"+curr_row).innerHTML+'</b>';
	{rdelim}
	else if(mode == 'tax')
	{ldelim}
		document.getElementById("tax_div_title"+curr_row).innerHTML = "<b>{$APP.LABEL_SET_TAX_FOR} "+document.getElementById("totalAfterDiscount"+curr_row).innerHTML+'</b>';
	{rdelim}
	else if(mode == 'discount_final')
	{ldelim}
		document.getElementById("discount_div_title_final").innerHTML = '<b>{$APP.LABEL_SET_DISCOUNT_FOR} '+document.getElementById("netTotal").innerHTML+'</b>';
	{rdelim}
	else if(mode == 'sh_tax_div_title')
	{ldelim}
		document.getElementById("sh_tax_div_title").innerHTML = '<b>{$APP.LABEL_SET_SH_TAX_FOR_COLON} '+document.getElementById("shipping_handling_charge").value+'</b>';
	{rdelim}
	else if(mode == 'group_tax_div_title')
	{ldelim}
		var net_total_after_discount = eval(document.getElementById("netTotal").innerHTML)-eval(document.getElementById("discountTotal_final").innerHTML);
		document.getElementById("group_tax_div_title").innerHTML = '<b>{$APP.LABEL_SET_GROUP_TAX_FOR_COLON} '+net_total_after_discount+'</b>';
	{rdelim}

	fnvshobj(currObj,'tax_container');
	if(document.all)
	{ldelim}
		var divleft = document.getElementById("tax_container").style.left;
		var divabsleft = divleft.substring(0,divleft.length-2);
		document.getElementById(obj).style.left = eval(divabsleft) - 120;

		var divtop = document.getElementById("tax_container").style.top;
		var divabstop =  divtop.substring(0,divtop.length-2);
		document.getElementById(obj).style.top = eval(divabstop);
	{rdelim}else
	{ldelim}
		document.getElementById(obj).style.left =  document.getElementById("tax_container").left;
		document.getElementById(obj).style.top = document.getElementById("tax_container").top;
	{rdelim}
	document.getElementById(obj).style.display = "block";

{rdelim}

	function doNothing(){ldelim}
	{rdelim}
	
	function fnHidePopDiv(obj){ldelim}
		document.getElementById(obj).style.display = 'none';
	{rdelim}

	var moreInfoFields = Array({$moreinfofields});
</script>

<tr><td colspan="4" align="left">

<table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="crmTable" id="proTab">
	<tr>
			<td colspan="3" class="dvInnerHeader">
		<b>{$APP.LBL_ITEM_DETAILS}</b>
	</td>
	
	<td class="dvInnerHeader" align="center" colspan="2">
		<input type="hidden" value="{$INV_CURRENCY_ID}" id="prev_selected_currency_id" />
		<b>{$APP.LBL_CURRENCY}</b>&nbsp;&nbsp;
		<select class="small" id="inventory_currency" name="inventory_currency" onchange="updatePrices();">
		{foreach item=currency_details key=count from=$CURRENCIES_LIST}
			{if $currency_details.curid eq $INV_CURRENCY_ID}
				{assign var=currency_selected value="selected"}
			{else}
				{assign var=currency_selected value=""}
			{/if}
			<OPTION value="{$currency_details.curid}" {$currency_selected}>{$currency_details.currencylabel|@getTranslatedCurrencyString} ({$currency_details.currencysymbol})</OPTION>
		{/foreach}
		</select>
	</td>
	
	<td class="dvInnerHeader" align="center" colspan="2">
		<b>{$APP.LBL_TAX_MODE}</b>&nbsp;&nbsp;
		<select id="taxtype" name="taxtype" onchange="decideTaxDiv(); calcTotal();">
			{if $TAX_TYPE eq 'group'}
				<OPTION value="individual">{$APP.LBL_INDIVIDUAL}</OPTION>
				<OPTION value="group" selected>{$APP.LBL_GROUP}</OPTION>
			{else}
				<OPTION value="individual" selected>{$APP.LBL_INDIVIDUAL}</OPTION>
				<OPTION value="group">{$APP.LBL_GROUP}</OPTION>
			{/if}
		</select>
	</td>
	</tr>

	<!-- Header for the Product Details -->
	<tr valign="top">
	<td width=5% valign="top" class="lvtCol" align="right"><b>{$APP.LBL_TOOLS}</b></td>
	<td width=35% class="lvtCol"><font color='red'>*</font><b>{$APP.LBL_ITEM_NAME}</b></td>
	<td width=20% class="lvtCol"><b>{$APP.LBL_INFORMATION}</b></td>
	<td width=10% class="lvtCol"><b>{$APP.LBL_QTY}</b></td>
	<td width=10% class="lvtCol" align="right"><b>{$APP.LBL_LIST_PRICE}</b></td>
	<td width=10% nowrap class="lvtCol" align="right"><b>{$APP.LBL_TOTAL}</b></td>
	<td width=10% valign="top" class="lvtCol" align="right"><b>{$APP.LBL_NET_PRICE}</b></td>
	</tr>

	<tr valign="top" id="row1">

	<!-- column 1 - delete link - starts -->
	<td class="crmTableRow small lineOnTop">&nbsp;
		<input type="hidden" id="deleted1" name="deleted1" value="0">
		<input type="hidden" id="lineitem_id1" name="lineitem_id1" value="1">
	</td>

	<!-- column 2 - Product Name - starts -->
	<td class="crmTableRow small lineOnTop">
		<table width="100%"  border="0" cellspacing="0" cellpadding="1">
			<tr>
			<td class="small" valign="top">
				<input type="text" id="productName1" name="productName1" class="small" style="width:70%" value="{if isset($PRODUCT_NAME)}{$PRODUCT_NAME}{/if}" readonly />
				<input type="hidden" id="hdnProductId1" name="hdnProductId1" value="{if isset($PRODUCT_ID)}{$PRODUCT_ID}{/if}" />
				{if $PRODUCT_OR_SERVICE eq 'Services'}
					<input type="hidden" id="lineItemType1" name="lineItemType1" value="Services" />
					&nbsp;<img id="searchIcon1" title="Services" src="{'services.gif'|@vtiger_imageurl:$THEME}" style="cursor: pointer;" align="absmiddle" onclick="servicePickList(this,'{$MODULE}',1)" />
				{else}
					<input type="hidden" id="lineItemType1" name="lineItemType1" value="Products" />
					&nbsp;<img id="searchIcon1" title="Products" src="{'products.gif'|@vtiger_imageurl:$THEME}" style="cursor: pointer;" align="absmiddle" onclick="productPickList(this,'{$MODULE}',1)" />
				{/if}
			</td>
		</tr>
		<tr>
			<td class="small">
				<input type="hidden" value="" id="subproduct_ids1" name="subproduct_ids1" />
				<span id="subprod_names1" name="subprod_names1" style="color:#C0C0C0;font-style:italic;"> </span>
			</td>
			</tr>
			<tr>
			<td class="small" id="setComment">
				<textarea id="comment1" name="comment1" class=small style="width:70%;height:40px"></textarea>
				<img src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" onClick="{literal}${/literal}('comment1').value=''"; style="cursor:pointer;" />
			</td>
		   </tr>
		</table>
	</td>
	<!-- column 2 - Product Name - ends -->

	<!-- column 3 - Quantity in Stock - starts -->
	<td class="crmTableRow small lineOnTop" valign="top">
		{if ($MODULE eq 'Quotes' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice' || $MODULE eq 'Issuecards')  && 'Products'|vtlib_isModuleActive}
		{$APP.LBL_QTY_IN_STOCK}:&nbsp;<span id="qtyInStock1">{if isset($QTY_IN_STOCK)}{$QTY_IN_STOCK}{/if}</span><br>
		{/if}
		{if isset($ASSOCIATEDPRODUCTS.moreinfo)}
		{foreach item=maindata from=$ASSOCIATEDPRODUCTS.moreinfo}
			{assign var="row_no" value="1"}
			{include file='Inventory/EditViewUI.tpl'}
		{/foreach}
		{/if}
	</td>
	<!-- column 3 - Quantity in Stock - ends -->

	<!-- column 4 - Quantity - starts -->
	<td class="crmTableRow small lineOnTop" valign="top">
		{if $TAX_TYPE eq 'group'}
			<input id="qty1" name="qty1" type="text" class="small " style="width:50px" onBlur="settotalnoofrows(); calcTotal(); loadTaxes_Ajax(1); calcGroupTax(); setDiscount(this,'1'); calcTotal();{if $MODULE eq 'Invoice'}stock_alert(1);{/if}" value=""/><br><span id="stock_alert1"></span>
		{else}
			<input id="qty1" name="qty1" type="text" class="small " style="width:50px" onBlur="settotalnoofrows(); calcTotal(); loadTaxes_Ajax(1); setDiscount(this,'1'); calcTotal();{if $MODULE eq 'Invoice'}stock_alert(1);{/if}" value=""/><br><span id="stock_alert1"></span>
		{/if}
	</td>
	<!-- column 4 - Quantity - ends -->

	<!-- column 5 - List Price with Discount, Total After Discount and Tax as table - starts -->
	<td class="crmTableRow small lineOnTop" align="right" valign="top">
		<table width="100%" cellpadding="0" cellspacing="0">
		   <tr>
			<td align="right">
				<input id="listPrice1" name="listPrice1" value="{if isset($UNIT_PRICE)}{$UNIT_PRICE}{/if}" type="text" class="small" style="width:70px" onBlur="calcTotal(); setDiscount(this,'1');callTaxCalc(1);calcTotal();"{if $Inventory_ListPrice_ReadOnly} readonly{/if}/>&nbsp;{if 'PriceBooks'|vtlib_isModuleActive}<img src="{'pricebook.gif'|@vtiger_imageurl:$THEME}" onclick="priceBookPickList(this,1)">{/if}
			</td>
		   </tr>
		   <tr>
			<td align="right" style="padding:5px;" nowrap>
				(-)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,'discount_div1','discount','1')" >{$APP.LBL_DISCOUNT}</a> : </b>
				<div class="discountUI" id="discount_div1">
					<input type="hidden" id="discount_type1" name="discount_type1" value="">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
					   <tr>
						<td id="discount_div_title1" nowrap align="left" ></td>
						<td align="right"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" onClick="fnHidePopDiv('discount_div1')" style="cursor:pointer;"></td>
					   </tr>
					   <tr>
						<td align="left" class="lineOnTop"><input type="radio" name="discount1" checked onclick="setDiscount(this,1); callTaxCalc(1);calcTotal();">&nbsp; {$APP.LBL_ZERO_DISCOUNT}</td>
						<td class="lineOnTop">&nbsp;</td>
					   </tr>
					   <tr>
						<td align="left"><input type="radio" name="discount1" onclick="setDiscount(this,1); callTaxCalc(1);calcTotal();">&nbsp; % {$APP.LBL_OF_PRICE}</td>
						<td align="right"><input type="text" class="small" size="5" id="discount_percentage1" name="discount_percentage1" value="0" style="visibility:hidden" onBlur="setDiscount(this,1); callTaxCalc(1);calcTotal();">&nbsp;%</td>
					   </tr>
					   <tr>
						<td align="left" nowrap><input type="radio" name="discount1" onclick="setDiscount(this,1); callTaxCalc(1);calcTotal();">&nbsp;{$APP.LBL_DIRECT_PRICE_REDUCTION}</td>
						<td align="right"><input type="text" id="discount_amount1" name="discount_amount1" size="5" value="0" style="visibility:hidden" onBlur="setDiscount(this,1); callTaxCalc(1);calcTotal();"></td>
					   </tr>
					</table>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right" style="padding:5px;" nowrap>
				<b>{$APP.LBL_TOTAL_AFTER_DISCOUNT} :</b>
			</td>
		   </tr>
		   <tr id="individual_tax_row1" class="TaxShow">
			<td align="right" style="padding:5px;" nowrap>
				(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,'tax_div1','tax','1')" >{$APP.LBL_TAX} </a> : </b>
				<div class="discountUI" id="tax_div1">
				</div>
			</td>
		   </tr>
		</table> 
	</td>
	<!-- column 5 - List Price with Discount, Total After Discount and Tax as table - ends -->


	<!-- column 6 - Product Total - starts -->
	<td class="crmTableRow small lineOnTop" align="right">
		<table width="100%" cellpadding="5" cellspacing="0">
		   <tr>
			<td id="productTotal1" align="right">&nbsp;</td>
		   </tr>
		   <tr>
			<td id="discountTotal1" align="right">0.00</td>
		   </tr>
		   <tr>
			<td id="totalAfterDiscount1" align="right">&nbsp;</td>
		   </tr>
		   <tr>
			<td id="taxTotal1" align="right">0.00</td>
		   </tr>
		</table>
	</td>
	<!-- column 6 - Product Total - ends -->

	<!-- column 7 - Net Price - starts -->
	<td valign="bottom" class="crmTableRow small lineOnTop" align="right"><span id="netPrice1"><b>&nbsp;</b></span></td>
	<!-- column 7 - Net Price - ends -->

   </tr>
   <!-- Product Details First row - Ends -->
</table>

<table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="crmTable">
   <!-- Add Product Button -->
   <tr>
	<td colspan="3">
		{if 'Products'|vtlib_isModuleActive}
		<input type="button" name="Button" class="crmbutton small create" value="{$APP.LBL_ADD_PRODUCT}" onclick="fnAddProductRow('{$MODULE}','{$IMAGE_PATH}');" />
		{/if}
		{if 'Services'|vtlib_isModuleActive}
		&nbsp;&nbsp;
		<input type="button" name="Button" class="crmbutton small create" value="{$APP.LBL_ADD_SERVICE}" onclick="fnAddServiceRow('{$MODULE}','{$IMAGE_PATH}');" />
		{/if}
	</td>
   </tr>

   <!-- Product Details Final Total Discount, Tax and Shipping&Hanling  - Starts -->
   <tr valign="top">
	<td width="88%" colspan="2" class="crmTableRow small lineOnTop" align="right"><b>{$APP.LBL_NET_TOTAL}</b></td>
	<td width="12%" id="netTotal" class="crmTableRow small lineOnTop" align="right">0.00</td>
   </tr>

   <tr valign="top">
	<td class="crmTableRow small lineOnTop" width="60%" style="border-right:1px #dadada;">&nbsp;</td>
	<td class="crmTableRow small lineOnTop" align="right">
		(-)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,'discount_div_final','discount_final','1')">{$APP.LBL_DISCOUNT}</a>

		<!-- Popup Discount DIV -->
		<div class="discountUI" id="discount_div_final">
			<input type="hidden" id="discount_type_final" name="discount_type_final" value="">
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
			   <tr>
				<td id="discount_div_title_final" nowrap align="left" ></td>
				<td align="right"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" onClick="fnHidePopDiv('discount_div_final')" style="cursor:pointer;"></td>
			   </tr>
			   <tr>
				<td align="left" class="lineOnTop"><input type="radio" name="discount_final" checked onclick="setDiscount(this,'_final'); calcGroupTax();calcTotal();">&nbsp; {$APP.LBL_ZERO_DISCOUNT}</td>
				<td class="lineOnTop">&nbsp;</td>
			   </tr>
			   <tr>
				<td align="left"><input type="radio" name="discount_final" onclick="setDiscount(this,'_final'); calcGroupTax();calcTotal();">&nbsp; % {$APP.LBL_OF_PRICE}</td>
				<td align="right"><input type="text" class="small" size="5" id="discount_percentage_final" name="discount_percentage_final" value="0" style="visibility:hidden" onBlur="setDiscount(this,'_final'); calcGroupTax();calcTotal();">&nbsp;%</td>
			   </tr>
			   <tr>
				<td align="left" nowrap><input type="radio" name="discount_final" onclick="setDiscount(this,'_final'); calcGroupTax();calcTotal();">&nbsp;{$APP.LBL_DIRECT_PRICE_REDUCTION}</td>
				<td align="right"><input type="text" id="discount_amount_final" name="discount_amount_final" size="5" value="0" style="visibility:hidden" onBlur="setDiscount(this,'_final'); calcGroupTax();calcTotal();"></td>
			   </tr>
			</table>
		</div>
		<!-- End Div -->

	</td>
	<td id="discountTotal_final" class="crmTableRow small lineOnTop" align="right">0.00</td>
   </tr>


   <!-- Group Tax - starts -->
   <tr id="group_tax_row" valign="top" class="TaxHide">
	<td class="crmTableRow small lineOnTop" style="border-right:1px #dadada;">&nbsp;</td>
	<td class="crmTableRow small lineOnTop" align="right">
		(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,'group_tax_div','group_tax_div_title',''); calcGroupTax();" >{$APP.LBL_TAX}</a></b>
			<!-- Pop Div For Group TAX -->
			<div class="discountUI" id="group_tax_div">{include file="Inventory/GroupTax.tpl"}</div>
			<!-- End Popup Div Group Tax -->
	</td>
	<td id="tax_final" class="crmTableRow small lineOnTop" align="right">0.00</td>
   </tr>
   <!-- Group Tax - ends -->

   <tr valign="top">
	<td class="crmTableRow small" style="border-right:1px #dadada;">&nbsp;</td>
	<td class="crmTableRow small" align="right">
		(+)&nbsp;<b>{$APP.LBL_SHIPPING_AND_HANDLING_CHARGES} </b>
	</td>
	<td class="crmTableRow small" align="right">
		<input id="shipping_handling_charge" name="shipping_handling_charge" type="text" class="small" style="width:40px" align="right" value="0.00" onBlur="calcSHTax();">
	</td>
   </tr>

   <tr valign="top">
	<td class="crmTableRow small" style="border-right:1px #dadada;">&nbsp;</td>
	<td class="crmTableRow small" align="right">
		(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,'shipping_handling_div','sh_tax_div_title',''); calcSHTax();" >{$APP.LBL_TAX_FOR_SHIPPING_AND_HANDLING} </a></b>

				<!-- Pop Div For Shipping and Handlin TAX -->
				<div class="discountUI" id="shipping_handling_div">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
					   <tr>
						<td id="sh_tax_div_title" colspan="2" nowrap align="left" ></td>
						<td align="right"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" onClick="fnHidePopDiv('shipping_handling_div')" style="cursor:pointer;"></td>
					   </tr>

					{foreach item=tax_detail name=sh_loop key=loop_count from=$SH_TAXES}

					   <tr>
						<td align="left" class="lineOnTop">
							<input type="text" class="small" size="3" name="{$tax_detail.taxname}_sh_percent" id="sh_tax_percentage{$smarty.foreach.sh_loop.iteration}" value="{$tax_detail.percentage}" onBlur="calcSHTax()">&nbsp;%
						</td>
						<td align="center" class="lineOnTop">{$tax_detail.taxlabel}</td>
						<td align="right" class="lineOnTop">
							<input type="text" class="small" size="4" name="{$tax_detail.taxname}_sh_amount" id="sh_tax_amount{$smarty.foreach.sh_loop.iteration}" style="cursor:pointer;" value="0.00" readonly>
						</td>
					   </tr>

					{/foreach}
					<input type="hidden" id="sh_tax_count" value="{$smarty.foreach.sh_loop.iteration}">

					</table>
				</div>
				<!-- End Popup Div for Shipping and Handling TAX -->

	</td>
	<td id="shipping_handling_tax" class="crmTableRow small" align="right">0.00</td>
   </tr>
   <tr valign="top">
	<td class="crmTableRow small" style="border-right:1px #dadada;">&nbsp;</td>
	<td class="crmTableRow small" align="right">
		{$APP.LBL_ADJUSTMENT}
		<select id="adjustmentType" name="adjustmentType" class=small onchange="calcTotal();">
			<option value="+">{$APP.LBL_ADD_ITEM}</option>
			<option value="-">{$APP.LBL_DEDUCT}</option>
		</select>
	</td>
	<td class="crmTableRow small" align="right">
		<input id="adjustment" name="adjustment" type="text" class="small" style="width:40px" align="right" value="0.00" onBlur="calcTotal();">
	</td>
   </tr>
   <tr valign="top">
	<td class="crmTableRow big lineOnTop" style="border-right:1px #dadada;">&nbsp;</td>
	<td class="crmTableRow big lineOnTop" align="right"><b>{$APP.LBL_GRAND_TOTAL}</b></td>
	<td id="grandTotal" name="grandTotal" class="crmTableRow big lineOnTop" align="right">&nbsp;</td>
   </tr>
</table>
		<input type="hidden" name="totalProductCount" id="totalProductCount" value="">
		<input type="hidden" name="subtotal" id="subtotal" value="">
		<input type="hidden" name="total" id="total" value="">
</td></tr>


<!-- Added to calculate the tax and total values when page loads -->
<script>
 decideTaxDiv();
 {if $TAX_TYPE eq 'group'}
 	calcGroupTax();
 {/if}
 calcTotal();
 calcSHTax();
</script>
<!-- This above div is added to display the tax informations --> 


