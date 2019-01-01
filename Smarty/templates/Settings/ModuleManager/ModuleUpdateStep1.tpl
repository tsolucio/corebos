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
                	<form method="POST" action="index.php" enctype="multipart/form-data" name="form">
						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr>
							<td class='big' colspan=2><b>{$MOD.VTLIB_LBL_SELECT_PACKAGE_FILE}</b></td>
						</tr>
						</table>
						<table cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr valign=top>
							<td class='cellLabel small'>
								<input type="radio" name="installtype" id="installtypefile" value="file" onclick="changeInstallType(this);"> <b><label for="module_zipfile" onclick="document.getElementById('installtypefile').checked=true;changeInstallType(document.getElementById('installtypefile'));">{$MOD.VTLIB_LBL_FILE_LOCATION}</label></b>
							</td>
							<td class='cellText small'>
								<input type="file" class="small" name="module_zipfile" id="module_zipfile" size=50 disabled>
								<p>{$MOD.VTLIB_LBL_PACKAGE_FILE_HELP}</p>
							</td>
						</tr>
						<tr valign=top>
							<td class='cellLabel small'>
								<input type="radio" name="installtype" id="installtypeurl" value="url" onclick="changeInstallType(this);"> <b><label for="module_zipfile" onclick="document.getElementById('installtypeurl').checked=true;changeInstallType(document.getElementById('installtypeurl'));">{$MOD.VTLIB_LBL_PACKAGE_URL}</label></b>
							</td>
							<td class='cellText small'>
								<input class="small" name="module_url" size="50" disabled>
								<p>{$MOD.VTLIB_LBL_PACKAGE_URL_HELP}</p>
							</td>
						</tr>
						</table>
						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr valign=top>
							<td class='cellText small cblds-t-align_right' colspan=2 align=right>
								<input type="hidden" name="module" value="Settings">
								<input type="hidden" name="action" value="ModuleManager">
								<input type="hidden" name="module_update" value="Step2">
								<input type="hidden" name="parenttab" value="Settings">
								<input type="hidden" name="target_modulename" value="{$smarty.request.src_module|@vtlib_purify}">
								
								<input type="submit" class="crmbutton small edit" value="{$MOD.LBL_UPGRADE}" onclick="return modulemanager_update_validate(this.form)">
								<input type="submit" class="crmbutton small delete" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" 
												onclick="this.form.module_update.value='';">
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
