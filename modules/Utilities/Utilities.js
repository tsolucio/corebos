/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (typeof(Utilities) == 'undefined') {

	/*
	 * Namespaced javascript class for Utilities
	 */
	Utilities = {
		smtp_config_setting_helper: function (selectBox) {
			var form = selectBox.form;

			var useServer = '', useProtocol = '', useSSLType = '', useCert = '';
			if (selectBox.value == 'gmail' || selectBox.value == 'yahoo') {
				useServer = 'imap.gmail.com';
				if (selectBox.value == 'yahoo') {
					useServer = 'imap.mail.yahoo.com';
				}
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				jQuery('#settings_details').show();
				jQuery('#additional_settings').hide();
			} else if (selectBox.value == 'fastmail') {
				useServer = 'mail.messagingengine.com';
				useProtocol = 'IMAP2';
				useSSLType = 'tls';
				useCert = 'novalidate-cert';
				jQuery('#settings_details').show();
				jQuery('#additional_settings').hide();
			} else if (selectBox.value == 'other') {
				useServer = '';
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				jQuery('#settings_details').show();
				jQuery('#additional_settings').show();
			} else {
				jQuery('#settings_details').hide();
				jQuery('#additional_settings').hide();
			}
			// Clear the User Name and Password field
			jQuery('#ic_mail_server_username').val('');
			jQuery('#ic_mail_server_password').val('');

			if (useProtocol != '') {
				form.ic_mail_server_name.value = useServer;
				jQuery('input[name="ic_mail_server_protocol"]').each(function () {
					this.checked = (this.value == useProtocol);
				});
				jQuery('input[name="ic_mail_server_ssltype"]').each(function () {
					this.checked = (this.value == useSSLType);
				});
				jQuery('input[name="ic_mail_server_sslmeth"]').each(function () {
					this.checked = (this.value == useCert);
				});
			}
		},
		validate_smtp_config_settings: function (form) {
			// for Incoming Mail Server
			if (form.ic_mail_server_type.value == '' && form.ic_mail_server_active.value == 'on') {
				jQuery('#ic-div-server-type').addClass('slds-has-error');
				jQuery('#form-error-ic-server-type').append('<span>'+Utilities.i18n('JSLBL_Choose_Server_Type')+'</span>');
				jQuery('#form-error-ic-server-type').show();
				return false;
			}

			if (form.ic_mail_server_name.value == '' && form.ic_mail_server_active.value == 'on') {
				jQuery('#ic-div-servername').addClass('slds-has-error');
				jQuery('#form-error-ic-server-name').append('<span>'+Utilities.i18n('JSLBL_SERVERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-ic-server-name').show();
				return false;
			}

			if (form.ic_mail_server_username.value == '' && form.ic_mail_server_active.value == 'on') {
				jQuery('#ic-div-username').addClass('slds-has-error');
				jQuery('#form-error-ic-server-username').append('<span>'+Utilities.i18n('JSLBL_USERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-ic-server-username').show();
				return false;
			}

			if (form.ic_mail_server_password.value == '' && form.ic_mail_server_active.value == 'on') {
				jQuery('#ic-div-password').addClass('slds-has-error');
				jQuery('#form-error-ic-server-password').append('<span>'+Utilities.i18n('JSLBL_PASSWORD_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-ic-server-password').show();
				return false;
			}

			// for Outgoing Mail Server
			if (form.og_mail_server_name.value == '' && form.og_mail_server_active.value == 'on') {
				jQuery('#og-div-server-name').addClass('slds-has-error');
				jQuery('#form-error-og-server-name').append('<span>'+Utilities.i18n('JSLBL_SERVERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-og-server-name').show();
				return false;
			}

			if (form.og_mail_server_username.value == '' && form.og_mail_server_active.value == 'on') {
				jQuery('#og-div-server-username').addClass('slds-has-error');
				jQuery('#form-error-og-server-username').append('<span>'+Utilities.i18n('JSLBL_USERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-og-server-username').show();
				return false;
			}

			if (form.og_mail_server_password.value == '' && form.og_mail_server_active.value == 'on') {
				jQuery('#ic-div-server-password').addClass('slds-has-error');
				jQuery('#form-error-og-server-password').append('<span>'+Utilities.i18n('JSLBL_PASSWORD_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-og-server-password').show();
				return false;
			}

			if (form.og_mail_server_from_email.value == '' && form.og_mail_server_active.value == 'on') {
				jQuery('#ic-div-server-from-email').addClass('slds-has-error');
				jQuery('#form-error-og-from-email').append('<span>'+Utilities.i18n('JSLBL_FROM_EMIL_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-og-server-from-email').show();
				return false;
			}
			console.log('index.php?'+Utilities._baseurl() + '&' + jQuery(form).serialize());
			Utilities.progress_show(Utilities.i18n('JSLBL_Saving_And_Verifying'), '...');
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?'+Utilities._baseurl() + '&' + jQuery(form).serialize()
			}).done(function (transport) {
				jQuery('#response-container').empty();
				jQuery('#response-container').append(transport);
				VtigerJS_DialogBox.unblock();
			});
		},
		/*
		* Progress indicator handlers.
		*/
		progress_show: function (msg, suffix) {
			if (typeof(suffix) == 'undefined') {
				suffix = '';
			}
			VtigerJS_DialogBox.block();
			if (typeof(msg) != 'undefined') {
				jQuery('#_progressmsg_').html(msg + suffix.toString());
			}
			jQuery('#_progress_').show();
		},
		progress_hide: function () {
			VtigerJS_DialogBox.unblock();
			jQuery('#_progressmsg_').html('');
			jQuery('#_progress_').hide();
		},

		/* Show error message */
		show_error: function (message) {
			var errordiv = jQuery('#_messagediv_');

			if (message == '') {
				errordiv.text('').hide();
			} else {
				errordiv.html('<p>' + message + '</p>').css('display', 'block').addClass('mm_error').removeClass('mm_message');
				Utilities.placeAtCenter(errordiv);
			}
			Utilities.hide_error();
		},

		hide_error: function () {
			setTimeout(function () {
				jQuery('#_messagediv_').hide();
			}, 5000);
		},

		show_message: function (message) {
			var errordiv = jQuery('#_messagediv_');
			if (message == '') {
				errordiv.text('').hide();
			} else {
				errordiv.html('<p>' + message + '</p>').css('display', 'block').removeClass('mm_error').addClass('mm_message');
				Utilities.placeAtCenter(errordiv);
			}
			Utilities.hide_error();
		},

		/* Base url for any ajax actions */
		_baseurl: function () {
			return 'module=Utilities&action=UtilitiesAjax&file=validatesmtpconfig&savemode=true';
		},

		/* Translation support */
		i18n: function (key) {
			if (typeof(Utilitiesi18nInfo) != 'undefined') {
				return Utilitiesi18nInfo[key];
			}
			if (typeof(alert_arr) != 'undefined' && alert_arr[key]) {
				return alert_arr[key];
			}
			return key;
		},

		close_ic_warning_toast: function () {
			jQuery('#ic-validation-warning').hide();
		},
		close_ic_success_toast: function () {
			jQuery('#ic-validation-success').hide();
		},
		close_ic_error_toast: function () {
			jQuery('#ic-validation-error').hide();
		},
		close_og_warning_toast: function () {
			jQuery('#og-validation-warning').hide();
		},
		close_og_success_toast: function () {
			jQuery('#og-validation-success').hide();
		},
		close_og_error_toast: function () {
			jQuery('#og-validation-error').hide();
		}
	};
}
