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
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
				<br>
					{include file='SetMenu.tpl'}
						<!-- DISPLAY Currency Edit View-->
							<form action="index.php" method="post" name="index" id="form" onsubmit="VtigerJS_DialogBox.block();">
								<input type="hidden" name="module" value="Settings">
								<input type="hidden" name="parenttab" value="{$PARENTTAB}">
								<input type="hidden" name="action" value="index">
								<input type="hidden" name="record" value="{$ID}">

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
																				<img src="{'currency.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}">
																			</span>
																		</div>
																	</span>
																</div>
															</div>
															<!-- Title and help text -->
															<div class="slds-media__body">
																<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																	<span class="uiOutputText" style="width: 100%;">
																		<b>
																			<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > 
																			<a href="index.php?module=Settings&action=CurrencyListView&parenttab=Settings">{$MOD.LBL_CURRENCY_SETTINGS}</a> > 
																			{if $ID neq ''}
																				{$MOD.LBL_EDIT} &quot;{$CURRENCY_NAME}&quot;
																			{else}
																				{$MOD.LBL_NEW_CURRENCY}
																			{/if}
																		</b>
																	</span>
																	<span class="small">{$MOD.LBL_CURRENCY_DESCRIPTION}</span>
																</h1>
															</div>
														</div>
													</div>
												</div>
											</td>
										</tr>
									</table>

									<table border=0 cellspacing=0 cellpadding=10 width=100% >
										<tr>
											<td>

												<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
													<tr>
														<td class="big">
															<div class="forceRelatedListSingleContainer">
																<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2>
																					<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																						<strong>
																						{if $ID neq ''}
																							{'LBL_SETTINGS'|@getTranslatedString} {$APP.LBL_FOR} &quot;{$CURRENCY_NAME|@getTranslatedCurrencyString}&quot;
																						{else}
																							&quot;{$MOD.LBL_NEW_CURRENCY}&quot;
																						{/if}
																						</strong>
																					</span>
																				</h2>
																			</div>
																		</header>
																		<div class="slds-no-flex">
																			<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button--small slds-button_success" onclick="this.form.action.value='SaveCurrencyInfo'; return validate()" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >&nbsp;&nbsp;
																			<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--small slds-button--destructive" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
																			<div id="CurrencyEditLay" class="layerPopup" style="display:none;width:25%;">
																				<table width="100%" border="0" cellpadding="3" cellspacing="0" class="layerHeadingULine">
																				<tr>
																					<td class="layerPopupHeading" align="left" width="60%">{$MOD.LBL_TRANSFER_CURRENCY}</td>
																					<td align="right" width="40%"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border=0 alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" style="cursor:pointer;" onClick="document.getElementById('CurrencyEditLay').style.display='none'";></td>
																				</tr>
																				<table>
																				<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
																					<tr>
																						<td class=small >
																							<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
																								<tr>
																									<td width="50%" class="dvtCellLabel small"><b>{$MOD.LBL_CURRENT_CURRENCY}</b></td>
																									<td width="50%" class="dvtCellInfo small"><b>{$CURRENCY_NAME|@getTranslatedCurrencyString}</b></td>
																								</tr>
																								<tr>
																									<td class="dvtCellLabel small"><b>{$MOD.LBL_TRANSCURR}</b></td>
																									<td class="dvtCellInfo small">
																										<select class="select small" name="transfer_currency_id" id="transfer_currency_id">';
																										 {foreach key=cur_id item=cur_name from=$OTHER_CURRENCIES}
																											 <option value="{$cur_id}">{$cur_name|@getTranslatedCurrencyString}</option>
																										 {/foreach}
																									</td>
																								</tr>
																							</table>
																						</td>
																					</tr>
																				</table>
																				<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
																					<tr>
																						<td align="center"><input type="button" onclick="form.submit();" name="Update" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmbutton small save">
																						</td>
																					</tr>
																				</table>
																			</div>
																		</div>
																	</div>
																</article>
															</div>


															<div class="slds-truncate">
																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																	<tr>
																		<td width="20%" nowrap class="small dvtCellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_NAME}</strong></td>
																		<td width="80%" class="small dvtCellInfo">
																			<!-- input type="hidden" class="slds-input small" value="" name="currency_name" -->
																			<select name="currency_name" id="currency_name" class="slds-select" onChange='updateSymbolAndCode();' style="width: 75%;">
																		{foreach key=header item=currency from=$CURRENCIES}
																			{if $header eq $CURRENCY_NAME}
																				<option value="{$header}" selected>{$header|@getTranslatedCurrencyString}({$currency.1})</option>
																			{else}
																				<option value="{$header}" >{$header|@getTranslatedCurrencyString}({$currency.1})</option>
																			{/if}
																		{/foreach}
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td nowrap class="small dvtCellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_CODE}</strong></td>
																		<td class="small dvtCellInfo"><input type="text" readonly class="slds-input small" value="{$CURRENCY_CODE}" name="currency_code" id="currency_code"></td>
																	</tr>
																	<tr>
																		<td nowrap class="small dvtCellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_SYMBOL}</strong></td>
																		<td class="small dvtCellInfo"><input type="text" readonly class="slds-input small" value="{$CURRENCY_SYMBOL}" name="currency_symbol" id="currency_symbol"></td>
																	</tr>
																	<tr>
																		<td nowrap class="small dvtCellLabel"><font color="red">*</font><strong>{'Symbol Placement'|@getTranslatedString:'Users'}</strong></td>
																		<td class="small dvtCellInfo">
																			<select name="currency_position" class="importBox slds-select" style="width: 75%;">
																				{html_options options=$symbolpositionvalues selected=$CURRENCY_POSITION}
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td nowrap class="small dvtCellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_CRATE}</strong><br>({$MOD.LBL_BASE_CURRENCY}{$MASTER_CURRENCY|@getTranslatedCurrencyString})</td>
																		<td class="small dvtCellInfo"><input type="text" class="slds-input small" value="{$CONVERSION_RATE}" name="conversion_rate"></td>
																	</tr>
																	<tr>
																		<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_CURRENCY_STATUS}</strong></td>
																		<td class="small dvtCellInfo">
																			<input type="hidden" value="{$CURRENCY_STATUS}" id="old_currency_status" />
																			<select name="currency_status" {$STATUS_DISABLE} class="importBox slds-select" style="width: 75%;">
																				<option value="Active" {$ACTSELECT}>{$MOD.LBL_ACTIVE}</option>
																				<option value="Inactive" {$INACTSELECT}>{$MOD.LBL_INACTIVE}</option>
																			</select>
																		</td>
																	</tr>
																</table>
															</div>

														</td>
													</tr>
												</table>

												<table border=0 cellspacing=0 cellpadding=5 width=100% >
													<tr>
														<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
													</tr>
												</table>

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

