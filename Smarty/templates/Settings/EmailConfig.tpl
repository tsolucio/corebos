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
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
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

								<!-- DISPLAY Outgoing server-->
								<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
									<tr class="slds-text-title--caps">
										<td style="padding: 0;">
											<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
												<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
													<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
														<!-- Image -->
														<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
															<div class="slds-media__figure slds-icon forceEntityIcon">
																<span class="photoContainer forceSocialPhoto">
																	<div class="small roundedSquare forceEntityIcon">
																		<span class="uiImage">
																			<img src="{'ogmailserver.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}">
																		</span>
																	</div>
																</span>
															</div>
														</div>
														<!-- Title and help text -->
														<div class="slds-media__body">
															<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																<span class="uiOutputText" style="width: 100%;">
																	<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_MAIL_SERVER_SETTINGS} </b>
																</span>
																<span class="small">{$MOD.LBL_MAIL_SERVER_DESC}</span>
															</h1>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
								</table>

								<table border=0 cellspacing=0 cellpadding=10 width=100%  class="tableHeading">
									<tr>
										<td class="big">

											<div class="forceRelatedListSingleContainer">
												<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
													<div class="slds-card__header slds-grid">
														<header class="slds-media slds-media--center slds-has-flexi-truncate">
															<div class="slds-media__body">
																<h2>
																	<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																		<strong>{$MOD.LBL_MAIL_SERVER_SMTP}</strong>
																	</span>
																</h2>
															</div>
														</header>
														<div class="slds-no-flex">
															{if $EMAILCONFIG_MODE neq 'edit'}
																<input class="slds-button slds-button--small slds-button--brand" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='EmailConfig';this.form.emailconfig_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
																<input class="slds-button slds-button--small slds-button--destructive" title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" onclick="if (confirm('{$APP.NTC_DELETE_CONFIRMATION}')) {literal}{this.form.action.value='EmailConfig';this.form.emailconfig_mode.value='delete';} else { return false;}{/literal}" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">
															{else}
																<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" onclick="this.form.action.value='Save';" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >&nbsp;&nbsp;
																<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--small slds-button--destructive" onclick="window.location.href = 'index.php?module=Settings&action=EmailConfig&parenttab=Settings';" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
															{/if}
														</div>
													</div>
												</article>
											</div>

											{if !empty($ERROR_MESSAGE)}
											<tr><td>{include file='applicationmessage.tpl'}</td></tr>
											{/if}

											<table border=0 cellspacing=0 cellpadding=0 width=100% class="dvtContentSpace">
												<tr>
													<td class="small" valign=top >
														{if $EMAILCONFIG_MODE neq 'edit'}
															<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																<tr>
																	<td width="20%" nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
																	<td width="80%" class="small dvtCellInfo"><strong>{$MAILSERVER}&nbsp;</strong></td>
																	<input type="hidden" value={$MAILSERVER} id="server">
																</tr>
																<tr valign="top">
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_USERNAME}</strong></td>
																	<td class="small dvtCellInfo">{$USERNAME}&nbsp;</td>
																	<input type="hidden" value={$USERNAME} id="server_username">
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_PASWRD}</strong></td>
																	<td class="small dvtCellInfo">
																		{if $PASSWORD neq ''}
																		******
																		{/if}&nbsp;
																	</td>
																	<input type="hidden" value={$PASSWORD} id="server_password">
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_FROM_EMAIL_FIELD}</strong></td>
																	<td class="small dvtCellInfo">{$FROM_EMAIL_FIELD}&nbsp;</td>
																	<input type="hidden" value={$FROM_EMAIL_FIELD} id="from_email_field">
																	</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_REQUIRES_AUTHENT}</strong></td>
																	<td class="small dvtCellInfo">{if $SMTP_AUTH=='true'}{$MOD.LBL_YES}{elseif $SMTP_AUTH=='false'}{$MOD.LBL_NO}{else}{$SMTP_AUTH_SHOW}{/if}</td>
																	<input type="hidden" value={$SMTP_AUTH} id="smtp_auth">
																</tr>
															</table>
														{else}
															<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table listTable">
																<tr>
																	<td width="20%" nowrap class="small dvtCellLabel text-left"><font color="red">*</font>&nbsp;<strong>{$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
																	<td width="80%" class="small dvtCellInfo">
																	<input type="text" class="slds-input small" value="{$MAILSERVER}" name="server" id="server">
																	</td>
																</tr>
																<tr valign="top">
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_USERNAME}</strong></td>
																	<td class="small dvtCellInfo">
																	<input type="text" class="slds-input small" value="{$USERNAME}" name="server_username" id="server_username">
																	</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_PASWRD}</strong></td>
																	<td class="small dvtCellInfo">
																	<input type="password" class="slds-input small" value="{$PASSWORD}" name="server_password" id="server_password">
																	</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_FROM_EMAIL_FIELD}</strong></td>
																	<td class="small dvtCellInfo">
																	<input type="text" class="slds-input small" value="{$FROM_EMAIL_FIELD}" name="from_email_field" id="from_email_field"/>
																	</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_REQUIRES_AUTHENT}</strong></td>
																	<td class="small dvtCellInfo">
																	{html_options name="smtp_auth" options=$SMTP_AUTH_OPTIONS selected=$SMTP_AUTH class="small slds-select" style="width:75%;"}
																	</td>
																</tr>
															</table>
														{/if}
													</td>
												</tr>
											</table>



										</td>
									</tr>
								</table>

							</td></tr></table><!-- close tables from setMenu -->
							</td></tr></table><!-- close tables from setMenu -->
					</form>

				</div>
			</td>
		</tr>
	</tbody>
</table>

{literal}
<script>
function validate_mail_server(form) {
	if (form.server.value == '') {
		alert("{/literal}{$APP.SERVERNAME_CANNOT_BE_EMPTY}{literal}")
		return false;
	}
	if (form.from_email_field.value != '') {
		if (patternValidate('from_email_field','{/literal}{$MOD.LBL_FROM_EMAIL_FIELD}{literal}','EMAIL') == false)
			return false;
	}
	return true;
}

function setDefaultMailServer() {
	var confirmMsg = document.getElementById('confirmMsg').value;
	if (confirm(confirmMsg)) {
		return true;
	} else {
		return false;
	}
}
</script>
{/literal}
