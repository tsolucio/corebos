/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function set_return(user_id, user_name) {
	window.opener.document.EditView.reports_to_name.value = user_name;
	window.opener.document.EditView.reports_to_id.value = user_id;
}

var fckEditoFrame = '';
function updateWidgetWindow() {
	var selObj = document.getElementById('heading');
	var selIndex = selObj.selectedIndex;

	if (selObj.options[selIndex].value == 'Company-Logo') {
		document.forms['addWidget'].headingNew.disabled = document.forms['addWidget'].announcement.disabled = document.forms['addWidget'].content.disabled = document.getElementById('content___Config').disabled = true;
		fckEditoFrame = document.getElementById('content___Frame').src;
		document.getElementById('content___Frame').height=20;
		document.getElementById('content___Frame').src='';
		document.getElementById('message').style.display='block';
	} else {
		document.forms['addWidget'].headingNew.disabled = document.forms['addWidget'].announcement.disabled = document.forms['addWidget'].content.disabled = document.getElementById('content___Config').disabled = false;
		document.getElementById('content___Frame').height=370;
		document.getElementById('content___Frame').src=fckEditoFrame;
		if (selObj.options[selIndex].value!= 'NONE') {
			window.location.href = 'index.php?module=Users&action=UsersAjax&file=AWWidget&contentHeading='+selObj.options[selIndex].value;
		} else {
			//document.getElementById('heading').setProperty("disable", "false");
		}
	}
	//document.forms["addWidget"].headingNew.disabled=document.forms["addWidget"].announcement.disabled=document.forms["addWidget"].content.disabled=false;
}

function saveWidget() {
	if (document.getElementById('heading').value=='Company-Logo') {
		if (!document.getElementById('content_image').value) {
			alert('Please upload an image file for company-logo.');
			return false;
		}
	}
}

function updateWidgets(activity) {
	var submitStr = '';
	if (activity=='saveWidgets' && document.forms['WidgetsEditView']) {
		for (var i=0; i<document.forms['WidgetsEditView'].elements.length; i++) {
			if (document.forms['WidgetsEditView'].elements[i].type == 'checkbox') {
				if (document.forms['WidgetsEditView'].elements[i].checked) {
					submitStr += (submitStr!='' ? '&' : '')+document.forms['WidgetsEditView'].elements[i].name+'=on';
				} else {
					submitStr += (submitStr!='' ? '&' : '')+document.forms['WidgetsEditView'].elements[i].name+'=off';
				}
			} else {
				submitStr += (submitStr!='' ? '&' : '')+document.forms['WidgetsEditView'].elements[i].name+'='+document.forms['WidgetsEditView'].elements[i].value;
			}
		}
	}

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?module=Users&action=UsersAjax&activity=' + activity + '&' + (submitStr?submitStr:'')
	}).done(function (response) {
		document.getElementById('widgets').style.display.block = 'block';
		document.getElementById('rView').style.display = (activity == 'EditWidgets' ? 'none' : 'block');
		document.getElementById('eView').style.display = (activity == 'EditWidgets' ? 'block' : 'none');
		document.getElementById('widgets').innerHTML = '';
		var newdiv = document.createElement('div');
		newdiv.innerHTML = response;
		var container = document.getElementById('widgets');
		container.appendChild(newdiv);
	//		document.getElementById("widgets").innerHTML = response.responseText;
	});
	return true;
}

var state = 'none';
var state2 = 'none';

function showhide(layer_ref) {
	if (state == 'block') {
		state = 'none';
		state2 = 'block';
		document.getElementById(layer_ref).innerHTML = '+';
	} else {
		state = 'block';
		state2 = 'none';
		document.getElementById(layer_ref).innerHTML = '-';
	}

	if (document.layers) { //IS NETSCAPE 4 or below
		document.layers['1_1'+layer_ref].display = state;
		document.layers['1_2'+layer_ref].display = state;
		document.layers['1_3'+layer_ref].display = state;
		document.layers['0_1'+layer_ref].display = state2;
		document.layers['0_2'+layer_ref].display = state2;
		document.layers['0_3'+layer_ref].display = state2;
	}

	if (document.getElementById) {
		var hza = document.getElementById('1_1'+layer_ref);
		hza.style.display = state;
		hza = document.getElementById('1_2'+layer_ref);
		hza.style.display = state;
		hza = document.getElementById('1_3'+layer_ref);
		hza.style.display = state;
		hza = document.getElementById('0_1'+layer_ref);
		hza.style.display = state2;
		hza = document.getElementById('0_2'+layer_ref);
		hza.style.display = state2;
		hza = document.getElementById('0_3'+layer_ref);
		hza.style.display = state2;
	}
}

function closeWindow() {
	window.close(true);
	opener.location.reload();
}

function manageWidgets(id, check) {
	var str = '';
	var checkBox = document.getElementById('VallWidgets_'+id);
	if (checkBox.checked == true) {
		str = 'module=Users&action=UsersAjax&activity=widgetUpdate&id='+id+'&update=0&type=delete';
	} else {
		str = 'module=Users&action=UsersAjax&activity=widgetUpdate&id='+id+'&update=1&type=delete';
	}

	jQuery.ajax({
		method: 'POST',
		url: 'index.php?' + str
	}).done(function (response) {
		updateWidgets('RefreshWidgetsrView');
	});
}