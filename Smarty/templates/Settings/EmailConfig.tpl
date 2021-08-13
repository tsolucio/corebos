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
{assign var="MODULEICON" value='outcome'}
{assign var="MODULESECTION" value=$MOD.LBL_MAIL_SERVER_SETTINGS}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_MAIL_SERVER_DESC}
{include file="SetMenu.tpl"}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none slds-card">
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
			<input type="hidden" name="return_module" value="Settings">
			<input type="hidden" name="return_action" value="EmailConfig">
			<input type="hidden" name="confirmMsg" value="{$MOD.LBL_CONFIRM_DEFAULT_SETTINGS}" id="confirmMsg">
			<input type="hidden" name="defaultMsg" value="{$MOD.EXISTING_DEFAULT_VALUES}" id="defaultMsg">
			<input type="hidden" name="mode">
<div align=center>
	<br>
	<table border=0 cellspacing=0 cellpadding=10 width=100%  class="slds-table slds-table_cell-buffer slds-table_header-hidden">
		<tr class="slds-line-height_reset">
			<td width="20%">
			{include file='Components/PageSubTitle.tpl' PAGESUBTITLE=$MOD.LBL_MAIL_SERVER_SMTP}
			</td>
			<td scope="col" >
				<div class="slds-truncate">
					<div style="float: right">
					{if $EMAILCONFIG_MODE neq 'edit'}
						<button class="slds-button slds-button_success edit" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='EmailConfig';this.form.emailconfig_mode.value='edit'" type="submit" name="Edit" >
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use> </svg>
						{$APP.LBL_EDIT_BUTTON_LABEL}
						</button>
						<button class="slds-button slds-button_destructive delete" title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" onclick="if (confirm('{$APP.NTC_DELETE_CONFIRMATION}')) {literal}{this.form.action.value='EmailConfig';this.form.emailconfig_mode.value='delete';} else { return false;}{/literal}" type="submit" name="Delete">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use> </svg>
						{$APP.LBL_DELETE_BUTTON_LABEL}
						</button>
					{else}
						<button title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success save" onclick="this.form.action.value='Save';" type="submit" name="button">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use> </svg>
						{$APP.LBL_SAVE_BUTTON_LABEL}
						&nbsp;&nbsp;
						</button>
						<button title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button_destructive cancel" onclick="window.location.href = 'index.php?module=Settings&action=EmailConfig';" type="button" name="button" >
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use> </svg>
						{$APP.LBL_CANCEL_BUTTON_LABEL}
						</button>
					{/if}
					</div>
				</div>
			</td>
		</tr>
		{if !empty($ERROR_MESSAGE)}
		<tr><td>{include file='applicationmessage.tpl'}</td></tr>
		{/if}
	</table>
		{if $EMAILCONFIG_MODE neq 'edit'}
	<table border=0 cellspacing=0 cellpadding=0 width=100% >
		<tr>
			<td class="small" valign=top >
				<table width="100%"  border="0" cellspacing="0" cellpadding="5" class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
					<tr class="slds-line-height_reset">
						<td width="20%" height="40px" nowrap >
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="Indica un campo obbligatorio">* </abbr>
							{$MOD.LBL_OUTGOING_MAIL_SERVER}
							</label>
						</td>
						<td width="80%" ><strong>{$MAILSERVER}&nbsp;</strong></td>
						<input type="hidden" value={$MAILSERVER} id="server">
					</tr>
					<tr>
						<td nowrap height="40px" >
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="Indica un campo obbligatorio">* </abbr>
							{$MOD.LBL_USERNAME}
							</label>
						</td>
						<td >{$USERNAME}&nbsp;</td>
						<input type="hidden" value={$USERNAME} id="server_username" class="slds-input">
					</tr>
					<tr>
						<td nowrap  height="40px">
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="Indica un campo obbligatorio">* </abbr>
							{$MOD.LBL_PASWRD}
							</label>
						</td>
						<td >
							{if $PASSWORD neq ''}
							******
							{/if}&nbsp;
						</td>
						<input type="hidden" class="slds-input" value={$PASSWORD} id="server_password">
					</tr>
					<tr>
						<td nowrap  height="40px">
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="Indica un campo obbligatorio">* </abbr>
							{$MOD.LBL_FROM_EMAIL_FIELD}
							</label>
						</td>
						<td >{$FROM_EMAIL_FIELD}&nbsp;</td>
						<input type="hidden" class="slds-input" value={$FROM_EMAIL_FIELD} id="from_email_field">
						</td>
					</tr>
					<tr>
						<td nowrap  height="40px">
							<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
							<abbr class="slds-required" title="Indica un campo obbligatorio">* </abbr>
							{$MOD.LBL_REQUIRES_AUTHENT}
							</label>
						</td>
						<td >{if $SMTP_AUTH=='true'}{$MOD.LBL_YES}{elseif $SMTP_AUTH=='false'}{$MOD.LBL_NO}{else}{$SMTP_AUTH_SHOW}{/if}</td>
						<input type="hidden" class="slds-input" value={$SMTP_AUTH} id="smtp_auth">
					</tr>
				</table>
				{else}
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr>
					<td class="small" valign=top >
						<table width="100%"  border="0" cellspacing="0" cellpadding="5" class="slds-table slds-table_cell-buffer slds-table_bordered ">
							<tr class="slds-line-height_reset">
								<td width="20%" height="40px" nowrap ><font color="red">
								<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
								<abbr class="slds-required" title="Indica un campo obbligatorio">* </abbr>
								{$MOD.LBL_OUTGOING_MAIL_SERVER}
								</label>
								</td>
								<td width="25%" >
								<input type="text" class="slds-input" value="{$MAILSERVER}" name="server" id="server">
								</td>
								<td width="55%%" ></td>
							</tr>
							<tr valign="top">
								<td nowrap >
								<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
								{$MOD.LBL_USERNAME}
								</label>
								</td>
								<td >
								<input type="text" class="slds-input" value="{$USERNAME}" name="server_username" id="server_username">
								</td>
								<td></td>
							</tr>
							<tr>
								<td nowrap >
								<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
								{$MOD.LBL_PASWRD}
								</label>
								</td>
								<td >
								<input type="password" class="slds-input" value="{$PASSWORD}" name="server_password" id="server_password">
								</td>
								<td></td>
							</tr>
							<tr>
								<td nowrap >
								<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
								{$MOD.LBL_FROM_EMAIL_FIELD}
								</label>
								</td>
								<td >
								<input type="text" class="slds-input" value="{$FROM_EMAIL_FIELD}" name="from_email_field" id="from_email_field"/>
								</td>
								<td></td>
							</tr>
							<tr>
								<td nowrap >
								<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
								{$MOD.LBL_REQUIRES_AUTHENT}
								</label>
								</td>
								<td >
								{html_options name="smtp_auth" options=$SMTP_AUTH_OPTIONS selected=$SMTP_AUTH class="slds-select"}
								</td>
								<td></td>
							</tr>
						</table>
					<td>
				<tr>
			</table>
			{/if}
			<td>
		</tr>
	</table>
</div>
		</form>
</div>
</section>
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