{*
<!--
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
<TABLE id="LB_buttonlist" border=0 cellspacing=0 cellpadding=0 width=98% align=center class=small>
<thead>
<tr class="slds-text-title--caps">
	{if empty($CATEGORY)}
		{assign var="CATEGORY" value=""}
	{/if}
	{if $CATEGORY eq 'Settings' || $MODULE eq 'Calendar4You'}
	{assign var="action" value="index"}
	{else}
	{assign var="action" value="ListView"}
	{/if}
	{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
	<th scope="col" style="padding: 1rem 1.5rem 1rem 1rem;">
		<div class="slds-truncate moduleName" title="{$MODULELABEL}">
			<a class="hdrLink" href="index.php?action={$action}&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a>
		</div>
	</th>
	<td width=100% nowrap>
		<table border="0" cellspacing="0" cellpadding="0" class="slds-table-buttons">
            <tr>
                <!-- <td class="sep1" style="width:1px;"></td> -->
                <td class=small >
                    <!-- Add and Search -->
                    <table class="slds-table slds-no-row-hover background">
                        <tr class="LD_buttonList">
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                   <div class="forceHeaderMenuTrigger">
                                        {if $CHECK.CreateView eq 'yes' && ($MODULE eq 'Calendar' || $MODULE eq 'Calendar4You')}
                                            <div id="LB_AddButton" class="LB_Button slds-truncate">
                                                <img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$MOD.LBL_ADD_EVENT}" title="{$MOD.LBL_ADD_EVENT}" border=0 {$ADD_ONMOUSEOVER}>
                                            </div>
                                        {elseif $CHECK.CreateView eq 'yes' && $MODULE neq 'Emails' && $MODULE neq 'Webmails'}
                                            <div class="slds-truncate LB_Button" id="LB_AddButton">
                                                <a class="globalCreateTrigger" href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}">
                                                    <img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}..." title="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}..." border=0>
                                                </a>
                                            </div>
                                        {else}
                                            <div id="LB_AddButtonFaded" class="slds-truncateLB_Button">
                                                <span class="disabled">
                                                    <img src="{'btnL3Add.gif'|@vtiger_imageurl:$THEME}" border=0>
                                                </span>
                                            </div>
                                        {/if}
                                  </div>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                        {if $CHECK.index eq 'yes' && ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index') && $MODULE neq 'Emails' && $MODULE neq 'Webmails' && $MODULE neq 'Calendar4You'}
                                            <div id="LB_SearchButton" class="slds-truncate LB_Button">
                                                <a href="javascript:;" onClick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch');mergehide('mergeDup')" >
                                                    <img src="{$IMAGE_PATH}btnL3Search.gif" alt="{$APP.LBL_SEARCH_ALT}{$MODULE|getTranslatedString:$MODULE}..." title="{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}..." border=0>
                                                </a>
                                            </div>
                                        {else}
                                            <div id="LB_SearchButtonFaded" class="LB_Button slds-truncate">
                                                <span class="disabled">
                                                    <img src="{'btnL3Search.gif'|@vtiger_imageurl:$THEME}" border=0>
                                                </span>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </table>
                </td>
                <td style="width:20px;" class="LB_Divider">&nbsp;&nbsp;</td>
                <td class="small">
                    <!-- Calendar, Clock and Calculator -->
                    <table class="slds-table slds-no-row-hover background">
                        <tr class="LD_buttonList">
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                    {if $CALENDAR_DISPLAY eq 'true'}

                                        {if $CATEGORY eq 'Settings' || $CATEGORY eq 'Tools' || $CATEGORY eq 'Analytics'}
                                            {assign var="PTCATEGORY" value='My Home Page'}
                                        {else}
                                            {assign var="PTCATEGORY" value=$CATEGORY}
                                        {/if}

                                        {if $CHECK.Calendar eq 'yes'}
                                            <div id="LB_CalButton" class="LB_Button slds-truncate">
                                                <a href="javascript:;" onclick="fnvshobj(this,'miniCal');getITSMiniCal('');">
                                                    <img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0>
                                                </a>
                                            </div>

                                        {else}
                                            <div id="LB_CalButtonFaded" class="LB_Button slds-truncate">
                                                <span class="disabled">
                                                    <img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}">
                                                </span>
                                            </div>
                                        {/if}

                                    {/if}
                                    </div>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                    {if $WORLD_CLOCK_DISPLAY eq 'true'}
                                        <div id="LB_ClockButton" class="LB_Button slds-truncate">
                                            <a href="javascript:;">
                                                <img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock');">
                                            </a>
                                        </div>
                                    {/if}
                                    </div>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                    {if $CALCULATOR_DISPLAY eq 'true'}
                                        <div id="LB_CalcButton" class="LB_Button slds-truncate">
                                            <a href="javascript:;">
                                                <img src="{$IMAGE_PATH}btnL3Calc.gif" alt="{$APP.LBL_CALCULATOR_ALT}" title="{$APP.LBL_CALCULATOR_TITLE}" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();">
                                            </a>
                                        </div>
                                    {/if}
                                    </div>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                        <div id="LB_TrackButton" class="LB_Button slds-truncate">
                                            <a href="javascript:;">
                                                <img src="{$IMAGE_PATH}btnL3Tracker.gif" alt="{$APP.LBL_LAST_VIEWED}" title="{$APP.LBL_LAST_VIEWED}" border=0 onClick="fnvshobj(this,'tracker');">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </table>
                </td>
                <td style="width:20px;" class="LB_Divider">&nbsp;</td>
                <td class="small">
                    <!-- Import / Export / DuplicatesHandling-->
                    <table class="slds-table slds-no-row-hover background">
                        <tr class="LD_buttonList">
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                    {if $CHECK.Import eq 'yes' && $MODULE neq 'Documents' && $MODULE neq 'Calendar' && $MODULE neq 'Calendar4You'}
                                        <div id="LB_ImportButton" class="LB_Button slds-truncate"><a href="index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=index&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></div>
                                    {elseif $CHECK.Import eq 'yes' && $MODULE eq 'Calendar'}
                                        <div id="LB_ImportButton" class="LB_Button slds-truncate"><a name='import_link' href="javascript:void(0);" onclick="fnvshobj(this,'CalImport');" ><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></div>
                                    {else}
                                        <div id="LB_ImportButtonFaded" class="LB_Button slds-truncate"><span class="disabled"><img src="{'tbarImport.gif'|@vtiger_imageurl:$THEME}" border="0"></span></div>
                                    {/if}
                                    </div>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                    {if $CHECK.Export eq 'yes' && $MODULE neq 'Calendar' && $MODULE neq 'Calendar4You'}
                                        <div id="LB_ExportButton" class="LB_Button slds-truncate"><a name='export_link' href="javascript:void(0)" onclick="return selectedRecords('{$MODULE}','{$CATEGORY}')"><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></div>
                                    {elseif $CHECK.Export eq 'yes' && $MODULE eq 'Calendar'}
                                        <div id="LB_ExportButton" class="LB_Button slds-truncate"><a name='export_link' href="javascript:void(0);" onclick="fnvshobj(this,'CalExport');" ><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></div>
                                    {else}
                                        <div id="LB_ExportButtonFaded" class="LB_Button slds-truncate"><span class="disabled"><img src="{'tbarExport.gif'|@vtiger_imageurl:$THEME}" border="0"></span></div>
                                    {/if}
                                    </div>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="globalCreateContainer oneGlobalCreate">
                                    <div class="forceHeaderMenuTrigger">
                                    {if $CHECK.DuplicatesHandling eq 'yes' && ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index')}
                                        <div id="LB_FindDuplButton" class="LB_Button slds-truncate"><a href="javascript:;" onClick="moveMe('mergeDup');mergeshowhide('mergeDup');searchhide('searchAcc','advSearch');"><img src="{'findduplicates.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_FIND_DUPLICATES}" title="{$APP.LBL_FIND_DUPLICATES}" border="0"></a></div>
                                    {else}
                                        <div id="LB_FindDuplButtonFaded" class="LB_Button slds-truncate"><span class="disabled"><img src="{'findduplicates.gif'|@vtiger_imageurl:$THEME}" border="0"></span></div>
                                    {/if}
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </table>
                    <td style="width:20px;" class="LB_Divider">&nbsp;</td>
                    <td class="small">
                        <!-- Settings -->
                        <table class="slds-table slds-no-row-hover background">
                            <tr class="LD_buttonList">
                                <th scope="col">
                                    <div class="globalCreateContainer oneGlobalCreate">
                                        <div class="forceHeaderMenuTrigger">
                                        {if $MODULE eq 'Calendar4You'}
                                            {if $MODE neq 'DetailView' && $MODE neq 'EditView' && $MODE neq 'RelatedList'}
                                            <div id="LB_ITSCalSettings" class="LB_Button slds-truncate" style="padding-left:50px;"><a href="javascript:;" onclick="fnvshobj(this,'calSettings'); getITSCalSettings();"><img src="themes/softed/images/tbarSettings.gif" alt="Settings" title="Settings" align="absmiddle" border="0"></a></div>
                                            {/if}
                                            <div id="LB_TaskIcon" class="LB_Button slds-truncate"><a href='index.php?module=Calendar&action=index'><img src="themes/images/tasks-icon.png" alt="{'Tasks'|getTranslatedString:$MODULE}" title="{'Tasks'|getTranslatedString:$MODULE}" border="0"></a></div>
                                        {/if}
                                        </div>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="globalCreateContainer oneGlobalCreate">
                                        <div class="forceHeaderMenuTrigger">
                                        {if $CHECK.moduleSettings eq 'yes'}
                                            <div id="LB_ModSettingsButton" class="LB_Button slds-truncate"><a href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings'><img src="{'settingsBox.png'|@vtiger_imageurl:$THEME}" alt="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" title="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" border="0"></a></div>
                                        {/if}
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </table>
                    </td>
            </tr>
		</table>
	</td>
</tr>
<tr><td style="height:2px"></td></tr>
</thead>
</TABLE>
{/if}