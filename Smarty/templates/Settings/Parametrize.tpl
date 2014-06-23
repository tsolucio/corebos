{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the Evolutivo BPM License Version 1.0
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
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
	<form action="index.php" method="post" name="parametrize" onsubmit="VtigerJS_DialogBox.block();">
    	<input type="hidden" name="module" value="Settings">
    	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="action">
			{include file="SetMenu.tpl"}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'personalize.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PARAMETRIZE}" width="48" height="48" border=0 title="{$MOD.LBL_PARAMETRIZE}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_PARAMETRIZE} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_PARAMETRIZE_DESCRIPTION} </td>
				</tr>
				</table>

				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>

					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_LOGO_DETAILS} </strong></td>
						<td class="small" align=right>
							<input class="crmButton small edit" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='EditParametrize'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
						</td>
					</tr>
					</table>

					<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
					<tr>
						<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                          <tr valign="top">
                            <td class="small cellLabel" width="20%"><strong>Login Logo</strong></td>
                            <td class="small cellText" width="80%" style="background-image: url(themes/login/images/{$LOGO_LOGIN}); background-position: left; background-repeat: no-repeat;" width="48" height="48" border="0"></td>
                          </tr>
                          <tr valign="top">
                            <td class="small cellLabel" ><strong>Top Logo</strong></td>
                            <td class="small cellText" style="background-image: url(themes/login/images/{$LOGO_TOP}); background-position: left; background-repeat: no-repeat;" width="48" height="48" border="0"></td>
                          </tr>

                        </table>

						</td>
					  </tr>
					</table>
					<!--table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
					</tr>
					</table-->
				</td>
				</tr>
				</table>



			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	</form>
	</div>
</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>