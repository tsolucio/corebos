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
<script type="text/javascript">
window.__smsHelpInfo = {$SMSHELPINFO};
</script>
<div id="EditInv" class="layerPopup">
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
<tr>
	<td class="layerPopupHeading" align="left">
		{if empty($SMSSERVERINFO)}
			{$CMOD.LBL_ADDNEW}
		{else}
			{$CMOD.LBL_UPDATE}
			<input type="hidden" name="smsserver_id" value="{$SMSSERVERINFO.id}">
		{/if}
	</td>
	<td align="right" class="small"><img onClick="hide('editdiv');" style="cursor:pointer;" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0"></td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
<tr>
	<td class="small">
	<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
		<td width="33%" nowrap="nowrap" align="right" class="cellLabel small"><b>{$CMOD.LBL_PROVIDER}</b></td>
		<td align="left" class="cellText small">
			<select name="smsserver_provider" class="small" onchange="_SMSCongiServerShowReqParams(this);">
				<option value="">-- {$CMOD.LBL_SELECT_ONE} --</option>
				{foreach item=SMSPROVIDER from=$SMSPROVIDERS}
				<option {if isset($SMSSERVERINFO.providertype) && $SMSSERVERINFO.providertype eq $SMSPROVIDER}selected="true"{/if} value="{$SMSPROVIDER}">{$SMSPROVIDER}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td width="33%" nowrap="nowrap" align="right" class="cellLabel small"><b>{$APP.LNK_HELP}</b></td>
		<td align="left" class="cellText small"><span id="_smshelpinfospan"><a href="{$smsHIurl}" target="_blank">{$smsHIlabel}</a></span></td>
	</tr>
	<tr>
		<td width="33%" nowrap="nowrap" align="right" class="cellLabel small"><b>{$APP.Active}</b></td>
		<td align="left" class="cellText small">
			<input type="radio" class="small" name="smsserver_isactive" value="1" {if isset($SMSSERVERINFO.isactive) && $SMSSERVERINFO.isactive}checked=true{/if}> {'LBL_YES'|getTranslatedString}
			<input type="radio" class="small" name="smsserver_isactive" value="0" {if empty($SMSSERVERINFO.isactive) || $SMSSERVERINFO.isactive neq 1}checked=true{/if}> {'LBL_NO'|getTranslatedString}
		</td>
	</tr>

	<tr>
		<td width="33%" nowrap="nowrap" align="right" class="cellLabel small"><b>{$MOD.LBL_USERNAME}</b></td>
		<td align="left" class="cellText small">
			<input type="text" class="detailedViewTextBox" name="smsserver_username" value="{if isset($SMSSERVERINFO.username)}{$SMSSERVERINFO.username}{/if}" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'">
		</td>
	</tr>

	<tr>
		<td width="33%" nowrap="nowrap" align="right" class="cellLabel small"><b>{$MOD.LBL_PASWRD}</b></td>
		<td align="left" class="cellText small">
			<input type="password" class="detailedViewTextBox" name="smsserver_password" value="{if isset($SMSSERVERINFO.password)}{$SMSSERVERINFO.password}{/if}" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'">
		</td>
	</tr>

	{foreach item=SMSPROVIDERPARAMS key=SMSPROVIDERPARAMKEY from=$SMSPROVIDERSPARAMS}
	<tr>
	<td colspan="2" class="cellLabel small" style="padding: 0; border-top: 0;">
	<div id="paramrows_{$SMSPROVIDERPARAMKEY}" {if empty($SMSSERVERINFO.providertype) || $SMSSERVERINFO.providertype neq $SMSPROVIDERPARAMKEY}style="display: none;"{/if}>
		<table width="100%" cellpadding="5" cellspacing="0" border="0" bgcolor="white">
		{foreach item=SMSPROVIDERPARAM from=$SMSPROVIDERPARAMS}

		<tr>
		<td width="33%" nowrap="nowrap" align="right" class="cellLabel small" style="border: none;"><b>{$SMSPROVIDERPARAM}</b></td>
		<td align="left" class="cellText small" style="border: none;">
			<input type="text" class="detailedViewTextBox" name="smsserverparam_{$SMSPROVIDERPARAMKEY}_{$SMSPROVIDERPARAM}" value="{if isset($SMSSERVERPARAMS.$SMSPROVIDERPARAM)}{$SMSSERVERPARAMS.$SMSPROVIDERPARAM}{/if}" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'">
		</td>
		</tr>
		
		{/foreach}
		
		</table>
	</div>

	</td>
	</tr>

	{/foreach}
	
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
<tr>
	<td align="center" class="small">
		<input value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" type="button" onClick="_SMSConfigServerSaveForm(this.form)">
		<input value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" type="button" onClick="hide('editdiv');">
	</td>
	</tr>
</table>
</div>
