{literal}
<script type='text/javascript'>
function vtlib_toggleModule(module, action, type) {
	if(typeof(type) == 'undefined') type = '';

	var data = "module=Settings&action=SettingsAjax&file=ModuleManager&module_name=" + encodeURIComponent(module) + "&" + action + "=true" + "&module_type=" + type;

	document.getElementById('status').style.display = "inline";
	jQuery.ajax({
			method:"POST",
			url:"index.php?"+data
	}).done(function(response) {
				document.getElementById('status').style.display = "none";
				// Reload the page to apply the effect of module setting
				window.location.href = 'index.php?module=Settings&action=ModuleManager&parenttab=Settings';
	});
}
</script>
{/literal}

<div id="vtlib_modulemanager" style="display:block;position:absolute;width:500px;"></div>
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
																					<img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.VTLIB_LBL_MODULE_MANAGER}" title="{$MOD.VTLIB_LBL_MODULE_MANAGER}">
																					</span>
																				</div>
																			</span>
																		</div>
																	</div>
																	<div class="slds-media__body">
																		<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																			<span class="uiOutputText">
																				<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> &gt; {$MOD.VTLIB_LBL_MODULE_MANAGER}</b>
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

											<table class="slds-table slds-no-row-hover">
												<tr>
													<td>
														<div id="vtlib_modulemanager_list">
															{include file="Settings/ModuleManager/ModuleManagerAjax.tpl"}
														</div>
														<table border="0" cellpadding="5" cellspacing="0" width="100%">
															<tr>
																<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
															</tr>
														</table>
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