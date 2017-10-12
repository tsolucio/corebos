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
						{include file="SetMenu.tpl"}
							<!-- DISPLAY Edit Sharing Access-->
								<form action="index.php" method="post" name="def_org_share" id="form" onsubmit="VtigerJS_DialogBox.block();">
									<input type="hidden" name="module" value="Users">
									<input type="hidden" name="action" value="SaveOrgSharing">
									<input type="hidden" name="parenttab" value="Settings">

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
																				<img src="{'shareaccess.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}"/>
																			</span>
																		</div>
																	</span>
																</div>
															</div>
															<div class="slds-media__body">
																<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																	<span class="uiOutputText">
																		<b>
																			<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_EDIT} {$MOD.LBL_SHARING_ACCESS}
																		</b>
																	</span>
																	<span class="small">
																		{$MOD.LBL_SHARING_ACCESS_DESCRIPTION}
																	</span>
																</h1>
															</div>
														</div>
													</div>
												</div>
											</td>
										</tr>
									</table>

									<table class="slds-table slds-no-row-hover detailview_table ">
										<tr class="slds-line-height--reset">
											<td valign="top">
												<div class="forceRelatedListSingleContainer">
													<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
														<div class="slds-card__header slds-grid">
															<header class="slds-media slds-media--center slds-has-flexi-truncate">
																<div class="slds-media__body">
																	<h2>
																		<span class="slds-text-title--caps slds-truncate actionLabel tableHeading">
																			<strong>{$CMOD.LBL_GLOBAL_ACCESS_PRIVILEGES}</strong>
																		</span>
																	</h2>
																</div>
															</header>
															<div class="slds-no-flex">
																<div class="actionsContainer">
																	<input class="slds-button slds-button--small slds-button_success" title="Save" accessKey="C" type="submit" name="Save" value="{$CMOD.LBL_SAVE_PERMISSIONS}">&nbsp;
																	<input class="slds-button--small slds-button slds-button--destructive" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" type="button" name="Cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="window.history.back();">
																</div>
															</div>
														</div>
													</article>
												</div>

												<table class="listTable slds-table slds-no-row-hover detailview_table org-sharing-table">
													{foreach item=elements from=$ORGINFO}
														{assign var="MODULELABEL" value=$elements.0|getTranslatedString:$elements.0}
														<tr class="slds-line-height--reset">
															<td width="30%" class="dvtCellLabel small" nowrap>{$MODULELABEL}</td>
															<td width="70%" class="dvtCellInfo listTable small">{$elements.2}</td>
														<tr>
													{/foreach}
												</table>

											</td>
										</tr>
									</table>
								</form>

								<table border=0 cellspacing=0 cellpadding=5 width=100% >
									<tr>
										<td class="small" >
											<div align=right><a href="#top">{$MOD.LBL_SCROLL}</a></div>
										</td>
									</tr>
								</table>

						</td></tr></table><!-- /.from setMenu -->
						</td></tr></table><!-- /.from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>

<script>
function checkAccessPermission(share_value)
{ldelim}
	if (share_value == "3")
	{ldelim}
		alert("{$APP.ACCOUNT_ACCESS_INFO}");
		document.getElementById('2_perm_combo').options[3].selected=true
		document.getElementById('13_perm_combo').options[3].selected=true
		document.getElementById('20_perm_combo').options[3].selected=true
		document.getElementById('22_perm_combo').options[3].selected=true
		document.getElementById('23_perm_combo').options[3].selected=true
	{rdelim}
{rdelim}
</script>
