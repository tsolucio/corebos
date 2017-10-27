<script type="text/javascript" src="modules/Tooltip/TooltipSettings.js"></script>
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
																		<img src="{'quickview.png'|@vtiger_imageurl:$THEME}" alt="{$MOD.Tooltip}" title="{$MOD.Tooltip}">
																		</span>
																	</div>
																</span>
															</div>
														</div>
														<div class="slds-media__body">
															<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																<span class="uiOutputText">
																	<b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{'VTLIB_LBL_MODULE_MANAGER'|@getTranslatedString:'Settings'}</a> > <a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$FORMODULE}&parenttab=Settings">{$FORMODULE|@getTranslatedString:$FORMODULE}</a> > {$MOD.LBL_TOOLTIP_MANAGEMENT} </b>
																</span>
																<span class="small">{$MOD.LBL_TOOLTIP_MANAGEMENT_DESCRIPTION}</span>
															</h1>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
								</table>

								<input type="hidden" id="pick_module" value="{$MODULE}">
								<table border="0" cellpadding="10" cellspacing="0" width="100%">
									<tbody>
										<tr>
											<td>

												<table class="slds-table slds-no-row-hover">
													<tr>
														<td class="dvtCellLabel" width='20%'>
															<strong><span id="field_info">{$APP.LBL_SELECT} {$MOD.LBL_FIELD}: </span></strong>
														</td>
														<td class="dvtCellInfo" id='pick_field_list'>{$FIELDNAMES}</td>
													</tr>
												</table>

												<div id="fieldList">
												</div>

											</td>
										</tr>
									</tbody>
								</table>

					</td></tr></table>
					</td></tr></table>

				</div>
			</td>
		</tr>
	</tbody>
</table>