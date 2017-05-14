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
<table border=0 cellspacing=0 cellpadding=5 width="350px" align="" class="dvtContentSpace">
	<tr>
		<td class="colHeader small">{'LBL_MODULE'|@getTranslatedString}</td>
		<td class="colHeader small" align="center">{'Active'|@getTranslatedString}</td>
	</tr>
	{foreach item=module key=tabid from=$INFOMODULES}
	<tr onmouseover="this.className='prvPrfHoverOn'" onmouseout="this.className='prvPrfHoverOff'">
		<td class="listTableRow small" width="50%">{$module.name|@getTranslatedString:$module.name}</td>
		<td class="listTableRow cellText small" align="center">
		<div id="status" style="position:absolute;left:850px;top:5px;height:27px;white-space:nowrap;display:none"><img src="themes/softed/images/status.gif"></div>
		{if $module.active eq '1'}
			<a href="javascript:void(0);" onclick="toggleModule_mod('{$tabid}', 'module_disable');">
			 <img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{'LBL_DISABLE'|@getTranslatedString:'ModTracker'} {$module.name|@getTranslatedString:$module.name}}" title="{'LBL_DISABLE'|@getTranslatedString:'ModTracker'} {$module.name|@getTranslatedString:$module.name}">
			</a>
		{else}
			<a href="javascript:void(0);" onclick="toggleModule_mod('{$tabid}', 'module_enable');">
			<img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{'LBL_ENABLE'|@getTranslatedString:'ModTracker'} {$module.name|@getTranslatedString:$module.name}" title="{'LBL_ENABLE'|@getTranslatedString:'ModTracker'} {$module.name|@getTranslatedString:$module.name}">
			</a>
		{/if}
		</td>
	</tr>
	{/foreach}
</table>
