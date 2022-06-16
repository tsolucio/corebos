{*<!--
/*+*******************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*******************************************************************************/
-->*}
<div style="width: 400px;" class=" sdls-card">
	<form method="POST" action="javascript:void(0);">
		<table width="100%" cellpadding="5" cellspacing="0" border="0" class="layerHeadingULine  slds-table slds-table_bordered">
			<tr>
				<td class="genHeaderSmall" width="90%" align="left">{'ServerNotConfigured'|getTranslatedString:$MODULE}</td>
				<td width="10%" align="right">
					<a href="javascript:void(0);" onclick="SMSNotifierCommon.hideSelectWizard();"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"/></a>
				</td>
		</table>

		<table width="95%" cellpadding="5" cellspacing="0" border="0" align="center" style="padding: 7px;">
			<tr>
				<td>
				<table width="100%" cellpadding="5" cellspacing="0" border="0" align="center" bgcolor="white">
					<tr>
						<td align="left"><strong>{'NO_ACTIVE_SERVER'|getTranslatedString:$MODULE}</strong>
						<br/>
						<br/>
						{if $IS_ADMIN}{'ReviewModuleSettings'|getTranslatedString:$MODULE}{else}{'ContactAdmin'|getTranslatedString:$MODULE}{/if} </td>
					</tr>
				</table></td>
			</tr>
		</table>

		<table style="text-align: center; margin: auto; display: flex; justify-content: center; display: grid;" width="100%" cellpadding="5" cellspacing="0" border="0" class="layerPopupTransport slds-table slds-table_bordered">
			<tr>
				<td class="small" align="center">
				<input type="button" class="slds-button slds-button_destructive small crmbutton cancel" onclick="SMSNotifierCommon.hideSelectWizard();" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"/>
				</td>
			</tr>
		</table>
	</form>
</div>