{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/ *}
<script type="text/javascript" src="include/js/customview.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<script>
{literal}
function check(){
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	if (val == "") {
		alert(alert_arr.BLOCK_NAME_CANNOT_BE_BLANK);
		return false;
	}
	return true;
}
{/literal}
</script>

<script>

function getCustomFieldList(customField)
{ldelim}
	var modulename = customField.options[customField.options.selectedIndex].value;
	document.getElementById('module_info').innerHTML = '{$MOD.LBL_CUSTOM_FILED_IN} "'+modulename+'" {$APP.LBL_MODULE}';
	jQuery.ajax({ldelim}
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&fld_module='+modulename+'&parenttab=Settings&ajax=true'
	{rdelim}).done(function(response) {ldelim}
		document.getElementById("cfList").innerHTML=response;
	{rdelim});
{rdelim}

function changeFieldorder(what_to_do,fieldid,blockid,modulename)
{ldelim}
	document.getElementById('vtbusy_info').style.display = "block";
	jQuery.ajax({ldelim}
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+what_to_do+'&fieldid='+fieldid+'&blockid='+blockid+'&ajax=true'
	{rdelim}).done(function(response) {ldelim}
		document.getElementById("cfList").innerHTML=response;
		document.getElementById('vtbusy_info').style.display = "none";
	{rdelim});
{rdelim}

function changeShowstatus(tabid,blockid,modulename)
{ldelim}
	var display_status = document.getElementById('display_status_'+blockid).value;
	jQuery.ajax({ldelim}
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+display_status+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true'
	{rdelim}).done(function(response) {ldelim}
		console.log('test: ' + response);
		document.getElementById("cfList").innerHTML=response;
	{rdelim});
{rdelim}

function changeBlockorder(what_to_do,tabid,blockid,modulename)
{ldelim}
	document.getElementById('vtbusy_info').style.display = "block";
	jQuery.ajax({ldelim}
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+what_to_do+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true'
	{rdelim}).done(function(response) {ldelim}
		document.getElementById("cfList").innerHTML=response;
		document.getElementById('vtbusy_info').style.display = "none";
	{rdelim});
{rdelim}

{literal}
function deleteCustomField(id, fld_module, colName, uitype)
{
	if(confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)){
		document.getElementById('vtbusy_info').style.display = "block";
			jQuery.ajax({
				method:"POST",
				url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=deleteCustomField&ajax=true&fld_module='+fld_module+'&fld_id='+id+'&colName='+colName+'&uitype='+uitype
			}).done(function(response) {
				document.getElementById("cfList").innerHTML=response;
				gselected_fieldtype = '';
				document.getElementById('vtbusy_info').style.display = "none";
			});
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
			document.getElementById('vtbusy_info').style.display = "block";
			jQuery.ajax({
				method:"POST",
				url:'index.php?module=Settings&action=SettingsAjax&fld_module='+module+'&file=LayoutBlockList&sub_mode=deleteCustomBlock&ajax=true&blockid='+blockid
			}).done(function(response) {
				document.getElementById("cfList").innerHTML=response;
				document.getElementById('vtbusy_info').style.display = "none";
			});
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
	var relblock = document.getElementById('relatedlistblock').value;
	document.getElementById('vtbusy_info').style.display = "block";
		jQuery.ajax({
			method:"POST",
			url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addBlock&fld_module='+modulename+'&parenttab=Settings&ajax=true&mode='+mode+'&blocklabel='+
			encodeURIComponent(val)+'&after_blockid='+blockid+'&relblock='+relblock
		}).done(function(response) {
			document.getElementById('vtbusy_info').style.display = "none";
			var str = response;
			if (str == 'ERROR') {
				alert(alert_arr.LABEL_ALREADY_EXISTS);
				return false;
			} else if(str == 'LENGTH_ERROR') {
				alert(alert_arr.LENGTH_OUT_OF_RANGE);
				return false;
			} else {
				document.getElementById("cfList").innerHTML=str;
			}
			gselected_fieldtype = '';
		});
}

