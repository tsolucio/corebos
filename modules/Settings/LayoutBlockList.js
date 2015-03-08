function le_editFieldExpression(fieldValueNode, fieldType) {
	editpopupobj.edit(fieldValueNode.attr('id'), fieldValueNode.val(), fieldType);
}

function check(){
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	if(val == "") {
		alert(alert_arr.BLOCK_NAME_CANNOT_BE_BLANK);
		return false;
	}
	return true;
}

function getCustomFieldList(customField)
{
	var modulename = customField.options[customField.options.selectedIndex].value;
	$('#module_info').html('{$MOD.LBL_CUSTOM_FILED_IN} "'+modulename+'" {$APP.LBL_MODULE}');
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&fld_module='+modulename+'&parenttab=Settings&ajax=true',
			onComplete: function(response) {
				$("#cfList").html(response.responseText);
			}
		}
	);
}

function changeFieldorder(what_to_do,fieldid,blockid,modulename)
{
	$('#vtbusy_info').show();
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+what_to_do+'&fieldid='+fieldid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {
				$("#cfList").html(response.responseText);
				$('#vtbusy_info').hide();
			}
		}
	);
}

function changeShowstatus(tabid,blockid,modulename)
{
	var display_status = $('#display_status_'+blockid).val();
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+display_status+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {
				$("#cfList").html(response.responseText);
			}
		}
	);
}

function changeBlockorder(what_to_do,tabid,blockid,modulename)
{
	$('#vtbusy_info').show();
		new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+what_to_do+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {
				$("#cfList").html(response.responseText);
				$('#vtbusy_info').hide();
			}
		}
	);
}

function deleteCustomField(id, fld_module, colName, uitype)
{
	if(confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)){
	$('#vtbusy_info').show();
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=deleteCustomField&ajax=true&fld_module='+fld_module+'&fld_id='+id+'&colName='+colName+'&uitype='+uitype,
				onComplete: function(response) {
					$("#cfList").html(response.responseText);
					gselected_fieldtype = '';
					$('#vtbusy_info').hide();
				}
			}
		);
	}else{
	fninvsh('editfield_'+id);
	}
}

function deleteCustomBlock(module,blockid,no){
	if(no > 0){
		alert(alert_arr.PLEASE_MOVE_THE_FIELDS_TO_ANOTHER_BLOCK);
		return false;
	}else{
		if(confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE_BLOCK)){
			$('#vtbusy_info').show();
			new Ajax.Request(
				'index.php',
				{queue : {position : 'end', scope: 'command'},
				method : 'post',
				postBody: 'module=Settings&action=SettingsAjax&fld_module='+module+'&file=LayoutBlockList&sub_mode=deleteCustomBlock&ajax=true&blockid='+blockid,
				onComplete: function(response) {
					$("#cfList").html(response.responseText);
					$('#vtbusy_info').hide();
				}
				}
			);
		}
	}
}

function getCreateCustomBlockForm(modulename,mode)
{
	var checlabel = check();
	if(checlabel == false)
		return false;
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	var blockid = document.getElementById('after_blockid').value;
	$('#vtbusy_info').show();
		new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addBlock&fld_module='+modulename+'&parenttab=Settings&ajax=true&mode='+mode+'&blocklabel='+
			encodeURIComponent(val)+'&after_blockid='+blockid,
			onComplete: function(response) {
				$('#vtbusy_info').hide();
				var str = response.responseText;
				if(str == 'ERROR'){
					alert(alert_arr.LABEL_ALREADY_EXISTS);
					return false;
				}else if(str == 'LENGTH_ERROR'){
					alert(alert_arr.LENGTH_OUT_OF_RANGE);
					return false;
				}else{
					$("#cfList").html(str);
				}
				gselected_fieldtype = '';
			}
		}
	);
}

function saveFieldInfo(fieldid,module,sub_mode,typeofdata){
	urlstring = '';
	var mandatory_check = $('#mandatory_check_'+fieldid);
	var presence_check = $('#presence_check_'+fieldid);
	var quickcreate_check = $('#quickcreate_check_'+fieldid);
	var massedit_check = $('#massedit_check_'+fieldid);
	var defaultvalue_check = $('#defaultvalue_check_'+fieldid);

	if(mandatory_check != null){
		urlstring = urlstring+'&ismandatory=' + mandatory_check.attr('checked');
	}
	if(presence_check != null){
		urlstring = urlstring + '&isPresent=' + presence_check.attr('checked');
	}
	if(quickcreate_check != null){
		urlstring = urlstring + '&quickcreate=' + quickcreate_check.attr('checked');
	}
	if(massedit_check != null){
		urlstring = urlstring + '&massedit=' + massedit_check.attr('checked');
	}
	if(defaultvalue_check != null) {
		var defaultvalueelement = document.getElementById('defaultvalue_'+fieldid);
		if(defaultvalueelement != null) {
			var defaultvalue = defaultvalueelement.value;
			if(defaultvalue_check.attr('checked') == true) {
				var typeinfo = typeofdata.split('~');
				var inputtype = typeinfo[0];
				if(inputtype == 'C') {
					defaultvalue = (defaultvalueelement.attr('checked') == true)?'1':'0';
				}
				if(validateInputData(defaultvalue, alert_arr['LBL_DEFAULT_VALUE_FOR_THIS_FIELD'], typeofdata) == false) {
					document.getElementById('defaultvalue_'+fieldid).focus();
					return false;
				}
			} else {
				defaultvalue = '';
			}
		} else {
			defaultvalue = '';
		}
		urlstring = urlstring + '&defaultvalue=' + encodeURIComponent(defaultvalue);
		if (defaultvalue!='') {
			var defaultvaluetype = document.getElementById('defaultvalue_'+fieldid+'_type');
			urlstring = urlstring + '&defaultvaluetype=' + defaultvaluetype.value;
		}
	}

	$('#vtbusy_info').show();
	new Ajax.Request(
			'index.php',
			{queue : {position: 'end',scope:'command'},
				method:'post',
				postBody:'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode='+sub_mode+'&parenttab=Settings'+
					'&fieldid='+fieldid+'&fld_module='+module+'&ajax=true'+urlstring,
				onComplete: function(response) {
					$("#cfList").html(response.responseText);
					$('#vtbusy_info').hide();
					fnvshNrm('editfield_+"fieldid"');
				}
			}
	);
}

