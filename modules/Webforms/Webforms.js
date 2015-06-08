var Webforms ={

	confirmAction:function(msg){
		return confirm(msg);
	},
	deleteForm:function(formname,id){
		if (typeof webforms_alert_arr != 'undefined') {
			var status=Webforms.confirmAction(getTranslatedString('LBL_DELETE_MSG', webforms_alert_arr));
		} else {
			var status=Webforms.confirmAction(getTranslatedString('LBL_DELETE_MSG'));
		}
		if(!status){
			return false;
		}
		Webforms.submitForm(formname, 'index.php?module=Webforms&action=Delete&id='+id);
		return true;
	},
	editForm:function(id){
		Webforms.submitForm('action_form', 'index.php?module=Webforms&action=WebformsEditView&id='+id+'&parenttab=Settings&operation=edit');
	},
	submitForm:function(formName,action){
		document.forms[formName].action=action;
		document.forms[formName].submit();
	},
	showHideElement:function(){
		var i;
		var len=arguments.length;
		for(i=0;i<len;i++){
			if($(arguments[i])){
				if($(arguments[i]).style.display!="none"){
					$(arguments[i]).style.display="none";
				}else{
					$(arguments[i]).style.display="inline";
				}
			}
		}
	},

	validateForm: function(form,action) {
		var name=$('name').value;
		var ownerid=$('ownerid').value;
		var module=$('targetmodule').value;
		if((name=="")||(name==null)||(ownerid=="")||(ownerid==null)||(module=="")||(module==null)){
			if (typeof webforms_alert_arr != 'undefined') {
				alert(getTranslatedString('LBL_MADATORY_FIELDS', webforms_alert_arr));
			} else {
				alert(getTranslatedString('LBL_MADATORY_FIELDS'));
			}
			return false;
		}
		elem=document.getElementById(form).elements;
		elemNo=document.getElementById(form).elements.length;
		for(i=0;i<elemNo;i++){
			if((elem[i].value!='' && elem[i].value!=null) && (elem[i].getAttribute('fieldtype')!=null && elem[i].getAttribute('fieldtype')!='') && elem[i].style.display!='none' ){
				switch(elem[i].getAttribute('fieldtype')){
					case 'date' :if(!dateValidate(elem[i].name,elem[i].getAttribute('fieldlabel'),elem[i].getAttribute('fieldtype')))
										return false;
						break;
					case 'time' :if(!timeValidate(elem[i].name,elem[i].getAttribute('fieldlabel'),elem[i].getAttribute('fieldtype')))
										return false;
						break;
					case 'currency':
					case 'number':
					case 'double' :if(!numValidate(elem[i].name,elem[i].getAttribute('fieldlabel'),elem[i].getAttribute('fieldtype')))
										return false;
						break;
					case 'email' :if(!patternValidate(elem[i].name,elem[i].getAttribute('fieldlabel'),elem[i].getAttribute('fieldtype')))
										return false;
						break;
					default :break;


				}
			}
		}
		if(mode=="save")
			Webforms.checkName(name,form,action);
		else
			Webforms.submitForm(form, action);
		return false;
	},

	getHTMLSource:function(id){
		var url = "module=Webforms&action=WebformsAjax&file=WebformsHTMLView&ajax=true&id=" + encodeURIComponent(id);

		VtigerJS_DialogBox.block();
		new Ajax.Request('index.php', {
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:url,
			onComplete: function(response) {
				VtigerJS_DialogBox.unblock();
				var str = response.responseText
				$('webform_source').innerText = str;
				$('webform_source').value=str;
				$('orgLay').style.display="block";
			}
		});
	},

	fetchFieldsView: function(module) {
		if((module=="")||(module==null)) return;
		var url = "module=Webforms&action=WebformsAjax&file=WebformsFieldsView&ajax=true&targetmodule=" + encodeURIComponent(module);

		VtigerJS_DialogBox.block();
		new Ajax.Request('index.php', {
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:url,
			onComplete: function(response) {
				VtigerJS_DialogBox.unblock();
				var str = response.responseText;
				$('Webforms_FieldsView').innerHTML = str;
				eval(document.getElementById('counter').innerHTML);
				for(i=1;i<=count;i++){
					if(document.getElementById("date_"+i)){
						eval(document.getElementById("date_"+i).innerHTML);
					}
				}
			}
		});
	},
	checkName: function(name,form,action) {
		if((name=="")||(name==null)) return;
		var url = "module=Webforms&action=WebformsAjax&file=Save&ajax=true&name=" + encodeURIComponent(name);

		new Ajax.Request('index.php', {
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:url,
			onComplete: function(response) {
				var JSONres = JSON.parse(response.responseText);
				if(JSONres.result==false){
					alert(getTranslatedString('LBL_DUPLICATE_NAME', webforms_alert_arr));
				}
				else{
					Webforms.submitForm(form, action);
				}
			}
		});

	}
}

