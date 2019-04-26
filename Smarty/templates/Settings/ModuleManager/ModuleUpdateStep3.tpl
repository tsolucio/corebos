<div id="vtlib_modulemanager_update" style="display:block;position:absolute;width:500px;"></div>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>

	<div align=center>
		{include file='SetMenu.tpl'}

		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" valign="top" width="50" class="cblds-p_none"><img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48"></td>
			<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> &gt; {$MOD.VTLIB_LBL_MODULE_MANAGER} &gt; {$MOD.LBL_UPGRADE} </b></td>
		</tr>

		<tr>
			<td class="small cblds-p-v_none" valign="top">{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</td>
		</tr>
		</table>
		<br>
		<table border="0" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td>
				<div id="vtlib_modulemanager_update_div">
					<form method="POST" action="index.php">
						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr>
							<td class='big' colspan=2><b>{$MOD.VTLIB_LBL_UPDAING_MODULE_START}</b></td>
						</tr>
						</table>
						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr valign=top>
							<td class='cellText small'>{$MODULEUPDATE_INFO}</td>
						</tr>
						</table>

						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr valign=top>
							<td class='cellText small cblds-t-align_right' align=right>
								<input type="hidden" name="module" value="Settings">
								<input type="hidden" name="action" value="ModuleManager">
								<input type="hidden" name="parenttab" value="Settings">
								<input type="submit" class="crmbutton small edit" value="{$APP.LBL_FINISH}">
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
		<td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
	</tr>
</table>
<br>
