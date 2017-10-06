{literal}
<script type="text/javascript">
function modulemanager_import_validate(form) {
	if(form.module_zipfile.value == '' && form.module_url.value == '') {
		alert("Please select the zip file before proceeding.");
		return false;
	}
	return true;
}
function changeInstallType(obj) {
	if (!obj.checked) {
		return;
	}
	switch (obj.value) {
		case 'file':
			document.form.module_zipfile.disabled = '';
			document.form.module_url.disabled = 'disabled';
			break;
		case 'url':
			document.form.module_url.disabled = '';
			document.form.module_zipfile.disabled = 'disabled';
			break;
	}
}
</script>
{/literal}

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
															<form method="POST" action="index.php" enctype="multipart/form-data" name="form">

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
																										<b>{$MOD.VTLIB_LBL_SELECT_PACKAGE_FILE}</b>
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
																							<span class="slds-radio" style="margin-top: .2rem;">
																								<input type="radio" name="installtype" id="installtypefile" value="file" onclick="changeInstallType(this);">
																								<label class="slds-radio__label" for="module_zipfile" onclick="document.getElementById('installtypefile').checked=true;changeInstallType(document.getElementById('installtypefile'));">
																									<span class="slds-radio--faux"></span>
																								</label>
																								<span class="slds-form-element__label">{$MOD.VTLIB_LBL_FILE_LOCATION}</span>
																							</span>
																						</td>
																						<td class='dvtCellInfo' width="70%">
																							<input type="file" class="small" name="module_zipfile" id="module_zipfile" size=50 disabled>
																							<p>{$MOD.VTLIB_LBL_PACKAGE_FILE_HELP}</p>
																						</td>
																					</tr>
																					<tr valign=top>
																						<td class='dvtCellLabel' width="20%">
																							<span class="slds-radio" style="margin-top: .5rem;">
																								<input type="radio" name="installtype" id="installtypeurl" value="url" onclick="changeInstallType(this);">
																								<label class="slds-radio__label" for="module_zipfile" onclick="document.getElementById('installtypeurl').checked=true;changeInstallType(document.getElementById('installtypeurl'));">
																									<span class="slds-radio--faux"></span>
																								</label>
																								<span class="slds-form-element__label">{$MOD.VTLIB_LBL_PACKAGE_URL}</span>
																							</span>
																						</td>
																						<td class='dvtCellInfo'>
																							<input class="slds-input" style="width: 50%;margin: 0;" name="module_url" size="50" disabled>
																							<p>{$MOD.VTLIB_LBL_PACKAGE_URL_HELP}</p>
																						</td>
																					</tr>
																				</table>
																			</div>

																			<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
																				<tr valign=top>
																					<td class='small' colspan=2 align=right>
																						<input type="hidden" name="module" value="Settings">
																						<input type="hidden" name="action" value="ModuleManager">
																						<input type="hidden" name="module_import" value="Step2">
																						<input type="hidden" name="parenttab" value="Settings">
																						<input type="submit" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="this.form.module_import.value='';">
																						<input type="submit" class="slds-button slds-button--small slds-button_success" value="{$APP.LBL_IMPORT}" onclick="return modulemanager_import_validate(this.form)">
																					</td>
																				</tr>
																			</table>

																		</td>
																	</tr>
																</table>

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
<br>
