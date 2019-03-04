/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
var Imagid_array = ['img_2', 'img_4', 'img_6', 'img_7', 'img_8', 'img_9', 'img_10', 'img_13', 'img_14', 'img_18', 'img_19', 'img_20', 'img_21', 'img_22', 'img_23', 'img_26'];

function fnToggleVIew(obj) {
	obj = '#'+obj;
	if (jQuery(obj).hasClass('hideTable')) {
		jQuery(obj).removeClass('hideTable');
	} else {
		jQuery(obj).addClass('hideTable');
	}
}

function invokeview_all() {
	if (document.getElementById('view_all_chk').checked == true) {
		for (var i = 0; i < document.profileform.elements.length; i++) {
			if (document.profileform.elements[i].type == 'checkbox') {
				if (document.profileform.elements[i].id.indexOf('tab_chk_com_') != -1 || document.profileform.elements[i].id.indexOf('tab_chk_4') != -1 || document.profileform.elements[i].id.indexOf('_field_') != -1) {
					document.profileform.elements[i].checked = true;
				}
			}
		}
		showAllImages();
	}
}

function showAllImages() {
	for (var j=0; j < Imagid_array.length; j++) {
		if (typeof(document.getElementById(Imagid_array[j])) != 'undefined') {
			document.getElementById(Imagid_array[j]).style.display = 'block';
		}
	}
}

function invokeedit_all() {
	if (document.getElementById('edit_all_chk').checked == true) {
		document.getElementById('view_all_chk').checked = true;
		for (var i = 0; i < document.profileform.elements.length; i++) {
			if (document.profileform.elements[i].type == 'checkbox') {
				if (document.profileform.elements[i].id.indexOf('tab_chk_com_') != -1 || document.profileform.elements[i].id.indexOf('tab_chk_4') != -1 || document.profileform.elements[i].id.indexOf('tab_chk_1') != -1 || document.profileform.elements[i].id.indexOf('_field_') != -1) {
					document.profileform.elements[i].checked = true;
				}
			}
		}
		showAllImages();
	}
}

function unselect_edit_all() {
	document.getElementById('edit_all_chk').checked = false;
}

function unselect_view_all() {
	document.getElementById('view_all_chk').checked = false;
}

function unSelectView(id) {
	var createid = 'tab_chk_1_'+id;
	var deleteid = 'tab_chk_2_'+id;
	var tab_id = 'tab_chk_com_'+id;
	if (document.getElementById('tab_chk_4_'+id).checked == false) {
		unselect_view_all();
		unselect_edit_all();
		document.getElementById(createid).checked = false;
		document.getElementById(deleteid).checked = false;
		document.getElementById(tab_id).checked = false;
	} else {
		var imageid = 'img_'+id;
		if (typeof(document.getElementById(imageid)) != 'undefined') {
			document.getElementById(imageid).style.display = 'block';
		}
		document.getElementById('tab_chk_com_'+id).checked = true;
	}
}
function unSelectCreate(id) {
	var viewid = 'tab_chk_4_'+id;
	if (document.getElementById('tab_chk_1_'+id).checked == false) {
		unselect_edit_all();
	} else {
		var imageid = 'img_'+id;
		viewid = 'tab_chk_4_'+id;
		if (typeof(document.getElementById(imageid)) != 'undefined') {
			document.getElementById(imageid).style.display = 'block';
		}
		document.getElementById('tab_chk_com_'+id).checked = true;
		document.getElementById(viewid).checked = true;
	}
}
function unSelectDelete(id) {
	if (document.getElementById('tab_chk_2_'+id).checked != false) {
		var imageid = 'img_'+id;
		var viewid = 'tab_chk_4_'+id;
		if (typeof(document.getElementById(imageid)) != 'undefined') {
			document.getElementById(imageid).style.display = 'block';
		}
		document.getElementById('tab_chk_com_'+id).checked = true;
		document.getElementById(viewid).checked = true;
	}
}

