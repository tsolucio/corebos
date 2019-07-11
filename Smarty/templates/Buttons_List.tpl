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
{if empty($Module_Popup_Edit)}
{if empty($CATEGORY)}
	{assign var="CATEGORY" value=""}
{/if}
{if $CATEGORY eq 'Settings' || $MODULE eq 'Calendar4You'}
	{assign var="action" value="index"}
{else}
	{assign var="action" value="ListView"}
{/if}
{if !empty($isDetailView)}
	{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
	{assign var='TITLEPREFIX' value=$MOD_SEQ_ID}
	{if $TITLEPREFIX eq ''} {assign var='TITLEPREFIX' value=$ID} {/if}
	{assign var='MODULELABEL' value=$NAME}
{elseif !empty($isEditView)}
	{if $OP_MODE eq 'edit_view'}
		{assign var='TITLEPREFIX' value=$APP.LBL_EDITING}
		{assign var='MODULELABEL' value=$NAME}
	{elseif $OP_MODE eq 'create_view'}
		{if $DUPLICATE neq 'true'}
			{assign var='TITLEPREFIX' value=$APP.LBL_CREATING}
			{assign var='MODULELABEL' value=$SINGLE_MOD|@getTranslatedString:$MODULE}
		{else}
			{assign var='TITLEPREFIX' value=$APP.LBL_DUPLICATING}
			{assign var='MODULELABEL' value=$NAME}
		{/if}
		{assign var='UPDATEINFO' value=''}
	{/if}
{else}
	{assign var='MODULELABEL' value=$MODULE|@getTranslatedString:$MODULE}
{/if}
{assign var='MODULEICON' value=$MODULE|@getModuleIcon}
<div class="slds-page-header" style="width:100%">
  <div class="slds-page-header__row">
	<div class="slds-p-right_medium">
		<div class="slds-media">
			<div class="slds-media__figure">
				<a class="hdrLink" href="index.php?action={$action}&module={$MODULE}">
				<span class="{$MODULEICON.__ICONContainerClass}" title="{$MODULE|@getTranslatedString:$MODULE}">
				<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
					<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/{$MODULEICON.__ICONLibrary}-sprite/svg/symbols.svg#{$MODULEICON.__ICONName}" />
				</svg>
				<span class="slds-assistive-text">{$MODULELABEL}</span>
				</span>
				</a>
			</div>
			<div class="slds-media__body">
				<div class="slds-page-header__name">
					<div class="slds-page-header__name-title">
						<span class="slds-page-header__title slds-truncate" title="{$MODULELABEL|@addslashes}">
						{if !empty($isDetailView) || !empty($isEditView)}
							<h1>
								<span class="slds-page-header__title slds-truncate" title="{$MODULELABEL|@addslashes}">
									<span class="slds-page-header__name-meta">[ {$TITLEPREFIX} ]</span> {$MODULELABEL|textlength_check:30}
								</span>
							</h1>
						{else}
							<a class="hdrLink" href="index.php?action={$action}&module={$MODULE}">{$MODULELABEL}</a>
						{/if}
						</span>
					</div>
				</div>
				{if !empty($isDetailView) || !empty($isEditView)}
					<p class="slds-page-header__name-meta">{$UPDATEINFO}</p>
				{/if}
			</div>
		</div>
	</div>
	<div class="slds-p-right_medium">
		{if $CHECK.CreateView eq 'yes' && ($MODULE eq 'Calendar' || $MODULE eq 'Calendar4You')}
			<span id="LB_AddButton" class="LB_Button slds-p-left_small slds-p-right_none"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$MOD.LBL_ADD_EVENT}" title="{$MOD.LBL_ADD_EVENT}" border=0 {$ADD_ONMOUSEOVER}></span>
		{elseif $CHECK.CreateView eq 'yes' && $MODULE neq 'Emails'}
			<span id="LB_AddButton" class="LB_Button slds-p-left_small slds-p-right_none"><a href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}..." title="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}..." border=0></a></span>
		{else}
			<span id="LB_AddButtonFaded" class="LB_Button slds-p-left_small slds-p-right_none"><img src="{'btnL3Add-Faded.gif'|@vtiger_imageurl:$THEME}" border=0></span>
		{/if}
		{if $CHECK.index eq 'yes' && ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index') && $MODULE neq 'Emails' && $MODULE neq 'Calendar4You'}
			<span id="LB_SearchButton" class="LB_Button slds-p-left_none slds-p-right_x-small"><a href="javascript:;" onClick="searchshowhide('searchAcc','advSearch');mergehide('mergeDup')" ><img src="{$IMAGE_PATH}btnL3Search.gif" alt="{$APP.LBL_SEARCH_ALT}{$MODULE|getTranslatedString:$MODULE}..." title="{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}..." border=0></a></span>
		{else}
			<span id="LB_SearchButtonFaded" class="LB_Button slds-p-left_none slds-p-right_x-small"><img src="{'btnL3Search-Faded.gif'|@vtiger_imageurl:$THEME}" border=0></span>
		{/if}
		{if $CALENDAR_DISPLAY eq 'true'}
			{if $CATEGORY eq 'Settings' || $CATEGORY eq 'Tools' || $CATEGORY eq 'Analytics'}
				{assign var="PTCATEGORY" value='My Home Page'}
			{else}
				{assign var="PTCATEGORY" value=$CATEGORY}
			{/if}
			{if $CHECK.Calendar eq 'yes'}
				<span id="LB_CalButton" class="LB_Button slds-p-left_x-small slds-p-right_none"><a href="javascript:;" onclick="fnvshobj(this,'miniCal');getITSMiniCal('');"><img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a></span>
			{else}
				<span id="LB_CalButtonFaded" class="LB_Button slds-p-left_x-small slds-p-right_none"><img src="{'btnL3Calendar-Faded.gif'|@vtiger_imageurl:$THEME}"></sp>
			{/if}
		{/if}
		{if $WORLD_CLOCK_DISPLAY eq 'true'}
			<span id="LB_ClockButton" class="LB_Button slds-p-left_none slds-p-right_x-small"><a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock');"></a></span>
		{/if}
		{if $CHECK.Import eq 'yes' && $MODULE neq 'Documents' && $MODULE neq 'Calendar' && $MODULE neq 'Calendar4You'}
			<span id="LB_ImportButton" class="LB_Button slds-p-left_x-small slds-p-right_none"><a href="index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=index&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></span>
		{elseif $CHECK.Import eq 'yes' && $MODULE eq 'Calendar'}
			<span id="LB_ImportButton" class="LB_Button slds-p-left_x-small slds-p-right_none"><a name='import_link' href="javascript:void(0);" onclick="fnvshobj(this,'CalImport');" ><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></span>
		{else}
			<span id="LB_ImportButtonFaded" class="LB_Button slds-p-left_x-small slds-p-right_none"><img src="{'tbarImport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
		{/if}
		{if $CHECK.Export eq 'yes' && $MODULE neq 'Calendar' && $MODULE neq 'Calendar4You'}
			<span id="LB_ExportButton" class="LB_Button slds-p-left_none slds-p-right_xx-small"><a name='export_link' href="javascript:void(0)" onclick="return selectedRecords('{$MODULE}','{$CATEGORY}')"><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></span>
		{elseif $CHECK.Export eq 'yes' && $MODULE eq 'Calendar'}
			<span id="LB_ExportButton" class="LB_Button slds-p-left_none slds-p-right_xx-small"><a name='export_link' href="javascript:void(0);" onclick="fnvshobj(this,'CalExport');" ><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></sp>
		{else}
			<span id="LB_ExportButtonFaded" class="LB_Button slds-p-left_none slds-p-right_xx-small"><img src="{'tbarExport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
		{/if}
		{if $CHECK.DuplicatesHandling eq 'yes' && $MODULE neq 'Calendar4You' && ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index')}
			<span id="LB_FindDuplButton" class="LB_Button slds-p-left_xx-small slds-p-right_x-small"><a href="javascript:;" onClick="mergeshowhide('mergeDup');searchhide('searchAcc','advSearch');"><img src="{'findduplicates.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_FIND_DUPLICATES}" title="{$APP.LBL_FIND_DUPLICATES}" border="0"></a></sp>
		{else}
			<span id="LB_FindDuplButtonFaded" class="LB_Button slds-p-left_xx-small slds-p-right_x-small"><img src="{'FindDuplicates-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
		{/if}
		{if $MODULE eq 'Calendar4You'}
			{if $MODE neq 'DetailView' && $MODE neq 'EditView' && $MODE neq 'RelatedList'}
			<span class="LB_Button slds-p-left_small slds-p-right_none">
				<a href="javascript:;" onclick="fnvshobj(this,'calSettings'); getITSCalSettings();"><img src="themes/softed/images/tbarSettings.gif" alt="Settings" title="Settings" align="absmiddle" border="0"></a>
			</span>
			{/if}
			<span id="LB_TaskIcon" class="LB_Button slds-p-left_none slds-p-right_small">
				<a href='index.php?module=cbCalendar&action=index'><img src="themes/images/tasks-icon.png" alt="{'Tasks'|getTranslatedString:$MODULE}" title="{'Tasks'|getTranslatedString:$MODULE}" border="0"></a>
			</span>
		{/if}
		{if $MODULE eq 'Reports'}
			<span class="LB_Button slds-p-left_medium slds-p-right_none">
				<a href="javascript:;" onclick="gcurrepfolderid=0;fnvshobj(this,'reportLay');"><img src="{'reportsCreate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_CREATE_REPORT}..." title="{$MOD.LBL_CREATE_REPORT}..." border=0></a>
			</span>
			<span class="LB_Button slds-p-left_none slds-p-right_none">
				<a href="javascript:;" onclick="createrepFolder(this,'orgLay');"><img src="{'reportsFolderCreate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.Create_New_Folder}..." title="{$MOD.Create_New_Folder}..." border=0></a>
				</span>
			<span class="LB_Button slds-p-left_none slds-p-right_none">
				<a href="javascript:;" onclick="fnvshobj(this,'folderLay');"><img src="{'reportsMove.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.Move_Reports}..." title="{$MOD.Move_Reports}..." border=0></a>
				</span>
			<span class="LB_Button slds-p-left_none slds-p-right_small">
				<a href="javascript:;" onClick="massDeleteReport();"><img src="{'reportsDelete.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_DELETE_FOLDER}..." title="{$MOD.Delete_Report}..." border=0></a>
				</span>
		{/if}
		{if $CHECK.moduleSettings eq 'yes'}
			<span class="LB_Button slds-p-left_x-small slds-p-right_x-small">
				<a href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings'><img src="{'settingsBox.png'|@vtiger_imageurl:$THEME}" alt="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" title="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" border="0"></a>
			</span>
		{/if}
	</div>
	<div class="slds-page-header__col-title">
	{assign var=ANNOUNCEMENT value=get_announcements()}
	{if $ANNOUNCEMENT}
		<marquee id="rss" direction="left" scrolldelay="10" scrollamount="3" behavior="scroll" class="marStyle" onMouseOver="javascript:stop();" onMouseOut="javascript:start();" style="width:90%;">&nbsp;{$ANNOUNCEMENT}</marquee>
		<img src="{'Announce.PNG'|@vtiger_imageurl:$THEME}">
	{/if}
	</div>
  </div>
</div>
<span id="vtbusy_info" style="display:none;" valign="bottom">
<div role="status" class="slds-spinner slds-spinner_brand slds-spinner_x-small" style="position:relative; top:6px;">
	<div class="slds-spinner__dot-a"></div>
	<div class="slds-spinner__dot-b"></div>
</div>
</span>
{/if}
