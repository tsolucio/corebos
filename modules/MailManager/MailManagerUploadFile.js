/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//extend the file upload xhr class
qq.extend(qq.UploadHandlerXhr.prototype, {
	_upload: function(id, params){
		if(!MailManager.checkUploadCount()) {
			return false;
		}
		var file = this._files[id],
				name = this.getName(id),
				size = this.getSize(id);

			this._loaded[id] = 0;

			var xhr = this._xhrs[id] = new XMLHttpRequest();
			var self = this;

			xhr.upload.onprogress = function(e){
				if (e.lengthComputable){
					self._loaded[id] = e.loaded;
					self._options.onProgress(id, name, e.loaded, e.total);
				}
			};

			xhr.onreadystatechange = function(){
				if (xhr.readyState == 4){
					self._onComplete(id, xhr);
				}
			};

			params['emailid'] = params['currentid'] = jQuery('#emailid').val();
			params['to'] = jQuery('#_mail_replyfrm_to_').val();
			params['cc'] = jQuery('#_mail_replyfrm_cc_').val();
			params['bcc'] = jQuery('#_mail_replyfrm_bcc_').val();
			params['subject'] = jQuery('#_mail_replyfrm_subject_').val();
			
			var body = CKEDITOR.instances['_mail_replyfrm_body_'];
			if(body != undefined) {
				params['body'] =  body.getData();
			}
			
			// build query string
			params = params || {};
			params['qqfile'] = name;
			var queryString = qq.obj2url(params, this._options.action);
			xhr.open("POST", queryString, true);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("X-File-Name", encodeURIComponent(name));
			xhr.setRequestHeader("Content-Type", "application/octet-stream");
			xhr.send(file);
		},

	 _onComplete: function(id, xhr){
		// the request was aborted/cancelled
		if (!this._files[id]) return;

		var name = this.getName(id);
		var size = this.getSize(id);

		this._options.onProgress(id, name, size, size);

		if (xhr.status == 200){
			var response;
			try {
				response = eval("(" + xhr.responseText + ")");
				this._postcompletActions(id, name, response);
			} catch(err){
				response = {};
			}
			this._options.onComplete(id, name, response);

		} else {
			this._options.onComplete(id, name, {});
		}

		this._files[id] = null;
		this._xhrs[id] = null;
		this._dequeue(id);
	},

	_postcompletActions : function(id, name, response) {
		if(response.success == true) {
			if(response.result.success == true) {
				if(response.result.emailid != "") {
					jQuery('#emailid').val(response.result.emailid);
				}
			}
		}
	}
});

// extending the file uploading class
qq.extend(qq.FileUploader.prototype, {
	 _onSubmit: function(id, fileName){
		qq.FileUploaderBasic.prototype._onSubmit.apply(this, arguments);
		this._addToList(id, fileName);
	},

	 _onComplete: function(id, fileName, response){
		qq.FileUploaderBasic.prototype._onComplete.apply(this, arguments);

		// mark completed
		var item = this._getItemByFileId(id);
		qq.remove(this._find(item, 'cancel'));
		qq.remove(this._find(item, 'spinner'));
		
		if (!response.result.error){
			qq.addClass(item, this._classes.success);

			var fileElementDelete = this._find(item, 'deleteupload');
			fileElementDelete.style.display = 'inline';
			jQuery(fileElementDelete).html("<img height='12' border='0' width='12' title="+MailManager.i18n('JSLBL_Delete')+" src='themes/images/no.gif'>")
			this._attachDeleteHandler(response.result.emailid, response.result.docid, fileElementDelete);
			MailManager.uploadCountUpdater();
		} else {
			qq.addClass(item, this._classes.fail);
			if(response.result.emailid != "") jQuery("#emailid").val(response.result.emailid);
			MailManager.show_error(response.result.error);
			MailManager.hide_error();
		}
	},

	_addToList: function(id, fileName){
		if(!MailManager.checkUploadCount()) {
			return false;
		}
		var item = qq.toElement(this._options.fileTemplate);
		item.qqFileId = id;
		var fileElement = this._find(item, 'file');
		qq.setText(fileElement, this._formatFileName(fileName));
		
		this._find(item, 'deleteupload').style.display = 'none';
		this._listElement.appendChild(item);
	},

	_attachDeleteHandler: function(emailId, docId, element) {
		jQuery(element).bind('click', function() {
			MailManager.deleteAttachment(emailId, docId, element);
		});
	}
});

qq.extend(qq.UploadHandlerForm.prototype, {
	_upload: function(id, params){
        var input = this._inputs[id];

        if (!input){
            throw new Error('file with passed id was not added, or already uploaded or cancelled');
        }

        var fileName = this.getName(id);

        var iframe = this._createIframe(id);

		params['emailid'] = params['currentid'] =  jQuery('#emailid').val();
		params['to'] = jQuery('#_mail_replyfrm_to_').val();
		params['cc'] = jQuery('#_mail_replyfrm_cc_').val();
		params['bcc'] = jQuery('#_mail_replyfrm_bcc_').val();
		params['subject'] = jQuery('#_mail_replyfrm_subject_').val();
		
		var body = CKEDITOR.instances['_mail_replyfrm_body_'];
		if(body != undefined) {
			params['body'] =  body.getData();
		}
		
        var form = this._createForm(iframe, params);
        form.appendChild(input);
        var self = this;

        this._attachLoadEvent(iframe, function(){
            var response = self._getIframeContentJSON(iframe);
			
			if(response.result != "" && response.result.emailid != "") {
				jQuery('#emailid').val(response.result.emailid);
			}

            self._options.onComplete(id, fileName, response);
            self._dequeue(id);

            delete self._inputs[id];
            // timeout added to fix busy state in FF3.6
            setTimeout(function(){
                qq.remove(iframe);
            }, 1);
        });
        form.submit();
        qq.remove(form);
        return id;
    }
});