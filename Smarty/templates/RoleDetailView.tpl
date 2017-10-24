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
									<!-- DISPLAY Role Detail View-->
									<form id="form" name="roleView" action="index.php" method="post" onsubmit="VtigerJS_DialogBox.block();">
										<input type="hidden" name="module" value="Settings">
										<input type="hidden" name="action" value="createrole">
										<input type="hidden" name="parenttab" value="Settings">
										<input type="hidden" name="returnaction" value="RoleDetailView">
										<input type="hidden" name="roleid" value="{$ROLEID}">
										<input type="hidden" name="mode" value="edit">

										<!-- Role Detail View Header -->
										<table class="slds-table slds-no-row-hover slds-table--cell-buffer" style="background-color: #f7f9fb;">
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
																					<img src="{'ico-roles.gif'|@vtiger_imageurl:$THEME}">
																				</span>
																			</div>
																		</span>
																	</div>
																</div>
																<div class="slds-media__body">
																	<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																		<span class="uiOutputText">
																			<b>
																				<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=listroles&parenttab=Settings">{$CMOD.LBL_ROLES}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$ROLE_NAME}&quot;
																			</b>
																		</span>
																		<span class="small">
																			{$CMOD.LBL_VIEWING} {$CMOD.LBL_PROPERTIES} &quot;{$ROLE_NAME}&quot; {$MOD.LBL_LIST_CONTACT_ROLE}
																		</span>
																	</h1>
																</div>
															</div>
														</div>
													</div>
												</td>
											</tr>
										</table>

										<!-- Role Detail View Content -->
										<table border=0 cellspacing=0 cellpadding=10 width=100% >
											<tr>
												<td valign=top>

													<!-- Properties and Edit button -->
													<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
														<tr>
															<td class="big">
																<div class="forceRelatedListSingleContainer">
																	<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																		<div class="slds-card__header slds-grid">
																			<header class="slds-media slds-media--center slds-has-flexi-truncate">
																				<div class="slds-media__body">
																					<h2>
																						<span class="slds-text-title--caps slds-truncate actionLabel prvPrfBigText">
																							<strong>{$CMOD.LBL_PROPERTIES} &quot;{$ROLE_NAME}&quot; </strong>
																						</span>
																					</h2>
																				</div>
																			</header>
																			<div class="slds-no-flex">
																				<div class="actionsContainer">
																					<input value="   {$APP.LBL_EDIT_BUTTON_LABEL}   " title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" type="submit" name="Edit" >
																				</div>
																			</div>
																		</div>
																	</article>
																</div>

																<!-- Properties Table Content -->
																<div class="slds-truncate">
																	<table class="slds-table slds-no-row-hover detailview_table">
																		<tr class="small">
																			<td width="15%" class="small cellLabel"><strong>{$CMOD.LBL_ROLE_NAME}</strong></td>
																			<td width="85%" class="cellText" >{$ROLE_NAME}</td>
																		</tr>
																		<tr class="small">
																			<td class="small cellLabel"><strong>{$CMOD.LBL_REPORTS_TO}</strong></td>
																			<td class="cellText">{$PARENTNAME}</td>
																		</tr>
																		<tr class="small">
																			<td valign=top class="cellLabel"><strong>{$CMOD.LBL_MEMBER}</strong></td>
																			<td class="cellText">

																				<table class="slds-table slds-no-row-hover detailview_table">
																					<tr class="small">
																						<td width="50%" class="dvtCellLabel cellBottomDotLine">
																							<div align="left"><strong>{$CMOD.LBL_ASSOCIATED_PROFILES}</strong></div>
																						</td>
																						<td width="50%" class="dvtCellLabel cellBottomDotLine">
																							<div align="left"><strong>{$CMOD.LBL_ASSOCIATED_USERS}</strong></div>
																						</td>
																					</tr>
																					
																					<tr class="small">
																						<td width="50%" class="dvtCellInfo">
																							{foreach item=elements from=$ROLEINFO.profileinfo}
																								<a href="index.php?module=Settings&action=profilePrivileges&parenttab=Settings&profileid={$elements.0}&mode=view">{$elements.1}</a><br/>
																							{/foreach}
																						</td>
																						<td width="50%" class="dvtCellInfo">
																							{if !empty($ROLEINFO.userinfo.0)}
																								{foreach item=elements from=$ROLEINFO.userinfo}
																									<a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$elements.0}">{$elements.1}</a><br/>
																								{/foreach}
																							{/if}
																						</td>
																					</tr>
																				</table>

																			</td>
																		</tr>
																	</table>
																</div>

															</td>
														</tr>
													</table>

													<table border=0 cellspacing=0 cellpadding=5 width=100% >
														<tr>
															<td class="small" nowrap align=right>
																<a href="#top">{$MOD.LBL_SCROLL}</a>
															</td>
														</tr>
													</table>

												</td>
											</tr>
										</table>

									</form>

					</td></tr></table>
					</td></tr></table>
				</div>
			</td>
		</tr>
	</tbody>
</table>