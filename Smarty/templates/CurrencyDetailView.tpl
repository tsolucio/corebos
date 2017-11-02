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
					<!-- DISPLAY DetailView-->
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
																			<img src="{'currency.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" />
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
																		{$MOD.LBL_VIEWING} &quot;{$CURRENCY_NAME}&quot;
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
																					<strong>{'LBL_SETTINGS'|@getTranslatedString} {$APP.LBL_FOR} &quot;{$CURRENCY_NAME|@getTranslatedCurrencyString}&quot;  </strong>
																				</span>
																			</h2>
																		</div>
																	</header>
																	<div class="slds-no-flex">
																		<input type="submit" class="slds-button slds-button--small slds-button--brand" value="Edit" onclick="this.form.action.value='CurrencyEditView'; this.form.parenttab.value='Settings'; this.form.record.value='{$ID}'">
																	</div>
																</div>
															</article>
														</div>

														<div class="slds-truncate">
															<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																<tr>
																	<td width="20%" nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_CURRENCY_NAME}</strong></td>
																	<td width="80%" class="small dvtCellInfo"><strong>{$CURRENCY_NAME|@getTranslatedCurrencyString}</strong></td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_CURRENCY_CODE}</strong></td>
																	<td class="small dvtCellInfo">{$CURRENCY_CODE}</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_CURRENCY_SYMBOL}</strong></td>
																	<td class="small dvtCellInfo">{$CURRENCY_SYMBOL}</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_CURRENCY_CRATE}</strong><br>({$MOD.LBL_BASE_CURRENCY}{$MASTER_CURRENCY|@getTranslatedCurrencyString})</td>
																	<td class="small dvtCellInfo">{$CONVERSION_RATE}</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_CURRENCY_STATUS}</strong></td>
																	<td class="small dvtCellInfo">{$CURRENCY_STATUS}</td>
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