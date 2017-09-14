{*<!--
/*********************************************************************************
	** The contents of this file are subject to the vtiger CRM Public License Version 1.0
	 * ("License"); You may not use this file except in compliance with the License
	 * The Original Code is:	vtiger CRM Open Source
	 * The Initial Developer of the Original Code is vtiger.
	 * Portions created by vtiger are Copyright (C) vtiger.
	 * All Rights Reserved.
 ********************************************************************************/
-->*}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$LBL_CHARSET}">
	<title>{$USER} - {$MODULE_NAME|@getTranslatedString:$MODULE_NAME} - {$coreBOS_app_name}</title>
	<link REL="SHORTCUT ICON" HREF="{$FAVICON}">
	<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
	{if $Application_JSCalendar_Load neq 0}<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">{/if}
	<link rel="stylesheet" href="include/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css" />
	<link rel="stylesheet" href="include/LD/assets/styles/slds-combobox.css" type="text/css" />
	<link rel="stylesheet" href="include/LD/assets/styles/mainmenu.css" type="text/css" />
	<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css" type="text/css" />
	<link rel="stylesheet" href="include/LD/assets/styles/customLD.css" type="text/css" />
{* vtlib customization: Inclusion of custom javascript and css as registered *}
{if $HEADERCSS}
	<!-- Custom Header CSS -->
	{foreach item=HDRCSS from=$HEADERCSS}
	<link rel="stylesheet" type="text/css" href="{$HDRCSS->linkurl}" />
	{/foreach}
	<!-- END -->
{/if}
{* END *}
	<!-- ActivityReminder customization for callback -->
{literal}
	<style type="text/css">div.fixedLay1 { position:fixed; }</style>
	<!--[if lte IE 6]>
	<style type="text/css">div.fixedLay { position:absolute; }</style>
	<![endif]-->
	<style type="text/css">div.drop_mnu_user { position:fixed; }</style>
	<!--[if lte IE 6]>
	<style type="text/css">div.drop_mnu_user { position:absolute; }</style>
	<![endif]-->
{/literal}
	<!-- End -->
</head>
{include file='BrowserVariables.tpl'}
{include file="Components.tpl"}
<body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 class=small>
	<a name="top"></a>
	<!-- header -->
	<script type="text/javascript" src="include/sw-precache/service-worker-registration.js"></script>
	<script type="text/javascript" src="include/jquery/jquery.js"></script>
	<script type="text/javascript" src="include/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/meld.js"></script>
	<script type="text/javascript" src="include/js/corebosjshooks.js"></script>
	<script type="text/javascript" src="include/js/general.js"></script>
	<script type="text/javascript" src="include/js/vtlib.js"></script>
	<script type="text/javascript" id="_current_language_" src="include/js/{$LANGUAGE}.lang.js"></script>
	<script type="text/javascript" src="include/js/QuickCreate.js"></script>
	{if $CALCULATOR_DISPLAY eq 'true'}
	<script type="text/javascript" src="include/calculator/calc.js"></script>
	{/if}
	<script type="text/javascript" src="modules/Calendar/script.js"></script>
	<script type="text/javascript" src="include/js/notificationPopup.js"></script>
	<script type="text/javascript" src="modules/Calendar4You/fullcalendar/lib/moment.min.js"></script>
	{if $Application_JSCalendar_Load neq 0}
	<script type="text/javascript" src="jscalendar/calendar.js"></script>
	<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
	<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
	{/if}
<!-- asterisk Integration -->
{if $USE_ASTERISK eq 'true'}
	<script type="text/javascript" src="include/js/asterisk.js"></script>
	<script type="text/javascript">
	if(typeof(use_asterisk) == 'undefined') use_asterisk = true;
	</script>
{/if}
	<script type="text/javascript">
	<!-- browser tab identification on ajax calls -->
	jQuery(document).ready(function() {ldelim}
		jQuery(document).ajaxSend(function() {ldelim}
			document.cookie = "corebos_browsertabID="+corebos_browsertabID;
		{rdelim});
	{rdelim});
	</script>

