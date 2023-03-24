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
{if $RELATEDLISTDATA.navigation.0!='' || $RELATEDLISTDATA.navigation.1 != '' || isset($RELATEDLISTDATA.CUSTOM_BUTTON)}
<table border=0 cellspacing=0 cellpadding=0 width=100% class="small rel_mod_data_paging"
	style="background-color: #f3f3f3;">
	<tr>
		<td align="left">
			{$RELATEDLISTDATA.navigation.0}
		</td>
		<td align="center">{$RELATEDLISTDATA.navigation.1} </td>
		<td align="right">
			{if isset($RELATEDLISTDATA.CUSTOM_BUTTON)}{$RELATEDLISTDATA.CUSTOM_BUTTON}{/if}
		</td>
	</tr>
</table>
{/if}
<table class="slds-table slds-table_bordered slds-table_fixed-layout slds-table_resizable-cols">
	<thead>
	<tr class="slds-line-height_reset">
		{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')
			&& $RELATEDLISTDATA.entries|@count > 0}
		<th class="slds-is-resizable slds-is-sortable slds-cell_action-mode">
			<input name ="{$RELATED_MODULE}_selectall" id="{$MODULE}_{$RELATED_MODULE}_selectCurrentPageRec" onclick="rel_toggleSelect(this.checked,'{$MODULE}_{$RELATED_MODULE}_selected_id','{$RELATED_MODULE}');"  type="checkbox">
		</th>
		{/if}
		{foreach key=index item=_HEADER_FIELD from=$RELATEDLISTDATA.header}
		<th class="slds-is-resizable slds-is-sortable slds-cell_action-mode">
			<span class="slds-truncate">
				{$_HEADER_FIELD}
			</span>
		</th>
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
	</thead>
		<tbody>
		{foreach key=_RECORD_ID item=_RECORD from=$RELATEDLISTDATA.entries}
		<tr id="row_{$_RECORD_ID}">
			{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')}
			<td class="slds-cell_action-mode"><input name="{$MODULE}_{$RELATED_MODULE}_selected_id" id="{$_RECORD_ID}" value="{$_RECORD_ID}" onclick="rel_check_object(this,'{$RELATED_MODULE}');" type="checkbox" {if isset($RELATEDLISTDATA.checked)}{$RELATEDLISTDATA.checked.$_RECORD_ID}{/if}></td>
			{/if}
			{foreach key=index item=_RECORD_DATA from=$_RECORD}
				{* vtlib customization: Trigger events on listview cell *}
				<td class="slds-cell_action-mode cbds-data-overflow" onmouseover="vtlib_listview.trigger('cell.onmouseover', this)" onmouseout="vtlib_listview.trigger('cell.onmouseout', this)">{$_RECORD_DATA}</td>
			{/foreach}
		</tr>
		{foreachelse}
		<tr>
			<td class="slds-cell_action-mode">
				<i>{$APP.LBL_NONE_INCLUDED}</i>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')
	&& $RELATEDLISTDATA.entries|@count > 0 && $RESET_COOKIE eq 'true'}
	<script type='text/javascript'>set_cookie('{$RELATED_MODULE}_all', '');</script>
{/if}