{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->
TASKActions
*}
{assign var=SHOWHEADER value=false}
{assign var=SHOWIMAGE value=false}
<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped" aria-label="{'LBL_PENDING_EVENTS'|@getTranslatedString:'cbCalendar'}">
	{if $SHOWHEADER}
	<thead>
	<tr class="slds-line-height_reset">
		<th scope="col" style="width:75%">
		<div class="slds-truncate" title="{'LBL_SUBJECT'|@getTranslatedString:'cbCalendar'}">{'LBL_SUBJECT'|@getTranslatedString:'cbCalendar'}</div>
		</th>
		<th scope="col">
		<div class="slds-truncate" title="{'LBL_ACTION'|@getTranslatedString:'cbCalendar'}">{'LBL_ACTION'|@getTranslatedString:'cbCalendar'}</div>
		</th>
	</tr>
	</thead>
	{/if}
	<tbody>
	{foreach from=$TASKActions item=action}
	<tr class="slds-hint-parent">
		<td style="width:75%">
		{if $SHOWIMAGE}
		<span class="slds-media__figure">
		<span class="slds-icon_container slds-icon_container_circle slds-icon-action-description" title="{$action.activitytype}">
			<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/{$action.activityimage[0]}-sprite/svg/symbols.svg#{$action.activityimage[1]|strtolower}"></use>
			</svg>
			<span class="slds-assistive-text">{$action.activitytype}</span>
		</span>
		</span>
		{/if}
		<span class="slds-truncate" title="{$action.cbsubject}">
		{if isRecordExists($action.cbrecord)}
			<a href="index.php?action=DetailView&module={$action.cbmodule}&record={$action.cbrecord}" target="pa{$action.cbrecord}_blank">{$action.cbsubject|textlength_check:25}</a>
		{else}
			{$action.cbsubject}
		{/if}
		</span>
		</td>
		<td>
			{if !empty($action.cbactionlink)}
			<a href="{if $action.cbactiontype=='link'}{$action.cbactionlink}{else}javascript:void(0);{/if}" class="slds-text-link_reset slds-has-flexi-truncate" {if $action.cbactiontype=='click'}onclick="{$action.cbactionlink}"{/if}>
				<p class="slds-m-top_x-small slds-text-color_weak">{$action.cbactionlabel}</p>
			</a>
			{/if}
		</td>
	</tr>
	{/foreach}
	</tbody>
</table>
