{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<form action="javascript:void(0);" method="POST" style="display:inline;">

	<div class="slds-table--scoped">
		<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
			<li class="slds-tabs--scoped__item active" role="presentation" style="border-top-left-radius: 5px;">
				<a href="javascript:void(0);" class="slds-tabs--scoped__link dvHeaderText" id="settings_mail_fldrname" role="tab" tabindex="0" aria-selected="true" aria-controls="tab--scoped-1">{'JSLBL_Settings'|@getTranslatedString}</a>
			</li>
		</ul>

		<div id="tab--scoped-1" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
			<div class="mm_outerborder" id="settings_email_con" name="settings_email_con" style="border:none;">
				<input type=hidden id="selected_servername" value="{$SERVERNAME}" >
				<table class="slds-table slds-no-row-hover slds-table-moz detailview_table">
					<tr class="blockStyleCss">
						<td class="detailViewContainer" valign="top">
							<div class="forceRelatedListSingleContainer">
								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
									<div class="slds-card__header slds-grid">
										<header class="slds-media slds-media--center slds-has-flexi-truncate">
											<div class="slds-media__body">
												<h2>
													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
														<b>{'LBL_SELECT_ACCOUNT_TYPE'|@getTranslatedString}</b>
													</span>
												</h2>
											</div>
										</header>
										<div class="slds-no-flex">
											<div class="actionsContainer ">
												<select id="_mbox_helper" class="slds-select" onchange="MailManager.handle_settings_confighelper(this);">
													<option value=''>{'JSLBL_Choose_Server_Type'|@getTranslatedString:'MailManager'}</option>
													<option value='gmail' {if $SERVERNAME eq 'gmail'} selected {/if}>{'JSLBL_Gmail'|@getTranslatedString:'MailManager'}</option>
													<option value='yahoo' {if $SERVERNAME eq 'yahoo'} selected {/if}>{'JSLBL_Yahoo'|@getTranslatedString:'MailManager'}</option>
													<option value='fastmail' {if $SERVERNAME eq 'fastmail'} selected {/if}>{'JSLBL_Fastmail'|@getTranslatedString:'MailManager'}</option>
													<option value='other' {if $SERVERNAME eq 'other'} selected {/if}>{'JSLBL_Other'|@getTranslatedString:'MailManager'}</option>
												</select>
											</div>
										</div>
									</div>
								</article>
							</div>

							<div id="settings_details" class="slds-truncate" {if $SERVERNAME neq ''} style="display:block;" {else} style="display:none;"{/if}>
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout detailview_table org-type-table">
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width=25%>
											<font color="red">*</font>{'LBL_Mail_Server'|@getTranslatedString}
										</td>
										<td class="dvtCellInfo">
											<input name="_mbox_server" value="{$MAILBOX->server()}" type="text" class="slds-input">
											<span class="mm_blur">{'LBL_Like'|@getTranslatedString}, mail.company.com or 192.168.10.20</span>
										</td>
									</tr>

									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width=25%>
											<font color="red">*</font>{'LBL_Username'|@getTranslatedString}
										</td>
										<td class="dvtCellInfo">
											<input name="_mbox_user" id="_mbox_user" value="{$MAILBOX->username()}" type="text" class="slds-input">
											<span class="mm_blur">{'LBL_Your_Mailbox_Account'|@getTranslatedString}</span>
										</td>
									</tr>

									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width=25%>
											<font color="red">*</font>{'LBL_Password'|@getTranslatedString}
										</td>
										<td class="dvtCellInfo">
											<input name="_mbox_pwd" id="_mbox_pwd" value="{$MAILBOX->password()}" type="password" class="slds-input">
											<span class="mm_blur">{'LBL_Account_Password'|@getTranslatedString}</span>
										</td>
									</tr>
								</table>

								<div id="additional_settings" {if $SERVERNAME eq 'other'} style="display:block;"{else} style="display:none;" {/if}>
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout detailview_table org-type-table">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel" width=25%>{'LBL_Protocol'|@getTranslatedString}</td>
											<td class="dvtCellInfo">
												<span class="slds-radio">
													<input type="radio" name="_mbox_protocol" value="IMAP2" id="IMAP2" {if strcasecmp($MAILBOX->protocol(), 'imap2')===0}checked=true{/if} />
													<label class="slds-radio__label" for="IMAP2">
														<span class="slds-radio--faux"></span>
													</label>
													<span class="slds-form-element__label">{'LBL_Imap2'|@getTranslatedString}</span>
												</span>
												<span class="slds-radio">
													<input type="radio" name="_mbox_protocol" value="IMAP4" id="IMAP4" {if strcasecmp($MAILBOX->protocol(), 'imap4')===0}checked=true{/if} />
													<label class="slds-radio__label" for="IMAP4">
														<span class="slds-radio--faux"></span>
													</label>
													<span class="slds-form-element__label">{'LBL_Imap4'|@getTranslatedString}</span>
												</span>
											</td>
										</tr>

										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel" width=25%>{'LBL_SSL_Options'|@getTranslatedString}</td>
											<td class="dvtCellInfo">
												<span class="slds-radio">
													<input type="radio" name="_mbox_ssltype" value="notls" id="notls" {if strcasecmp($MAILBOX->ssltype(), 'notls')===0}checked=true{/if} />
													<label class="slds-radio__label" for="notls">
														<span class="slds-radio--faux"></span>
													</label>
													<span class="slds-form-element__label">{'LBL_No_TLS'|@getTranslatedString}</span>
												</span>
												<span class="slds-radio">
													<input type="radio" name="_mbox_ssltype" value="tls" id="tls" {if strcasecmp($MAILBOX->ssltype(), 'tls')===0}checked=true{/if} />
													<label class="slds-radio__label" for="tls">
														<span class="slds-radio--faux"></span>
													</label>
													<span class="slds-form-element__label">{'LBL_TLS'|@getTranslatedString}</span>
												</span>
												<span class="slds-radio">
													<input type="radio" name="_mbox_ssltype" value="ssl" id="ssl" {if strcasecmp($MAILBOX->ssltype(), 'ssl')===0}checked=true{/if} />
													<label class="slds-radio__label" for="ssl">
														<span class="slds-radio--faux"></span>
													</label>
													<span class="slds-form-element__label">{'LBL_SSL'|@getTranslatedString}</span>
												</span>
											</td>
										</tr>

										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel" width=25%>{'LBL_Certificate_Validations'|@getTranslatedString}</td>
											<td class="dvtCellInfo">
												<span class="slds-radio">
													<input type="radio" name="_mbox_certvalidate" value="validate-cert" id="validate-cert" {if strcasecmp($MAILBOX->certvalidate(), 'validate-cert')===0}checked=true{/if} />
													<label class="slds-radio__label" for="validate-cert">
														<span class="slds-radio--faux"></span>
													</label>
													<span class="slds-form-element__label">{'LBL_Validate_Cert'|@getTranslatedString}</span>
												</span>

												<span class="slds-radio">
													<input type="radio" name="_mbox_certvalidate" value="novalidate-cert" id="novalidate-cert" {if strcasecmp($MAILBOX->certvalidate(), 'novalidate-cert')===0}checked=true{/if} />
													<label class="slds-radio__label" for="novalidate-cert">
														<span class="slds-radio--faux"></span>
													</label>
													<span class="slds-form-element__label">{'LBL_Do_Not_Validate_Cert'|@getTranslatedString}</span>
												</span>
											</td>
										</tr>

									</table>
								</div>

								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout detailview_table org-type-table">
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width=25%>{'LBL_REFRESH_TIME'|@getTranslatedString}</td>
										<td class="dvtCellInfo">
											<select class="slds-select" name="_mbox_refresh_timeout">
												<option value="0" {if $MAILBOX->refreshTimeOut() eq ''}selected{/if}>{$MOD.LBL_NONE}</option>
												<option value="300000" {if strcasecmp($MAILBOX->refreshTimeOut(), '300000')==0}selected{/if}>{$MOD.LBL_5_MIN}</option>
												<option value="600000" {if strcasecmp($MAILBOX->refreshTimeOut(), '600000')==0}selected{/if}>{$MOD.LBL_10_MIN}</option>
											</select>
										</td>
									</tr>
								</table>
								<table width="100%">
									<tr class="slds-line-height--reset">
										<td align="center" colspan=2>
											<input type="button" class="slds-button slds-button--small slds-button_success" value="{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString}" onclick="MailManager.save_settings(this.form);" >
											{if $MAILBOX && $MAILBOX->exists()}
											<input type="button" class="slds-button slds-button--small slds-button--destructive" onclick="MailManager.close_settings();" value="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString}" >
											<input type="button" class="slds-button slds-button--small slds-button--destructive" onclick="MailManager.remove_settings(this.form);" value="{'LBL_Remove'|@getTranslatedString}" >
											{/if}
										</td>
									</tr>
								</table>
							</div>

						</td>
					</tr>
				</table>

			</div>
		</div>

	</div>

</form>