function enableDisableCheckBox(obj, elementName) {
	var ele = $(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.checked = true;
		ele.disabled = true;
	} else {
		ele.disabled = false;
	}
}

function showHideTextBox(obj, elementName) {
	var ele = $(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.disabled = false;
	} else {
		ele.disabled = true;
	}
}

function getCreateCustomFieldForm(modulename,blockid,mode)
{
	var check = validate(blockid);
	if(check == false)
		return false;
	var type = document.getElementById("fieldType_"+blockid).value;
	var label = document.getElementById("fldLabel_"+blockid).value;
	var fldLength = document.getElementById("fldLength_"+blockid).value;
	var fldDecimal = document.getElementById("fldDecimal_"+blockid).value;
	var fldPickList = encodeURIComponent(document.getElementById("fldPickList_"+blockid).value);
	$('#__vtigerjs_dialogbox_olayer__').show();
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addCustomField&fld_module='+modulename+'&ajax=true&blockid='+blockid+'&fieldType='+type+'&fldLabel='+label+'&fldLength='+fldLength+'&fldDecimal='+fldDecimal+'&fldPickList='+fldPickList,
			onComplete: function(response) {
				$('#__vtigerjs_dialogbox_olayer__').hide();
				var str = response.responseText;
				if(str == 'ERROR'){
					alert(alert_arr.LABEL_ALREADY_EXISTS);
					return false;
				}else{
					$("#cfList").html(str);
				}
				gselected_fieldtype = '';
			}
		}
	);
}

function makeFieldSelected(oField,fieldid,blockid)
{
	if(gselected_fieldtype != '')
	{
		$(gselected_fieldtype).className = 'customMnu';
	}
	oField.className = 'customMnuSelected';
	gselected_fieldtype = oField.id;
	selFieldType(fieldid,'','',blockid);
	document.getElementById('selectedfieldtype_'+blockid).value = fieldid;
}

function show_move_hiddenfields(modulename,tabid,blockid,sub_mode){
	if(sub_mode == 'showhiddenfields'){
	var selectedfields = document.getElementById('hiddenfield_assignid_'+blockid);
	var selectedids_str = '';
	for(var i=0; i<selectedfields.length; i++) {
		if (selectedfields[i].selected == true) {
			selectedids_str = selectedids_str + selectedfields[i].value + ":";
		}
	}
	}else{
		var selectedfields = document.getElementById('movefield_assignid_'+blockid);
		var selectedids_str = '';
		for(var i=0; i<selectedfields.length; i++) {
			if (selectedfields[i].selected == true) {
				selectedids_str = selectedids_str + selectedfields[i].value + ":";
			}
		}
	}
	$('#vtbusy_info').show();
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode='+sub_mode+'&fld_module='+modulename+'&parenttab=Settings&ajax=true&tabid='+tabid+'&blockid='+blockid+'&selected='+selectedids_str,
			onComplete: function(response) {
				$("#cfList").html(response.responseText);
				$('#vtbusy_info').hide();
				}
			}
		);
}

function changeRelatedListorder(what_to_do,tabid,sequence,id,module)
{
	$('#vtbusy_info').show();
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeRelatedInfoOrder&sequence='+sequence+'&fld_module='+module+'&parenttab=Settings&what_to_do='+what_to_do+'&tabid='+tabid+'&id='+id+'&ajax=true',
			onComplete: function(response) {
			$("#relatedlistdiv").html(response.responseText);
			$('#vtbusy_info').hide();
			}
		}
	);
}

function callRelatedList(module){
	$('#vtbusy_info').show();
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=getRelatedInfoOrder&parenttab=Settings&formodule='+module+'&ajax=true',
			onComplete: function(response) {
			$("#relatedlistdiv").html(response.responseText);
			fnvshNrm('relatedlistdiv');
			$('#vtbusy_info').hide();
			}
		}
	);
}

function showProperties(field,man,pres,quickc,massed){
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