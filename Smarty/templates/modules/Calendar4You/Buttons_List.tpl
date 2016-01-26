{*<!--
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>
<tr><td style="height:2px"></td></tr>
<tr>
	<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap>{* {$APP.$CATEGORY} > *}<a class="hdrLink" href="index.php?action=index&module=Calendar4You&parenttab={$CATEGORY}">{$MOD.LBL_CALENDAR4YOU}</a></td>
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
                					{if $EDIT eq 'permitted'}
                	                    <td style="padding-right:0px;padding-left:10px;"><a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$MOD.LBL_ADD_EVENT}" title="{$MOD.LBL_ADD_EVENT}" border=0 {$ADD_ONMOUSEOVER}></a></td>
                					{else}
                						<td style="padding-right:0px;padding-left:10px;"><img src="{'btnL3Add-Faded.gif'|@vtiger_imageurl:$THEME}" border=0></td>	
                					{/if}
                					<td style="padding-right:10px"><img src="{'btnL3Search-Faded.gif'|@vtiger_imageurl:$THEME}" border=0></td>
                				</tr>
            				</table>
            			</td>
        			</tr>
    			</table>
    		</td>
            <td style="width:20px;">&nbsp;</td>
    		<td class="small">
    			<!-- Calendar Clock Calculator and Chat -->
    			<table border=0 cellspacing=0 cellpadding=5>
    				<tr>
     		            <td style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onclick="fnvshobj(this,'miniCal');getITSMiniCal('parenttab={$APP.$CATEGORY}');"><img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" border=0></a></td> 
    					{if $WORLD_CLOCK_DISPLAY eq 'true'} 
                            <td style="padding-right:0px"><a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock');"></a></a></td> 
                        {/if} 
                        {if $CALCULATOR_DISPLAY eq 'true'} 
                            <td style="padding-right:0px"><a href="#"><img src="{$IMAGE_PATH}btnL3Calc.gif" alt="{$APP.LBL_CALCULATOR_ALT}" title="{$APP.LBL_CALCULATOR_TITLE}" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();"></a></td> 
                        {/if} 
                        {if $CHAT_DISPLAY eq 'true'} 
                            <td style="padding-right:0px"><a href="javascript:;" onClick='return window.open("index.php?module=Home&action=vtchat","Chat","width=600,height=450,resizable=1,scrollbars=1");'><img src="{$IMAGE_PATH}tbarChat.gif" alt="{$APP.LBL_CHAT_ALT}" title="{$APP.LBL_CHAT_TITLE}" border=0></a> 
                        {/if} 
    				</td>
    					<td style="padding-right:10px"><img src="{$IMAGE_PATH}btnL3Tracker.gif" alt="{$APP.LBL_LAST_VIEWED}" title="{$APP.LBL_LAST_VIEWED}" border=0 onClick="fnvshobj(this,'tracker');">
                        </td>	
    				</tr>
    			</table>
    		</td>
    		<td style="width:20px;">&nbsp;</td>
    		<td class="small">
    			<!-- Import / Export -->
    			<table border=0 cellspacing=0 cellpadding=5>
        			<tr>
        			{* vtlib customization: Hook to enable import/export button for custom modules. Added CUSTOM_MODULE *}
        	   		{if $IMPORT eq 'yes'}	
        				<td style="padding-right:0px;padding-left:10px;"><a href="index.php?module=EMAILMaker&action=ImportEmailTemplate&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></td>	
        			{else}	
        				<td style="padding-right:0px;padding-left:10px;"><img src="{'tbarImport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>	
        			{/if}	
        			{if $EXPORT eq 'yes'}
        			    <td style="padding-right:10px"><a name='export_link' href="javascript:void(0)" onclick="return ExportTemplates();"><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}" border="0"></a></td>			
        			{else}	
        				<td style="padding-right:10px"><img src="{'tbarExport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
                    {/if}
        			<td style="padding-right:10px"><img src="{'FindDuplicates-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
        			</tr>
    			</table>	
    		<td style="width:20px;">&nbsp;</td>
    		<td class="small">
    			<!-- All Menu -->
    			<table border=0 cellspacing=0 cellpadding=5>
    				<tr>
                        <td style="padding-left:10px;"><a href="javascript:;" onmouseout="fninvsh('allMenu');" onClick="fnvshobj(this,'allMenu')"><img src="{$IMAGE_PATH}btnL3AllMenu.gif" alt="{$APP.LBL_ALL_MENU_ALT}" title="{$APP.LBL_ALL_MENU_TITLE}" border="0"></a></td>
    				{if $MODE neq 'DetailView' && $MODE neq 'EditView' && $MODE neq 'RelatedList'}    
                        <td style="padding-left:50px;"><a href="javascript:;" onclick="fnvshobj(this,'calSettings'); getITSCalSettings();"><img src="themes/softed/images/tbarSettings.gif" alt="Settings" title="Settings" align="absmiddle" border="0"></a></td> 
                    {/if}  
                    {if $CHECK.moduleSettings eq 'yes'}
    	        		<td><a href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule=Calendar4You&parenttab=Settings'><img src="{'settingsBox.png'|@vtiger_imageurl:$THEME}" alt="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" title="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" border="0"></a></td>
    				{/if}
    				</tr>
    			</table>
    		</td>			
		</tr>
		</table>
	</td>
</tr>
</TABLE>