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
<input name="contact_id" id="contact_id" type="hidden" value="">
<input name='contact_name' id="contact_name" type='hidden' value=''>
<input name='mailingstreet' id="mailingstreet" type='hidden' value=''>
<input name='mailingcity' id="mailingcity" type='hidden' value=''>
<input name='mailingstate' id="mailingstate" type='hidden' value=''>
<input name='mailingzip' id="mailingzip" type='hidden' value=''>
<input name='mailingcountry' id="mailingcountry" type='hidden' value=''>
<input name='mailingpobox' id="mailingpobox" type='hidden' value=''>
<input name='otherstreet' id="otherstreet" type='hidden' value=''>
<input name='othercity' id="othercity" type='hidden' value=''>
<input name='otherstate' id="otherstate" type='hidden' value=''>
<input name='otherzip' id="otherzip" type='hidden' value=''>
<input name='othercountry' id="othercountry" type='hidden' value=''>
<input name='otherpobox' id="otherpobox" type='hidden' value=''>
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
	<tr>
		<td width="90%" align="left" class="genHeaderSmall">{'SetReturnAddressTitle'|@getTranslatedString}</td>
		<td width="10%" align="right">
			<a href="javascript:fninvsh('setaddresscontactdiv');"><img title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
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
								<td align="center"><input type="checkbox" checked name="sca_bill" id="sca_bill" /></td>
								<td align="left"><b>{'Billing Address'|@getTranslatedString}</b></td>
							</tr>
							<tr>
								<td align="center"><input type="checkbox" name="sca_ship" id="sca_ship" /></td>
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
		<input type="button" name="{$APP.LBL_SELECT_BUTTON_LABEL}" value=" {$APP.LBL_SELECT_BUTTON_LABEL} " class="crmbutton small create" onClick="sca_fillinvalues();"/>
	</td></tr>
</table>