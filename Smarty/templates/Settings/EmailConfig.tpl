{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
			<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
			<br>
			{if $EMAILCONFIG_MODE neq 'edit'}
				<form action="index.php" method="post" name="MailServer" id="form" onsubmit="VtigerJS_DialogBox.block();">
					<input type="hidden" name="emailconfig_mode">
			{else}
				{literal}
					<form action="index.php" method="post" name="MailServer" id="form" onsubmit="if(validate_mail_server(MailServer)){ VtigerJS_DialogBox.block(); return true; } else { return false; }">
				{/literal}
						<input type="hidden" name="server_type" value="email">
			{/if}
							<input type="hidden" id="module" name="module" value="Settings">
							<input type="hidden" name="action">
							<input type="hidden" name="parenttab" value="Settings">
							<input type="hidden" name="return_module" value="Settings">
							<input type="hidden" name="return_action" value="EmailConfig">
							<input type="hidden" name="confirmMsg" value="{$MOD.LBL_CONFIRM_DEFAULT_SETTINGS}" id="confirmMsg">
							<input type="hidden" name="defaultMsg" value="{$MOD.EXISTING_DEFAULT_VALUES}" id="defaultMsg">
							<input type="hidden" name="mode">
							<div align=center>

								{include file="SetMenu.tpl"}

								<!-- DISPLAY -->
								<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
									<tr>
										<td width=50 rowspan=2 valign=top><img src="{'ogmailserver.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
										<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_MAIL_SERVER_SETTINGS} </b></td>
									</tr>
									<tr>
										<td valign=top class="small">{$MOD.LBL_MAIL_SERVER_DESC} </td>
									</tr>
								</table>
								<br>
								<table border=0 cellspacing=0 cellpadding=10 width=100%  class="tableHeading">
								<tr>
									<td class="small">
										<strong>{$MOD.LBL_MAIL_SERVER_SMTP}</strong>
										<div style="float: right">
										{if $EMAILCONFIG_MODE neq 'edit'}
											<input class="crmButton small edit" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='EmailConfig';this.form.emailconfig_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
											<input class="crmButton small delete" title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" onclick="if (confirm('{$APP.NTC_DELETE_CONFIRMATION}')) {literal}{this.form.action.value='EmailConfig';this.form.emailconfig_mode.value='delete';} else { return false;}{/literal}" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">
										{else}
											<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" onclick="this.form.action.value='Save';" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >&nbsp;&nbsp;
											<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="window.location.href = 'index.php?module=Settings&action=EmailConfig&parenttab=Settings';" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
										{/if}
										</div>
									</td>
								</tr>
								{if $ERROR_MSG neq ''}
								<tr>
								{$ERROR_MSG}
								</tr>
								{/if}
								</table>

								{if $EMAILCONFIG_MODE neq 'edit'}
								<table border=0 cellspacing=0 cellpadding=0 width=100% class="dvtContentSpace">
									<tr>
										<td class="small" valign=top >
											<table width="100%"  border="0" cellspacing="0" cellpadding="5">
												<tr>
													<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
													<td width="80%" class="small cellText"><strong>{$MAILSERVER}&nbsp;</strong></td>
													<input type="hidden" value={$MAILSERVER} id="server">
												</tr>
												<tr valign="top">
													<td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
													<td class="small cellText">{$USERNAME}&nbsp;</td>
													<input type="hidden" value={$USERNAME} id="server_username">
												</tr>
												<tr>
													<td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
													<td class="small cellText">
														{if $PASSWORD neq ''}
														******
														{/if}&nbsp;
													</td>
													<input type="hidden" value={$PASSWORD} id="server_password">
												</tr>
												<tr>
													<td nowrap class="small cellLabel"><strong>{$MOD.LBL_FROM_EMAIL_FIELD}</strong></td>
													<td class="small cellText">{$FROM_EMAIL_FIELD}&nbsp;</td>
													<input type="hidden" value={$FROM_EMAIL_FIELD} id="from_email_field">
													</td>
												</tr>
												<tr>
													<td nowrap class="small cellLabel"><strong>{$MOD.LBL_REQUIRES_AUTHENT}</strong></td>
													<td class="small cellText">{if $SMTP_AUTH=='true'}{$MOD.LBL_YES}{elseif $SMTP_AUTH=='false'}{$MOD.LBL_NO}{else}{$SMTP_AUTH}{/if}</td>
													<input type="hidden" value={$SMTP_AUTH} id="smtp_auth">
												</tr>
										</table>
										{else}
										<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
											<tr>
												<td class="small" valign=top >
													<table width="100%"  border="0" cellspacing="0" cellpadding="5">
														<tr>
															<td width="20%" nowrap class="small cellLabel"><font color="red">*</font>&nbsp;<strong>{$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
															<td width="80%" class="small cellText">
															<input type="text" class="detailedViewTextBox small" value="{$MAILSERVER}" name="server" id="server">
															</td>
														</tr>
														<tr valign="top">
															<td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
															<td class="small cellText">
															<input type="text" class="detailedViewTextBox small" value="{$USERNAME}" name="server_username" id="server_username">
															</td>
														</tr>
														<tr>
															<td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
															<td class="small cellText">
															<input type="password" class="detailedViewTextBox small" value="{$PASSWORD}" name="server_password" id="server_password">
															</td>
														</tr>
														<tr>
															<td nowrap class="small cellLabel"><strong>{$MOD.LBL_FROM_EMAIL_FIELD}</strong></td>
															<td class="small cellText">
															<input type="text" class="detailedViewTextBox small" value="{$FROM_EMAIL_FIELD}" name="from_email_field" id="from_email_field"/>
															</td>
														</tr>
														<tr>
															<td nowrap class="small cellLabel"><strong>{$MOD.LBL_REQUIRES_AUTHENT}</strong></td>
															<td class="small cellText">
															{html_options name="smtp_auth" options=$SMTP_AUTH_OPTIONS selected=$SMTP_AUTH class="small"}
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									{/if}

									<!--table border=0 cellspacing=0 cellpadding=5 width=100% >
									<tr>
									<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
									</tr>
									</table-->
									</td>
								</tr>
							</table>
						</div>
					</form>
				</td>
			<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
		</tr>
	</tbody>
</table>
{literal}
<script>
function validate_mail_server(form)
{
	if(form.server.value =='')
	{
		{/literal}
                alert("{$APP.SERVERNAME_CANNOT_BE_EMPTY}")
                        return false;
                {literal}
	}
	return true;
}

function setDefaultMailServer()
{
	var confirmMsg = document.getElementById('confirmMsg').value;
	if(confirm(confirmMsg)){
		return true;
	}
	else{
		return false;
	}
}
</script>
{/literal}