{* vtlib customization: Inclusion of custom javascript and css as registered *}
{if $HEADERSCRIPTS}
	<!-- Custom Header Script -->
	{foreach item=HEADERSCRIPT from=$HEADERSCRIPTS}
	<script type="text/javascript" src="{$HEADERSCRIPT->linkurl}"></script>
	{/foreach}
	<!-- END -->
{/if}

	{* PREFECTHING IMAGE FOR BLOCKING SCREEN USING VtigerJS_DialogBox API *}
	<img src="{'layerPopupBg.gif'|@vtiger_imageurl:$THEME}" style="display: none;"/>
{if empty($Module_Popup_Edit)}
<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class="small">
	<tr>
		<td valign=top align=left><img src="test/logo/{$FRONTLOGO}" alt="{$COMPANY_DETAILS.name}" title="{$COMPANY_DETAILS.name}" border=0 style="width: 15em;height: 4.2em;"></td>
		<td align="center" valign=bottom>
			{if $Application_Global_Search_Active || (isset($GS_AUTOCOMP) && isset($GS_AUTOCOMP['searchin']))}
				<div class="slds-combobox_container slds-has-object-switcher" id="search" style="width: 50%;margin: .5rem 0;">
			{else}
				<div class="slds-combobox_container slds-has-object-switcher" style="width: 50%;margin-top: .5rem;">
			{/if}
					<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
						<button class="slds-button slds-button_icon" onclick="UnifiedSearch_SelectModuleForm(this);" aria-haspopup="true" title="Select object to search in" style="padding: .5rem;">
							<img src="themes/images/chevrondown_60.png" style="width: 16px;" >
						</button>
					</div>
					<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
						<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right">
							{if $Application_Global_Search_Active}
								<form name="UnifiedSearch" method="post" action="index.php" style="margin:0px" onsubmit="if (document.getElementById('query_string').value=='') return false; VtigerJS_DialogBox.block();">
							{else}
								<form name="UnifiedSearch" style="margin:0px" onsubmit="return false;">
							{/if}
									{if $Application_Global_Search_Active || (isset($GS_AUTOCOMP) && isset($GS_AUTOCOMP['searchin']))}
									<input type="hidden" name="action" value="UnifiedSearch">
									<input type="hidden" name="module" value="Home">
									<input type="hidden" name="parenttab" value="{$CATEGORY}">
									<input type="hidden" name="search_onlyin" value="--USESELECTED--">
									<input type="text" name="query_string" id="query_string" value="{$QUERY_STRING}" class="slds-input slds-combobox__input" onFocus="this.value=''" autocomplete="off" data-autocomp='{$GS_AUTOCOMP|@json_encode}'>
									<input type="image" alt="{$APP.LBL_FIND}" title="{$APP.LBL_FIND}" src="themes/softed/images/btnL3Search.gif" width="16px" style="vertical-align: middle;">
									{/if}
								</form>
						</div>
					</div>

				</div>
		</td>
		<td class=small nowrap align="right" style="padding-right:10px;">
			<table border=0 cellspacing=0 cellpadding=0 class="headerlink-lds">
				<tr class="td-space">
					<td valign="top" class="genHeaderSmall" style="padding-left:10px;padding-top:3px;">
						<span class="userName">{$USER}</span>
					</td>
					<td class="small" valign="bottom" nowrap>
						<a id="headerUser" class="headerlink" href="index.php?module=Users&action=DetailView&record={$CURRENT_USER_ID}&modechk=prefview">
							<img src="{$IMAGEPATH}user.PNG" border=0 style="padding: 0px;padding-left:5px" title="{$APP.LBL_MY_PREFERENCES}" alt="{$APP.LBL_MY_PREFERENCES}">
						</a>
					</td>
					{* vtlib customization: Header links on the top panel *}
					{if $HEADERLINKS}
						<td valign="bottom" nowrap class="small" nowrap>
							<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_headerLinksLay');" onclick="fnvshobj(this,'vtlib_headerLinksLay');"><img src="{'menu_more.png'|@vtiger_imageurl:$THEME}" border=0 style="padding: 0px;padding-left:5px"></a>
							<div class="drop_mnu_user" style="display: none; width:155px;" id="vtlib_headerLinksLay"
								 onmouseout="fninvsh('vtlib_headerLinksLay')" onmouseover="fnvshNrm('vtlib_headerLinksLay')">
								<ul>
									{foreach key=actionlabel item=HEADERLINK from=$HEADERLINKS}
										{assign var="headerlink_href" value=$HEADERLINK->linkurl}
										{assign var="headerlink_label" value=$HEADERLINK->linklabel}
										{if $headerlink_label eq ''}
											{assign var="headerlink_label" value=$headerlink_href}
										{else}
											{assign var="headerlink_label" value=$headerlink_label|@getTranslatedString:$HEADERLINK->module()}
										{/if}
										<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger--hover" aria-haspopup="true">
											<a href="{$headerlink_href}" class="slds-context-bar__label-action" title="{$headerlink_label}">
													<span class="slds-truncate">{$headerlink_label}</span>
											</a>
										</li>
									{/foreach}
								</ul>
							</div>
						</td>
					{/if}
				{if $HELP_URL}
				<td valign="bottom" nowrap class="small" nowrap><a id="headerHelp" class="headerlink" href="{$HELP_URL}" target="_blank"><img src="{$IMAGEPATH}info.PNG" border=0 style="padding: 0px;padding-left:5px" title="{$APP.LNK_HELP}"></a></td>
				{/if}
				{if !empty($ADMIN_LINK)}
					<td valign="bottom" nowrap class="small" onmouseout="fnHideDrop('mainsettings');" onmouseover="fnDropDown(this,'mainsettings');" nowrap><a id="headerSettings" class="headerlink" href="index.php?module=Settings&action=index&parenttab=" id="settingslink"><img src="{$IMAGEPATH}mainSettings.PNG" border=0 style="padding: 0px;padding-left:5px"></a></td>
				{/if}
				<td valign="bottom" nowrap class="small" nowrap><a id="headerLogout" class="headerlink" href="index.php?module=Users&action=Logout"> <img src="themes/images/logout.png" border=0 style="padding: 0px;padding-left:5px " title="{$APP.LBL_LOGOUT}" alt="{$APP.LBL_LOGOUT}"></a></td>
			</tr>
			</table>
		</td>
	</tr>
