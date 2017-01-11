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

<table class="small" width="100%" cellpadding=2 cellspacing=0 border=0>
<tr>
	<td class="big tableHeading" colspan=5 width="10%" align="right">
		<form style="display: inline;" action="index.php?module=Settings&action=ModuleManager&module_import=Step1&parenttab=Settings" method="POST">
			<input type="submit" class="crmbutton small create" value='{$APP.LBL_IMPORT} {$APP.LBL_NEW}' title='{$APP.LBL_IMPORT}'>
		</form>
	</td>
</tr>
<td colspan="2" valign="top">
<!-- Standard modules -->
<table border=0 cellspacing=0 cellpadding=3 width=100% class="listRow" id="modmgr_standard">
<tr>
	<td class="big tableHeading" colspan=2 align="center">{$MOD.VTLIB_LBL_MODULE_MANAGER_STANDARDMOD}</td>
	<td class="big tableHeading" colspan=4 width=10% align="center">&nbsp;</td>
</tr>
{foreach key=modulename item=modinfo from=$TOGGLE_MODINFO}
{if $modinfo.customized eq false}
	{assign var="modulelabel" value=$modulename|getTranslatedString:$modulename}
	<tr>
		<!--td class="cellLabel small" width="20px">&nbsp;</td -->
		<td class="cellText small" width="20px"><img src="{'appmodule.jpg'|@vtiger_imageurl:$THEME}" border="0"></td>
		<td class="cellLabel small" {if $modinfo.presence eq 0 && $modinfo.hassettings} onclick="location.href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings';"{/if}>{$modulelabel}</td>
		<td class="cellText small" width="15px" align=center>
			{if $modinfo.presence eq 0}
				<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_disable');"><img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLE} {$modulelabel}" title="{$MOD.LBL_DISABLE} {$modulelabel}"></a>
			{else}
				<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_enable');"><img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE} {$modulelabel}" title="{$MOD.LBL_ENABLE} {$modulelabel}"></a>
			{/if}
		</td>
		<td class="cellText small" width="15px" align=center>&nbsp;</td>
		<td class="cellText small" width="15px" align=center>&nbsp;</td>
		<td class="cellText small" width="15px" align=center>
			{if $modinfo.presence eq 0 && $modinfo.hassettings}<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings"><img src="{'Settings.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}" title="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}"></a>
			{elseif $modinfo.hassettings eq false}&nbsp;
			{/if}
		</td>
	</tr>
{/if}
{/foreach}
<tr>
	<td class="big" colspan=5>&nbsp;</td>
</tr>
<tr>
	<td class="big tableHeading" colspan=2 align="center">{$MOD.LBL_LANGUAGES_PACKS}</td>
	<td class="big tableHeading" colspan=4 width=10% align="center">&nbsp;</td>
</tr>
{assign var="totalCustomModules" value=0}
{foreach key=langprefix item=langinfo from=$TOGGLE_LANGINFO}
	{assign var="totalCustomModules" value=$totalCustomModules+1}
	<tr>
		<td class="cellText small"><img src="{'text.gif'|@vtiger_imageurl:$THEME}" border=0"></td>
		<td class="cellLabel small" onclick="location.href='index.php?module=Settings&action=LanguageEdit&parenttab=Settings&languageid={$langinfo.id}';">{$langinfo.label}</td>
		<td class="cellText small" width="15px" align=center>
			<a href="index.php?module=Settings&action=ModuleManager&module_update=Step1&src_module={$langprefix}&parenttab=Settings"><img src="{'reload.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_UPGRADE} {$langinfo.label}" title="{$MOD.LBL_UPGRADE} {$langinfo.label}"></a>
		</td>
		<td class="cellText small" width="15px" align=center>
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
		<td class="cellText small" width="15px" align=center>
			<a href="index.php?modules=Settings&action=ModuleManagerExport&module_export={$langprefix}"><img src="themes/images/webmail_uparrow.gif" border="0" align="absmiddle" alt="{$APP.LBL_EXPORT} {$langinfo.label}" title="{$APP.LBL_EXPORT} {$langinfo.label}"></a>
		</td>
		<td class="cellText small" width="10px" align=left>
			<a href="index.php?module=Settings&action=LanguageEdit&parenttab=Settings&languageid={$langinfo.id}"><img src="{'Settings.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$langinfo.label} {'LBL_SETTINGS'|@getTranslatedString}" title="{$langinfo.label} {'LBL_SETTINGS'|@getTranslatedString}"></a>
		</td>
	</tr>
{/foreach}

</table>

</td><td></td><td colspan="2" valign="top">

<!-- Custom Modules -->
<table border=0 cellspacing=0 cellpadding=3 width=100% class="listRow" id="modmgr_custom">
<tr>
	<td class="big tableHeading" colspan=2 align="center">{$MOD.VTLIB_LBL_MODULE_MANAGER_CUSTOMMOD}</td>
	<td class="big tableHeading" colspan=4 align="center">&nbsp;</td>
</tr>

{assign var="totalCustomModules" value=0}

{foreach key=modulename item=modinfo from=$TOGGLE_MODINFO}
{if $modinfo.customized eq true}
	{assign var="totalCustomModules" value=$totalCustomModules+1}

	{assign var="modulelabel" value=$modulename|getTranslatedString:$modulename}
	<tr height="30px">
		<td class="cellText small" width="20px"><img src="{'uparrow.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
		<td class="cellLabel small"{if $modinfo.presence eq 0 && $modinfo.hassettings} onclick="location.href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings';"{/if}>{$modulelabel}</td>
		<td class="cellText small" width="15px" align=center>
			<a href="index.php?module=Settings&action=ModuleManager&module_update=Step1&src_module={$modulename}&parenttab=Settings"><img src="{'reload.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_UPGRADE} {$modulelabel}" title="{$MOD.LBL_UPGRADE} {$modulelabel}"></a>
		</td>
		<td class="cellText small" width="15px" align=center>
		{if $modinfo.presence eq 0}
			<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_disable');"><img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLE} {$modulelabel}" title="{$MOD.LBL_DISABLE} {$modulelabel}"></a>
		{else}
			<a href="javascript:void(0);" onclick="vtlib_toggleModule('{$modulename}', 'module_enable');"><img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE} {$modulelabel}" title="{$MOD.LBL_ENABLE} {$modulelabel}"></a>
		{/if}
		</td>
		<td class="cellText small" width="15px" align=center>
			{if $modulename eq 'Calendar' || $modulename eq 'Home'}
				<img src="{'menuDnArrow.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle">
			{else}
				<a href="index.php?modules=Settings&action=ModuleManagerExport&module_export={$modulename}"><img src="{'webmail_uparrow.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$APP.LBL_EXPORT} {$modulelabel}" title="{$APP.LBL_EXPORT} {$modulelabel}"></a>
			{/if}
		</td>
		<td class="cellText small" width="15px" align=center>
			{if $modinfo.presence eq 0 && $modinfo.hassettings}<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$modulename}&parenttab=Settings"><img src="{'Settings.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}" title="{$modulelabel} {'LBL_SETTINGS'|@getTranslatedString}"></a>
			{elseif $modinfo.hassettings eq false}&nbsp;
			{/if}
		</td>
	</tr>
{/if}
{/foreach}
{if $totalCustomModules eq 0}
	<tr>
		<td class="cellLabel small" colspan=4><b>{$MOD.VTLIB_LBL_MODULE_MANAGER_NOMODULES}</b></td>
	</tr>
{/if}
</table>
</td></tr></table>
