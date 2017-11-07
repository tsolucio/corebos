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
<!-- BEGIN: main -->
<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
	<tr class="slds-text-title--caps">
		<td style="padding: 0;">
			<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
				<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
					<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
						<!-- Image -->
						<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
							<div class="slds-media__figure slds-icon forceEntityIcon">
								<span class="photoContainer forceSocialPhoto">
									<div class="small roundedSquare forceEntityIcon sites-settings">
										<span class="uiImage">
											<a href="#" onclick="fetchContents('manage');"><img src="{'webmail_settings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 /></a>
										</span>
									</div>
								</span>
							</div>
						</div>
						<!-- Title and help text -->
						<div class="slds-media__body">
							<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
								<span class="uiOutputText componentName">
									<b>{$MOD.LBL_MY_SITES}</b>
								</span>
							</h1>
							<span><a href="#" onclick="fetchContents('manage');">{$MOD.LBL_MANAGE_SITES}</a></span>
						</div>
						<div class="slds-no-flex">
							<input type="button" name="setdefault" value=" {$MOD.LBL_SET_DEFAULT_BUTTON}  " class="slds-button slds-button--small slds-button--brand" onClick="defaultMysites(this);"/>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>

<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table mailSubHeader">
	<tr>
		<td class="dvtCellLabel" width="20%" nowrap align=left>{$MOD.LBL_BOOKMARK_LIST} : </span></td>
		<td class="dvtCellInfo" align=left>
			<select id="urllist" name="urllist" class="small slds-select" onChange="setSite(this);">
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