function saveFieldInfo(fieldid,module,sub_mode,typeofdata){
	urlstring = '';
	var mandatory_check = document.getElementById('mandatory_check_'+fieldid);
	var presence_check = document.getElementById('presence_check_'+fieldid);
	var quickcreate_check = document.getElementById('quickcreate_check_'+fieldid);
	var massedit_check = document.getElementById('massedit_check_'+fieldid);
	var defaultvalue_check = document.getElementById('defaultvalue_check_'+fieldid);

	if(mandatory_check != null){
		urlstring = urlstring+'&ismandatory=' + mandatory_check.checked;
	}
	if(presence_check != null){
		urlstring = urlstring + '&isPresent=' + presence_check.checked;
	}
	if(quickcreate_check != null){
		urlstring = urlstring + '&quickcreate=' + quickcreate_check.checked;
	}
	if(massedit_check != null){
		urlstring = urlstring + '&massedit=' + massedit_check.checked;
	}
	if(defaultvalue_check != null) {
		var defaultvalueelement = document.getElementById('defaultvalue_'+fieldid);
		if(defaultvalueelement != null) {
			var defaultvalue = defaultvalueelement.value;
			if(defaultvalue_check.checked == true) {
				var typeinfo = typeofdata.split('~');
				var inputtype = typeinfo[0];
				if(inputtype == 'C') {
					defaultvalue = (defaultvalueelement.checked == true)?'1':'0';
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
	}

	document.getElementById('vtbusy_info').style.display = "block";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode='+sub_mode+'&parenttab=Settings'+
			'&fieldid='+fieldid+'&fld_module='+module+'&ajax=true'+urlstring
	}).done(function(response) {
		fninvsh('editfield_'+fieldid);
		document.getElementById("cfList").innerHTML=response;
		document.getElementById('vtbusy_info').style.display = "none";
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
	if(check == false)
		return false;
	var type = document.getElementById("fieldType_"+blockid).value;
	var label = document.getElementById("fldLabel_"+blockid).value;
	var fldLength = document.getElementById("fldLength_"+blockid).value;
	var fldDecimal = document.getElementById("fldDecimal_"+blockid).value;
	var fldPickList = encodeURIComponent(document.getElementById("fldPickList_"+blockid).value);
	var selrelationmodules=document.getElementById("fldRelMods_"+blockid).selectedOptions;
	var relationmodules='';
	for (var mods=0, mod;mod=selrelationmodules[mods];mods++) {
		relationmodules=relationmodules+mod.value+';'
	}
	var relationmodules=encodeURIComponent(relationmodules);
	VtigerJS_DialogBox.block();
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addCustomField&fld_module='+modulename+'&ajax=true&blockid='+blockid+'&fieldType='+type+'&fldLabel='+label+'&fldLength='+fldLength+'&fldDecimal='+fldDecimal+'&fldPickList='+fldPickList+'&relationmodules='+relationmodules,
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
			document.getElementById("cfList").innerHTML=str;
		}
		gselected_fieldtype = '';
	});
}

function makeFieldSelected(oField,fieldid,blockid)
{
	if(gselected_fieldtype != '')
	{
		document.getElementById(gselected_fieldtype).className = 'customMnu';
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
	document.getElementById('vtbusy_info').style.display = "block";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode='+sub_mode+'&fld_module='+modulename+'&parenttab=Settings&ajax=true&tabid='+tabid+'&blockid='+blockid+'&selected='+selectedids_str,
	}).done(function(response) {
		document.getElementById("cfList").innerHTML=response;
		document.getElementById('vtbusy_info').style.display = "none";
	});
}

function changeRelatedListorder(what_to_do,tabid,sequence,id,module)
{
	document.getElementById('vtbusy_info').style.display = "block";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeRelatedInfoOrder&sequence='+sequence+'&fld_module='+module+'&parenttab=Settings&what_to_do='+what_to_do+'&tabid='+tabid+'&id='+id+'&ajax=true'
	}).done(function(response) {
		document.getElementById("relatedlistdiv").innerHTML=response;
		document.getElementById('vtbusy_info').style.display = "none";
	});
}

function deleteRelatedList(tabid,sequence,id,module) {
	document.getElementById('vtbusy_info').style.display = "block";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=deleteRelatedList&sequence='+sequence+'&fld_module='+module+'&parenttab=Settings&tabid='+tabid+'&id='+id+'&ajax=true'
	}).done(function(response) {
		document.getElementById("relatedlistdiv").innerHTML=response;
		document.getElementById('vtbusy_info').style.display = "none";
	});
}

function createRelatedList(module) {
	document.getElementById('vtbusy_info').style.display = "block";
	var relmodpl = document.getElementById('relatewithmodule');
	var relmod = relmodpl.options[relmodpl.selectedIndex].value;
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=createRelatedList&fld_module='+module+'&relwithmod='+relmod+'&parenttab=Settings&ajax=true',
	}).done(function(response) {
		document.getElementById("relatedlistdiv").innerHTML=response;
		document.getElementById('vtbusy_info').style.display = "none";
	});
}

