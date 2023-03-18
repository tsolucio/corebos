function check() {
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	if (val == '') {
		alert(alert_arr.BLOCK_NAME_CANNOT_BE_BLANK);
		return false;
	}
	return true;
}

function getCustomFieldList(customField) {
	var modulename = customField.options[customField.options.selectedIndex].value;
	document.getElementById('module_info').innerHTML = `${LBL_CUSTOM_FILED_IN} ${modulename} ${LBL_MODULE}`;
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&fld_module=${modulename}&ajax=true`
	}).done(function(response) {
		document.getElementById('cfList').innerHTML=response;
	});
}

function changeFieldorder(what_to_do,fieldid,blockid,modulename) {
	VtigerJS_DialogBox.showbusy();
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module=${modulename}&what_to_do=${what_to_do}&fieldid=${fieldid}&blockid=${blockid}&ajax=true`
	}).done(function(response) {
		document.getElementById('cfList').innerHTML=response;
		VtigerJS_DialogBox.hidebusy();
	});
}

function changeShowstatus(tabid,blockid,modulename) {
	var display_status = document.getElementById(`display_status_${blockid}`).value;
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module=${modulename}&what_to_do=${display_status}&tabid=${tabid}&blockid=${blockid}&ajax=true`
	}).done(function(response) {
		document.getElementById('cfList').innerHTML=response;
	});
}

function changeBlockorder(what_to_do,tabid,blockid,modulename) {
	VtigerJS_DialogBox.showbusy();
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module=${modulename}&what_to_do=${what_to_do}&tabid=${tabid}&blockid=${blockid}&ajax=true`
	}).done(function(response) {
		document.getElementById('cfList').innerHTML=response;
		VtigerJS_DialogBox.hidebusy();
	});
}

function deleteCustomField(id, fld_module, colName, uitype) {
	if (confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)) {
		VtigerJS_DialogBox.showbusy();
		jQuery.ajax({
			method: 'POST',
			url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=deleteCustomField&ajax=true&fld_module=${fld_module}&fld_id=${id}&colName=${colName}&uitype=${uitype}`
		}).done(function(response) {
			document.getElementById('cfList').innerHTML=response;
			gselected_fieldtype = '';
			VtigerJS_DialogBox.hidebusy();
		});
	} else {
		fninvsh(`editfield_${id}`);
	}
}

function deleteCustomBlock(module, blockid,no) {
	if (no > 0) {
		alert(alert_arr.PLEASE_MOVE_THE_FIELDS_TO_ANOTHER_BLOCK);
		return false;
	} else {
		if (confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE_BLOCK)) {
			VtigerJS_DialogBox.showbusy();
			jQuery.ajax({
				method: 'POST',
				url: `index.php?module=Settings&action=SettingsAjax&fld_module=${module}&file=LayoutBlockList&sub_mode=deleteCustomBlock&ajax=true&blockid=${blockid}`
			}).done(function(response) {
				document.getElementById('cfList').innerHTML=response;
				VtigerJS_DialogBox.hidebusy();
			});
		}
	}
}

function getCreateCustomBlockForm(modulename,mode) {
	var checlabel = check();
	if (checlabel == false) {
		return false;
	}
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	var blockid = document.getElementById('after_blockid').value;
	var relblock = document.getElementById('relatedlistblock').value;
	VtigerJS_DialogBox.showbusy();
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addBlock&fld_module=${modulename}&ajax=true&mode=${mode}&blocklabel=${encodeURIComponent(val)}&after_blockid=${blockid}&relblock=${relblock}`
	}).done(function(response) {
		VtigerJS_DialogBox.hidebusy();
		var str = response;
		if (str == 'ERROR') {
			alert(alert_arr.LABEL_ALREADY_EXISTS);
			return false;
		} else if(str == 'LENGTH_ERROR') {
			alert(alert_arr.LENGTH_OUT_OF_RANGE);
			return false;
		} else {
			document.getElementById('cfList').innerHTML = str;
		}
		gselected_fieldtype = '';
	});
}

