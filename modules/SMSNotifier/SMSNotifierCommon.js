/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if(typeof(SMSNotifierCommon) == 'undefined') {
	var SMSNotifierCommon = {
		
		/** Wizard Container **/
		getWizardContainer : function() {
			var container = $('__smsnotifier_wizard_container__');
			if(!container) {
				container = document.createElement('div');
				container.className = 'layerPopup';
				container.id = '__smsnotifier_wizard_container__';
				document.body.appendChild(container);				
			}
			
			return container;
		},
		
		displayWizardContainer : function(container, sourcenode) {
			if(typeof(container) == 'undefined') container = SMSNotifierCommon.getWizardContainer();
			if(container) {
				if(typeof(sourcenode) != 'undefined') {
					
					if(sourcenode != null) {
						fnvshobj(sourcenode, container.id);
					} else {
						// Place at center if not already positioned.
						if(container.style.top == '') {
							placeAtCenter(container);
						}
					}
				}
				container.show();
			}
		},
		
		hideWizardContainer : function() {
			if(typeof(container) == 'undefined') container = SMSNotifierCommon.getWizardContainer();
			if(container) {
				container.hide();
			}
		},	
		
		/** Select Wizard **/
		displaySelectWizard_DetailView : function(sourcemodule, recordid) {
			var sourcenode = null;			
			SMSNotifierCommon.displaySelectWizard(sourcenode, sourcemodule, recordid);
		},
		
		displaySelectWizard : function(sourcenode, sourcemodule, recordid) {
			var idstring = false;
			
			// Record id not sent directly? Could be from ListView
			if(typeof(recordid) == 'undefined' || recordid == null || recordid == '') {
				var excludedRecords = document.getElementById("excludedRecords").value;
				var select_options = document.getElementById('allselectedboxes').value;
				var searchurl = document.getElementById('search_url').value;
				var numOfRows = document.getElementById('numOfRows').value;
				var viewid = getviewId();
				if(select_options != 'all'){
					var x = select_options.split(';');
					var count = x.length;
                                
					if(count > 1) {
						idstring = select_options;
					} else {
						alert(alert_arr.SELECT);
						return false;
					}
				}else{
					idstring = select_options;
					count = numOfRows;
				}
			} else {
				// Record id sent? Could be from DetailView
				idstring = recordid;
			}
			if(count > getMaxMassOperationLimit()) {
				var confirm_str = alert_arr.MORE_THAN_500;
				if(confirm(confirm_str)) {
					var confirm_status = true;
				} else {
					return false;
				}
			} else {
				confirm_status = true;
			}

			if(confirm_status){
	
				if(idstring) {
					var url = 'module=SMSNotifier&action=SMSNotifierAjax&ajax=true&file=SMSNotifierSelectWizard';
					url += '&sourcemodule=' + encodeURIComponent(sourcemodule);
					url += '&idstring=' + encodeURIComponent(idstring);
					url += '&excludedRecords=' + encodeURIComponent(excludedRecords);
					url += '&viewname=' +encodeURIComponent(viewid);
					url += '&searchurl=' +encodeURIComponent(searchurl);
							
					new Ajax.Request('index.php', {
						queue: {
							position: 'end',
							scope: 'command'
						},
						method: 'post',
						postBody:url,
						onComplete: function(response) {
							SMSNotifierCommon.buildSelectWizard(response.responseText, sourcenode);
						}
					});
				}
			}
		},
		
		buildSelectWizard : function(content, sourcenode) {
			var container = SMSNotifierCommon.getWizardContainer();
			container.innerHTML = content;			
			SMSNotifierCommon.displayWizardContainer(container, sourcenode);			
		},
		
		hideSelectWizard : function() {
			SMSNotifierCommon.hideWizardContainer();
		},
		
		/** Compose Wizard **/
		displayComposeWizard : function(form, sourcenode) {
			
			var form_phonetype_inputs = form.phonetype;
			if(typeof form_phonetype_inputs.length == 'undefined') {
				form_phonetype_inputs = [form.phonetype];
			}
			
			// Variables to submit for next wizard
			var phonefields = '';
			var idstring = form.idstring.value;
            var excludedRecords = form.excludedRecords.value;
            var viewid = form.viewid.value;
            var searchurl = form.searchurl.value;
			var sourcemodule = form.sourcemodule.value;
			
			// Gather the phone fields selected.
			for(var index = 0; index < form_phonetype_inputs.length; ++index) {
				if(form_phonetype_inputs[index].checked) {
					phonefields += form_phonetype_inputs[index].value + ';';
				}
			}
			
			if(phonefields == '') {
				// TODO Show alert?
				return false;
			}			
			
			var url = 'module=SMSNotifier&action=SMSNotifierAjax&ajax=true&file=SMSNotifierComposeWizard';			
			url += '&sourcemodule=' + encodeURIComponent(sourcemodule);			
			url += '&idstring=' + encodeURIComponent(idstring);
            url += '&excludedRecords=' + encodeURIComponent(excludedRecords);
            url += '&viewname=' +encodeURIComponent(viewid);
            url += '&searchurl=' +encodeURIComponent(searchurl);
			url += '&phonefields='+ encodeURIComponent(phonefields);
			
			new Ajax.Request('index.php', {
				queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:url,
				onComplete: function(response) {					
					SMSNotifierCommon.buildComposeWizard(response.responseText, sourcenode);					
				}
			});
		},
		
		buildComposeWizard : function(content, sourcenode) {
			var container = SMSNotifierCommon.getWizardContainer();
			container.innerHTML = content;			
			SMSNotifierCommon.displayWizardContainer(container, sourcenode);
		},
		
		hideComposeWizard : function() {
			SMSNotifierCommon.hideWizardContainer();
		},
		
		/** Send Operation **/
		triggerSendSMS : function(form) {
			
			var messageTextInput = form.message;
			if(messageTextInput.value == '') {
				messageTextInput.focus();
				return false; 
			}
			
			// Variables to submit for next wizard
			var phonefields = form.phonefields.value;
			var idstring = form.idstring.value;
            var excludedRecords = form.excludedRecords.value;
            var viewid = form.viewid.value;
            var searchurl = form.searchurl.value;
			var sourcemodule = form.sourcemodule.value;
			var message = messageTextInput.value;
			
			$('status').show();
			
			VtigerJS_DialogBox.block();
			
			var url = 'module=SMSNotifier&action=SMSNotifierAjax&ajax=true&file=SMSNotifierSend';			
			url += '&sourcemodule=' + encodeURIComponent(sourcemodule);			
			url += '&idstring=' + encodeURIComponent(idstring);
			url += '&phonefields='+ encodeURIComponent(phonefields);
			url += '&message=' + encodeURIComponent(message);
            url += '&excludedRecords=' + encodeURIComponent(excludedRecords);
            url += '&viewname=' +encodeURIComponent(viewid);
            url += '&searchurl=' +encodeURIComponent(searchurl);
			
			new Ajax.Request('index.php', {
				queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:url,
				onComplete: function(response) {					
					SMSNotifierCommon.hideComposeWizard();
					
					$('status').hide();
					VtigerJS_DialogBox.unblock();
				}
			});
		}
	};
}
