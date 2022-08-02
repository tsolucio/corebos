/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (typeof (Utilities) == 'undefined') {

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
				jQuery('#form-error-ic-server-type').append('<span>' + Utilities.i18n('JSLBL_Choose_Server_Type') + '</span>');
				jQuery('#form-error-ic-server-type').show();
				return false;
			}

			if (form.ic_mail_server_name.value == '' && form.ic_mail_server_active.checked) {
				jQuery('#ic-div-servername').addClass('slds-has-error');
				jQuery('#form-error-ic-server-name').append('<span>' + Utilities.i18n('JSLBL_SERVERNAME_CANNOT_BE_EMPTY') + '</span>');
				jQuery('#form-error-ic-server-name').show();
				return false;
			}

			if (form.ic_mail_server_username.value == '' && form.ic_mail_server_active.checked) {
				jQuery('#ic-div-username').addClass('slds-has-error');
				jQuery('#form-error-ic-server-username').append('<span>' + Utilities.i18n('JSLBL_USERNAME_CANNOT_BE_EMPTY') + '</span>');
				jQuery('#form-error-ic-server-username').show();
				return false;
			}

			if (form.ic_mail_server_password.value == '' && form.ic_mail_server_active.checked) {
				jQuery('#ic-div-password').addClass('slds-has-error');
				jQuery('#form-error-ic-server-password').append('<span>' + Utilities.i18n('JSLBL_PASSWORD_CANNOT_BE_EMPTY') + '</span>');
				jQuery('#form-error-ic-server-password').show();
				return false;
			}

			// for Outgoing Mail Server
			if (form.og_mail_server_name.value == '' && form.og_mail_server_active.checked) {
				jQuery('#og-div-server-name').addClass('slds-has-error');
				jQuery('#form-error-og-server-name').append('<span>' + Utilities.i18n('JSLBL_SERVERNAME_CANNOT_BE_EMPTY') + '</span>');
				jQuery('#form-error-og-server-name').show();
				return false;
			}

			if (form.og_mail_server_username.value == '' && form.og_mail_server_active.checked) {
				jQuery('#og-div-server-username').addClass('slds-has-error');
				jQuery('#form-error-og-server-username').append('<span>' + Utilities.i18n('JSLBL_USERNAME_CANNOT_BE_EMPTY') + '</span>');
				jQuery('#form-error-og-server-username').show();
				return false;
			}

			if (form.og_mail_server_password.value == '' && form.og_mail_server_active.checked) {
				jQuery('#ic-div-server-password').addClass('slds-has-error');
				jQuery('#form-error-og-server-password').append('<span>' + Utilities.i18n('JSLBL_PASSWORD_CANNOT_BE_EMPTY') + '</span>');
				jQuery('#form-error-og-server-password').show();
				return false;
			}

			Utilities.progress_show(Utilities.i18n('JSLBL_Saving_And_Verifying'), '...');
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?' + Utilities._baseurl() + '&' + jQuery(form).serialize()
			}).done(function (transport) {
				var response_obj = JSON.parse(transport);
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
			if (typeof (suffix) == 'undefined') {
				suffix = '';
			}
			VtigerJS_DialogBox.block();
			if (typeof (msg) != 'undefined') {
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
			if (typeof (Utilitiesi18nInfo) != 'undefined') {
				return Utilitiesi18nInfo[key];
			}
			if (typeof (alert_arr) != 'undefined' && alert_arr[key]) {
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
const defaultURL = 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions';

// document.addEventListener('DOMContentLoaded', function (event) {
// 	loadJS(
// 		'index.php?module=Utilities&action=UtilitiesAjax&file=getjslanguage'
// 	).then(() => {
// 		loadTUIGridData();

// 	});
// });

function loadTUIGridData() {
	gridInstance = new Grid({
		el: document.getElementById('chgrid'),
		columns: [
			{
				name: 'ws_name',
				header: mod_alert_arr.LBL_WS_NAME,
				sortingType: 'desc',
				editor: 'text',
				onAfterChange(ev) {
					const idx = gridInstance.getIndexOfRow(ev.rowKey);
					updateFieldData(ev, idx);
				},
			},
			{
				name: 'table_name',
				header: mod_alert_arr.LBL_TABLE_NAME,
				sortingType: 'desc',
				editor: 'text',
				onAfterChange(ev) {
					const idx = gridInstance.getIndexOfRow(ev.rowKey);
					updateFieldData(ev, idx);
				},
			},
			{
				name: 'access',
				header: mod_alert_arr.LBL_ACCESS,
				whiteSpace: 'normal',
				sortingType: 'desc',
				renderer: {
					type: CheckboxWithActionRender,
				}
			},
			{
				name: 'create',
				header: mod_alert_arr.LBL_CREATE,
				whiteSpace: 'normal',
				sortingType: 'desc',
				renderer: {
					type: CheckboxWithActionRender,
				}
			},
			{
				name: 'read',
				header: mod_alert_arr.LBL_READ,
				whiteSpace: 'normal',
				sortingType: 'desc',
				renderer: {
					type: CheckboxWithActionRender,
				}
			},
			{
				name: 'write',
				header: mod_alert_arr.LBL_WRITE,
				whiteSpace: 'normal',
				sortingType: 'desc',

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
		data: {
			api: {
				readData: {
					url: `${defaultURL}&functiontocall=clickHouse&method=getTables`,
					method: 'GET'
				}
			}
		},
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
}



function addChRow() {
	const total_row = gridInstance.getRowCount();
	const lastrowvalue = gridInstance.getValue(total_row - 1, 'name');
	if (lastrowvalue === '') {
		return false;
	}

	gridInstance.appendRow(
		{
			ws_name: '',
			table_name: '',
			access: '1',
			create: '1',
			read: '1',
			write: '1',
			delete: '1',
		});
}

function changeChCheckbox(rowId, fieldName) {
	let newValue = 1;
	if (document.getElementById('checkbox-' + fieldName + '-' + rowId).checked) {
		newValue = 1;
	} else {
		newValue = 0;
	}

	const table_name = gridInstance.getValue(rowId, 'table_name');
	const old_table_name = table_name;
	const ws_name = fieldName === 'ws_name' ? newValue : gridInstance.getValue(rowId, 'ws_name');
	const access = fieldName === 'access' ? newValue : gridInstance.getValue(rowId, 'access');
	const create = fieldName === 'create' ? newValue : gridInstance.getValue(rowId, 'create');
	const read = fieldName === 'read' ? newValue : gridInstance.getValue(rowId, 'read');
	const write = fieldName === 'write' ? newValue : gridInstance.getValue(rowId, 'write');
	const old_ws_name = ws_name;

	data = {
		table_name,
		old_table_name,
		ws_name,
		old_ws_name,
		access,
		create,
		read,
		write,
	};
	updateAjax(data);
}

function deleteRow(rowId) {
	const table_name = gridInstance.getValue(rowId, 'table_name');
	gridInstance.removeRow(rowId);
	//send ajax call to delete table
	jQuery.ajax({
		method: 'POST',
		url: `${defaultURL}&functiontocall=clickHouse&method=deleteTable`,
		data: { table_name }
	}).then(function (response) {
		console.log(response);
	});
}


function updateFieldData(ev, idx) {
	const columnChanged = ev.columnName;
	const oldValue = ev.prevValue;
	const newValue = ev.value;

	const table_name = columnChanged === 'table_name' ? newValue : gridInstance.getValue(idx, 'table_name');
	const old_table_name = columnChanged !== 'table_name' ? table_name : oldValue;
	const ws_name = columnChanged === 'ws_name' ? newValue : gridInstance.getValue(idx, 'ws_name');
	const old_ws_name = columnChanged === 'ws_name' ? oldValue : ws_name;
	const access = columnChanged === 'access' ? newValue : gridInstance.getValue(idx, 'access');
	const create = columnChanged === 'create' ? newValue : gridInstance.getValue(idx, 'create');
	const read = columnChanged === 'read' ? newValue : gridInstance.getValue(idx, 'read');
	const write = columnChanged === 'write' ? newValue : gridInstance.getValue(idx, 'write');
	data = {
		table_name,
		ws_name,
		access,
		create,
		read,
		write,
		old_table_name,
		old_ws_name,

	};
	updateAjax(data);
}

function updateAjax(data) {
	jQuery.ajax({
		method: 'POST',
		url: `${defaultURL}&functiontocall=clickHouse&method=addUpdateTable`,
		data: data
	}).then(function (response) {
		console.log(response);
	});
}


function showChTab(tab) {
	var hide = '';
	var show = '';
	if (tab === 'settings') {
		hide = 'tables';
		show = 'settings';
	} else {
		hide = 'settings';
		show = 'tables';
		loadJS(
			'index.php?module=Utilities&action=UtilitiesAjax&file=getjslanguage'
		).then(() => {
			loadTUIGridData();

		});
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