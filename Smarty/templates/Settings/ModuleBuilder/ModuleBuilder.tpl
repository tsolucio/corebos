<script src="modules/Settings/ModuleBuilder/ModuleBuilder.js"></script>
{include file="Smarty/templates/Settings/ModuleBuilder/ErrorSave.tpl"}
{include file="Smarty/templates/Settings/ModuleBuilder/ErrorMessage.tpl"}
<div id="vtlib_modulebuilder" style="display:block;position:absolute;width:500px;"></div>
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
			<div align=center>
				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td rowspan="2" valign="top" width="50" class="cblds-p_none">
							<img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.VTLIB_LBL_MODULE_MANAGER}" title="{$MOD.VTLIB_LBL_MODULE_MANAGER}" border="0" height="48" width="48">
						</td>
						<td class="heading2" valign="bottom">
							<b>
								<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> &gt; {$MOD.LBL_MODULE_BUILDER}
							</b>
						</td>
					</tr>
					<tr>
						<td class="small cblds-p-v_none" valign="top">{$MOD.LBL_MODULE_BUILDER_DESCRIPTION}</td>
					</tr>
				</table>
				<br>
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tr>
					<td>
						<div id="vtlib_modulemanager_list">
							<div id="moduleListsModal" style="display: none">
								{include file="Smarty/templates/Settings/ModuleBuilder/modulesList.tpl"}
							</div>
							{include file="Smarty/templates/Settings/ModuleBuilder/Content.tpl"}
						</div>
						<table border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
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