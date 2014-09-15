{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
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
											<form  action="index.php" name="langpacklist" enctype="multipart/form-data" method="POST">
								    			<input name="idlist" type="hidden">
							    				<input name="module" type="hidden" value="Languages">
							    				<input name="action" type="hidden" value="DeletePackage">
												<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
													<tr>
														<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}languages.gif" width="48" height="48" border=0></td>
														<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$UMOD.LBL_LANGUAGES_PACKS} </b></td>
													</tr>
													<tr>
														<td valign=top class="small">{$UMOD.LBL_LANGUAGES_PACKS_DESC}</td>
													</tr>
												</table>
											
												<br>
												<table border=0 cellspacing=0 cellpadding=10 width=100% >
													<tr>
														<td>
														
															<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
															<tr>
																<td class="big"><strong>{$UMOD.LBL_LANGUAGES_PACKS}</strong></td>
																<td class="small" align="right"><div><span style="color:#269">{$UMOD.$SUCCESS}</span></div><div><span style="color:#F00">{$UMOD.$ERROR}</span></div></td>
															</tr>
															</table>
															
															<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
																<tr>
																	<td class=small>
																	{if $LANGUAGESPACKEDITOR}
																		<input type="submit" value="{$UMOD.LBL_CREATE}" onclick="this.form.action.value='CreatePackage';this.form.parenttab.value='Settings';" class="crmButton create small">
																	{/if}
																	</td>
																	<td class="small" align="right">
																	{if $LANGUAGESPACKUPLOAD}
																		<input type="file" name="newpack">
																		<input type="submit" value="{$UMOD.LBL_SEND}" onclick="this.form.action.value='redirectInstallPackage';this.form.parenttab.value='Settings';" class="crmButton create small">
																	{/if}
																	</td>
																</tr>
															</table>
															<table border="0" cellspacing="0" cellpadding="5" width="100%" class="listTable">
																<tr>
																	<td class="colHeader small">&nbsp;</td>
																	<td class="colHeader small">{$UMOD.LBL_LANGUAGES}</td>
																	<td class="colHeader small">{$UMOD.LBL_AUTHOR}</td>
																	<td class="colHeader small">{$UMOD.LBL_CREATEDDATE}</td>
																	<td class="colHeader small">{$UMOD.LBL_ACTIONS}</td>
																</tr>
															{foreach name=languagepacktemplate item=template from=$TEMPLATES}
																<tr>
																	<td class="listTableRow small" valign=top>
																		{if $template.prefix == $default_language}<img src="{$IMAGE_PATH}default.png" alt="default">{/if}
																		{if $template.prefix == $current_language}<img src="{$IMAGE_PATH}current.png" alt="current">{/if}
																	</td>
																	<td class="listTableRow small">
																		<a href="index.php?module=Languages&action=DetailViewPackages&parenttab=Settings&languageid={$template.languageid}" ><b>{$template.language}</b></a>
																	</td>
																	<td class="listTableRow small">{$template.author}</td>
																	<td class="listTableRow small" align="center">{$template.createddate}</td>
																	<td class="listTableRow small">
																	{if $LANGUAGESPACKEDITOR}
																		{if $template.lockfor != 2 && $template.lockfor != 3} <!-- Edit Allowed -->
																		<a href="index.php?module=Languages&action=LanguageEdit&parenttab=Settings&languageid={$template.languageid}"><img src="{$IMAGE_PATH}translate.png" alt="{$UMOD.LNK_EDIT}" title="{$UMOD.LNK_EDIT}" border="0"></a>
																		{else}
																		<img src="{$IMAGE_PATH}blank16x16.png" alt="" title="" border="0">
																		{/if}
																	{/if}
																		<a href="index.php?module=Languages&action=downloadPackage&parenttab=Settings&languageid={$template.languageid}"><img src="{$IMAGE_PATH}download.png" alt="{$UMOD.LNK_MAKEPACKAGE}" title="{$UMOD.LNK_MAKEPACKAGE}" border="0"></a>
																	{if $template.prefix != $default_language && $template.prefix != $current_language && $template.lockfor != 1 && $template.lockfor != 3} <!-- Delete Allowed -->
																		<a href="index.php?module=Languages&action=DeletePackage&return_module=Languages&return_action=ListPackages&languageid={$template.languageid}"><img src="{$IMAGE_PATH}delete.png" alt="{$UMOD.LBL_DELETE}" title="{$UMOD.LBL_DELETE}" border="0"></a>
																	{else}
																		<img src="{$IMAGE_PATH}blank16x16.png" alt="" title="" border="0">
																	{/if}
																	{if $template.prefix != $default_language && $CONFIG_INC_W == true}
																		<a href="index.php?module=Languages&action=redirectSetDefaultPackage&return_module=Languages&return_action=ListPackages&languageid={$template.languageid}"><img src="{$IMAGE_PATH}default.png" alt="{$UMOD.LBL_SETDEFAULT}" title="{$UMOD.LBL_SETDEFAULT}" border="0"></a>
																	{else}
																		<img src="{$IMAGE_PATH}blank16x16.png" alt="" title="" border="0">
																	{/if}
																	{if $template.prefix != $current_language}
																		<a href="index.php?module=Languages&action=redirectSetCurrentPackage&return_module=Languages&return_action=ListPackages&languageid={$template.languageid}"><img src="{$IMAGE_PATH}current.png" alt="{$UMOD.LBL_SETCURRENT}" title="{$UMOD.LBL_SETCURRENT}" border="0"></a>
																	{else}
																		<img src="{$IMAGE_PATH}blank16x16.png" alt="" title="" border="0">
																	{/if}
																	</td>
																</tr>
															{/foreach}	
															</table>
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