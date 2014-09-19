{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<script type="text/javascript" src="modules/{$MODULE}/language/{$LANGUAGE}.lang.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<table border=0 cellspacing=0 cellpadding=0 width=100% class=small>
	<tr><td style="height:2px"></td></tr>
	<tr>
		{assign var="action" value="WebformsListView"}
		{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
		<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap>{$APP.$CATEGORY} > <a class="hdrLink" href="index.php?action={$action}&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a></td>
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
									<td style="padding-right:0px;padding-left:10px;"><a href="index.php?module=Webforms&action=WebformsEditView&parenttab=Settings"><img id="createWebForm" src="{'btnL3Add.gif'|@vtiger_imageurl:$THEME}" border=0 title="Create Webform"></td>
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
						{if $CALENDAR_DISPLAY eq 'true'}
							{if $CATEGORY eq 'Settings' || $CATEGORY eq 'Tools' || $CATEGORY eq 'Analytics'}
								{if $CHECK.Calendar eq 'yes'}
									<td style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab=My Home Page");'><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a></a></td>
								{else}
									<td style="padding-right:0px;padding-left:10px;"><img src="{'btnL3Calendar-Faded.gif'|@vtiger_imageurl:$THEME}"></td>
								{/if}
							{else}
								{if $CHECK.Calendar eq 'yes'}
									<td style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab={$CATEGORY}");'><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a></a></td>
								{else}
									<td style="padding-right:0px;padding-left:10px;"><img src="{'btnL3Calendar-Faded.gif'|@vtiger_imageurl:$THEME}"></td>
								{/if}
							{/if}
						{/if}
						{if $WORLD_CLOCK_DISPLAY eq 'true'}
							<td style="padding-right:0px"><a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock');"></a></a></td>
						{/if}
						{if $CALCULATOR_DISPLAY eq 'true'}
							<td style="padding-right:0px"><a href="#"><img src="{$IMAGE_PATH}btnL3Calc.gif" alt="{$APP.LBL_CALCULATOR_ALT}" title="{$APP.LBL_CALCULATOR_TITLE}" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();"></a></td>
						{/if}
						{if $CHAT_DISPLAY eq 'true'}
							<td style="padding-right:10px"><a href="javascript:;" onClick='return window.open("index.php?module=Home&action=vtchat","Chat","width=600,height=450,resizable=1,scrollbars=1");'><img src="{$IMAGE_PATH}tbarChat.gif" alt="{$APP.LBL_CHAT_ALT}" title="{$APP.LBL_CHAT_TITLE}" border=0></a>
						{/if}
							</td>
							<td style="padding-right:10px"><img src="{$IMAGE_PATH}btnL3Tracker.gif" alt="{$APP.LBL_LAST_VIEWED}" title="{$APP.LBL_LAST_VIEWED}" border=0 onClick="fnvshobj(this,'tracker');"></td>
					</tr>
				</table>
			</td>
			<td style="width:20px;">&nbsp;</td>
			<td class="small">
				<!-- Import / Export -->
				<table border=0 cellspacing=0 cellpadding=5>
					<tr>
						<td style="padding-right:0px;padding-left:10px;"><img src="{'tbarImport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
						<td style="padding-right:10px"><img src="{'tbarExport-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
						<td style="padding-right:10px"><img src="{'FindDuplicates-Faded.gif'|@vtiger_imageurl:$THEME}" border="0"></td>
					</tr>
				</table>
			<td style="width:20px;">&nbsp;</td>
			<td class="small">
				<!-- All Menu -->
				<table border=0 cellspacing=0 cellpadding=5>
					<tr>
					{if $CHECK.moduleSettings eq 'yes'}
						<td style="padding-left:10px;"><a href='index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings'><img src="{'settingsBox.png'|@vtiger_imageurl:$THEME}" alt="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" title="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}" border="0"></a></td>
					{/if}
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>