</TABLE>
{if $ANNOUNCEMENT}
	<table width ="100%">
		<tr colspan="3" width="100%">
			<td width="90%" align=center>
				<marquee id="rss" direction="left" scrolldelay="10" scrollamount="3" behavior="scroll" class="marStyle" onMouseOver="javascript:stop();" onMouseOut="javascript:start();">&nbsp;{$ANNOUNCEMENT}</marquee>
			</td>
			<td width="10%" align="right" style="padding-right:38px;"><img src="{'Announce.PNG'|@vtiger_imageurl:$THEME}"></td>
		</tr>
	</table>
{/if}

<div id='miniCal' style='width:300px; position:absolute; display:none; left:100px; top:100px; z-index:100000'></div>

{if $MODULE_NAME eq 'Calendar'}
	<div id="CalExport" style="width:300px; position:absolute; display:none; left:500px; top:100px; z-index:100000" class="layerPopup">
		<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
			<tr>
				<td class="genHeaderSmall" nowrap align="left" width="30%" >{$APP.LBL_EXPORT} </td>
				<td align="right"><a href='javascript:ghide("CalExport");'><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="right" border="0"></a></td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
			<tr>
				<td class="small">
					<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
						<tr>
							<td align="right" nowrap class="cellLabel small">
								<input class="small" type='radio' name='exportCalendar' value = 'iCal' onclick="jQuery('#ics_filename').removeAttr('disabled');" checked /> iCal Format
							</td>
							<td align="left">
								<input class="small" type='text' name='ics_filename' id='ics_filename' size='25' value='export.calendar'/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr>
				<td class="small" align="center">
					<input type="button" onclick="return exportCalendar();" value="Export" class="crmbutton small edit" name="button"/>
				</td>
			</tr>
		</table>
	</div>
	<div id='CalImport' style='position:absolute; display:none; left:500px; top:100px; z-index:100000' class="layerPopup">
		{assign var='label_filename' value='LBL_FILENAME'}
		<form name='ical_import' id='ical_import' onsubmit="VtigerJS_DialogBox.block();" enctype="multipart/form-data" action="index.php" method="POST">
			<input type='hidden' name='module' value=''>
			<input type='hidden' name='action' value=''>
			<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
				<tr>
					<td class="genHeaderSmall" nowrap align="left" width="30%" id="editfolder_info">{$APP.LBL_IMPORT}</td>
					<td align="right"><a href='javascript:ghide("CalImport");'><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a></td>
				</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
				<tr>
					<td class="small">
						<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
							<tr>
								<td align="right" nowrap class="cellLabel small"><b>{$label_filename|@getTranslatedString} </b></td>
								<td align="left">
									<input class="small" type='file' name='ics_file' id='ics_file'/>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
				<tr>
					<td class="small" align="center">
						<input type="button" onclick="return importCalendar();" value="Import" class="crmbutton small edit" name="button"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
{/if}
{$COREBOS_HEADER_PREMENU}
<!-- header - master tabs -->
<div class="noprint">
<div class="slds-context-bar">
	<div class="slds-context-bar__primary slds-context-bar__item--divider-right">
		<div class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger--click slds-no-hover">
			<div class="slds-context-bar__icon-action">
				<a href="index.php" class="slds-icon-waffle_container slds-context-bar__button">
					<div class="slds-icon-waffle">
						<div class="slds-r1"></div>
						<div class="slds-r2"></div>
						<div class="slds-r3"></div>
						<div class="slds-r4"></div>
						<div class="slds-r5"></div>
						<div class="slds-r6"></div>
						<div class="slds-r7"></div>
						<div class="slds-r8"></div>
						<div class="slds-r9"></div>
					</div>
				</a>
			</div>
			<span class="slds-context-bar__label-action slds-context-bar__app-name">
				<span class="slds-truncate" title="{$coreBOS_app_name}">{$coreBOS_app_name}</span>
			</span>
		</div>
	</div>
{call cbmenu menu=$MENU}
</div>
</div>
</td>

