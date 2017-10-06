<div id="vtlib_modulemanager_import" style="display:block;position:absolute;width:500px;"></div>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
						{include file='SetMenu.tpl'}

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
																						<img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}"/>
																					</span>
																				</div>
																			</span>
																		</div>
																	</div>
																	<div class="slds-media__body">
																		<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																			<span class="uiOutputText">
																				<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> &gt; {$MOD.VTLIB_LBL_MODULE_MANAGER} &gt; {$APP.LBL_IMPORT} </b>
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

											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td>

														<div id="vtlib_modulemanager_import_div">
															<form method="POST" action="index.php">

																{if $MODULEIMPORT_FAILED neq ''}
																	<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
																		<tr>
																			<td class='big' colspan=2><b>{$MOD.VTLIB_LBL_IMPORT_FAILURE}</b></td>
																		</tr>
																	</table>

																	<table cellpadding=5 cellspacing=0 border=0 width=80%>
																		<tr valign=top>
																			<td class='cellText small'>
																				{if $MODULEIMPORT_FILE_INVALID eq "true"}
																					<font color=red><b>{$MOD.VTLIB_LBL_INVALID_FILE}</b></font> {$MOD.VTLIB_LBL_INVALID_IMPORT_TRY_AGAIN}
																				{else}
																					<font color=red>{$MOD.VTLIB_LBL_UNABLE_TO_UPLOAD}</font> {$MOD.VTLIB_LBL_UNABLE_TO_UPLOAD2}
																				{/if}
																			</td>
																		</tr>
																	</table>

																	<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
																		<tr valign=top>
																			<td class='cellText small' colspan=2 align=right>
																				<input type="hidden" name="module" value="Settings">
																				<input type="hidden" name="action" value="ModuleManager">
																				<input type="hidden" name="parenttab" value="Settings">
																				<input type="submit" class="crmbutton small edit" value="{$APP.LBL_FINISH}">
																			</td>
																		</tr>
																	</table>
																{else}
																	<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
																		<tr class="blockStyleCss">
																			<td class="detailViewContainer" valign="top">
																				<div class="forceRelatedListSingleContainer">
																					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																						<div class="slds-card__header slds-grid">
																							<header class="slds-media slds-media--center slds-has-flexi-truncate">
																								<div class="slds-media__body">
																									<h2>
																										<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																											<b>{$MOD.VTLIB_LBL_VERIFY_IMPORT_DETAILS}</b>
																										</span>
																									</h2>
																								</div>
																							</header>
																						</div>
																					</article>
																				</div>

																				<div class="slds-truncate">
																					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table select-package-table">
																						<tr valign=top>
																							<td class='dvtCellLabel' width="20%">
																								<b>{$MOD.VTLIB_LBL_MODULE_NAME}</b>
																							</td>
																							<td class='dvtCellInfo' width="70%">
																								{$MODULEIMPORT_NAME}
																								{if $MODULEIMPORT_EXISTS eq 'true'} <font color=red><b>{$MOD.VTLIB_LBL_EXISTS}</b></font> {/if}
																							</td>
																						</tr>
																						{if $MODULEIMPORT_DIR}
																							<tr valign=top>
																								<td class='dvtCellLabel' width="20%">
																									<b>{$MOD.VTLIB_LBL_MODULE_DIR}</b>
																								</td>
																								<td class='dvtCellInfo' width="70%">
																									{$MODULEIMPORT_DIR}
																									{if $MODULEIMPORT_DIR_EXISTS eq 'true'} <font color=red><b>{$MOD.VTLIB_LBL_EXISTS}</b></font>
																										{* -- Avoiding File Overwrite
																										 <br> Overwrite existing files? <input type="checkbox" name="module_dir_overwrite" value="true">
																										-- *}
																									{/if}
																								</td>
																							</tr>
																						{/if}
																						<tr valign=top>
																							<td class='dvtCellLabel' width="20%">
																								<b>{$MOD.VTLIB_LBL_REQ_VTIGER_VERSION}</b>
																							</td>
																							<td class='dvtCellInfo' width="70%">
																								{$MODULEIMPORT_DEP_VTVERSION}
																							</td>
																						</tr>

																						{assign var="need_license_agreement" value="false"}
																						{if $MODULEIMPORT_LICENSE}
																							{assign var="need_license_agreement" value="true"}
																							<tr valign=top>
																								<td class='dvtCellLabel' width="20%">
																									<b>{$MOD.VTLIB_LBL_LICENSE}</b>
																								</td>
																								<td class='dvtCellInfo' width="70%">
																									<textarea readonly class='slds-textarea' style="background-color: #F5F5F5; border: 0; height: 250px; font-size:10px;">{$MODULEIMPORT_LICENSE}</textarea><br>
																									{literal}
																									<input type="checkbox" onclick="if(this.form.yesbutton){if(this.checked){this.form.yesbutton.disabled=false;}else{this.form.yesbutton.disabled=true;}}"> {/literal} {$MOD.VTLIB_LBL_LICENSE_ACCEPT_AGREEMENT}
																								</td>
																							</tr>
																						{/if}
																					</table>

																					<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
																						<tr valign=top>
																							<td class='small' colspan=2 align=right>
																								<input type="hidden" name="module" value="Settings">
																								<input type="hidden" name="action" value="ModuleManager">
																								<input type="hidden" name="parenttab" value="Settings">
																								<input type="hidden" name="module_import_file" value="{$MODULEIMPORT_FILE}">
																								<input type="hidden" name="module_import_type" value="{$MODULEIMPORT_TYPE}">
																								<input type="hidden" name="module_import" value="Step3">
																								<input type="hidden" name="module_import_cancel" value="false">

																								{if $MODULEIMPORT_EXISTS eq 'true' || $MODULEIMPORT_DIR_EXISTS eq 'true'}
																									<input type="submit" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="this.form.module_import.value=''; this.form.module_import_cancel.value='true';">
																								{else}
																									{$MOD.VTLIB_LBL_PROCEED_WITH_IMPORT}
																									<input type="submit" class="slds-button slds-button--small slds-button_success" value="{$MOD.LBL_YES}" {if $need_license_agreement eq 'true'} disabled=true {/if} name="yesbutton">
																									<input type="submit" class="cslds-button slds-button--small slds-button--destructive" value="{$MOD.LBL_NO}" onclick="this.form.module_import.value=''; this.form.module_import_cancel.value='true';">
																								{/if}
																							</td>
																						</tr>
																					</table>
																				</div>

																			</td>
																		</tr>
																	</table>

																{/if}

															</form>
														</div>

													</td>
												</tr>
											</table>
											<!-- End of Display -->

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