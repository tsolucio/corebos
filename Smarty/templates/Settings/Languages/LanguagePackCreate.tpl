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
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
<style type="text/css">@import url(../themes/blue/style.css);</style>

<script language="JavaScript" type="text/javascript">
{literal}
	
	function cancelForm(frm)
	{
		frm.action.value='DetailViewPackages';
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
											<form action="index.php" method="post" name="languagecreate" onsubmit="return check4null(languagecreate);">  
												<input type="hidden" name="action">
												<input type="hidden" name="mode" value="{$EMODE}">
												<input type="hidden" name="module" value="Languages">
												<input type="hidden" name="languageid" value="{$LANGUAGEID}">
												<input type="hidden" name="parenttab" value="{$PARENTTAB}">
												<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
													<tr>
														<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}languages.gif" width="48" height="48" border=0></td>
													{if $EMODE eq 'edit'}
														<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$UMOD.LBL_LANGUAGES_PACKS}</a> &gt; {$MOD.LBL_EDIT} &quot;{$LANGUAGE}&quot; </b></td>
													{else}
														<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$UMOD.LBL_LANGUAGES_PACKS}</a> &gt; {$MOD.LBL_CREATE_PACK} </b></td>
													{/if}
													</tr>
													<tr>
														<td valign=top class="small">{$UMOD.LBL_LANGUAGE_PACK_DESC}</td>
													</tr>
												</table>
												<br>
												<table border=0 cellspacing=0 cellpadding=10 width=100% >
													<tr>
														<td>
															<span style="color:#F00;">{$UMOD.$ERROR}</span>
															<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
																<tr>
																{if $EMODE eq 'edit'}
																	<td class="big"><strong>{$UMOD.LBL_PROPERTIES} &quot;{$LANGUAGE}&quot; </strong></td>
																{else}
																	<td class="big"><strong>{$MOD.LBL_CREATE_PACKAGE}</strong></td>
																{/if}
																	<td class="small" align=right>
																		<input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="this.form.action.value='SavePackage'; this.form.parenttab.value='Settings'" >&nbsp;&nbsp;
																	{if $EMODE eq 'edit'}
																		<input type="submit" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="cancelForm(this.form)" />
																	{else}
																		<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="gotourl('index.php?action=ListPackages&module=Languages&parenttab=Settings')" >
																	{/if}
																	</td>
																</tr>
															</table>
															
															<table border=0 cellspacing=0 cellpadding=5 width=100% >
																<tr>
																	<td width=20% class="small cellLabel"><font color="red">*</font> <strong>{$UMOD.LBL_NAME} :</strong></td>
																	<td width=80% class="small cellText"><input name="lang" type="text" value="{$LANGUAGE}" class="detailedViewTextBox" tabindex="1">&nbsp;</td>
																  </tr>
																<tr>
																	<td valign=top class="small cellLabel"><font color="red">*</font> <strong>{$UMOD.LBL_PREFIX} :</strong></td>
																	<td class="cellText small" valign=top><span class="small cellText">
																	  <input name="prefix" type="text" value="{$PREFIX}" class="detailedViewTextBox" tabindex="2" maxlength="5"> <!-- length <= column size in table-->
																	</span></td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><font color="red">*</font> <strong>{$UMOD.LBL_ENCODING} :</strong></td>
																	<td class="cellText small" valign=top><span class="small cellText">
																	  <input name="encode" type="text" value="{$ENCODING}" class="detailedViewTextBox" tabindex="3">
																	</span></td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_DATECREA} :</strong></td>
																	<td class="cellText small" valign=top><span class="small cellText">
																	  <input name="createddate" type="text" value="{$DATECREATION}" class="detailedViewTextBox" tabindex="4">
																	</span></td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_VERSION} :</strong></td>
																	<td class="cellText small" valign=top><span class="small cellText">
																	  <input name="version" type="text" value="{$VERSION}" class="detailedViewTextBox" tabindex="5">
																	</span></td>
																</tr>
																<tr>
																	<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_OPTIONS} :</strong></td>
																	<td class="cellText small" valign=top>
																		<select class="detailedViewTextBox" name="lockfor" tabindex="6" >
																			{if $LOCKFOR == 0}<option value="0" selected>{else}<option value="0">{/if}{$UMOD.LBL_ALLOWEDIT_ALLOWDELETE}</option>
																			{if $LOCKFOR == 1}<option value="1" selected>{else}<option value="1">{/if}{$UMOD.LBL_ALLOWEDIT_NODELETE}</option>
																			{if $LOCKFOR == 2}<option value="2" selected>{else}<option value="2">{/if}{$UMOD.LBL_NOEDIT_ALLOWDELETE}</option>
																			{if $LOCKFOR == 3}<option value="3" selected>{else}<option value="3">{/if}{$UMOD.LBL_NOEDIT_NODELETE}</option>
																		</select>
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
																							<td width="85%" class="cellText small"><span class="small cellText">
																								<input name="author" type="text" value="{$AUTHOR}" class="detailedViewTextBox" tabindex="7"></span>
																							</td>
																						</tr>
																						<tr>
																							<td valign="top" width=10% class="cellLabel small">{$UMOD.LBL_LICENSE} :</td>
																							<td valign="top" width=60% class="cellText small"><p><textarea name="license" style="width:90%;height:200px" class=small tabindex="8">{$LICENSE}</textarea></p>
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
<script>

function check4null(form)
{ldelim}

        var isError = false;
        var errorMessage = "";
        // Here we decide whether to submit the form.
        if (trim(form.lang.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n {$UMOD.LBL_NAME}";
                form.lang.focus();
        {rdelim}
        if (trim(form.prefix.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n {$UMOD.LBL_PREFIX}";
                form.prefix.focus();
        {rdelim}
        if (trim(form.encode.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n {$UMOD.LBL_ENCODING}";
                form.encode.focus();
        {rdelim}

        // Here we decide whether to submit the form.
        if (isError == true) {ldelim}
                alert("{$UMOD.LBL_MISSING_FIELDS}: " + errorMessage);
                return false;
        {rdelim}
 return true;

{rdelim}
</script>