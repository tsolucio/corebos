<div id="vtlib_modulemanager_update" style="display:block;position:absolute;width:500px;"></div>
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

						<!-- Upgrade Step 3 body container -->
						<table class="slds-table slds-no-row-hover">
							<tr>
								<td>
									<div id="vtlib_modulemanager_update_div">
									<!-- Upgrade step 3 form -->
										<form method="POST" action="index.php">
											<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
												<!-- Upgrade Title -->
												<tr>
													<td class='dvtCellLabel text-left big' colspan=2><b>{$MOD.VTLIB_LBL_UPDAING_MODULE_START}</b></td>
												</tr>
												<!-- Upgrade info section -->
												<tr valign=top>
													<td class='dvtCellInfo small'>{$MODULEUPDATE_INFO}</td>
												</tr>
												<!-- Finish button -->
												<tr valign=top>
													<td class='dvtCellInfo small' align=right>
														<input type="hidden" name="module" value="Settings">
														<input type="hidden" name="action" value="ModuleManager">
														<input type="hidden" name="parenttab" value="Settings">
														<input type="submit" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_FINISH}">
													</td>
												</tr>
											</table>
										</form>

									</div>
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