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
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
						{include file='SetMenu.tpl'}

							<!-- Update/Upgrade Step 3 Header Content -->
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
																		<img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.VTLIB_LBL_MODULE_MANAGER}" title="{$MOD.VTLIB_LBL_MODULE_MANAGER}">
																	</span>
																</div>
															</span>
														</div>
													</div>
													<!-- Title and help text -->
													<div class="slds-media__body">
														<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
															<span class="uiOutputText">
																<b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> &gt; {$MODULE_LBL}</b>
															</span>
															<span class="small">{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</span>
														</h1>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</table>

							<table class="slds-table slds-no-row-hover slds-table--fixed-layout moduleManager-settings">
								<tr class="slds-line-height--reset">
									{foreach key=mod_name item=mod_array from=$MENU_ARRAY name=itr}
										<td width=25% valign=top>
											{if empty($mod_array.label)}
												&nbsp;
											{else}
												<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 100px; min-width: 300px;">
													<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
														<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
															<table border=0 cellspacing=0 cellpadding=5 width=100%>
																<tr class="row-2nd-css">
																	{assign var=count value=$smarty.foreach.itr.iteration}
																	<td rowspan=2 valign=top style="width: 72px;">
																		<div class="profilePicWrapper slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																			<div class="slds-media__figure slds-icon forceEntityIcon">
																				<span class="photoContainer forceSocialPhoto">
																					<div class="small roundedSquare forceEntityIcon">
																						<span class="uiImage">
																							<a href="{$mod_array.location}">
																								<img src="{$mod_array.image_src}" alt="{$mod_array.label}" title="{$mod_array.label}">
																							</a>
																						</span>
																					</div>
																				</span>
																			</div>
																		</div>
																	</td>
																	<td class=big valign=top>
																		<div class="slds-media__body">
																			<h2>
																				<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="{$mod_array.label}" style="white-space: normal;">
																					<a href="{$mod_array.location}">{$mod_array.label}</a>
																				</span>
																			</h2>
																		</div>
																	</td>
																</tr>
																<tr>
																	<td class="small" valign=top style="white-space: normal;">
																		<span class="small">{$mod_array.desc}</span>
																	</td>
																</tr>
															</table>
														</div>
													</div>
												</div>
											{/if}
										</td>
										{if $count mod 3 eq 0}
											</tr><tr class="second-row">
										{/if}
									{/foreach}
								</tr>
							</table>

					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>