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
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td colwidth=90% align=left class=small>
		<a href="#" onclick="fetchContents('manage');">
		<p class="slds-accordion__summary-heading">
		<span class="slds-icon_container slds-icon-utility-announcement" title="{'SINGLE_Portal'|@getTranslatedString}">
			<svg class="slds-icon slds-icon-text-default" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#web_link"></use>
			</svg>
		</span>
		<span class="slds-m-left_small">{$MOD.LBL_MANAGE_SITES}</span>
		</p>
		</a>
	</td>
	<td align=right width=15%>
		<input type="button" name="setdefault" value=" {$MOD.LBL_SET_DEFAULT_BUTTON} " class="crmbutton small create" onClick="defaultMysites(this);"/>
	</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="mailSubHeader">
<tr>
<td nowrap align=left>{$MOD.LBL_BOOKMARK_LIST} : </span></td>
<td align=left width=90%>
	<select id="urllist" name="urllist" style="width: 99%;" class="small" onChange="setSite(this);">
	{if $DEFAULT_EMBED eq 0}
		<option disabled selected value></option>
	{/if}
	{foreach item=portaldetails key=sno from=$PORTALS}
	{if $portaldetails.set_def eq '1' && $portadetails.embed eq 1}
		<option selected value="{$portaldetails.portalid}">{$portaldetails.portalname}</option>
	{else}
		<option value="{$portaldetails.portalid}">{$portaldetails.portalname}</option>
	{/if}
	{/foreach}
	</select>
</td>
</tr>
<tr>
	<td bgcolor="#ffffff" colspan=2>
		<div id="mysites_noload_message" style="display: none;">
		{assign var='ERROR_MESSAGE' value='ERR_NOT_PERMITTED_LOAD'|@getTranslatedString:'Portal'}
		{assign var='ERROR_MESSAGE_CLASS' value='cb-alert-info'}
		{include file="applicationmessage.tpl"}
		</div>
		<iframe id="locatesite" src="{$DEFAULT_URL}" frameborder="0" height="1100" scrolling="auto" width="100%"></iframe>
	</td>
</tr>
</table>