{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/ *}
<script type="text/javascript">
	var LBL_CUSTOM_FILED_IN = '{$MOD.LBL_CUSTOM_FILED_IN}';
	var LBL_MODULE = '{$APP.LBL_MODULE}';
</script>
<script type="text/javascript" src="include/js/customview.js"></script>
<script type="text/javascript" src="include/js/relatedlists.js"></script>
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div id="layoutblock" class="slds-modal__container slds-p-around_none">
<div id="relatedlistdiv" style="display:none; position: absolute; width: 225px; left: 300px; top: 300px;"></div>
{assign var=entries value=$CFENTRIES}
{if $CFENTRIES.0.tabpresence eq '0' }
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
		<br>
			<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td rowspan="2" valign="top" width="50"><img src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" alt="Users" title="Users" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom">
						<b><a href="index.php?module=Settings&action=ModuleManager">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a>
						&gt;&nbsp;<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}">{$MODULE|@getTranslatedString:$MODULE}</a> &gt;
						{$MOD.LBL_LAYOUT_EDITOR}</b>
					</td>
				</tr>
				<tr>
					<td class="small" valign="top">{$MOD.LBL_LAYOUT_EDITOR_DESCRIPTION}
					</td>
					<td align="right" class="cblds-t-align_right" width="15%"><input type="button" class="crmButton create small" onclick="callRelatedList('{$CFENTRIES.0.module}');fnvshNrm('relatedlistdiv');posLay(this,'relatedlistdiv');" alt="{$MOD.ARRANGE_RELATEDLIST}" title="{$MOD.ARRANGE_RELATEDLIST}" value="{$MOD.ARRANGE_RELATEDLIST}"/>
					</td>
					<td align="right" class="cblds-t-align_right" width="8%"><input type="button" class="crmButton create small" onclick="fnvshobj(this,'addblock');" alt="{$MOD.ADD_BLOCK}" title="{$MOD.ADD_BLOCK}" value="{$MOD.ADD_BLOCK}"/>
					</td>
				</tr>
			</table>
			<div id="cfList">
			{include file="Settings/LayoutBlockEntries.tpl"}
			</div>
		</td>
	</tr>
</table>
<!-- End of Display for field -->
{else}
	{include file='modules/Vtiger/OperationNotPermitted.tpl'}
{/if}
</div>
</section>