{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
{if !$ISAJAXCALL}
{if $MODULE eq $SEARCH_MODULE && $SEARCH_MODULE neq ''}
	<div id="global_list_{$SEARCH_MODULE}" style="display:block">
{elseif $MODULE eq 'Contacts' && $SEARCH_MODULE eq ''}
	<div id="global_list_{$MODULE}" style="display:block">
{elseif $SEARCH_MODULE neq ''}
	<div id="global_list_{$MODULE}" style="display:none">
{else}
	<div id="global_list_{$MODULE}" style="display:block">
{/if}
{/if}
<form name="massdelete" method="POST">
	<input name="idlist" type="hidden">
	<input name="search_tag" type="hidden" value="{$TAG_SEARCH}" >
	<input name="search_criteria" type="hidden" value="{$SEARCH_STRING}">
	<input name="module" type="hidden" value="{$MODULE}" />
	<input name="{$MODULE}RecordCount" id="{$MODULE}RecordCount" type="hidden" value="{$ModuleRecordCount.$MODULE.count}" />
	<br>
	{assign var='MODULEICON' value=$MODULE|@getModuleIcon}
	<div class="small slds-m-top_small slds-m-bottom_small">
		<div class="slds-m-left_large slds-m-right_large slds-grid slds-gutters slds-m-bottom_x-small slds-page-header slds-page-header__row">
			<div class="slds-col slds-size_1-of-3 slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action=index&module={$MODULE}" target="_blank">
					<span class="{$MODULEICON.__ICONContainerClass}" title="{$MODULE|@getTranslatedString:$MODULE}">
					<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/{$MODULEICON.__ICONLibrary}-sprite/svg/symbols.svg#{$MODULEICON.__ICONName}" />
					</svg>
					<span class="slds-assistive-text">{$MODULE|@getTranslatedString:$MODULE}</span>
					</span>
					</a>
				</div>
				<div class="slds-media__body">
				<span class='slds-dynamic-menu__header'>{$MODULE|@getTranslatedString:$MODULE}</span><br>{$SEARCH_CRITERIA}
				</div>
			</div>
			<div class="slds-col slds-size_1-of-3 slds-chat-message__text">{$ModuleRecordCount.$MODULE.recordListRangeMessage}</div>
			<div class="slds-col slds-size_1-of-3">
				<span>{if $ModuleRecordCount.$MODULE.recordListRangeMessage!=''}{$NAVIGATION}{/if}</span>
			</div>
		</div>
		<div class="searchResults slds-m-left_large slds-m-right_large">
			<table class="slds-table slds-table_cell-buffer slds-table_bordered">
			<thead>
				<tr class="slds-line-height_reset">
				{if $DISPLAYHEADER eq 1}
					{foreach item=header from=$LISTHEADER}
					<th class="mailSubHeader">{$header}</th>
					{/foreach}
				{else}
					<th class="searchResultsRow" colspan=$HEADERCOUNT> {$APP.LBL_NO_DATA} </th>
				{/if}
				</tr>
			</thead>
			<tbody>
				{foreach item=entity key=entity_id from=$LISTENTITY}
				<tr class="slds-hint-parent">
				{foreach item=data from=$entity}
					<td scope="row" onmouseout="vtlib_listview.trigger('cell.onmouseout', this)" onmouseover="vtlib_listview.trigger('cell.onmouseover', this)">{$data}</td>
				{/foreach}
				</tr>
				{/foreach}
			</tbody>
			</table>
		</div>
	</div>
</form>
{if $SEARCH_MODULE eq 'All'}
<script>
displayModuleList(document.getElementById('global_search_module'));
</script>
{/if}
{if !$ISAJAXCALL}
</div>
{/if}