function callRelatedList(module){
	document.getElementById('vtbusy_info').style.display = "block";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=getRelatedInfoOrder&parenttab=Settings&formodule='+module+'&ajax=true'
	}).done(function(response) {
		document.getElementById("relatedlistdiv").innerHTML=response;
		fnvshNrm('relatedlistdiv');
		document.getElementById('vtbusy_info').style.display = "none";
	});
}

function showProperties(field,man,pres,quickc,massed){
	var str='<section aria-labelledby="panel-heading-id" class="small slds-popover slds-popover_panel slds-nubbin_left-top" role="dialog" style="font-size: .8125rem; width:200px; line-height: 1.5; color: #16325c;"><div class="slds-popover__header"><header class="slds-media slds-media_center slds-m-bottom_small"><div class="slds-media__body"><h4 style="margin-top:0;" class="slds-text-heading-title slds-hyphenate" id="panel-heading-id">'+field+'</h4></div></header><div class="slds-grid slds-wrap slds-grid_pull-padded">';
	if (man == 0 || man == 2)
		str = str+'<div class="slds-p-horizontal_small slds-size_2-of-2 slds-p-bottom_x-small"><dl><dd>'+alert_arr.FIELD_IS_MANDATORY+'</dd></dl></div>';
	if (pres == 0 || pres == 2)
		str = str+'<div class="slds-p-horizontal_small slds-size_2-of-2 slds-p-bottom_x-small"><dl><dd>'+alert_arr.FIELD_IS_ACTIVE+'</dd></dl></div>';
	if (quickc == 0 || quickc == 2)
		str = str+'<div class="slds-p-horizontal_small slds-size_2-of-2 slds-p-bottom_x-small"><dl><dd>'+alert_arr.FIELD_IN_QCREATE+'</dd></dl></div>';
	if(massed == 0 || massed == 1)
		str = str+'<div class="slds-p-horizontal_small slds-size_2-of-2 slds-p-bottom_x-small"><dl><dd>'+alert_arr.FIELD_IS_MASSEDITABLE+'</dd></dl></div>';
	str = str + '</div></div></section>';
	return str;
}

var gselected_fieldtype = '';
{/literal}
</script>
<div id = "layoutblock">
	<div id="relatedlistdiv" style="display:none; position: absolute; width: 400px; left:900px;"></div>
	{assign var=entries value=$CFENTRIES}
	{if $CFENTRIES.0.tabpresence eq '0' }

		<table border="0" align="center" cellpadding="5" cellspacing="0" width="98%">
			<tr>
				<td>
					<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
						<tr class="slds-text-title--caps">
							<td style="padding: 0;">
								<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
									<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
										<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
											<!-- Image -->
											<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
												<div class="slds-media__figure slds-icon forceEntityIcon">
													<span class="photoContainer forceSocialPhoto">
														<div class="small roundedSquare forceEntityIcon">
															<span class="uiImage">
																<img src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" alt="Users" title="Users"/>
															</span>
														</div>
													</span>
												</div>
											</div>
											<!-- Title and help text -->
											<div class="slds-media__body">
												<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
													<span class="uiOutputText">
														<b>
														<a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a>
														&nbsp;&gt;&nbsp;
														<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings">{$MODULE|@getTranslatedString:$MODULE}</a>
														&nbsp;&gt;&nbsp;
														{$MOD.LBL_LAYOUT_EDITOR}</b>
													</span>
													<span class="small">{$MOD.LBL_LAYOUT_EDITOR_DESCRIPTION}</span>
												</h1>
											</div>
											<!-- Arrange Related List & Add Block buttons -->
											<div class="slds-no-flex">
												<div class="actionsContainer">
													<input type="button" class="slds-button slds-button--small slds-button--brand" onclick="callRelatedList('{$CFENTRIES.0.module}');fnvshNrm('relatedlistdiv');posLay(this,'relatedlistdiv');" alt="{$MOD.ARRANGE_RELATEDLIST}" title="{$MOD.ARRANGE_RELATEDLIST}" value="{$MOD.ARRANGE_RELATEDLIST}"/>
													<input type="button" class="slds-button slds-button--small slds-button_success" onclick="fnvshobj(this,'addblock');" alt="{$MOD.ADD_BLOCK}" title="{$MOD.ADD_BLOCK}" value="{$MOD.ADD_BLOCK}"/>
													&nbsp; <img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" id="vtbusy_info" style="display:none;position:absolute;top:180px;right:100px;" border="0" />
												</div>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
					<br/>

					<div id="cfList">
					{include file="Settings/LayoutBlockEntries.tpl"}
					</div>
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<tr>
							<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

	{else}
		{include file='modules/Vtiger/OperationNotPermitted.tpl'}
	{/if}
</div>
