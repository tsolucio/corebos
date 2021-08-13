{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
<script type="text/javascript" src="modules/PickList/DependencyPicklist.js"></script>
{if empty($MODULE)}
	{assign var="MODULE" value='PickList'}
	{assign var="RESETMODULE" value=true}
{/if}
{include file='SetMenu.tpl'}
{if !empty($RESETMODULE)}
	{assign var="MODULE" value=''}
{/if}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
		<!-- DISPLAY -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		<tr>
			<td width=50 rowspan=2 valign=top class="cblds-p_none"><img src="{'picklist.gif'|@vtiger_imageurl:$THEME}" width="48" height="48" border=0 ></td>
			<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD_PICKLIST.LBL_PICKLIST_DEPENDENCY_SETUP}</b></td>
		</tr>
		<tr>
			<td valign=top class="small cblds-p-v_none">{$MOD_PICKLIST.LBL_PICKLIST_DEPENDENCY_DESCRIPTION}</td>
		</tr>
		</table>

		<table border=0 cellspacing=0 cellpadding=10 width=100% >
		<tr>
			<td valign=top>
				<div id="picklist_datas">
					{if $SUBMODE eq 'editdependency'}
						{include file='modules/PickList/PickListDependencyContents.tpl'}
					{else}
						{include file='modules/PickList/PickListDependencyList.tpl'}
					{/if}
				</div>
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
</tbody>
</table>
</div>
</section>