<div id="calculator_cont" style="position:absolute; z-index:10000" ></div>
{include file="Clock.tpl"}

<div id="qcform" style="position:absolute;width:910px;top:80px;left:450px;z-index:90000;"></div>

<!-- Unified Search module selection feature -->
<div id="UnifiedSearch_moduleformwrapper" style="position:absolute;width:417px;z-index:100002;display:none;"></div>

<div id="status" style="position:absolute;display:none;left:850px;top:95px;height:27px;white-space:nowrap;"><img src="{'status.gif'|@vtiger_imageurl:$THEME}"></div>

<div id="tracker" style="display:none;position:absolute;z-index:100000001;" class="layerPopup">
	<table class="slds-table slds-no-row-hover" width="200">
		<tr class="slds-text-title--header" style="cursor:move;">
			<th scope="col" id="Track_Handle">
				<div class="slds-truncate moduleName">
					<strong>{$APP.LBL_LAST_VIEWED}</strong>
				</div>
			</th>
			<th scope="col" style="padding: .5rem 0 .5rem 2.5rem;">
				<div class="slds-truncate">
					<a href="javascript:;"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" onClick="fninvsh('tracker')" hspace="5" align="absmiddle"></a>
				</div>
			</th>
		</tr>
	</table>
	<table class="slds-table slds-no-row-hover slds-table--bordered" width="200">
		{foreach name=trackinfo item=trackelements from=$TRACINFO}
			<tr class="slds-line-height--reset">
				<td class="trackerListBullet" align="center" width="12">{$smarty.foreach.trackinfo.iteration}</td>
				<td class="trackerList"> <a href="index.php?module={$trackelements.module_name}&action=DetailView&record={$trackelements.crmid}&parenttab={$CATEGORY}">{$trackelements.item_summary}</a></td>
			</tr>
		{/foreach}
	</table>
</div>

<div id="mainsettings" class="drop_mnu_user" onmouseout="fnHideDrop('mainsettings');" onmouseover="fnvshNrm('mainsettings');" style="width:180px;">
	<ul>
		{foreach key=actionlabel item=actionlink from=$HEADERS}
			<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger--hover" aria-haspopup="true">
				<a href="{$actionlink}" class="slds-context-bar__label-action" title="{$actionlabel}">
						<span class="slds-truncate">{$actionlabel}</span>
				</a>
			</li>
		{/foreach}
		<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger--hover" aria-haspopup="true">
			<a href="index.php?module=Settings&action=index&parenttab=" class="slds-context-bar__label-action" title="{'LBL_CRM_SETTINGS'|@getTranslatedString:$MODULE_NAME}">
					<span class="slds-truncate">{'LBL_CRM_SETTINGS'|@getTranslatedString:$MODULE_NAME}</span>
			</a>
		</li>
	</ul>
</div>
<script type="text/javascript">
	jQuery('#tracker').draggable({ldelim} handle: "#Track_Handle" {rdelim});
</script>
<script type="text/javascript" src="modules/evvtMenu/evvtMenu.js"></script>
</div>
<!-- ActivityReminder Customization for callback -->
<div class="lvtCol fixedLay1" id="ActivityRemindercallback" style="border: 0; right: 0px; bottom: 2px; display:none; padding: 2px; z-index: 10; font-weight: normal;" align="left">
</div>

<!-- divs for asterisk integration -->
<div class="lvtCol fixedLay1" id="notificationDiv" style="float: right; padding-right: 5px; overflow: hidden; border-style: solid; right: 0px; border-color: rgb(141, 141, 141); bottom: 0px; display: none; padding: 2px; z-index: 10; font-weight: normal;" align="left">
</div>

<div id="OutgoingCall" style="display: none;position: absolute;z-index:200;" class="layerPopup">
	<table border='0' cellpadding='5' cellspacing='0' width='100%'>
		<tr style='cursor:move;' >
			<td class='mailClientBg small' id='outgoing_handle'>
				<b>{$APP.LBL_OUTGOING_CALL}</b>
			</td>
		</tr>
	</table>
	<table border='0' cellpadding='0' cellspacing='0' width='100%' class='hdrNameBg'>
		</tr>
		<tr><td style='padding:10px;' colspan='2'>
			{$APP.LBL_OUTGOING_CALL_MESSAGE}
		</td></tr>
	</table>
</div>
<!-- divs for asterisk integration :: end-->
{/if}