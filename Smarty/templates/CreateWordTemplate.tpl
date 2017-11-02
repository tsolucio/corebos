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
							<!-- DISPLAY Mail Merge New Template-->
							<form action="index.php?module=Settings&action=savewordtemplate" method="post" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
								<input type="hidden" name="return_module" value="Settings">
								<input type="hidden" name="parenttab" value="{$PARENTTAB}">
								<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE}">
								<input type="hidden" name="action">

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
																			<img src="{'mailmarge.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" title="{$MOD.LBL_MODULE_NAME}">
																		</span>
																	</div>
																</span>
															</div>
														</div>
														<!-- Title and help text -->
														<div class="slds-media__body">
															<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																<span class="uiOutputText">
																	<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=listwordtemplates&parenttab=Settings">{$UMOD.LBL_WORD_TEMPLATES}</a> > {$UMOD.LBL_NEW_TEMPLATE} </b>
																</span>
																<span class="small">{$MOD.LBL_MAIL_MERGE_DESC}</span>
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
																					<strong>{$UMOD.LBL_NEW_TEMPLATE}</strong><br>{$ERRORFLAG}
																				</span>
																			</h2>
																		</div>
																	</header>
																	<div class="slds-no-flex">
																		<input class="slds-button slds-button--small slds-button--destructive" title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" type="button" tabindex="5" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="window.history.back();" />
																		&nbsp;
																		<input class="slds-button slds-button--small slds-button_success" title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" type="submit" tabindex="4" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='savewordtemplate'; this.form.parenttab.value='Settings'" />
																	</div>
																</div>
															</article>
														</div>
														<div class="slds-truncate">
															<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																<tr valign="top">
																	<td nowrap class="dvtCellLabel small" width="20%"><font color="red">*</font><strong>{$APP.LBL_NEW} {$UMOD.LBL_TEMPLATE_FILE}</strong></td>
																	<td class="dvtCellInfo small">
																		<strong>
																			<input type="file" name="binFile" class="small" onchange="validateFilename(this);" />
																			<input type="hidden" name="binFile_hidden" value="" />
																		</strong>
																	</td>
																</tr>
																<tr>
																	<td class="small dvtCellLabel" width="20%"><strong>{$UMOD.LBL_DESCRIPTION}</strong></td>
																	<td class="dvtCellInfo small" valign=top><textarea name="txtDescription" class="slds-textarea">{if isset($smarty.request.description)}{$smarty.request.description|@vtlib_purify}{/if}</textarea></td>
																</tr>
																<tr>
																	<td valign=top class="small dvtCellLabel" width="20%"><strong>{$UMOD.LBL_MODULENAMES}</strong></td>
																	<td class="dvtCellInfo small" valign=top>
																	<select name="target_module" size=1 class="slds-select" tabindex="3">
																		<option value="Leads" {$LEADS_SELECTED}>{$APP.COMBO_LEADS}</option>
																		<option value="Accounts" {$ACCOUNTS_SELECTED}>{$APP.COMBO_ACCOUNTS}</option>
																		<option value="Contacts" {$CONTACTS_SELECTED}>{$APP.COMBO_CONTACTS}</option>
																		<option value="HelpDesk" {$HELPDESK_SELECTED}>{$APP.COMBO_HELPDESK}</option>
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
													<td class="small" nowrap align=right><a href="#top">{$APP.LBL_SCROLL}</a></td>
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