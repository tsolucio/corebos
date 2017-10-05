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
{assign var='BLOCKS' value=getSettingsBlocks()}
{assign var='FIELDS' value=getSettingsFields()}
<table border=0 cellspacing=0 cellpadding=20 width="99%" class=""><!-- removed settingsUI  -->
	<tr>
		<td valign=top style="padding: 0;">
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<tr>
					<td valign=top id="settingsSideMenu" class="settings-block">
						<!--Left Side Navigation Table-->
						<table border=0 cellspacing=0 cellpadding=0 width="100%">
{foreach key=BLOCKID item=BLOCKLABEL from=$BLOCKS}
	{if $BLOCKLABEL neq 'LBL_MODULE_MANAGER'}
	{assign var=blocklabel value=$BLOCKLABEL|@getTranslatedString:'Settings'}
							<tr>
								<td class="settingsTabHeader" nowrap style="padding: 0;background-color: #fff;">
									<div class="flexipageComponent" style="margin-top: .5rem;">
										<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
											<div class="slds-card__header slds-grid" style="padding: .5rem; margin: 0;">
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<span class="slds-truncate" style="font-size: 11px;letter-spacing: 0;"><b>{$blocklabel}</b></span>
													</div>
												</header>
											</div>
										</article>
									</div>
								</td>
							</tr>
		{foreach item=data from=$FIELDS.$BLOCKID}
			{if $data.link neq ''}
				{assign var=label value=$data.name|@getTranslatedString:$data.module}
				{if $label eq $data.name}
				{assign var=label value=$data.name|@getTranslatedString:'Settings'}
				{/if}
				{if ($smarty.request.action eq $data.action && $smarty.request.module eq $data.module)}
							<tr>
								<td class="settingsTabSelected" nowrap style="border-left: 1px solid #d4d4d4;">
									<a href="{$data.link}">
										{$label}
									</a>
								</td>
							</tr>
				{else}
							<tr>
								<td class="settingsTabList" nowrap style="border-left: 1px solid #d4d4d4;">
									<a href="{$data.link}">
										{$label}
									</a>
								</td>
							</tr>
				{/if}
			{/if}
		{/foreach}
	{/if}
{/foreach}
						</table>
						<!-- Left side navigation table ends -->
					</td>
					<td width="8px" valign="top" class="togglePanel" style="padding-top: 1rem;"> 
						<img src="{'panel-left.png'|@vtiger_imageurl:$THEME}" title="Hide Menu" id="hideImage" style="display:inline;cursor:pointer;" onclick="toggleShowHide_panel('showImage','settingsSideMenu'); toggleShowHide_panel('showImage','hideImage');" />
						<img src="{'panel-right.png'|@vtiger_imageurl:$THEME}" title="Show Menu" id="showImage" style="display:none;cursor:pointer;" onclick="toggleShowHide_panel('settingsSideMenu','showImage'); toggleShowHide_panel('hideImage','showImage');"/>
					</td>
					<td class="small settingsSelectedUI" valign=top align=left style="padding: 0 0 0 1rem;">
<script type="text/javascript">
{literal}
function toggleShowHide_panel(showid, hideid){
	var show_ele = document.getElementById(showid);
	var hide_ele = document.getElementById(hideid);
	if(show_ele != null){ 
		show_ele.style.display = "";
		}
	if(hide_ele != null) 
		hide_ele.style.display = "none";
}
{/literal}
</script>
