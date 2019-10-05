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
<table width="100%" cellpadding="5" cellspacing="0" class="listTable" >
	<tr>
	<td class="colHeader small cblds-p_medium" width="30%" nowrap="nowrap">{$CMOD.LBL_PROVIDER}</td>
	<td class="colHeader small cblds-p_medium" width="25%" nowrap="nowrap">{$MOD.LBL_USERNAME}</td>
	<td class="colHeader small cblds-p_medium" width="10%" nowrap="nowrap">{$APP.Active}</td>
	<td class="colHeader small cblds-p_medium" width="10%">{$MOD.Tools}</td>
	</tr>
	{foreach item=SMSSERVER from=$SMSSERVERS}
	<tr>
	<td class="listTableRow small cblds-p_medium">{$SMSSERVER.providertype}</td>
	<td class="listTableRow small cblds-p_medium">{$SMSSERVER.username}</td>
	<td class="listTableRow small cblds-p_medium">{if $SMSSERVER.isactive}{$APP.yes}{else}{$APP.no}{/if}</td>
	<td class="listTableRow small cblds-p_medium">
		<img onClick="fnvshobj(this,'editdiv');_SMSConfigServerFetchEdit('{$SMSSERVER.id}');" style="cursor:pointer;" src="{'editfield.gif'|@vtiger_imageurl:$THEME}" title="{$APP.LBL_EDIT}">
		<img onClick="fnvshobj(this,'editdiv');_SMSConfigServerDelete('{$SMSSERVER.id}');" style="cursor:pointer;" src="{'delete.gif'|@vtiger_imageurl:$THEME}" title="{$APP.LBL_DELETE}">
	</td>

	</tr>
	{/foreach}
</table>

