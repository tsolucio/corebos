{*<!--
/*********************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
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
								<td align="center"><input type="checkbox" {if $BillAddressChecked eq 1}checked {/if}name="sca_bill" id="sca_bill" /></td>
								<td align="left"><b><label for="sca_bill">{'Billing Address'|@getTranslatedString}</label></b></td>
							</tr>
							<tr>
								<td align="center"><input type="checkbox" {if $ShipAddressChecked eq 1}checked {/if}name="sca_ship" id="sca_ship" /></td>
								<td align="left"><b><label for="sca_ship">{'Shipping Address'|@getTranslatedString}</label></b></td>
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