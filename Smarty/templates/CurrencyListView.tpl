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
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
				<br>
					{include file='SetMenu.tpl'}
					<!-- DISPLAY Currencies Settings-->
						<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
							<input type="hidden" name="module" value="Settings">
							<input type="hidden" name="action" value="CurrencyEditView">

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
																<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_CURRENCY_SETTINGS} </b>
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
												</td>
													<div class="forceRelatedListSingleContainer">
														<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
															<div class="slds-card__header slds-grid">
																<header class="slds-media slds-media--center slds-has-flexi-truncate">
																	<div class="slds-media__body">
																		<h2>
																			<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																				<strong>{$MOD.LBL_CURRENCY_LIST}</strong>
																			</span>
																		</h2>
																	</div>
																</header>
																<div class="slds-no-flex">
																	<input type="submit" value="{$MOD.LBL_NEW_CURRENCY}" class="slds-button slds-button--small slds-button_success">
																</div>
															</div>
														</article>
													</div>

													<div class="slds-truncate" id="CurrencyListViewContents">
														{include file="CurrencyListViewEntries.tpl"}
													</div>

												</td>
											</tr>
										</table>

									</td>
								</tr>
							</table>

							<table border=0 cellspacing=0 cellpadding=5 width=100% >
								<tr>
									<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
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

<div id="currencydiv" style="display:block;position:absolute;width:250px;"></div>

{literal}
<script>
	function deleteCurrency(currid){
		document.getElementById("status").style.display="inline";
		jQuery.ajax({
			method:"POST",
			url:'index.php?action=SettingsAjax&file=CurrencyDeleteStep1&return_action=CurrencyListView&return_module=Settings&module=Settings&parenttab=Settings&id='+currid,
		}).done(function(response) {
			jQuery("#status").hide();
				jQuery("#currencydiv").html(response);
			}
		);
	}

	function transferCurrency(del_currencyid){
		document.getElementById("status").style.display="inline";
		jQuery("#CurrencyDeleteLay").hide();
		var trans_currencyid=jQuery("#transfer_currency_id").val();
		jQuery.ajax({
				method:"POST",
				url:'index.php?module=Settings&action=SettingsAjax&file=CurrencyDelete&ajax=true&delete_currency_id='+del_currencyid+'&transfer_currency_id='+trans_currencyid,
		}).done(function(response) {
			jQuery("#status").hide();
			jQuery("#CurrencyListViewContents").html(response);
		});
	}
</script>

{/literal}
