{if $DIR_NOTWRITABLE_LIST && !empty($DIR_NOTWRITABLE_LIST)}
<table class="small" width="100%" cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td>
			<div style='background-color: #FFFABF; padding: 2px; margin: 0 0 2px 0; border: 1px solid yellow'>
			<b style='color: red'>{$MOD.VTLIB_LBL_WARNING}:</b> {$DIR_NOTWRITABLE_LIST|@implode:', '} <b>{$MOD.VTLIB_LBL_NOT_WRITEABLE}!</b>
		</td>
	</tr>
</table>
{/if}

<div class="forceRelatedListSingleContainer">
	<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
		<div class="slds-card__header slds-grid">
			<header class="slds-media slds-media--center slds-has-flexi-truncate">
				<div class="slds-media__body">
					<h2><span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">&nbsp;</span></h2>
				</div>
			</header>
			<div class="slds-no-flex">
				<div class="actionsContainer">
					<form style="display: inline;" action="index.php?module=Settings&action=ModuleManager&module_import=Step1&parenttab=Settings" method="POST">
						<input type="submit" class="slds-button slds-button--small slds-button_success" value='{$APP.LBL_IMPORT} {$APP.LBL_NEW}' title='{$APP.LBL_IMPORT}'>
					</form>
				</div>
			</div>
		</div>
	</article>
</div>

<table class="slds-table slds-no-row-hover">
	<tr>
		<td colspan="2" valign="top">
			<!-- Standard modules -->
			<table class="slds-table slds-table--bordered slds-no-row-hover listRow" id="modmgr_standard">
				<thead>
					<tr>
						<th class="slds-text-title--caps" colspan="6"><span class="slds-truncate">{$MOD.VTLIB_LBL_MODULE_MANAGER_STANDARDMOD}</span></th>
					</tr>
				</thead>
			{foreach key=modulename item=modinfo from=$TOGGLE_MODINFO}
				{if $modinfo.customized eq false}
				{assign var="modulelabel" value=$modulename|getTranslatedString:$modulename}
					<tr class="slds-hint-parent slds-line-height--reset">
						<!--td class="cellLabel small" width="20px">&nbsp;</td -->
						<td class="dvtCellInfo small" width="20px"><img src="{'appmodule.jpg'|@vtiger_imageurl:$THEME}" border="0"></td>
						<td class="dvtCellLabel text-left" {if $modinfo.presence eq 0 && $modinfo.hassettings} onclick="location.href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings';"{/if}>{$modulelabel}</td>
						<td class="dvtCellInfo small" width="15px" align=center>&nbsp;</td>
						<td class="dvtCellInfo small" width="15px" align=center>
							{if $modinfo.presence eq 0}
								<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_disable');"><img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLE} {$modulelabel}" title="{$MOD.LBL_DISABLE} {$modulelabel}"></a>
							{else}
								<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_enable');"><img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE} {$modulelabel}" title="{$MOD.LBL_ENABLE} {$modulelabel}"></a>
							{/if}
						</td>
						<td class="dvtCellInfo small" width="15px" align=center>&nbsp;</td>
						<td class="dvtCellInfo small" width="15px" align=center>
							{if $modinfo.presence eq 0 && $modinfo.hassettings}<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings"><img src="{'Settings.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}" title="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}"></a>
							{elseif $modinfo.hassettings eq false}&nbsp;
							{/if}
						</td>
					</tr>
				{/if}
			{/foreach}
			<tr>
				<th class="slds-text-title--caps slds-text-align--center" colspan="6" style="padding-top: 2.5rem;"><span class="slds-truncate">{$MOD.LBL_LANGUAGES_PACKS}</span>
			</tr>
			{assign var="totalCustomModules" value=0}
			{foreach key=langprefix item=langinfo from=$TOGGLE_LANGINFO}
				{assign var="totalCustomModules" value=$totalCustomModules+1}
				<tr>
					<td class="dvtCellInfo small"><img src="{'text.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
					<td class="dvtCellLabel text-left" onclick="location.href='index.php?module=Settings&action=LanguageEdit&parenttab=Settings&languageid={$langinfo.id}';">{$langinfo.label}</td>
					<td class="dvtCellInfo small" width="15px" align=center>
						<a href="index.php?module=Settings&action=ModuleManager&module_update=Step1&src_module={$langprefix}&parenttab=Settings"><img src="{'reload.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_UPGRADE} {$langinfo.label}" title="{$MOD.LBL_UPGRADE} {$langinfo.label}"></a>
					</td>
					<td class="dvtCellInfo small" width="15px" align=center>
					{if $langprefix neq 'en_us'}
					{if $langinfo.active eq 1}
						<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$langprefix}', 'module_disable', 'language');"><img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLE} {$MOD.Language} {$langinfo.label}" title="{$MOD.LBL_DISABLE} {$MOD.Language} {$langinfo.label}"></a>
					{else}
						<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$langprefix}', 'module_enable', 'language');"><img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE} {$MOD.Language} {$langinfo.label}" title="{$MOD.LBL_ENABLE} {$MOD.Language} {$langinfo.label}"></a>
					{/if}
					{else}
						<img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.Language} {$langinfo.label} {$MOD.LBL_ENABLE}" title="{$MOD.Language} {$langinfo.label} {$MOD.AlwaysActive}">
					{/if}
					</td>
					<td class="dvtCellInfo small" width="15px" align=center>
						<a href="index.php?modules=Settings&action=ModuleManagerExport&module_export={$langprefix}"><img src="themes/images/webmail_uparrow.gif" border="0" align="absmiddle" alt="{$APP.LBL_EXPORT} {$langinfo.label}" title="{$APP.LBL_EXPORT} {$langinfo.label}"></a>
					</td>
					<td class="dvtCellInfo small" width="10px" align=left>
						<a href="index.php?module=Settings&action=LanguageEdit&parenttab=Settings&languageid={$langinfo.id}"><img src="{'Settings.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$langinfo.label} {'LBL_SETTINGS'|@getTranslatedString}" title="{$langinfo.label} {'LBL_SETTINGS'|@getTranslatedString}"></a>
					</td>
				</tr>
			{/foreach}

			</table>

