/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
var attachmentManager = {
	UploadLimit : 6,
	createUploader : function () {
		Dropzone.autoDiscover = false;
		var uploader = new Dropzone('#file-uploader', {
			url : 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&mode=ajax&functiontocall=saveAttachment',
			paramName: 'qqfile',
			parallelUploads: 1,
			addRemoveLinks: true,
			createImageThumbnails: true,
			dictRemoveFile: attachmentManager.i18n('JSLBL_Delete'),
			uploadMultiple: false,
			clickable: ['#file-uploader-message', '#file-uploader']
		});
		uploader.on('success', function (file, response) {
			var res = JSON.parse(response);
			file.docid = res.docid;
			file.attachid = res.attachid;
			attachmentManager.addAttachment(file.docid, this);
			var attcnt = document.getElementById('attachmentCount');
			attcnt.value = parseInt(attcnt.value) + 1;
		});
		uploader.on('removedfile', function (file) {
			attachmentManager.deleteAttachment(file.docid, this);
		});
		uploader.on('addedfile', function (file) {
			if (file.docid!=undefined) {
				attachmentManager.addAttachment(file.docid, this);
			}
		});
		return uploader;
	},
	addAttachment : function (docid, ele) {
		var attids = document.getElementById('attachmentids');
		if (attids.value.indexOf(docid) == -1) {
			attids.value = attids.value + docid + ',';
		}
	},
	deleteAttachment : function (docid, ele) {
		var attids = document.getElementById('attachmentids');
		attids.value = attids.value.replace(docid+',', '');
		var attcnt = document.getElementById('attachmentCount');
		attcnt.value = attcnt.value-1;
	},
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
	show_error: function (message) {
		var errordiv = jQuery('#_messagediv_');
		if (message == '') {
			errordiv.text('').hide();
		} else {
			errordiv.html('<p>' + message + '</p>').css('display', 'block').addClass('mm_error').removeClass('mm_message');
			attachmentManager.placeAtCenter(errordiv);
		}
		attachmentManager.hide_error();
	},
	hide_error: function () {
		setTimeout(
			function () {
				jQuery('#_messagediv_').hide();
			},
			5000
		);
	},
	show_message: function (message) {
		var errordiv = jQuery('#_messagediv_');
		if (message == '') {
			errordiv.text('').hide();
		} else {
			errordiv.html('<p>' + message + '</p>').css('display', 'block').removeClass('mm_error').addClass('mm_message');
			attachmentManager.placeAtCenter(errordiv);
		}
		attachmentManager.hide_error();
	},
	i18n: function (key) {
		if (typeof(alert_arr) != 'undefined' && alert_arr[key]) {
			return alert_arr[key];
		}
		return key;
	},
	placeAtCenter : function (element) {
		element.css('position', 'absolute');
		element.css('top', ((jQuery(window).height() - element.outerHeight()) / 2) + jQuery(window).scrollTop() + 'px');
		element.css('left', ((jQuery(window).width() - element.outerWidth()) / 2) + jQuery(window).scrollLeft() + 'px');
	},
	getDocuments : function () {
		if (!attachmentManager.checkUploadCount()) {
			return false;
		}
		window.open('index.php?module=Documents&return_module=MailManager&action=Popup&popuptype=detailview&form=EditView&form_submit=false&recordid=&forrecord=&srcmodule=MailManager&popupmode=ajax&RLreturn_module=MailManager&callback=MailManager.add_data_to_relatedlist', 'test', 'width=640,height=602,resizable=0,scrollbars=0');
	},
	checkUploadCount : function () {
		var CurrentUploadCount = jQuery('#attachmentCount').val();
		if (CurrentUploadCount >= attachmentManager.UploadLimit) {
			attachmentManager.show_error(attachmentManager.i18n('JSLBL_FILEUPLOAD_LIMIT_EXCEEDED'));
			return false;
		}
		return true;
	}
};
jQuery(document).ready(function () {
	attachmentManager.createUploader();
	if (__attinfo.length>0) {
		var dzelem = document.getElementById('file-uploader');
		for (var i=0; i<__attinfo.length; i++) {
			dzelem.dropzone.emit('addedfile', {name:__attinfo[i]['name'], size:__attinfo[i]['size'], docid:__attinfo[i]['docid']});
		}
	}
});