function saveFieldInfo(fieldid,module,sub_mode,typeofdata,uitype) {
	urlstring = '';
	var mandatory_check = document.getElementById(`mandatory_check_${fieldid}`);
	var presence_check = document.getElementById(`presence_check_${fieldid}`);
	var quickcreate_check = document.getElementById(`quickcreate_check_${fieldid}`);
	var massedit_check = document.getElementById(`massedit_check_${fieldid}`);
	var defaultvalue_check = document.getElementById(`defaultvalue_check_${fieldid}`);
	var longfield_check = document.getElementById(`longfield_check_${fieldid}`);

	if(mandatory_check != null){
		urlstring = `${urlstring}&ismandatory=${mandatory_check.checked}`;
	}
	if(presence_check != null){
		urlstring = `${urlstring}&isPresent=${presence_check.checked}`;
	}
	if(quickcreate_check != null){
		urlstring = `${urlstring}&quickcreate=${quickcreate_check.checked}`;
	}
	if(massedit_check != null){
		urlstring = `${urlstring}&massedit=${massedit_check.checked}`;
	}
	if(longfield_check != null){
		urlstring = `${urlstring}&longfield=${longfield_check.checked}`;
	}
	if(defaultvalue_check != null) {
		var defaultvalueelement = document.getElementById(`defaultvalue_${fieldid}`);
		if(defaultvalueelement != null) {
			var defaultvalue = defaultvalueelement.value;
			if(defaultvalue_check.checked == true) {
				var typeinfo = typeofdata.split('~');
				var inputtype = typeinfo[0];
				if(inputtype == 'C') {
					defaultvalue = (defaultvalueelement.checked == true)?'1':'0';
				}
				if(validateInputData(defaultvalue, alert_arr['LBL_DEFAULT_VALUE_FOR_THIS_FIELD'], typeofdata) == false) {
					document.getElementById(`defaultvalue_${fieldid}`).focus();
					return false;
				}
			} else {
				defaultvalue = '';
			}
		} else {
			defaultvalue = '';
		}
		urlstring = `${urlstring}&defaultvalue=${encodeURIComponent(defaultvalue)}`;
	}
	if (document.getElementById(`dependent_list_${fieldid}`) && document.getElementById(`dependent_list_${fieldid}`).length > 0) {
		let dependentlistselectElement = document.getElementById(`dependent_list_${fieldid}`);
		let dependentlistselectedValues = Array.from(dependentlistselectElement.selectedOptions).map(option => option.value);
		urlstring = `${urlstring}&dependentmoduleselected=${encodeURIComponent(dependentlistselectedValues)}`;
	}
	VtigerJS_DialogBox.showbusy();
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=${sub_mode}&fieldid=${fieldid}&fld_module=${module}&uitype=${uitype}&ajax=true${urlstring}`
	}).done(function(response) {
		fninvsh(`editfield_${fieldid}`);
		document.getElementById('cfList').innerHTML=response;
		VtigerJS_DialogBox.hidebusy();
	});
}

function enableDisableCheckBox(obj, elementName) {
	var ele = document.getElementById(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.checked = true;
		ele.disabled = true;
	} else {
		ele.disabled = false;
	}
}

function showHideTextBox(obj, elementName) {
	var ele = document.getElementById(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.disabled = false;
	} else {
		ele.disabled = true;
	}
}

function getCreateCustomFieldForm(modulename,blockid,mode) {
	var check = validate(blockid);
	if (check == false) {
		return false;
	}
	var type = document.getElementById(`fieldType_${blockid}`).value;
	var label = document.getElementById(`fldLabel_${blockid}`).value;
	var fldLength = document.getElementById(`fldLength_${blockid}`).value;
	var fldDecimal = document.getElementById(`fldDecimal_${blockid}`).value;
	var fldPickList = encodeURIComponent(document.getElementById(`fldPickList_${blockid}`).value);
	var selrelationmodules=document.getElementById(`fldRelMods_${blockid}`).selectedOptions;
	var relationmodules='';
	for (var mods=0, mod;mod=selrelationmodules[mods];mods++) {
		relationmodules=relationmodules+mod.value+';'
	}
	var relationmodules = encodeURIComponent(relationmodules);
	VtigerJS_DialogBox.block();
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addCustomField&fld_module=${modulename}&ajax=true&blockid=${blockid}&fieldType=${type}&fldLabel=${label}&fldLength=${fldLength}&fldDecimal=${fldDecimal}&fldPickList=${fldPickList}&relationmodules=${relationmodules}`,
	}).done(function(response) {
		VtigerJS_DialogBox.unblock();
		var str = response;
		if (str == 'ERROR') {
			alert(alert_arr.LABEL_ALREADY_EXISTS);
			return false;
		} else if (str.indexOf('ERROR::') > -1) {
			var msg = str.split('ERROR::');
			alert(msg[1]);
			return false;
		} else {
			document.getElementById('cfList').innerHTML = str;
		}
		gselected_fieldtype = '';
	});
}

