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
<script type="text/javascript" src="include/js/picklist.js"></script>
<script src="modules/Home/Homestuff.js" type="text/javascript"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
						{include file='SetMenu.tpl'}
											<!-- DISPLAY Picklist Editor Settings-->
											<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
												<tr class="slds-text-title--caps">
													<td style="padding: 0;">
														<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
															<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
																<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
																	<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																		<div class="slds-media__figure slds-icon forceEntityIcon">
																			<span class="photoContainer forceSocialPhoto">
																				<div class="small roundedSquare forceEntityIcon">
																					<span class="uiImage">
																						<img src="{'picklist.gif'|@vtiger_imageurl:$THEME}" />
																					</span>
																				</div>
																			</span>
																		</div>
																	</div>
																	<div class="slds-media__body">
																		<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																			<span class="uiOutputText">
																				<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_PICKLIST_EDITOR}</b>
																			</span>
																			<span class="small">{$MOD.LBL_PICKLIST_DESCRIPTION}</span>
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
													<td valign=top>

														<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
															<tr>
																<td class="dvtCellLabel" width="40%"><strong>{$MOD.LBL_SELECT_MODULE}</strong></td>
																<td class="dvtCellInfo" align="left" width="30%">
																	<select name="pickmodule" id="pickmodule" class="slds-select" onChange="changeModule();">
																		{html_options options=$MODULE_LISTS selected=$MODULE sortoptions=asc}
																	</select>
																</td>
																<td>&nbsp;</td>
															</tr>
														</table>

														<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
															<tr>
																<td class="big" rowspan="2" style="padding: .2rem 0;">
																<div id="picklist_datas">
																	{include file='modules/PickList/PickListContents.tpl'}
																</div>
																</td>
															</tr>
														</table>

														<table border=0 cellspacing=0 cellpadding=5 width=100% >
															<tr>
																<td class="small" nowrap align=right>
																	<a href="#top">
																		{$MOD.LBL_SCROLL}
																	</a>
																</td>
															</tr>
														</table>

													</td>
												</tr>
											</table>

										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div id="actiondiv" style="display:block;position:absolute;"></div>
<div id="editdiv" style="display:block;position:absolute;width:510px;"></div>
