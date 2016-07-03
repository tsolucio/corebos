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

{*<!-- module header -->*}

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
</script>
{/if}

<script type="text/javascript">

function sensex_info()
{ldelim}
		var Ticker = document.getElementById('tickersymbol').value;
		if(Ticker!='')
		{ldelim}
				document.getElementById("vtbusy_info").style.display="inline";
				jQuery.ajax({ldelim}
						method:"POST",
						url:'index.php?module={$MODULE}&action=Tickerdetail&tickersymbol='+Ticker,
				{rdelim}).done(function(response) {ldelim}
							document.getElementById('autocom').innerHTML = response;
							document.getElementById('autocom').style.display="block";
							document.getElementById("vtbusy_info").style.display="none";
				{rdelim}
				);
		{rdelim}
{rdelim}

</script>

		{include file='Buttons_List1.tpl'}

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
   <tr>
	<td valign=top>
		<img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}">
	</td>

	<td class="showPanelBg" valign=top width=100%>
		{*<!-- PUBLIC CONTENTS STARTS-->*}
		{include file='EditViewHidden.tpl'}
		<div class="small" style="padding:20px">

			{if $OP_MODE eq 'edit_view'}
			 <span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} -  {$APP.LBL_EDITING} {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</span> <br>
				{$UPDATEINFO}
			{/if}
			{if $OP_MODE eq 'create_view'}
			{if $DUPLICATE neq 'true'}
			<span class="lvtHeaderText">{$APP.LBL_CREATING} {$APP.LBL_NEW} {$SINGLE_MOD|@getTranslatedString:$MODULE}</span> <br>
			{else}
			<span class="lvtHeaderText">{$APP.LBL_DUPLICATING} "{$NAME}" </span> <br>
			{/if}
		 {/if}

			<hr noshade size=1>
			<br>



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

						<td align=left style="padding:10px;border-right:1px #CCCCCC;" width=80%>
							{*<!-- content cache -->*}

							<table border=0 cellspacing=0 cellpadding=0 width=100%>
							   <tr>
								<td id ="autocom"></td>
							   </tr>
							   <tr>
								<td style="padding:10px">
								<!-- General details -->
									<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
									   <tr>
										<td  colspan=4 style="padding:5px">
										   <div align="center">
										<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='Save';  return validateInventory('{$MODULE}')" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
                                                                 		<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
										   </div>
										</td>
									   </tr>

								   <!-- included to handle the edit fields based on ui types -->
									   {foreach key=header item=data from=$BLOCKS}
									      <tr id="tbl{$header|replace:' ':''}Head">
										{if $header== $MOD.LBL_ADDRESS_INFORMATION && ($MODULE == 'Accounts' || $MODULE == 'Contacts' || $MODULE == 'Quotes' || $MODULE == 'PurchaseOrder' || $MODULE == 'SalesOrder'|| $MODULE == 'Invoice') && $SHOW_COPY_ADDRESS eq 'yes'}
                                                                        	<td colspan=2 class="detailedViewHeader">
	                                                                        <b>{$header}</b></td>
        	                                                                <td class="detailedViewHeader">
                	                                                        <input name="cpy" onclick="return copyAddressLeft(EditView)" type="radio"><b>{$APP.LBL_RCPY_ADDRESS}</b></td>
                        	                                                <td class="detailedViewHeader">
                                	                                        <input name="cpy" onclick="return copyAddressRight(EditView)" type="radio"><b>{$APP.LBL_LCPY_ADDRESS}</b></td>
                                        	                                {else}
										<td colspan=4 class="detailedViewHeader">
											<b>{$header}</b>
										{/if}
										</td>
									      </tr>


								   <!-- Here we should include the uitype handlings-->
										{include file="DisplayFields.tpl"}

									      <tr style="height:25px"><td>&nbsp;</td></tr>
									   {/foreach}

								   <!-- This if is added to restrict display in more tab-->
								   {if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice'}
								   	<!-- Added to display the product details -->
									<!-- This if is added when we want to populate product details from the related entity  for ex. populate product details in new SO page when select Quote -->
									{if $AVAILABLE_PRODUCTS eq 'true'}
											{include file="Inventory/ProductDetailsEditView.tpl"}
									{else}
										{include file="Inventory/ProductDetails.tpl"}
									   {/if}

								   {/if}
									   <tr>
										<td  colspan=4 style="padding:5px">
											<div align="center">
										<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='Save'; return validateInventory('{$MODULE}')" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
												<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
										<input type="hidden" name="convert_from" value="{$CONVERT_MODE}">
                                                                                <input type="hidden" name="duplicate_from" value="{$DUPLICATE_FROM}">
											</div>
										</td>
									   </tr>
									</table>
								<!-- General details - end -->
								</td>
							   </tr>
							</table>
						</td>
					   </tr>
					</table>

				</td>
			   </tr>
			</table>
		</div>
	</td>
	<td align=right valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
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