function makeFieldSelected(oField,fieldid,blockid) {
	if(gselected_fieldtype != '')
	{
		document.getElementById(gselected_fieldtype).className = 'customMnu';
	}
	oField.className = 'customMnuSelected';
	gselected_fieldtype = oField.id;
	selFieldType(fieldid, '', '', blockid);
	document.getElementById(`selectedfieldtype_${blockid}`).value = fieldid;
}

function show_move_hiddenfields(modulename,tabid,blockid,sub_mode) {
	if(sub_mode == 'showhiddenfields') {
		var selectedfields = document.getElementById(`hiddenfield_assignid_${blockid}`);
		var selectedids_str = '';
		for(var i=0; i<selectedfields.length; i++) {
			if (selectedfields[i].selected == true) {
				selectedids_str = selectedids_str + selectedfields[i].value + ':';
			}
		}
	} else {
		var selectedfields = document.getElementById(`movefield_assignid_${blockid}`);
		var selectedids_str = '';
		for (var i=0; i<selectedfields.length; i++) {
			if (selectedfields[i].selected == true) {
				selectedids_str = selectedids_str + selectedfields[i].value + ':';
			}
		}
	}
	VtigerJS_DialogBox.showbusy();
	jQuery.ajax({
		method: 'POST',
		url: `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=${sub_mode}&fld_module=${modulename}&ajax=true&tabid=${tabid}&blockid=${blockid}&selected=${selectedids_str}`,
	}).done(function(response) {
		document.getElementById('cfList').innerHTML=response;
		VtigerJS_DialogBox.hidebusy();
	});
}

function changeRelatedListorder(what_to_do,tabid,sequence,id,module) {
	VtigerJS_DialogBox.showbusy();
	let url = `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeRelatedInfoOrder&sequence=${sequence}&fld_module=${module}&what_to_do=${what_to_do}&tabid=${tabid}&id=${id}&ajax=true`;
	$('#global-modal-container__content').load(url, function() {
		VtigerJS_DialogBox.hidebusy();
	});
}

function deleteRelatedList(tabid,sequence,id,module) {
	VtigerJS_DialogBox.showbusy();
	let url = `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=deleteRelatedList&sequence=${sequence}&fld_module=${module}&tabid=${tabid}&id=${id}&ajax=true`;
	$('#global-modal-container__content').load(url, function() {
		VtigerJS_DialogBox.hidebusy();
	});
}

function createRelatedList(module) {
	VtigerJS_DialogBox.showbusy();
	var relmodpl = document.getElementById('relatewithmodule');
	var rllabel = document.getElementById('rllabel').value;
	var relation = document.getElementById('relation').value;
	var relmod = relmodpl.options[relmodpl.selectedIndex].value;
	let url = `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=createRelatedList&fld_module=${module}&rllabel=${encodeURIComponent(rllabel)}&relation=${relation}&relwithmod=${relmod}&ajax=true`;
	$('#global-modal-container__content').load(url, function() {
		VtigerJS_DialogBox.hidebusy();
	});
}

function callRelatedList(module) {
	VtigerJS_DialogBox.showbusy();
	let url = `index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=getRelatedInfoOrder&formodule=${module}&ajax=true`;
	ldsModal.show(alert_arr.RelatedList, '', 'small');
	$('#global-modal-container__content').load(url, function() {
		VtigerJS_DialogBox.hidebusy();
	});
}

function showProperties(field,man,pres,quickc,massed) {
	var str='<table class="small" cellpadding="2" cellspacing="0" border="0"><tr><th>'+field+'</th></tr>';
	if (man == 0 || man == 2)
		str = str+'<tr><td>'+alert_arr.FIELD_IS_MANDATORY+'</td></tr>';
	if (pres == 0 || pres == 2)
		str = str+'<tr><td>'+alert_arr.FIELD_IS_ACTIVE+'</td></tr>';
	if (quickc == 0 || quickc == 2)
		str = str+'<tr><td>'+alert_arr.FIELD_IN_QCREATE+'</td></tr>';
	if(massed == 0 || massed == 1)
		str = str+'<tr><td>'+alert_arr.FIELD_IS_MASSEDITABLE+'</td></tr>';
	str = str + '</table>';
	return str;
}

var gselected_fieldtype = '';