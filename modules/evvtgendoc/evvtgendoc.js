/*
 * Copyright 2012 JPL TSolucio, S.L.   --   This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 */
function showgendoctemplates(module) {
	if (document.getElementById('allids').value=='' && document.getElementById('allselectedboxes').value=='') {
		alert(alert_arr.SELECT);
	} else {
		jQuery.ajax({
			method: 'post',
			url: 'index.php?module=evvtgendoc&action=evvtgendocAjax&file=GetTemplates&moduletemplate='+module,
		}).done(function (response) {
			document.getElementById('generatedocument').style.display='block';
			document.getElementById('closegendoc').style.display='block';
			document.getElementById('gendoc').innerHTML=response;
			if (document.getElementById('recordval').value=='') {
				document.getElementById('recordval').value=document.getElementById('allselectedboxes').value;
			}
		});
	}
}

function checkOneTplSelected() {
	if (document.getElementById('gendoctemplate')==null || document.getElementById('gendoctemplate').selectedIndex==-1) {
		alert(alert_arr.SELECTTEMPLATE);
		return false;
	} else {
		return true;
	}
}

function gendocAction(action, format, module, crmid, i18n) {
	if (checkOneTplSelected()) {
		var url = 'index.php?module=evvtgendoc&action=evvtgendocAjax&file=gendocAction';
		url = url + '&gdaction=' + action;
		url = url + '&gdformat=' + format;
		url = url + '&gdtemplate=' + jQuery('#gendoctemplate').val();
		url = url + '&gdmodule=' + module;
		url = url + '&gdcrmid=' + crmid;
		switch (action) {
		case 'export':
			document.getElementById('gendociframe').src=url;
			break;
		case 'email':
			document.getElementById('gendociframe').src=url;
			var emailurl = 'index.php?module=Emails&action=EmailsAjax&file=EditView&attachment='+i18n+'_'+crmid+'.'+(format=='pdf' ? 'pdf' : 'odt');
			if (module=='Invoice' || module=='SalesOrder' || module=='PurchaseOrder' || module=='Quotes') {
				emailurl = emailurl + '&invmodid='+crmid;
			} else {
				emailurl = emailurl + '&sendmail=true&pmodule='+module+'&idlist='+crmid;
			}
			openPopUp('xComposeEmail', this, emailurl, 'createemailWin', 900, 700, 'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
			break;
		case 'save':
			document.getElementById('gendociframe').src=url;
			alert(alert_arr.GENDOCSAVED);
			break;
		}
	}
}

function toggleModule_mod(tabid, action) {
	document.getElementById('status').style.display='block';
	jQuery.ajax({
		method: 'post',
		url: 'index.php?module=evvtgendoc&action=evvtgendocAjax&file=BasicSettings&tabid='+tabid+'&status='+action+'&ajax=true',
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('gendocContents').innerHTML=response;
	});
}

function validateFields() {
	var template=document.forms['EditView']['gendoctemplate'];
	var entity=document.forms['EditView']['recordval'];
	if (template.value==null || template.value=='') {
		document.getElementById('gendoctemplate_display').style.borderColor= '#FFBABA';
		document.getElementById('template_error_msg').innerHTML='<li>'+alert_arr.SELECTTEMPLATE+'</li>';
		if (entity.value==null || entity.value=='') {
			document.getElementById('recordval_display').style.borderColor = '#FFBABA';
			document.getElementById('entity_error_msg').innerHTML='<li>'+alert_arr.SELECT+'</li>';
		}
		return false;
	}
	if (entity.value==null || entity.value=='') {
		document.getElementById('recordval_display').style.borderColor = '#FFBABA';
		document.getElementById('entity_error_msg').innerHTML='<li>'+alert_arr.SELECTMERGE+'</li>';
		return false;
	}
}
