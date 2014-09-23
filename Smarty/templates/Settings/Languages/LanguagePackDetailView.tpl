<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript">
{literal}
	
	function cancelForm(frm)
	{
		frm.action.value='ListPackages';
		frm.parenttab.value='Settings';
		frm.submit();
	}
{/literal}
</script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
			<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
				<br>
				<div align=center>
					{include file='SetMenu.tpl'}
					<!-- table --> <!-- SetMenu.tpl -->
						<!-- tr --> <!-- SetMenu.tpl -->
							<!-- td --> <!-- SetMenu.tpl -->
								<!-- table --> <!-- SetMenu.tpl -->
									<!-- tr --> <!-- SetMenu.tpl -->
										<!-- td --> <!-- SetMenu.tpl -->
											<!-- DISPLAY -->
											<form method="post" action="index.php" name="etemplatedetailview">  
												<input type="hidden" name="action" value="EditPackage">
												<input type="hidden" name="module" value="Languages">
												<input type="hidden" name="templatename" value="{$TEMPLATENAME}">
												<input type="hidden" name="languageid" value="{$LANGUAGEID}">
												<input type="hidden" name="foldername" value="{$FOLDERNAME}">
												<input type="hidden" name="parenttab" value="{$PARENTTAB}">
												<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
													<tr>
														<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}languages.gif" width="48" height="48" border=0></td>
														<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$UMOD.LBL_LANGUAGES_PACKS}</a> &gt; {$MOD.LBL_VIEWING} &quot;{$LANGUAGE}&quot; </b></td>
													</tr>
													<tr>
														<td valign=top class="small">{$UMOD.LBL_LANGUAGE_PACK_DESC}</td>
													</tr>
												</table>
														
												<br>
												<table border=0 cellspacing=0 cellpadding=10 width=100% >
													<tr>
														<td>
														
															<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
																<tr>
																	<td class="big"><strong>{$UMOD.LBL_PROPERTIES} &quot;{$LANGUAGE}&quot; </strong></td>
																	<td class="small" align=right>&nbsp;&nbsp;
																		<input type="submit" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="cancelForm(this.form)" />
																		{if $LANGUAGESPACKEDITOR}
																		<input class="crmButton edit small" type="submit" name="Button" value="{$APP.LBL_EDIT_BUTTON_LABEL}" class="small" onclick="this.form.action.value='EditPackage'; this.form.parenttab.value='Settings'">
																		{/if}
																	</td>
																</tr>
															</table>
															
															<table border=0 cellspacing=0 cellpadding=5 width=100% >
																<tr>
																	<td width=20% class="small cellLabel"><strong>{$UMOD.LBL_NAME} :</strong></td>
																	<td width=80% class="small cellText"><strong>{$LANGUAGE}</strong></td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_VERSION} :</strong></td>
																	<td class="cellText small" valign=top>{$VERSION}</td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_PREFIX} :</strong></td>
																	<td class="cellText small" valign=top>{$PREFIX}</td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_ENCODING} :</strong></td>
																	<td class="cellText small" valign=top>{$ENCODING}</td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_DATECREA} :</strong></td>
																	<td class="cellText small" valign=top>{$DATECREATION}</td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_DATEUPD} :</strong></td>
																	<td class="cellText small" valign=top>{$LASTCHANGE}</td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_OPTIONS} :</strong></td>
																	<td class="cellText small" valign=top>
																		{if $LOCKFOR == 0}{$UMOD.LBL_ALLOWEDIT_ALLOWDELETE}{/if}
																		{if $LOCKFOR == 1}{$UMOD.LBL_ALLOWEDIT_NODELETE}{/if}
																		{if $LOCKFOR == 2}{$UMOD.LBL_NOEDIT_ALLOWDELETE}{/if}
																		{if $LOCKFOR == 3}{$UMOD.LBL_NOEDIT_NODELETE}{/if}
																	</td>
																</tr>
																<tr>
																	<td colspan="2" valign=top class="cellText small">
																		<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="thickBorder">
																			<tr>
																				<td valign=top>
																					<table width="100%"  border="0" cellspacing="0" cellpadding="5" >
																						<tr>
																							<td colspan="2" valign="top" class="small" style="background-color:#cccccc"><strong>{$UMOD.LBL_INFO}</strong></td>
																						</tr>
																						<tr>
																							<td width="15%" valign="top" class="cellLabel small">{$UMOD.LBL_AUTHOR} :</td>
																							<td width="85%" class="cellText small">{$AUTHOR}</td>
																						</tr>
																						<tr>
																							<td valign="top" class="cellLabel small">{$UMOD.LBL_LICENSE} :</td>
																							<td class="cellText small">{$LICENSE}</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
															<br>
															<table border=0 cellspacing=0 cellpadding=5 width=100% >
																<tr>
																	<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</form>
										</td> <!-- SetMenu.tpl -->
									</tr> <!-- SetMenu.tpl -->
								</table> <!-- SetMenu.tpl -->
							</td> <!-- SetMenu.tpl -->
						</tr> <!-- SetMenu.tpl -->
					</table> <!-- SetMenu.tpl -->
				</div>
			</td>
			<td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
		</tr>
	</tbody>
</table>