{literal}
<script>
function validate() {
	if (!emptyCheck("currency_name","Currency Name","text")) return false
	if (!emptyCheck("currency_code","Currency Code","text")) return false
	if (!emptyCheck("currency_symbol","Currency Symbol","text")) return false
	if (!emptyCheck("conversion_rate","Conversion Rate","text")) return false
	if (!emptyCheck("currency_status","Currency Status","text")) return false
	if(isNaN(getObj("conversion_rate").value) || eval(getObj("conversion_rate").value) <= 0)
	{
{/literal}
		alert("{$APP.ENTER_VALID_CONVERSION_RATE}")
		return false
{literal}
	}
	if (getObj("currency_status") != null && getObj("currency_status").value == "Inactive"
			&& getObj("old_currency_status") != null && getObj("old_currency_status").value == "Active")
	{
		if (getObj("CurrencyEditLay") != null) getObj("CurrencyEditLay").style.display = "block";
		return false;
	}
	else
	{
		return true;
	}
}
{/literal}
var currency_array = {$CURRENCIES_ARRAY}
{literal}
updateSymbolAndCode();
function updateSymbolAndCode(){
	selected_curr = document.getElementById('currency_name').value;
	getObj('currency_code').value = currency_array[selected_curr][0];
	getObj('currency_symbol').value = currency_array[selected_curr][1];
}
</script>
{/literal}
