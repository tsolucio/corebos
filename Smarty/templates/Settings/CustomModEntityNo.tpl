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
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none slds-card">
<div class="slds-page-header">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
							<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_NEW_CURRENCY}">
								<svg class="slds-button__icon slds-icon-text-success slds-icon_large slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#richtextnumberedlist"></use> </svg>
								&nbsp;{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}
								<p valign=top class="small cblds-p-v_none">&nbsp;&nbsp;&nbsp;&nbsp;{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER_DESCRIPTION}</p>
							</h1>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
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
		<div class="slds-col">
			<p class="slds-p-right_small" nowrap align="right"><a href="#top">{$MOD.LBL_SCROLL}</a></p>
		</div>
	</form>
</div>
</div>
</section>