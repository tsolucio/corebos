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

	document.getElementById("status").style.display="inline";
	button.disabled = true;

	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true' + '&selmodule=' + encodeURIComponent(module) +
			'&recprefix=' + encodeURIComponent(recprefix) + '&recnumber=' + encodeURIComponent(recnumber) + '&mode=' + encodeURIComponent(mode)
	}).done(function(response) {
		document.getElementById("status").style.display="none";
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

	VtigerJS_DialogBox.progress();
	button.disabled = true;

	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true' + '&selmodule=' + encodeURIComponent(module) + '&mode=' + encodeURIComponent(mode)
	}).done(function(response) {
		VtigerJS_DialogBox.hideprogress();
		var restext = response;
		document.getElementById('customentity_infodiv').innerHTML = restext;
	});
}
</script>
{/literal}

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
				<br>
					{include file='SetMenu.tpl'}
						<!-- DISPLAY  Customize Record Numbering-->
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
																		<img src="{'settingsInvNumber.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}" title="{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}">
																	</span>
																</div>
															</span>
														</div>
													</div>
													<!-- Title and help text -->
													<div class="slds-media__body">
														<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
															<span class="uiOutputText" style="width: 100%;">
																<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}</b>
															</span>
															<span class="small">{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER_DESCRIPTION}</span>
														</h1>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</table>

							<form method="POST" action="javascript:;" onsubmit="VtigerJS_DialogBox.block();">
								<table border="0" cellpadding="10" cellspacing="0" width="100%">
									<tr>
										<td>

											<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
												<tr>
													<td class="small" align="right">
														<div class="forceRelatedListSingleContainer">
															<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																<div class="slds-card__header slds-grid">
																	<header class="slds-media slds-media--center slds-has-flexi-truncate">
																		<div class="slds-media__body">
																			<h2>
																				<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																					
																				</span>
																			</h2>
																		</div>
																	</header>
																	<div class="slds-no-flex" style="display: inherit;">
																		<span style="margin-top: 5px;">{$MOD.LBL_SELECT_CF_TEXT}</span>
																		&nbsp;
																		<select name="selmodule" class="small slds-select" style="width: 65%;" onChange="getModuleEntityNoInfo(this.form)">
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
																	</div>
																</div>
															</article>
														</div>
														<div id='customentity_infodiv' class="listRow">
															{include file='Settings/CustomModEntityNoInfo.tpl'}
														</div>
													</td>
												</tr>
											</table>

											<!-- <table border="0" cellpadding="5" cellspacing="0" width="100%">
												<tr>
													<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
												</tr>
											</table> -->
										</td>
									</tr>
								</table>
							</form>


						</td></tr></table><!-- close tables from setMenu -->
						</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>