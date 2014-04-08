{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<table width="100%" cellpadding="5" cellspacing="0" class="listTable" >
	<tr>
	<td class="colHeader small" width="5%">#</td>
	<td class="colHeader small" width="20%">Cron Job</td>
	<td class="colHeader small" width="15%">{$MOD.LBL_FREQUENCY}{$MOD.LBL_HOURMIN}</td>
	<td class="colHeader small" width="10%">{$CMOD.LBL_STATUS}</td>
        <td class="colHeader small" width="20%">{$MOD.LAST_START}</td>
        <td class="colHeader small" width="15%">{$MOD.LAST_END}</td>
        <td class="colHeader small" width='10%'>{$MOD.LBL_SEQUENCE}</td>
        <td class="colHeader small" width="5%">{$MOD.LBL_TOOLS}</td>
	</tr>
	{foreach name=cronlist item=elements from=$CRON}
	<tr>
	<td class="listTableRow small">{$smarty.foreach.cronlist.iteration}</td>
	<td class="listTableRow small">{$elements.cronname}</td>
	<td class="listTableRow small">{$elements.days} {$elements.hours}:{$elements.mins}</td>
	{if $elements.status eq 'Active'}
	<td class="listTableRow small active">{$elements.status}</td>
	{else}
	<td class="listTableRow small inactive">{$elements.status}</td>
	{/if}
        <td class="listTableRow small">{$elements.laststart}</td>
        <td class="listTableRow small">{$elements.lastend}</td>
	{if $smarty.foreach.cronlist.first neq true}
		<td  align="center" class="listTableRow"><a href="javascript:move_module('{$elements.id}','Up');" ><img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" /></a>
	{/if}
	{if $smarty.foreach.cronlist.last eq true}
		<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />
	{/if}	
	{if $smarty.foreach.cronlist.first eq true}
		<td align="center" class="listTableRow"><img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />
			<a href="javascript:move_module('{$elements.id}','Down');" ><img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" /></a></td>
	{/if}
	
	{if $smarty.foreach.cronlist.last neq true && $smarty.foreach.cronlist.first neq true}
		<a href="javascript:move_module('{$elements.id}','Down');" ><img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" /></a></td>
	{/if}
        <td class="listTableRow small" align="center" ><img onClick="fnvshobj(this,'editdiv');fetchEditCron('{$elements.id}');" style="cursor:pointer;" src="{'editfield.gif'|@vtiger_imageurl:$THEME}" title="{$APP.LBL_EDIT}"></td>
        </tr>
	{/foreach}
	</table>

