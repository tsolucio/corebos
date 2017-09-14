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

<script type="text/javascript" src="include/js/general.js"></script>

<!-- header - level 2 tabs -->
{include file='Buttons_List.tpl'}

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td>
			<table width=100% align="center">
				<tr class="slds-text-title--header">
						<form  name="Export_Records"  method="POST" onsubmit="VtigerJS_DialogBox.block();">
							<input type="hidden" name="module" value="{$MODULE}">
							<input type="hidden" name="action" value="Export">
							<input type="hidden" name="idstring" value="{if isset($IDSTRING)}{$IDSTRING}{/if}">
							<table class="slds-table slds-no-row-hover slds-table-moz" align="center" style="border-collapse:separate; border-spacing: 1rem;">
								<tr class="blockStyleCss slds-line-height--reset">
									<td class="detailViewContainer" valign="top">
										<div class="forceRelatedListSingleContainer">
											<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
												<div class="slds-card__header slds-grid">
													<header class="slds-media slds-media--center slds-has-flexi-truncate">
														<div class="slds-media__body">
															<h2>
																<span class="slds-text-title--caps slds-truncate heading2 actionLabel">
																	<b>{$MODULELABEL} >> {$APP.LBL_EXPORT}</b>
																</span>
															</h2>
														</div>

													</header>
												</div>
											</article>
										</div>
										<div class="slds-truncate" style="display:block;">
											<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
												<tr class="slds-line-height--reset">
													<td class="dvtCellLabel text-left" valign="top">
														<span class="genHeaderSmall">{$APP.LBL_SEARCH_CRITERIA_RECORDS}:</span>
													</td>
													<td class="dvtCellLabel text-left" valign="top">
														<span class="genHeaderSmall">{$APP.LBL_EXPORT_RECORDS}:</span>
													</td>
												</tr>
											</table>
											<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
												<tr class="slds-line-height--reset">
													<td valign="top" style="padding-top: 0;">
														<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout">
															<tr class="slds-line-height--reset">
																{if $SESSION_WHERE neq ''}
																<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_WITH_SEARCH}</td>
																<td class="dvtCellInfo" valign="top" style="width: 25%;">
																	<input type="radio" name="search_type" checked value="includesearch">
																</td>
																{else}
																<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_WITH_SEARCH}</td>
																<td class="dvtCellInfo" valign="top" style="width: 25%;">
																	<input type="radio" name="search_type"  value="includesearch">
																</td>
																{/if}
															</tr>
															<tr class="slds-line-height--reset">
																{if $SESSION_WHERE eq ''}
																<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_WITHOUT_SEARCH}</td>
																<td class="dvtCellInfo" valign="top" style="width: 25%;">
																	<input type="radio" name="search_type" checked value="withoutsearch">
																</td>
																{else}
																<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_WITHOUT_SEARCH}</td>
																<td class="dvtCellInfo" valign="top" style="width: 25%;">
																	<input type="radio" name="search_type" value="withoutsearch">
																</td>
																{/if}
															</tr>
														</table>
													</td>
													<td valign="top" style="padding-top: 0;padding-right: .5rem;">
														<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout">
															<tr class="slds-line-height--reset">
																{if empty($IDSTRING)}
																	<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_ALL_DATA}</td>
																	<td class="dvtCellInfo" valign="top" style="width: 25%;">
																		<input type="radio" name="export_data" checked value="all">
																	</td>
																{else}
																	<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_ALL_DATA}</td>
																	<td class="dvtCellInfo" valign="top" style="width: 25%;">
																		<input type="radio" name="export_data"  value="all">
																	</td>
																{/if}
															</tr>
															<tr class="slds-line-height--reset">
																<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_DATA_IN_CURRENT_PAGE}</td>
																<td class="dvtCellInfo" valign="top" style="width: 25%;">
																	<input type="radio" name="export_data" value="currentpage">
																</td>
															</tr>
															<tr>
																{if !empty($IDSTRING)}
																	<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_ONLY_SELECTED_RECORDS}</td>
																	<td class="dvtCellInfo" valign="top" style="width: 25%;">
																		<input type="radio" name="export_data" checked value="selecteddata">
																	</td>
																{else}
																	<td class="dvtCellLabel" valign="top" style="width: 30%;">{$APP.LBL_ONLY_SELECTED_RECORDS}</td>
																	<td class="dvtCellInfo" valign="top" style="width: 25%;">
																		<input type="radio" name="export_data"  value="selecteddata">
																	</td>
																{/if}
															</tr>
														</table>
													</td>
												</tr>
												<tr class="slds-line-height--reset">
													<td colspan="2" align="center">
														<div id="not_search" style="position:absolute;display:none;width:400px;height:25px;"></div>
													</td>
												</tr>
												<tr class="slds-line-height--reset">
													<td colspan="2" align="center">
														<input type="button" name="{$APP.LBL_EXPORT}" value="{$APP.LBL_EXPORT} {$MODULELABEL} " class="slds-button slds-button--small slds-button_success" onclick="record_export('{$MODULELABEL}','{$CATEGORY}',this.form,'{if isset($smarty.request.idstring)}{$smarty.request.idstring}{/if}')"/>&nbsp;&nbsp;
														<input type="button" name="{$APP.LBL_CANCEL_BUTTON_LABEL}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" onclick="window.history.back()" />
													</td>
												</tr>
											</table>
										</div>
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

