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

{assign var="fromlink" value=""}
{foreach key=label item=subdata from=$data}
	{if $header eq 'Product Details'}
		<tr name="tbl{$header|replace:' ':''}Content" class="createview_field_row">
	{else}
		<tr name="tbl{$header|replace:' ':''}Content" style="height:25px" class="createview_field_row">
	{/if}
	{foreach key=mainlabel item=maindata from=$subdata}
		{if count($maindata)>0}{include file='EditViewUI.tpl'}{/if}
	{/foreach}
	</tr>
{/foreach}