</td><td></td><td colspan="2" valign="top">

<!-- Custom Modules -->
<table class="slds-table slds-no-row-hover slds-table--bordered listRow" id="modmgr_custom">
	<tr>
		<th class="slds-text-title--caps slds-text-align--center" colspan="6"><span class="slds-truncate">{$MOD.VTLIB_LBL_MODULE_MANAGER_CUSTOMMOD}</span></th>
	</tr>

{assign var="totalCustomModules" value=0}

{foreach key=modulename item=modinfo from=$TOGGLE_MODINFO}
{if $modinfo.customized eq true}
	{assign var="totalCustomModules" value=$totalCustomModules+1}

	{assign var="modulelabel" value=$modulename|getTranslatedString:$modulename}
	<tr height="30px">
		<td class="dvtCellInfo small" width="20px"><img src="{'uparrow.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
		<td class="dvtCellLabel text-left"{if $modinfo.presence eq 0 && $modinfo.hassettings} onclick="location.href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings';"{/if}>{$modulelabel}</td>
		<td class="dvtCellInfo small" width="15px" align=center>
			<a href="index.php?module=Settings&action=ModuleManager&module_update=Step1&src_module={$modulename}&parenttab=Settings"><img src="{'reload.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_UPGRADE} {$modulelabel}" title="{$MOD.LBL_UPGRADE} {$modulelabel}"></a>
		</td>
		<td class="dvtCellInfo small" width="15px" align=center>
		{if $modinfo.presence eq 0}
			<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_disable');"><img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLE} {$modulelabel}" title="{$MOD.LBL_DISABLE} {$modulelabel}"></a>
		{else}
			<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_enable');"><img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE} {$modulelabel}" title="{$MOD.LBL_ENABLE} {$modulelabel}"></a>
		{/if}
		</td>
		<td class="dvtCellInfo small" width="15px" align=center>
			{if $modulename eq 'Calendar' || $modulename eq 'Home'}
				<img src="{'menuDnArrow.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle">
			{else}
				<a href="index.php?modules=Settings&action=ModuleManagerExport&module_export={$modulename}"><img src="{'webmail_uparrow.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$APP.LBL_EXPORT} {$modulelabel}" title="{$APP.LBL_EXPORT} {$modulelabel}"></a>
			{/if}
		</td>
		<td class="dvtCellInfo small" width="15px" align=center>
			{if $modinfo.presence eq 0 && $modinfo.hassettings}<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings"><img src="{'Settings.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}" title="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}"></a>
			{elseif $modinfo.hassettings eq false}&nbsp;
			{/if}
		</td>
	</tr>
{/if}
{/foreach}
{if $totalCustomModules eq 0}
	<tr>
		<td class="dvtCellLabel small" colspan=4><b>{$MOD.VTLIB_LBL_MODULE_MANAGER_NOMODULES}</b></td>
	</tr>
{/if}
</table>
</td></tr></table>
