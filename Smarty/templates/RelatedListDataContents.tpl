{*<!--
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *********************************************************************************/
-->*}
<table border=0 cellspacing=0 cellpadding=0 width=100% class="small rel_mod_data_paging" 
	style="border-bottom:1px solid #999999;padding:5px; background-color: #eeeeff;">
	<tr>
		<td align="left">
			{$RELATEDLISTDATA.navigation.0}
			{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads'
				|| $RELATED_MODULE eq 'Accounts') && $RELATEDLISTDATA.entries|@count > 0}
			{/if}
		</td>
		<td align="center">{$RELATEDLISTDATA.navigation.1} </td>
		<td align="right">
			{if isset($RELATEDLISTDATA.CUSTOM_BUTTON)}{$RELATEDLISTDATA.CUSTOM_BUTTON}{/if}
		</td>
	</tr>
</table>

<table border=0 cellspacing=1 cellpadding=3 width=100% style="background-color:#eaeaea;" class="small rel_mod_data">
	<tr style="height:25px" bgcolor=white class="rel_mod_data_header">
		{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')
			&& $RELATEDLISTDATA.entries|@count > 0}
		<td class="lvtCol">
			<input name ="{$RELATED_MODULE}_selectall" id="{$MODULE}_{$RELATED_MODULE}_selectCurrentPageRec" onclick="rel_toggleSelect(this.checked,'{$MODULE}_{$RELATED_MODULE}_selected_id','{$RELATED_MODULE}');"  type="checkbox">
		</td>
		{/if}
		{foreach key=index item=_HEADER_FIELD from=$RELATEDLISTDATA.header}
		<td class="lvtCol">{$_HEADER_FIELD}</td>
		{/foreach}
	</tr>
	{if $MODULE eq 'Campaigns'}
	<tr class="rel_mod_data_campaigns">
		<td id="{$MODULE}_{$RELATED_MODULE}_linkForSelectAll" class="linkForSelectAll" style="display:none;" colspan=10>
			<span id="{$MODULE}_{$RELATED_MODULE}_selectAllRec" class="selectall" style="display:inline;" onClick="rel_toggleSelectAll_Records('{$MODULE}','{$RELATED_MODULE}',true,'{$MODULE}_{$RELATED_MODULE}_selected_id')">{$APP.LBL_SELECT_ALL} <span id={$RELATED_MODULE}_count class="folder"> </span> {$APP.LBL_RECORDS_IN} {$RELATED_MODULE|@getTranslatedString:$RELATED_MODULE} {$APP.LBL_RELATED_TO_THIS} {$APP.SINGLE_Campaigns}</span>
			<span id="{$MODULE}_{$RELATED_MODULE}_deSelectAllRec" class="selectall" style="display:none;" onClick="rel_toggleSelectAll_Records('{$MODULE}','{$RELATED_MODULE}',false,'{$MODULE}_{$RELATED_MODULE}_selected_id')">{$APP.LBL_DESELECT_ALL} {$RELATED_MODULE|@getTranslatedString:$RELATED_MODULE} {$APP.LBL_RELATED_TO_THIS} {$APP.SINGLE_Campaigns}</span>
		</td>
	</tr>
	{/if}
	{foreach key=_RECORD_ID item=_RECORD from=$RELATEDLISTDATA.entries}
		<tr bgcolor=white class="rel_mod_data_row" id="row_{$_RECORD_ID}">
			{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')}
			<td><input name="{$MODULE}_{$RELATED_MODULE}_selected_id" id="{$_RECORD_ID}" value="{$_RECORD_ID}" onclick="rel_check_object(this,'{$RELATED_MODULE}');" type="checkbox" {if isset($RELATEDLISTDATA.checked)}{$RELATEDLISTDATA.checked.$_RECORD_ID}{/if}></td>
			{/if}
			{foreach key=index item=_RECORD_DATA from=$_RECORD}
				{* vtlib customization: Trigger events on listview cell *}
				<td onmouseover="vtlib_listview.trigger('cell.onmouseover', this)" onmouseout="vtlib_listview.trigger('cell.onmouseout', this)">{$_RECORD_DATA}</td>
			{/foreach}
		</tr>
	{foreachelse}
		<tr style="height: 25px;" bgcolor="white" class="rel_mod_data_emptyrow"><td><i>{$APP.LBL_NONE_INCLUDED}</i></td></tr>
	{/foreach}
</table>
{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')
	&& $RELATEDLISTDATA.entries|@count > 0 && $RESET_COOKIE eq 'true'}
		<script type='text/javascript'>set_cookie('{$RELATED_MODULE}_all', '');</script>
{/if}