function hideTab(id) {
	var createid = 'tab_chk_1_'+id;
	var viewid = 'tab_chk_4_'+id;
	var deleteid = 'tab_chk_2_'+id;
	var imageid = 'img_'+id;
	var contid = id+'_view';
	if (document.getElementById('tab_chk_com_'+id).checked == false) {
		unselect_view_all();
		unselect_edit_all();
		if (typeof(document.getElementById(imageid)) != 'undefined') {
			document.getElementById(imageid).style.display = 'none';
		}
		document.getElementById(contid).className = 'hideTable';
		if (typeof(document.getElementById(createid)) != 'undefined') {
			document.getElementById(createid).checked = false;
		}
		if (typeof(document.getElementById(deleteid)) != 'undefined') {
			document.getElementById(deleteid).checked = false;
		}
		if (typeof(document.getElementById(viewid)) != 'undefined') {
			document.getElementById(viewid).checked = false;
		}
	} else {
		if (typeof(document.getElementById(imageid)) != 'undefined') {
			document.getElementById(imageid).style.display = 'block';
		}
		if (typeof(document.getElementById(createid)) != 'undefined') {
			document.getElementById(createid).checked = true;
		}
		if (typeof(document.getElementById(deleteid)) != 'undefined') {
			document.getElementById(deleteid).checked = true;
		}
		if (typeof(document.getElementById(viewid)) != 'undefined') {
			document.getElementById(viewid).checked = true;
		}
		var fieldid = id +'_field_';
		for (var i = 0; i < document.profileform.elements.length; i++) {
			if (document.profileform.elements[i].type == 'checkbox' && document.profileform.elements[i].id.indexOf(fieldid) != -1) {
				document.profileform.elements[i].checked = true;
			}
		}
	}
}
function selectUnselect(oCheckbox) {
	if (oCheckbox.checked == false) {
		unselect_view_all();
		unselect_edit_all();
	}
}
function initialiseprofile() {
	var module_array = Array(1, 2, 4, 6, 7, 8, 9, 10, 13, 14, 15, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27);
	for (var i=0; i < module_array.length; i++) {
		hideTab(module_array[i]);
	}
}
//initialiseprofile();

function toogleAccess(elementId) {
	var element = document.getElementById(elementId);
	if (element == null || typeof(element) == 'undefined') {
		return;
	}

	if (element.value == 0) {
		element.value = 1;
	} else {
		element.value = 0;
	}

	var lockedImage = document.getElementById(elementId+'_locked');
	if (lockedImage != null && typeof(lockedImage) != 'undefined') {
		if (lockedImage.style.display == 'none') {
			lockedImage.style.display = 'inline';
		} else {
			lockedImage.style.display = 'none';
		}
	}

	var unlockedImage = document.getElementById(elementId+'_unlocked');
	if (unlockedImage != null && typeof(unlockedImage) != 'undefined') {
		if (unlockedImage.style.display == 'none') {
			unlockedImage.style.display = 'inline';
		} else {
			unlockedImage.style.display = 'none';
		}
	}
}

function tooglePosition(elementId) {
	var element = $('#' + elementId);
	if (element == null || typeof(element) == 'undefined') {
		return;
	}

	var orderedPositions = ['T', 'H', 'B', 'N'];
	var actualPosition = element.val();
	var actualIndex = orderedPositions.indexOf(actualPosition);

	if (actualIndex < 0) {
		return;
	}

	var nextIndex;
	if (actualIndex === (orderedPositions.length - 1)) {
		nextIndex = 0;
	} else {
		nextIndex = actualIndex + 1;
	}

	var nextPosition = orderedPositions[nextIndex];
	$('.' + elementId + 'position_image').css({'display': 'none'});

	if (nextPosition === 'T') {
		$('#' + elementId + '_position_title').css({'display': 'inline'});
	} else if (nextPosition === 'H') {
		$('#' + elementId + '_position_header').css({'display': 'inline'});
	} else if (nextPosition === 'B') {
		$('#' + elementId + '_position_body').css({'display': 'inline'});
	} else {
		$('#' + elementId + '_position_no_show').css({'display': 'inline'});
	}

	element.val(nextPosition);
}

