var Webforms ={

	confirmAction:function (msg) {
		return confirm(msg);
	},
	deleteForm:function (formname, id) {
		if (typeof webforms_alert_arr != 'undefined') {
			var status=Webforms.confirmAction(getTranslatedString('LBL_DELETE_MSG', webforms_alert_arr));
		} else {
			var status=Webforms.confirmAction(getTranslatedString('LBL_DELETE_MSG'));
		}
		if (!status) {
			return false;
		}
		Webforms.submitForm(formname, 'index.php?module=Webforms&action=Delete&id='+id);
		return true;
	},
	editForm:function (id) {
		Webforms.submitForm('action_form', 'index.php?module=Webforms&action=WebformsEditView&id='+id+'&operation=edit');
	},
	submitForm:function (formName, action) {
		document.forms[formName].action=action;
		document.forms[formName].submit();
	},
	showHideElement:function () {
		var len=arguments.length;
		for (var i=0; i<len; i++) {
			if (document.getElementById(arguments[i])) {
				if (document.getElementById(arguments[i]).style.display!='none') {
					document.getElementById(arguments[i]).style.display='none';
				} else {
					document.getElementById(arguments[i]).style.display='inline';
				}
			}
		}
	},

	validateForm: function (form, action) {
		var name=document.getElementById('name').value;
		var ownerid=document.getElementById('ownerid').value;
		var module=document.getElementById('targetmodule').value;
		var web_domain=document.getElementById('web_domain').value;
		if ((name=='') || (name==null) || (ownerid=='') || (ownerid==null) || (module=='') || (module==null) || (web_domain=='') || (web_domain==null)) {
			if (typeof webforms_alert_arr != 'undefined') {
				alert(getTranslatedString('LBL_MADATORY_FIELDS', webforms_alert_arr));
			} else {
				alert(getTranslatedString('LBL_MADATORY_FIELDS'));
			}
			return false;
		}
		var elem=document.getElementById(form).elements;
		var elemNo=document.getElementById(form).elements.length;
		for (var i=0; i<elemNo; i++) {
			if ((elem[i].value!='' && elem[i].value!=null) && (elem[i].getAttribute('fieldtype')!=null && elem[i].getAttribute('fieldtype')!='') && elem[i].style.display!='none' ) {
				switch (elem[i].getAttribute('fieldtype')) {
				case 'date':
					if (!dateValidate(elem[i].name, elem[i].getAttribute('fieldlabel'), elem[i].getAttribute('fieldtype'))) {
						return false;
					}
					break;
				case 'time':
					if (!timeValidate(elem[i].name, elem[i].getAttribute('fieldlabel'), elem[i].getAttribute('fieldtype'))) {
						return false;
					}
					break;
				case 'currency':
				case 'number':
				case 'double':
					if (!numValidate(elem[i].name, elem[i].getAttribute('fieldlabel'), elem[i].getAttribute('fieldtype'))) {
						return false;
					}
					break;
				case 'email':
					if (!patternValidate(elem[i].name, elem[i].getAttribute('fieldlabel'), elem[i].getAttribute('fieldtype'))) {
						return false;
					}
					break;
				default :break;
				}
			}
		}
		if (mode=='save') {
			Webforms.checkName(name, form, action);
		} else {
			Webforms.submitForm(form, action);
		}
		return false;
	},

	getHTMLSource:function (id) {
		var url = 'module=Webforms&action=WebformsAjax&file=WebformsHTMLView&ajax=true&id=' + encodeURIComponent(id);

		VtigerJS_DialogBox.block();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+url
		}).done(function (response) {
			VtigerJS_DialogBox.unblock();
			var str = response;
			document.getElementById('webform_source').innerText = str;
			document.getElementById('webform_source').value=str;
			document.getElementById('orgLay').style.display='block';
		});
	},

	fetchFieldsView: function (module) {
		if ((module=='')||(module==null)) {
			return;
		}
		var url = 'module=Webforms&action=WebformsAjax&file=WebformsFieldsView&ajax=true&targetmodule=' + encodeURIComponent(module);

		VtigerJS_DialogBox.block();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+url
		}).done(function (response) {
			VtigerJS_DialogBox.unblock();
			var str = response;
			document.getElementById('Webforms_FieldsView').innerHTML = str;
			eval(document.getElementById('counter').innerHTML);
			for (var i=1; i<=count; i++) {
				if (document.getElementById('date_'+i)) {
					eval(document.getElementById('date_'+i).innerHTML);
				}
			}
		});
	},
	checkName: function (name, form, action) {
		if ((name=='')||(name==null)) {
			return;
		}
		var url = 'module=Webforms&action=WebformsAjax&file=Save&ajax=true&name=' + encodeURIComponent(name);

		jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+url
		}).done(function (response) {
			var JSONres = JSON.parse(response);
			if (JSONres.result==false) {
				alert(getTranslatedString('LBL_DUPLICATE_NAME', webforms_alert_arr));
			} else {
				Webforms.submitForm(form, action);
			}
		});
	}
};
