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
function openPopup(del_roleid) {ldelim}
	window.open("index.php?module=Users&action=UsersAjax&file=RolePopup&maskid="+del_roleid, "roles_popup_window", cbPopupWindowSettings+",toolbar=no,menubar=no,dependent=yes");
{rdelim}
</script>
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>

	<div align=center>

{literal}
<form name="newProfileForm" action="index.php" onsubmit="if(roleDeleteValidate()) { VtigerJS_DialogBox.block();} else { return false; }" method="post">
{/literal}
<input type="hidden" name="module" value="Users">
<input type="hidden" name="action" value="DeleteRole">
<input type="hidden" name="delete_role_id" value="{$ROLEID}">
<table width="100%" border="0" cellpadding="3" cellspacing="0">
<tr>
	<td class="genHeaderSmall" align="left" style="border-bottom:1px solid #CCCCCC;" width="50%">{$CMOD.LBL_DELETE_ROLE}</td>
	<td style="border-bottom:1px solid #CCCCCC;">&nbsp;</td>
	<td align="right" style="border-bottom:1px solid #CCCCCC;" width="40%"><a href="#" onClick="window.history.back();">{$APP.LBL_BACK}</a></td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td width="50%"><b>{$CMOD.LBL_ROLE_TO_BE_DELETED}</b></td>
	<td width="2%"><b>:</b></td>
	<td width="48%"><b>{$ROLENAME}</b></td>
</tr>
<tr>
	<td style="text-align:left;"><b>{$CMOD.LBL_TRANSFER_USER_ROLE}</b></td>
	<td ><b>:</b></td>
	<td align="left">
		<input type="text" name="role_name"  id="role_name" value="" class="txtBox" readonly="readonly">&nbsp;
		{$ROLEPOPUPBUTTON}
		<input type="hidden" name="user_role" id="user_role" value="">
	</td>
</tr>
<tr><td colspan="3" style="border-bottom:1px dashed #CCCCCC;">&nbsp;</td></tr>
<tr>
	<td colspan="3" align="center"><input type="submit" name="Delete" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmbutton small save"></td>
</tr>
</table>
</form></div>
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
</tr>
</table>
</div>
</section>
<script>
{literal}
function roleDeleteValidate() {
	if (document.getElementById('role_name').value == '') {
{/literal}
		alert('{$APP.SPECIFY_ROLE_INFO}');
		return false;
{literal}
	}
	return true;
}
{/literal}
</script>
