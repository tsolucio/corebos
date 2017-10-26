<div id="vtlib_modulemanager_update" style="display:block;position:absolute;width:500px;"></div>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
					{include file='SetMenu.tpl'}

						<!-- Update/Upgrade Step 2 Content -->
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
																	<img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}"/>
																</span>
															</div>
														</span>
													</div>
												</div>
												<!-- Title and help text -->
												<div class="slds-media__body">
													<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
														<span class="uiOutputText">
															<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> &gt; {$MOD.VTLIB_LBL_MODULE_MANAGER} &gt; {$MOD.LBL_UPGRADE} </b>
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

						<!-- Upgrade methods content -->
						<table class="slds-table slds-no-row-hover">
							<tr>
								<td>

									<div id="vtlib_modulemanager_update_div">
									<!-- Upgrade methods form -->
										<form method="POST" action="index.php">
											<!-- Module upgrade fail -->
											{if $MODULEUPDATE_FAILED neq ''}
												<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
													<!-- Upgrade Fail Title -->
													<tr>
														<td class='dvtCellLabel text-left big' colspan=2><b>{$MOD.VTLIB_LBL_UPDATE_FAILURE}</b></td>
													</tr>
													<!-- Upgrade Fail body content -->
													<tr valign=top>
														<td class='dvtCellInfo small'>
															{if $MODULEUPDATE_FILE_INVALID eq "true"}
																<font color=red><b>{$MOD.VTLIB_LBL_INVALID_FILE}</b></font> {$MOD.VTLIB_LBL_INVALID_IMPORT_TRY_AGAIN}
															{elseif $MODULEUPDATE_NAME_MISMATCH eq "true"}
																<font color=red><b>{$MOD.VTLIB_LBL_MODULENAME_MISMATCH}!</b></font> {$MOD.VTLIB_LBL_TRY_AGAIN}
															{elseif $MODULEUPDATE_SAME_VERSION eq "true"}
																<font color=red><b>{$MOD.VTLIB_LBL_CANNOT_UPGRADE}</b></font> {$MOD.VTLIB_LBL_INST_VERSION} <font color=red><b>{$MODULEUPDATE_CUR_VERSION}</b></font> {$MOD.VTLIB_LBL_MATCHES_PACKAGE_VERSION}
															{else}
																<font color=red>{$MOD.VTLIB_LBL_UNABLE_TO_UPLOAD}</font> {$MOD.VTLIB_LBL_UNABLE_TO_UPLOAD2}
															{/if}
														</td>
													</tr>
													<!-- Finish button -->
													<tr valign=top>
														<td class='dvtCellInfo small' colspan=2 align=right>
															<input type="hidden" name="module" value="Settings">
															<input type="hidden" name="action" value="ModuleManager">
															<input type="hidden" name="parenttab" value="Settings">
															<input type="submit" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_FINISH}">
														</td>
													</tr>
												</table>
											<!-- End Module Upgrade Fail -->
											{else}
											<!-- Start Module Upgrade Success -->
												<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
													<tr class="blockStyleCss">
														<td class="detailViewContainer" valign="top">
															<!-- Upgrade Success Title -->
															<div class="forceRelatedListSingleContainer">
																<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2>
																					<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																						<b>{$MOD.VTLIB_LBL_VERIFY_UPDATE_DETAILS}</b>
																					</span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>

															<!-- Verify Upgrade Details -->
															<div class="slds-truncate">
																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																	<!-- Module Name -->
																	<tr valign=top>
																		<td class='dvtCellLabel small' width=20%>
																			<b>{$MOD.VTLIB_LBL_MODULE_NAME}</b>
																		</td>
																		<td class='dvtCellInfo' width="70%">
																			{$MODULEUPDATE_NAME}
																			{if $MODULEUPDATE_NOT_EXISTS eq 'true'} <font color=red><b>{$MOD.VTLIB_LBL_NOT_PRESENT}</b></font> {/if}
																		</td>
																	</tr>
																	<!-- Module Directory -->
																	{if $MODULEUPDATE_DIR}
																	<tr valign=top>
																		<td class='dvtCellLabel small' width=20%>
																			<b>{$MOD.VTLIB_LBL_MODULE_DIR}</b>
																		</td>
																		<td class='dvtCellInfo' width="70%">
																			{$MODULEUPDATE_DIR}
																			{if $MODULEUPDATE_DIR_NOT_EXISTS eq 'true'} <font color=red><b>{$MOD.VTLIB_LBL_NOT_PRESENT}</b></font> 
																			{* -- Avoiding File Overwrite <br> Overwrite existing files? <input type="checkbox" name="module_dir_overwrite" value="true"> -- *}
																			{/if}
																		</td>
																	</tr>
																	{/if}
																	<!-- Module Version -->
																	<tr valign=top>
																		<td class='dvtCellLabel small' width=20%>
																			<b>{$MOD.VTLIB_LBL_PACKAGE_VERSION}</b>
																		</td>
																		<td class='dvtCellInfo' width="70%">
																			{$MODULEUPDATE_VERSION} {if $MODULEUPDATE_CUR_VERSION neq ''} [<b>{$MOD.VTLIB_LBL_INST_VERSION}</b> {$MODULEUPDATE_CUR_VERSION}]{/if}
																		</td>
																	</tr>
																	<!-- Required Version -->
																	<tr valign=top>
																		<td class='dvtCellLabel small' width=20%>
																			<b>{$MOD.VTLIB_LBL_REQ_VTIGER_VERSION}</b>
																		</td>
																		<td class='dvtCellInfo' width="70%">
																			{$MODULEUPDATE_DEP_VTVERSION}
																		</td>
																	</tr>
																	{assign var="need_license_agreement" value="false"}
																	<!-- License -->
																	{if $MODULEUPDATE_LICENSE}
																		{assign var="need_license_agreement" value="true"}
																		<tr valign=top>
																			<td class='dvtCellLabel small' width=20%>
																				<b>{$MOD.VTLIB_LBL_LICENSE}</b>
																			</td>
																			<td class='dvtCellInfo small'>
																				<textarea readonly class='slds-textarea' style="min-height: 200px;">{$MODULEUPDATE_LICENSE}</textarea><br>
																				<span class="slds-checkbox">
																					<input type="checkbox" id="accept-license">
																					{literal}
																					<label class="slds-checkbox__label" for="accept-license" onclick="if(this.form.yesbutton){if(this.checked){this.form.yesbutton.disabled=false;}else{this.form.yesbutton.disabled=true;}}">
																						<span class="slds-checkbox--faux"></span>
																					</label>
																					{/literal}
																					<span class="slds-form-element__label">{$MOD.VTLIB_LBL_LICENSE_ACCEPT_AGREEMENT}</span>
																				</span>
																			</td>
																		</tr>
																	{/if}
																	<!-- Yes & No Buttons -->
																	<tr valign=top>
																		<td class='dvtCellInfo small' colspan=2 align=right>
																			<input type="hidden" name="module" value="Settings">
																			<input type="hidden" name="action" value="ModuleManager">
																			<input type="hidden" name="parenttab" value="Settings">
																			<input type="hidden" name="module_import_file" value="{$MODULEUPDATE_FILE}">
																			<input type="hidden" name="module_update_type" value="{$MODULEUPDATE_TYPE}">
																			<input type="hidden" name="module_update" value="Step3">
																			<input type="hidden" name="target_modulename" value="{$smarty.request.target_modulename|@vtlib_purify}">
																			<input type="hidden" name="module_import_cancel" value="false">
																			<!-- No button if update not exists -->
																			{if $MODULEUPDATE_NOT_EXISTS eq 'true' || $MODULEUPDATE_DIR_NOT_EXISTS eq 'true'}
																				<input type="submit" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="this.form.module_update.value=''; this.form.module_import_cancel.value='true';">
																			{else}
																			<!-- Yes & No buttons if update OK -->
																				{$MOD.VTLIB_LBL_PROCEED_WITH_UPDATE}
																				<input type="submit" class="slds-button slds-button--small slds-button_success" value="{$MOD.LBL_YES}" {if $need_license_agreement eq 'true'} disabled=true {/if}	name="yesbutton">
																				<input type="submit" class="slds-button--small slds-button slds-button--destructive" value="{$MOD.LBL_NO}" onclick="this.form.module_update.value=''; this.form.module_import_cancel.value='true';">
																			{/if}
																		</td>
																	</tr>
																</table>
															</div>

														</td>
													</tr>
												</table>
												<!-- End Module Upgrade Success -->
											{/if}
										</form>

									</div><!-- /#vtlib_modulemanager_update_div -->
								</td>
							</tr>
						</table>

					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>