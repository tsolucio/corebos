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
			if (form.ic_mail_server_type.value == '' && form.ic_mail_server_active.checked) {
				jQuery('#ic-div-server-type').addClass('slds-has-error');
				jQuery('#form-error-ic-server-type').append('<span>'+Utilities.i18n('JSLBL_Choose_Server_Type')+'</span>');
				jQuery('#form-error-ic-server-type').show();
				return false;
			}

			if (form.ic_mail_server_name.value == '' && form.ic_mail_server_active.checked) {
				jQuery('#ic-div-servername').addClass('slds-has-error');
				jQuery('#form-error-ic-server-name').append('<span>'+Utilities.i18n('JSLBL_SERVERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-ic-server-name').show();
				return false;
			}

			if (form.ic_mail_server_username.value == '' && form.ic_mail_server_active.checked) {
				jQuery('#ic-div-username').addClass('slds-has-error');
				jQuery('#form-error-ic-server-username').append('<span>'+Utilities.i18n('JSLBL_USERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-ic-server-username').show();
				return false;
			}

			if (form.ic_mail_server_password.value == '' && form.ic_mail_server_active.checked) {
				jQuery('#ic-div-password').addClass('slds-has-error');
				jQuery('#form-error-ic-server-password').append('<span>'+Utilities.i18n('JSLBL_PASSWORD_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-ic-server-password').show();
				return false;
			}

			// for Outgoing Mail Server
			if (form.og_mail_server_name.value == '' && form.og_mail_server_active.checked) {
				jQuery('#og-div-server-name').addClass('slds-has-error');
				jQuery('#form-error-og-server-name').append('<span>'+Utilities.i18n('JSLBL_SERVERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-og-server-name').show();
				return false;
			}

			if (form.og_mail_server_username.value == '' && form.og_mail_server_active.checked) {
				jQuery('#og-div-server-username').addClass('slds-has-error');
				jQuery('#form-error-og-server-username').append('<span>'+Utilities.i18n('JSLBL_USERNAME_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-og-server-username').show();
				return false;
			}

			if (form.og_mail_server_password.value == '' && form.og_mail_server_active.checked) {
				jQuery('#ic-div-server-password').addClass('slds-has-error');
				jQuery('#form-error-og-server-password').append('<span>'+Utilities.i18n('JSLBL_PASSWORD_CANNOT_BE_EMPTY')+'</span>');
				jQuery('#form-error-og-server-password').show();
				return false;
			}

			Utilities.progress_show(Utilities.i18n('JSLBL_Saving_And_Verifying'), '...');
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?'+Utilities._baseurl() + '&' + jQuery(form).serialize()
			}).done(function (transport) {
				var response_obj =  JSON.parse(transport);
				if (response_obj.ic_validation_error_status) {
					jQuery('#ic-validation-success').css('display', 'none');
					jQuery('#ic-validation-error').css('display', 'block');
					jQuery('#ic-message-error').text(Utilities.i18n('JSLBL_ERROR') + '::' + response_obj.ic_validation_error_message);
				} else if (response_obj.ic_mail_server_validation_success) {
					jQuery('#ic-validation-error').css('display', 'none');
					jQuery('#ic-validation-success').css('display', 'block');
				} else {
					jQuery('#ic-validation-success').css('display', 'none');
					jQuery('#ic-validation-error').css('display', 'none');
				}

				if (response_obj.og_validation_error_status) {
					jQuery('#og-validation-success').css('display', 'none');
					jQuery('#og-validation-error').css('display', 'block');
					jQuery('#og-message-error').text(Utilities.i18n('JSLBL_ERROR') + '::' + response_obj.og_validation_error_message);
				} else if (response_obj.og_mail_server_validation_success) {
					jQuery('#og-validation-error').css('display', 'none');
					jQuery('#og-validation-success').css('display', 'block');
				} else {
					jQuery('#og-validation-success').css('display', 'none');
					jQuery('#og-validation-error').css('display', 'none');
				}
				VtigerJS_DialogBox.unblock();
				Utilities.progress_hide();
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
			ldsModal.show(alert_arr['ERROR'], DOMPurify.sanitize(message), 'small', '');
		},

		hide_error: function () {
			setTimeout(function () {
				ldsModal.close();
			}, 5000);
		},

		show_message: function (message) {
			ldsModal.show('', DOMPurify.sanitize(message), 'small', '');
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
		},
		cache_control_input_visibility: function () {
			var adapter = jQuery('#adapter_type').val();
			var ipPortContainer = jQuery('#ip_port_container');

			if (adapter === 'memory') {
				ipPortContainer.hide();
			} else {
				ipPortContainer.show();
			}
		},
		cache_form_submit_validation: function () {
			var adapter = jQuery('#adapter_type').val();
			var ip = jQuery('#ip').val();
			var port = jQuery('#port').val();
			var form = jQuery('#cache_form');

			if (adapter !== 'memory') {
				if (ip !== '') {
					this.cache_hide_ip_error();
				} else {
					this.cache_show_ip_error();
					return;
				}
				if (port !== '') {
					this.cache_hide_port_error();
				} else {
					this.cache_show_port_error();
					return;
				}
			}
			form.submit();
		},
		cache_show_ip_error: function () {
			jQuery('#ip').addClass('slds-has-error').focus();
			jQuery('#ip_required_message').show();
		},
		cache_hide_ip_error: function () {
			jQuery('#ip').removeClass('slds-has-error');
			jQuery('#ip_required_message').hide();
		},
		cache_show_port_error: function () {
			jQuery('#port').addClass('slds-has-error').focus();
			jQuery('#port_required_message').show();
		},
		cache_hide_port_error: function () {
			jQuery('#port').removeClass('slds-has-error');
			jQuery('#port_required_message').hide();
		}
	};
}

var Grid = tui.Grid;
var gridInstance = {};

document.addEventListener('DOMContentLoaded', function (event) {
	loadJS(
		'index.php?module=Utilities&action=UtilitiesAjax&file=getjslanguage'
	).then(() => {
		gridInstance = new Grid({
			el: document.getElementById('chgrid'),
			columns: [
				{
					name: 'name',
					header: mod_alert_arr.LBL_TABLE_NAME,
					sortingType: 'desc',
					sortable: true,
					editor: 'text',
					onAfterChange(ev) {
						const idx = gridInstance.getIndexOfRow(ev.rowKey);
						updateFieldData(ev, idx);
						console.table(ev)
					},
				},
				{
					name: 'access',
					header: mod_alert_arr.LBL_ACCESS,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true,
					renderer: {
						type: CheckboxWithActionRender,
					}
				},
				{
					name: 'create',
					header: mod_alert_arr.LBL_CREATE,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true,
					renderer: {
						type: CheckboxWithActionRender,
					}
				},
				{
					name: 'read',
					header: mod_alert_arr.LBL_READ,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true,
					renderer: {
						type: CheckboxWithActionRender,
					}
				},
				{
					name: 'write',
					header: mod_alert_arr.LBL_WRITE,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true,
					renderer: {
						type: CheckboxWithActionRender,
					}
				},
				{
					name: 'action',
					header: mod_alert_arr.LBL_ACTION,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: false,
					renderer: {
						type: DeleteButtonRender,
					}
				},
			],
			data:
				[
					{
						id: '1',
						name: 'vtiger_audit_trail',
						access: '1',
						create: '1',
						read: '1',
						write: '1',
						delete: '1',
					},
					{
						id: '2',
						name: 'marvel',
						access: '1',
						create: '1',
						read: '1',
						write: '0',
						delete: '0',
					},
					{
						id: '3',
						name: 'marvel3',
						access: '1',
						create: '0',
						read: '1',
						write: '0',
						delete: '0',
					},
				],
			useClientSort: false,
			rowHeight: 'auto',
			bodyHeight: 500,
			scrollX: false,
			scrollY: false,
			header: {
				align: 'left',
				valign: 'top',
			},
		});
		tui.Grid.applyTheme('striped');
	});
});



function addRow() {
	const total_row = gridInstance.getRowCount();
	const lastrowvalue = gridInstance.getValue(total_row - 1, 'name');
	if (lastrowvalue === '') return false;

	gridInstance.appendRow(
		{
			id: '',
			name: '',
			access: '1',
			create: '1',
			read: '1',
			write: '1',
			delete: '1',
		})
}

function changeCheckbox(rowId, fieldName){
	if(document.getElementById('checkbox-'+fieldName+'-'+rowId).checked){
		console.log("is true")
	}else{
		console.log("is false now")
	}
}

function deleteRow(rowId) {
	const table_name = gridInstance.getValue(rowId, 'name');
	gridInstance.removeRow(rowId);
	//send ajax call to delete table
}


function updateFieldData(ev, idx) {
	const table_name = gridInstance.getValue(idx, 'name');
	data = {
		columnName: ev.columnName,
		column_value: ev.value,
		table_name
	}
	//send update to ajax
}


function showTab(tab) {
	var hide = '';
	var show = '';
	if (tab === 'settings') {
		hide = 'tables';
		show = 'settings';
	} else {
		hide = 'settings';
		show = 'tables';
	}
	document.getElementById('tab-' + hide).classList.remove('slds-is-active');
	document.getElementById('tab-' + show).classList.add('slds-is-active');

	document.getElementById('tab-data-' + show).style.visibility = 'visible';
	document.getElementById('tab-data-' + hide).style.visibility = 'hidden';

	document
		.getElementById('tab-data-' + show)
		.classList.remove('slds-is-active');
	document.getElementById('tab-data-' + show).classList.add('slds-is-active');

	document.getElementById('tab-data-' + show).classList.remove('slds-hide');
	document.getElementById('tab-data-' + show).classList.add('slds-show');
	document.getElementById('tab-data-' + hide).classList.remove('slds-show');
	document.getElementById('tab-data-' + hide).classList.add('slds-hide');
}