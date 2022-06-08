<div id="vtlib_modulemanager" style="display:block;position:absolute;width:500px;"></div>
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" valign="top" width="50" class="cblds-p_none">
				<img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.VTLIB_LBL_MODULE_MANAGER}" title="{$MOD.VTLIB_LBL_MODULE_MANAGER}" border="0" height="48" width="48">
			</td>
			<td class="heading2" valign="bottom">
				<b><a href="index.php?module=Settings&action=index">{'LBL_SETTINGS'|@getTranslatedString}</a> &gt; {$MOD.VTLIB_LBL_MODULE_MANAGER}</b>
			</td>
		</tr>
		<tr>
			<td class="small cblds-p-v_none" valign="top">{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</td>
		</tr>
		</table>
		<br>
		<table border="0" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td>
				<div id="vtlib_modulemanager_list">
					{include file="Settings/ModuleManager/ModuleManagerAjax.tpl"}
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
</table>
</div>
</section>