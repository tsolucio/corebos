{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
{literal}
<script type="text/javascript">
function getModuleEntityNoInfo(form) {
	var module = form.selmodule.value;

	document.getElementById("status").style.display="inline";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true&selmodule=' + encodeURIComponent(module)
	}).done(function(response) {
		document.getElementById("status").style.display="none";
		var restext = response;
		document.getElementById('customentity_infodiv').innerHTML = restext;
	});
}
function updateModEntityNoSetting(button, form) {
	var module = form.selmodule.value;
	var recprefix = form.recprefix.value;
	var recnumber = form.recnumber.value;
	var mode = 'UPDATESETTINGS';

	if(recnumber == '') {
		alert("{/literal}{$MOD.ERR_CUSTOMIZE_MODENT_NUMBER_EMPTY}{literal}");
		return;
	}

	if(recnumber.match(/[^0-9]+/) != null) {
		alert("{/literal}{$MOD.ERR_CUSTOMIZE_MODENT_NUMBER_NUMERIC}{literal}");
		return;
	}

	document.getElementById('status').style.display='inline';
	button.disabled = true;

	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true' + '&selmodule=' + encodeURIComponent(module) +
			'&recprefix=' + encodeURIComponent(recprefix) + '&recnumber=' + encodeURIComponent(recnumber) + '&mode=' + encodeURIComponent(mode)
	}).done(function(response) {
		document.getElementById('status').style.display='none';
		var restext = response;
		document.getElementById('customentity_infodiv').innerHTML = restext;
	});
}
function updateModEntityExisting(button, form) {
	var module = form.selmodule.value;
	var recprefix = form.recprefix.value;
	var recnumber = form.recnumber.value;
	var mode = 'UPDATEBULKEXISTING';

	if(recnumber == '') {
		alert("{/literal}{$MOD.ERR_CUSTOMIZE_MODENT_NUMBER_EMPTY}{literal}");
		return;
	}

	if(recnumber.match(/[^0-9]+/) != null) {
		alert("{/literal}{$MOD.ERR_CUSTOMIZE_MODENT_NUMBER_NUMERIC}{literal}");
		return;
	}

	VtigerJS_DialogBox.block();
	button.disabled = true;

	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true' + '&selmodule=' + encodeURIComponent(module) + '&mode=' + encodeURIComponent(mode)
	}).done(function(response) {
		VtigerJS_DialogBox.unblock();
		var restext = response;
		document.getElementById('customentity_infodiv').innerHTML = restext;
	});
}
</script>
{/literal}
{assign var="MODULEICON" value='richtextnumberedlist'}
{assign var="MODULESECTION" value=$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_CUSTOMIZE_MODENT_NUMBER_DESCRIPTION}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none slds-card">
<div align=center>
	<br>
	<form method="POST" action="javascript:;" onsubmit="VtigerJS_DialogBox.block();">
		<table class="slds-table slds-table_cell-buffer slds-table-no_bordered " border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td  width="30%" align="left">
					{$MOD.LBL_SELECT_CF_TEXT}
					<select name="selmodule" class="slds-select" width="30%" onChange="getModuleEntityNoInfo(this.form)">
					{foreach key=sel_value item=value from=$MODULES}
						{if $SELMODULE eq $sel_value}
							{assign var = "selected_val" value="selected"}
						{else}
							{assign var = "selected_val" value=""}
						{/if}
						{assign var="MODULE_LABEL" value=$value}
						{assign var="MODULE_LABEL" value=$value|getTranslatedString:$value}
						<option value="{$sel_value}" {$selected_val}>{$MODULE_LABEL}</option>
					{/foreach}
					</select>
				</td>
				<td  width="70%" align="left"></td>
			</tr>
		</table>
		<div id='customentity_infodiv' class="listRow">
			{include file='Settings/CustomModEntityNoInfo.tpl'}
		</div>
	</form>
</div>
</div>
</section>