function saveprofile(frm) {
	if (frm == 'create') {
		var file = 'SaveProfile';
	} else {
		file = 'UpdateProfileChanges';
	}

	var mode = document.getElementsByName('mode').item(0).value;
	var profileid = document.getElementsByName('profileid').item(0).value;
	var profile_name = document.getElementsByName('profile_name').item(0).value;
	var profile_description = document.getElementsByName('profile_description').item(0).value;
	var parent_profile = document.getElementsByName('parent_profile').item(0).value;
	var radio_button = document.getElementsByName('radio_button').item(0).value;
	var return_action = document.getElementsByName('return_action').item(0).value;
	if (document.getElementsByName('view_all').item(0).checked == true) {
		var viewall = 'on';
	} else {
		viewall = 'off';
	}
	if (document.getElementsByName('edit_all').item(0).checked == true) {
		var editall = 'on';
	} else {
		editall = 'off';
	}
	var view_all = viewall;
	var edit_all = editall;

	var sentForm = {};

	for (var i = 0; i < document.profileform.elements.length; i++) {
		if (document.profileform.elements[i].type == 'checkbox') {
			if (document.profileform.elements[i].id.indexOf('tab_chk_com_')!=-1) {
				var split1 = document.profileform.elements[i].id.split('_');
				var fieldname = split1[3]+'_tab';
			}
			if (document.profileform.elements[i].id.indexOf('tab_chk_1_')!=-1) {
				var split2 = document.profileform.elements[i].id.split('_');
				fieldname = split2[3]+'_EditView';
			}
			if (document.profileform.elements[i].id.indexOf('tab_chk_4_')!=-1) {
				var split3 = document.profileform.elements[i].id.split('_');
				fieldname = split3[3]+'_DetailView';
			}
			if (document.profileform.elements[i].id.indexOf('tab_chk_2_')!=-1) {
				var split4 = document.profileform.elements[i].id.split('_');
				fieldname = split4[3]+'_Delete';
			}
			if (document.profileform.elements[i].id.indexOf('field_util_7')!=-1) {
				var split5 = document.profileform.elements[i].id.split('_');
				fieldname = split5[0]+'_CreateView';
			}
			if (document.profileform.elements[i].id.indexOf('_field_')!=-1 && document.profileform.elements[i].id.indexOf('field_util')==-1) {
				var split6 = document.profileform.elements[i].id.split('_');
				fieldname = split6[2];
			}
			if (document.profileform.elements[i].id.indexOf('field_util_5')!=-1) {
				var split7 = document.profileform.elements[i].id.split('_');
				fieldname = split7[0]+'_Import';
			}
			if (document.profileform.elements[i].id.indexOf('field_util_6')!=-1) {
				var split8 = document.profileform.elements[i].id.split('_');
				fieldname = split8[0]+'_Export';
			}
			if (document.profileform.elements[i].id.indexOf('field_util_8')!=-1) {
				var split9 = document.profileform.elements[i].id.split('_');
				fieldname = split9[0]+'_Merge';
			}
			if (document.profileform.elements[i].id.indexOf('field_util_9')!=-1) {
				var split10 = document.profileform.elements[i].id.split('_');
				fieldname = split10[0]+'_ConvertLead';
			}
			if (document.profileform.elements[i].id.indexOf('field_util_10')!=-1) {
				var split11 = document.profileform.elements[i].id.split('_');
				fieldname = split11[0]+'_DuplicatesHandling';
			}

			if (document.getElementsByName(fieldname).item(0)!=null) {
				if (document.getElementsByName(fieldname).item(0).checked == true) {
					var checked = 'on';
				} else {
					checked = 'off';
				}
				sentForm[fieldname] = checked;
			}
		}
		if (document.profileform.elements[i].type == 'hidden' && document.profileform.elements[i].name.indexOf('_readonly')!=-1) {
			sentForm[document.profileform.elements[i].name] = document.profileform.elements[i].value;
		}
		if (document.profileform.elements[i].type == 'hidden' && document.profileform.elements[i].name.indexOf('_position')!=-1) {
			sentForm[document.profileform.elements[i].name] = document.profileform.elements[i].value;
		}
	}
	VtigerJS_DialogBox.block();
	jQuery.ajax({
		type : 'post',
		data : {'sentvariables':JSON.stringify(sentForm)},
		url : 'index.php?module=Users&action=UsersAjax&file='+file+'&mode='+mode+'&profileid='+profileid+'&profile_name='+profile_name+'&profile_description='+profile_description+'&parent_profile='+parent_profile+'&radio_button='+radio_button+'&return_action='+return_action+'&edit_all='+edit_all+'&view_all='+view_all
	}).done(function (msg) {
		window.location.href = msg;
	}).fail(function () {
		alert('Error with AJAX');
		VtigerJS_DialogBox.unblock();
	});
}
