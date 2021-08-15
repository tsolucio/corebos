<div id="response-container">
{* Parse the translation string applicable to javascript *}
<script type='text/javascript'>
var Utilitiesi18nInfo = {ldelim}{rdelim};
{foreach item=i18nValue key=i18nKey from=$MOD}
	{if strpos($i18nKey, 'JSLBL_') === 0}
		Utilitiesi18nInfo['{$i18nKey}'] = '{$i18nValue}';
	{/if}
{/foreach}
</script>
{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
	<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium">
<div id='_progress_' style='float: right; display: none; position: absolute; right: 35px; font-weight: bold;'>
	<span id='_progressmsg_'>...</span><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border='0' align='absmiddle'>
</div>
<div id="respose"></div>
<form role="form" style="margin:0 100px;" method="POST" onSubmit="Utilities.validate_smtp_config_settings(this); return false;">
	<div class="slds-grid slds-gutters">
		<div class="slds-col slds-size_1-of-2">
			<h1 class="slds-page-header__title">{'LBL_CONFIG_INCOMING_MAIL_SERVER'|@getTranslatedString:'vtsendgrid'}</h1>
			<hr />
			<div id="ic-validation-success" style="height:4rem; display:none;">
				<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_success" role="alert">
					<span class="slds-assistive-text"></span>
					<span class="slds-icon_container slds-icon-utility-error slds-m-right_x-small" title="">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#success"></use>
				</svg>
				</span>
					<div class="slds-notify__content">
						<h2 class="slds-text-heading_small">{'LBL_IC_SUCCESS_CONFIG_VALIDATION'|@getTranslatedString:'Utilities'}</h2>
					</div>
					<div class="slds-notify__close">
						<button type="button" class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse" title="Close" onClick="Utilities.close_ic_success_toast()">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{'LBL_CLOSE'|@getTranslatedString:'Utilities'}</span>
						</button>
					</div>
				</div>
				<br />
			</div>
			<div id="ic-validation-error" style="height:4rem; display:none;">
				<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_error" role="alert">
					<span class="slds-assistive-text"></span>
					<span class="slds-icon_container slds-icon-utility-error slds-m-right_x-small" title="">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#error"></use>
				</svg>
				</span>
					<div class="slds-notify__content">
						<h2 class="slds-text-heading_small">{'LBL_IC_CONFIG_VALIDATION_FAIL'|@getTranslatedString:'Utilities'}</h2>
						<p id="ic-message-error"></p>
					</div>
					<div class="slds-notify__close">
						<button type="button" class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse" title="Close" onClick="Utilities.close_ic_error_toast()">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{'LBL_CLOSE'|@getTranslatedString:'Utilities'}</span>
						</button>
					</div>
				</div>
			</div>
			<br />
			<div class="slds-form-element">
				<div class="slds-form-element__control">
					<div class="slds-checkbox">
					<input type="checkbox" name="ic_mail_server_active" id="ic_mail_server_active" {if $ic_mail_server_active}checked{/if}/>
					<label class="slds-checkbox__label" for="ic_mail_server_active">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
					</label>
					<button class="slds-button slds-button_text-destructive delete" title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" onClick="if (confirm('{$APP.SMTP_DELETE_CONFIRMATION}')) {literal}{window.location.href = 'index.php?action=integration&module=Utilities&_op=getconfigsmtp&savemode=false&smtp_settings=inc_set';} else { return false;}{/literal}" type="submit" name="Delete">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use> </svg>
						{$APP.LBL_DELETE_BUTTON_LABEL}
					</button>
					</div>
				</div>
			</div>
			<div class="slds-form-element slds-m-top--small" id="ic-div-server-type">
				<label class="slds-form-element__label" for="ic_mail_server_type">{'LBL_SELECT_SERVER_TYPE'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<select id="ic_mail_server_type" name="ic_mail_server_type" class="slds-input" onchange="Utilities.smtp_config_setting_helper(this);">
						<option value=''>{'JSLBL_Choose_Server_Type'|@getTranslatedString:'MailManager'}</option>
						<option value='gmail' {if $ic_mail_server_displayname eq 'gmail'} selected {/if}>{'JSLBL_Gmail'|@getTranslatedString:'MailManager'}</option>
						<option value='yahoo' {if $ic_mail_server_displayname eq 'yahoo'} selected {/if}>{'JSLBL_Yahoo'|@getTranslatedString:'MailManager'}</option>
						<option value='fastmail' {if $ic_mail_server_displayname eq 'fastmail'} selected {/if}>{'JSLBL_Fastmail'|@getTranslatedString:'MailManager'}</option>
						<option value='other' {if $ic_mail_server_displayname eq 'other'} selected {/if}>{'JSLBL_Other'|@getTranslatedString:'MailManager'}</option>
					</select>
				</div>
				<div class="slds-form-element__help" id="form-error-ic-server-type"></div>
			</div>
			<div id="settings_details" {if $ic_mail_server_displayname neq ''} style="display:block;" {else} style="display:none;"{/if}>
				<div class="slds-form-element slds-m-top--small" id="ic-div-servername">
					<font color="red">*</font>&nbsp;
					<label class="slds-form-element__label" for="ic_mail_server_name">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="text" id="ic_mail_server_name" name="ic_mail_server_name" class="slds-input" value="{$ic_mail_server_name}" />
					</div>
					<div class="slds-form-element__help" id="form-error-ic-server-name"></div>
				</div>
				<div class="slds-form-element slds-m-top--small" id="ic-div-username">
					<font color="red">*</font>&nbsp;
					<label class="slds-form-element__label" for="ic_mail_server_username">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="text" id="ic_mail_server_username" name="ic_mail_server_username" aria-describedby="form-error-02" class="slds-input" value="{$ic_mail_server_username}" />
					</div>
					<div class="slds-form-element__help" id="form-error-ic-server-username"></div>
				</div>
				<div class="slds-form-element slds-m-top--small" id="ic-div-password">
					<font color="red">*</font>&nbsp;
					<label class="slds-form-element__label" for="ic_mail_server_password">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="password" id="ic_mail_server_password" name="ic_mail_server_password" aria-describedby="form-error-01" class="slds-input" value="{$ic_mail_server_password}" />
					</div>
					<div class="slds-form-element__help" id="form-error-ic-server-password"></div>
				</div>
				<div id="additional_settings" {if $ic_mail_server_displayname eq 'other'} style="display:block;"{else} style="display:none;" {/if}>
					<div class="slds-form-element slds-m-top--small">
						<fieldset class="slds-form-element">
							<legend class="slds-form-element__legend slds-form-element__label">{'LBL_Protocol'|@getTranslatedString:'MailManager'}</legend>
							<div class="slds-form-element__control">
								<div class="slds-radio_button-group">
									<span class="slds-button slds-radio_button">
										<input type="radio" name="ic_mail_server_protocol" id="ic_mail_server_imap_2" value="IMAP2" {if strcasecmp($ic_mail_server_protocol, 'imap2')===0} checked=true {/if}/>
										<label class="slds-radio_button__label" for="ic_mail_server_imap_2">
										<span class="slds-radio_faux">{'LBL_Imap2'|@getTranslatedString:'MailManager'}</span>
										</label>
									</span>
									<span class="slds-button slds-radio_button">
										<input type="radio" name="ic_mail_server_protocol" id="ic_mail_server_imap_4" value="IMAP4" {if strcasecmp($ic_mail_server_protocol, 'imap4')===0} checked=true {/if}/>
										<label class="slds-radio_button__label" for="ic_mail_server_imap_4">
										<span class="slds-radio_faux">{'LBL_Imap4'|@getTranslatedString:'MailManager'}</span>
										</label>
									</span>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="slds-form-element slds-m-top--small">
					<div class="slds-grid slds-gutters slds-gutters_medium">
						<fieldset class="slds-col slds-form-element">
							<legend class="slds-form-element__legend slds-form-element__label">{'LBL_SSL_Options'|@getTranslatedString:'MailManager'}</legend>
							<div class="slds-form-element__control">
								<div class="slds-radio_button-group">
									<span class="slds-button slds-radio_button">
										<input type="radio" name="ic_mail_server_ssltype" id="ic_no_tls" value="notls" {if strcasecmp($ic_mail_server_ssltype, 'notls')===0} checked=true {/if}/>
										<label class="slds-radio_button__label" for="ic_no_tls">
										<span class="slds-radio_faux">{'LBL_No_TLS'|@getTranslatedString:'MailManager'}</span>
										</label>
									</span>
									<span class="slds-button slds-radio_button">
										<input type="radio" name="ic_mail_server_ssltype" id="ic_tls" value="tls" {if strcasecmp($ic_mail_server_ssltype, 'tls')===0} checked=true {/if}/>
										<label class="slds-radio_button__label" for="ic_tls">
										<span class="slds-radio_faux">{'LBL_TLS'|@getTranslatedString:'MailManager'}</span>
										</label>
									</span>
									<span class="slds-button slds-radio_button">
										<input type="radio" name="ic_mail_server_ssltype" id="ic_ssl" value="ssl" {if strcasecmp($ic_mail_server_ssltype, 'ssl')===0} checked=true {/if}/>
										<label class="slds-radio_button__label" for="ic_ssl">
										<span class="slds-radio_faux">{'LBL_SSL'|@getTranslatedString:'MailManager'}</span>
										</label>
									</span>
								</div>
							</div>
						</fieldset>
						<fieldset class="slds-col slds-form-element">
							<legend class="slds-form-element__legend slds-form-element__label">{'LBL_Certificate_Validations'|@getTranslatedString:'MailManager'}</legend>
							<div class="slds-form-element__control">
								<div class="slds-radio_button-group">
									<span class="slds-button slds-radio_button">
										<input type="radio" name="ic_mail_server_sslmeth" id="ic_cert_validation" value="validate-cert" {if strcasecmp($ic_mail_server_sslmeth, 'validate-cert')===0} checked=true {/if}/>
										<label class="slds-radio_button__label" for="ic_cert_validation">
										<span class="slds-radio_faux">{'LBL_Validate_Cert'|@getTranslatedString:'MailManager'}</span>
										</label>
									</span>
									<span class="slds-button slds-radio_button">
										<input type="radio" name="ic_mail_server_sslmeth" id="ic_no_validate_cert" value="novalidate-cert" {if strcasecmp($ic_mail_server_sslmeth, 'novalidate-cert')===0} checked=true {/if}/>
										<label class="slds-radio_button__label" for="ic_no_validate_cert">
										<span class="slds-radio_faux">{'LBL_Do_Not_Validate_Cert'|@getTranslatedString:'MailManager'}</span>
										</label>
									</span>
								</div>
							</div>
						</fieldset>
					</div>
					</div>
				</div>
				<div class="slds-form-element slds-m-top--small">
					<label class="slds-form-element__label" for="ic_mail_server_refresh_time">{'LBL_REFRESH_TIME'|@getTranslatedString:'MailManager'}</label>
					<div class="slds-form-element__control">
						<select id="ic_mail_server_refresh_time" name="ic_mail_server_refresh_time" class="slds-input">
							<option value="0" {if $ic_mail_server_refresh_time eq ''}selected{/if}>{'LBL_NONE'|@getTranslatedString:'MailManager'}</option>
							<option value="300000" {if strcasecmp($ic_mail_server_refresh_time, '300000')==0}selected{/if}>{'LBL_5_MIN'|@getTranslatedString:'MailManager'}</option>
							<option value="600000" {if strcasecmp($ic_mail_server_refresh_time, '600000')==0}selected{/if}>{'LBL_10_MIN'|@getTranslatedString:'MailManager'}</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-2">
			<h1 class="slds-page-header__title">{'LBL_CONFIG_OUTGOING_MAIL_SERVER'|@getTranslatedString:'vtsendgrid'}</h1>
			<hr />
			<div id="og-validation-success" style="height:4rem; display:none;">
				<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_success" role="alert">
					<span class="slds-assistive-text"></span>
					<span class="slds-icon_container slds-icon-utility-error slds-m-right_x-small" title="">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#success"></use>
				</svg>
				</span>
					<div class="slds-notify__content">
						<h2 class="slds-text-heading_small">{'LBL_OG_SUCCESS_CONFIG_VALIDATION'|@getTranslatedString:'Utilities'}</h2>
					</div>
					<div class="slds-notify__close">
						<button type="button" class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse" title="Close" onClick="Utilities.close_og_success_toast()">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{'LBL_CLOSE'|@getTranslatedString:'Utilities'}</span>
						</button>
					</div>
				</div>
			</div>
			<div id="og-validation-error" style="height:4rem; display:none;">
				<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_error" role="alert">
					<span class="slds-assistive-text"></span>
					<span class="slds-icon_container slds-icon-utility-error slds-m-right_x-small" title="">
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#error"></use>
				</svg>
				</span>
					<div class="slds-notify__content">
						<h2 class="slds-text-heading_small">{'LBL_OG_CONFIG_VALIDATION_FAIL'|@getTranslatedString:'Utilities'}</h2>
						<p id="og-message-error"></p>
					</div>
					<div class="slds-notify__close">
						<button type="button" class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse" title="Close" onClick="Utilities.close_og_error_toast()">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{'LBL_CLOSE'|@getTranslatedString:'Utilities'}</span>
						</button>
					</div>
				</div>
			</div>
		<br>
			<div class="slds-form-element">
				<div class="slds-form-element__control">
					<div class="slds-checkbox">
					<input type="checkbox" name="og_mail_server_active" id="og_mail_server_active" {if $og_mail_server_active}checked{/if}/>
					<label class="slds-checkbox__label" for="og_mail_server_active">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
					</label>
					<button class="slds-button slds-button_text-destructive delete" title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" onClick="if (confirm('{$APP.SMTP_DELETE_CONFIRMATION}')) {literal}{window.location.href = 'index.php?action=integration&module=Utilities&_op=getconfigsmtp&savemode=false&smtp_settings=og_set';} else { return false;}{/literal}" type="submit" name="Delete">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use> </svg>
						{$APP.LBL_DELETE_BUTTON_LABEL}
					</button>
					</div>
				</div>
			</div>
			<div class="slds-form-element slds-m-top--small" id="og-div-server-name">
				<font color="red">*</font>&nbsp;
				<label class="slds-form-element__label" for="og_mail_server_name">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
				<div class="slds-form-element__control">
					<input type="text" id="og_mail_server_name" name="og_mail_server_name" class="slds-input" value="{$og_mail_server_name}" />
				</div>
				<div class="slds-form-element__help" id="form-error-og-server-name"></div>
			</div>
			<div class="slds-form-element slds-m-top--small" id="og-div-server-username">
				<font color="red">*</font>&nbsp;
				<label class="slds-form-element__label" for="og_mail_server_username">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
				<div class="slds-form-element__control">
					<input type="text" id="og_mail_server_username" name="og_mail_server_username" class="slds-input" value="{$og_mail_server_username}" />
				</div>
				<div class="slds-form-element__help" id="form-error-og-server-username"></div>
			</div>
			<div class="slds-form-element slds-m-top--small" id="og-div-server-password">
				<font color="red">*</font>&nbsp;
				<label class="slds-form-element__label" for="og_mail_server_password">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
				<div class="slds-form-element__control">
					<input type="password" id="og_mail_server_password" name="og_mail_server_password" class="slds-input" value="{$og_mail_server_password}" />
				</div>
				<div class="slds-form-element__help" id="form-error-og-server-password"></div>
			</div>
			<div class="slds-form-element slds-m-top--small">
				<label class="slds-form-element__label" for="og_mail_server_smtp_auth">{'LBL_REQUIRES_AUTHENT'|@getTranslatedString:'Settings'}</label>
				<div class="slds-form-element__control">
					<select id="og_mail_server_smtp_auth" name="og_mail_server_smtp_auth" class="slds-input">
						<option value="false" {if $og_mail_server_smtp_auth eq 'false'}selected{/if}>{'LBL_NO'|@getTranslatedString:'Settings'}</option>
						<option value="true" {if $og_mail_server_smtp_auth eq 'true'}selected{/if}>{'LBL_YES'|@getTranslatedString:'Settings'}</option>
						<option value="ssl" {if $og_mail_server_smtp_auth eq 'ssl'}selected{/if}>SSL {'LBL_CERT_VAL'|@getTranslatedString:'Settings'}</option>
						<option value="sslnc" {if $og_mail_server_smtp_auth eq 'sslnc'}selected{/if}>SSL</option>
						<option value="tls" {if $og_mail_server_smtp_auth eq 'tls'}selected{/if}>TLS {'LBL_CERT_VAL'|@getTranslatedString:'Settings'}</option>
						<option value="tlsnc" {if $og_mail_server_smtp_auth eq 'tlsnc'}selected{/if}>TLS</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class="slds-m-top--large">
		<div class="slds-float_right">
			<button type="submit" class="slds-button slds-button--brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
	</div>
	</form>
	<script type='text/javascript' src='modules/Utilities/Utilities.js'></script>
	{* {/if} *}
	</div>
	</div>
</div>
</section>