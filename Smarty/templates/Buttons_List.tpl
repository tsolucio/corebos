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
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<TABLE id="LB_buttonlist" border=0 cellspacing=0 cellpadding=0 width=100% class=small>
<tr><td style="height:2px"></td></tr>
<tr>
	{if empty($CATEGORY)}
		{assign var="CATEGORY" value=""}
	{/if}
	{if $CATEGORY eq 'Settings' || $MODULE eq 'Calendar4You'}
	{assign var="action" value="index"}
	{else}
	{assign var="action" value="ListView"}
	{/if}
	{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
	<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap><a class="hdrLink" href="index.php?action={$action}&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a></td>
	<td width=100% nowrap>
		<table border="0" cellspacing="0" cellpadding="0" >
		<tr>
		<td class="sep1" style="width:1px;"></td>
		<td class=small >
			<!-- Add and Search -->
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
					{if $CHECK.CreateView eq 'yes' && ($MODULE eq 'Calendar' || $MODULE eq 'Calendar4You')}
						<td id="LB_AddButton" class="LB_Button" style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$MOD.LBL_ADD_EVENT}" title="{$MOD.LBL_ADD_EVENT}" border=0 {$ADD_ONMOUSEOVER}></td>
					{elseif $CHECK.CreateView eq 'yes' && $MODULE neq 'Emails' && $MODULE neq 'Webmails'}
						<td id="LB_AddButton" class="LB_Button" style="padding-right:0px;padding-left:10px;"><a href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}..." title="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}..." border=0></a></td>
					{else}
						<td id="LB_AddButtonFaded" class="LB_Button" style="padding-right:0px;padding-left:10px;"><img src="{'btnL3Add-Faded.gif'|@vtiger_imageurl:$THEME}" border=0></td>
					{/if}
					{if $CHECK.index eq 'yes' && ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index') && $MODULE neq 'Emails' && $MODULE neq 'Webmails' && $MODULE neq 'Calendar4You'}
						<td id="LB_SearchButton" class="LB_Button" style="padding-right:10px"><a href="javascript:;" onClick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch');mergehide('mergeDup')" ><img src="{$IMAGE_PATH}btnL3Search.gif" alt="{$APP.LBL_SEARCH_ALT}{$MODULE|getTranslatedString:$MODULE}..." title="{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}..." border=0></a></td>
					{else}
						<td id="LB_SearchButtonFaded" class="LB_Button" style="padding-right:10px"><img src="{'btnL3Search-Faded.gif'|@vtiger_imageurl:$THEME}" border=0></td>
					{/if}
				</tr>
				</table>
			</td>
			</tr>
			</table>
		</td>
		<td style="width:20px;" class="LB_Divider">&nbsp;</td>
		<td class="small">
			<!-- Calendar, Clock and Calculator -->
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
					{if $CALENDAR_DISPLAY eq 'true'}
						{if $CATEGORY eq 'Settings' || $CATEGORY eq 'Tools' || $CATEGORY eq 'Analytics'}
							{assign var="PTCATEGORY" value='My Home Page'}
						{else}
							{assign var="PTCATEGORY" value=$CATEGORY}
						{/if}
						{if $CHECK.Calendar eq 'yes'}
							<td id="LB_CalButton" class="LB_Button" style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onclick="fnvshobj(this,'miniCal');getITSMiniCal('parenttab={$PTCATEGORY}');"><img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a></td>
						{else}
							<td id="LB_CalButtonFaded" class="LB_Button" style="padding-right:0px;padding-left:10px;"><img src="{'btnL3Calendar-Faded.gif'|@vtiger_imageurl:$THEME}"></td>
						{/if}
					{/if}
					{if $WORLD_CLOCK_DISPLAY eq 'true'}
						<td id="LB_ClockButton" class="LB_Button" style="padding-right:0px"><a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock');"></a></td>
					{/if}
					{if $CALCULATOR_DISPLAY eq 'true'}
						<td id="LB_CalcButton" class="LB_Button" style="padding-right:0px"><a href="#"><img src="{$IMAGE_PATH}btnL3Calc.gif" alt="{$APP.LBL_CALCULATOR_ALT}" title="{$APP.LBL_CALCULATOR_TITLE}" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();"></a></td>
					{/if}
					<td id="LB_TrackButton" class="LB_Button" style="padding-right:10px"><img src="{$IMAGE_PATH}btnL3Tracker.gif" alt="{$APP.LBL_LAST_VIEWED}" title="{$APP.LBL_LAST_VIEWED}" border=0 onClick="fnvshobj(this,'tracker');"></td>
				</tr>
				</table>
		</td>
		<td style="width:20px;" class="LB_Divider">&nbsp;</td>
		<td class="small">
			<!-- Import / Export -->
			<table border=0 cellspacing=0 cellpadding=5>
			<tr>
			{if $CHECK.Import eq 'yes' && $MODULE neq 'Documents' && $MODULE neq 'Calendar' && $MODULE neq 'Calendar4You'}
				<td id="LB_ImportButton" class="LB_Button" style="padding-right:0px;padding-left:10px;"><a href="index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=index&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></td>
			{elseif $CHECK.Import eq 'yes' && $MODULE eq 'Calendar'}
				<td id="LB_ImportButton" class="LB_Button" style="padding-right:10px"><a name='import_link' href="javascript:void(0);" onclick="fnvshobj(this,'CalImport');" ><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></td>
			{else}
				<td id="LB_ImportButtonFaded" class="LB_Button" style="padding-right:0px;padding-left:10px;"><img src="{'tbarImport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
			{/if}
			{if $CHECK.Export eq 'yes' && $MODULE neq 'Calendar' && $MODULE neq 'Calendar4You'}
				<td id="LB_ExportButton" class="LB_Button" style="padding-right:10px"><a name='export_link' href="javascript:void(0)" onclick="return selectedRecords('{$MODULE}','{$CATEGORY}')"><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></td>
			{elseif $CHECK.Export eq 'yes' && $MODULE eq 'Calendar'}
				<td id="LB_ExportButton" class="LB_Button" style="padding-right:10px"><a name='export_link' href="javascript:void(0);" onclick="fnvshobj(this,'CalExport');" ><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></td>
			{else}
				<td id="LB_ExportButtonFaded" class="LB_Button" style="padding-right:10px"><img src="{'tbarExport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
			{/if}
			{if $MODULE eq 'Contacts' || $MODULE eq 'Leads' || $MODULE eq 'Accounts'|| $MODULE eq 'Products'|| $MODULE eq 'Potentials'|| $MODULE eq 'HelpDesk'|| $MODULE eq 'Vendors' || $MODULE eq 'Campaigns' || $CUSTOM_MODULE eq 'true'}
				{if $CHECK.DuplicatesHandling eq 'yes' && ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index')}
					<td id="LB_FindDuplButton" class="LB_Button" style="padding-right:10px"><a href="javascript:;" onClick="moveMe('mergeDup');mergeshowhide('mergeDup');searchhide('searchAcc','advSearch');"><img src="{'findduplicates.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_FIND_DUPLICATES}" title="{$APP.LBL_FIND_DUPLICATES}" border="0"></a></td>
				{else}
					<td id="LB_FindDuplButtonFaded" class="LB_Button" style="padding-right:10px"><img src="{'FindDuplicates-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
				{/if}
			{else}
				<td id="LB_FindDuplButtonFaded" class="LB_Button" style="padding-right:10px"><img src="{'FindDuplicates-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
			{/if}
			</tr>
			</table>
		<td style="width:20px;" class="LB_Divider">&nbsp;</td>
		<td class="small">
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
				{if $MODULE eq 'Calendar4You'}
					{if $MODE neq 'DetailView' && $MODE neq 'EditView' && $MODE neq 'RelatedList'}
					<td id="LB_ITSCalSettings" class="LB_Button" style="padding-left:50px;"><a href="javascript:;" onclick="fnvshobj(this,'calSettings'); getITSCalSettings();"><img src="themes/softed/images/tbarSettings.gif" alt="Settings" title="Settings" align="absmiddle" border="0"></a></td>
					{/if}
					<td id="LB_TaskIcon" class="LB_Button"><a href='index.php?module=Calendar4You&action=ListView'><img src="themes/images/tasks-icon.png" alt="{'Tasks'|getTranslatedString:$MODULE}" title="{'Tasks'|getTranslatedString:$MODULE}" border="0"></a></td>
				{/if}
				{if $CHECK.moduleSettings eq 'yes'}
					<td id="LB_ModSettingsButton" class="LB_Button" style="padding-left:10px;"><a href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings'><img src="{'settingsBox.png'|@vtiger_imageurl:$THEME}" alt="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" title="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" border="0"></a></td>
				{/if}
				</tr>
				</table>
		</td>
		</tr>
		</table>
	</td>
</tr>
<tr><td style="height:2px"></td></tr>
</TABLE>
