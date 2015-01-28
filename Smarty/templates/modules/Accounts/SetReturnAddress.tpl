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
<input name="account_id" id="account_id" type="hidden" value="">
<input name='account_name' id="account_name" type='hidden' value=''>
<input name='bill_street' id="bill_street" type='hidden' value=''>
<input name='bill_city' id="bill_city" type='hidden' value=''>
<input name='bill_state' id="bill_state" type='hidden' value=''>
<input name='bill_code' id="bill_code" type='hidden' value=''>
<input name='bill_country' id="bill_country" type='hidden' value=''>
<input name='bill_pobox' id="bill_pobox" type='hidden' value=''>
<input name='ship_street' id="ship_street" type='hidden' value=''>
<input name='ship_city' id="ship_city" type='hidden' value=''>
<input name='ship_state' id="ship_state" type='hidden' value=''>
<input name='ship_code' id="ship_code" type='hidden' value=''>
<input name='ship_country' id="ship_country" type='hidden' value=''>
<input name='ship_pobox' id="ship_pobox" type='hidden' value=''>
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
	<tr>
		<td width="90%" align="left" class="genHeaderSmall">{'SetReturnAddressTitle'|@getTranslatedString}</td>
		<td width="10%" align="right">
			<a href="javascript:fninvsh('setaddressaccountdiv');"><img title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
		</td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
	<tr><td class="small">
		<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
			<tr>
				<td align="left">{'SetReturnAddressDescription'|@getTranslatedString}.<br><br>
					<div style="height:120px;overflow-y:auto;overflow-x:hidden;" align="center">
						<table border="0" cellpadding="5" cellspacing="0" width="90%">
							<tr>
								<td align="center"><input type="checkbox" checked name="saa_bill" id="saa_bill" /></td>
								<td align="left"><b>{'Billing Address'|@getTranslatedString}</b></td>
							</tr>
							<tr>
								<td align="center"><input type="checkbox" name="saa_ship" id="saa_ship" /></td>
								<td align="left"><b>{'Shipping Address'|@getTranslatedString}</b></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</td></tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr><td align=center class="small">
		<input type="button" name="{$APP.LBL_SELECT_BUTTON_LABEL}" value=" {$APP.LBL_SELECT_BUTTON_LABEL} " class="crmbutton small create" onClick="saa_fillinvalues();"/>
	</td></tr>
</table>