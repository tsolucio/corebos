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
<script>
{literal}

function confirmAction(msg) {
	return confirm(msg);
}

function deleteForm(formname,address) {
	var status=confirmAction(alert_arr['SURE_TO_DELETE_CUSTOM_MAP']);
	if (!status) {
		return false;
	}
	submitForm(formname, address);
	return true;
}

function submitForm(formName,action) {
	document.forms[formName].action=action;
	document.forms[formName].submit();
}

var gselected_fieldtype = '';
function getCustomFieldList(customField) {
	var modulename = customField.options[customField.options.selectedIndex].value;
	var modulelabel = customField.options[customField.options.selectedIndex].text;
	document.getElementById('module_info').innerHTML = '{$MOD.LBL_CUSTOM_FILED_IN} "'+modulelabel+'" {$APP.LBL_MODULE}';
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Settings&action=SettingsAjax&file=CustomFieldList&fld_module='+modulename+'&ajax=true'
	}).done(function (response) {
		document.getElementById('cfList').innerHTML=response;
	});
}

function deleteCustomField(id, fld_module, colName, uitype) {
	if (confirm(alert_arr.ARE_YOU_SURE)) {
		document.form.action='index.php?module=Settings&action=DeleteCustomField&fld_module='+fld_module+'&fld_id='+id+'&colName='+colName+'&uitype='+uitype;
		document.form.submit();
	}
}

function makeFieldSelected(oField, fieldid, blockid) {
	if (gselected_fieldtype != '') {
		document.getElementById(gselected_fieldtype).className = 'customMnu';
	}
	oField.className = 'customMnuSelected';
	gselected_fieldtype = oField.id;
	selFieldType(fieldid, '', '', blockid)
	document.getElementById('selectedfieldtype_'+blockid).value = fieldid;
}

function CustomFieldMapping() {
	document.form.action='index.php?module=Settings&action=LeadCustomFieldMapping';
	document.form.submit();
}
var gselected_fieldtype = '';
{/literal}
</script>
{include file='SetMenu.tpl'}
<div id="createcf" style="display:block;position:absolute;width:500px;"></div>
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
			<!-- DISPLAY -->
			{if $MODE neq 'edit'}
			<b><font color=red>{$DUPLICATE_ERROR} </font></b>
			{/if}
				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%" >
					<tbody>
						<tr align="left">
							<td rowspan="2" valign="top" width="50"><img src="{'custom.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48" onmouseover="tooltip.tip(this,'{'LBL_FIELD_SETTINGS'|@getTranslatedString:'Leads'}');" onmouseout="tooltip.untip(true);"></td>
							<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=ModuleManager">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> &gt; <a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}">{$MODULE|@getTranslatedString:$MODULE}</a> &gt; {'LBL_FIELD_SETTINGS'|@getTranslatedString:'Leads'}</b></td>
						</tr>
					</tbody>
				</table>
				<br>
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tbody><tr>
				<td>
				{if $MODULE eq 'Leads'}
				<div id="cfList">
				{include file='Leads'|@vtlib_getModuleTemplate:'LeadsCustomEntries.tpl'}
				</div>
				{else}
				<div id="cfList">
				{include file='Vtiger'|@vtlib_getModuleTemplate:'CustomFieldEntries.tpl'}
				</div>
				{/if}
			</td>
			</tr>
			</table>
		<!-- End of Display -->
		</div>
		</td>
		</tr>
		<tr>
		</tr>
</tbody>
</table>
</div>
</section>