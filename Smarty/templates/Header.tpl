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
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$LBL_CHARSET}">
	<meta name="robots" content="noindex">
	<title>{$USER} - {$MODULE_NAME|@getTranslatedString:$MODULE_NAME} - {$coreBOS_app_name}</title>
	<link REL="SHORTCUT ICON" HREF="{$COMPANY_DETAILS.favicon}">
	{if !empty($SET_CSS_PROPERTIES) && is_file($SET_CSS_PROPERTIES)}
		<link rel="stylesheet" type="text/css" media="all" href="{$SET_CSS_PROPERTIES}">
	{/if}
	<link rel="stylesheet" type="text/css" media="all" href="themes/{$THEME}/style.css">
	{if $Application_JSCalendar_Load neq 0}<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">{/if}
	<link rel="stylesheet" href="include/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css" />
	{include file='Components/ComponentsCSS.tpl'}
	<link rel="stylesheet" href="include/LD/assets/styles/mainmenu.css" type="text/css" />
	<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css" type="text/css" />
	<link rel="stylesheet" href="include/style.css" type="text/css" />
	<style type="text/css">
		html {
			background: url({$coreBOS_app_coverimage}) no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}
	</style>
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
	{include file='BrowserVariables.tpl'}
	{include file='Components/Components.tpl'}
	{if $ONESIGNAL_IS_ACTIVE eq true}
		<script src='https://cdn.onesignal.com/sdks/OneSignalSDK.js' async=''></script>
		<script>
			window.OneSignal = window.OneSignal || [];
			OneSignal.push(function() {
				OneSignal.init({ 'appId': '{$ONESIGNAL_APP_ID}' });
				OneSignal.on('subscriptionChange', function(isSubscribed) {
					if (isSubscribed) {
						OneSignal.push(function() {
						OneSignal.setExternalUserId({$CURRENT_USER_ID});
						OneSignal.setEmail('{$CURRENT_USER_MAIL}');
					});
					}
				});
			});
		</script>
	{/if}
</head>
<body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 class=small style="min-width:1100px; width: 100%">
	<!-- header -->
	<script type="text/javascript" src="include/sw-precache/service-worker-registration.js"></script>
	<script type="text/javascript" src="include/jquery/jquery.js"></script>
	<script type="text/javascript" src="include/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="include/dompurify/purify.min.js"></script>
	<script type="text/javascript" src="include/js/meld.js"></script>
	<script type="text/javascript" src="include/js/corebosjshooks.js"></script>
	<script type="text/javascript" src="include/js/general.js"></script>
	<script type="text/javascript" src="include/js/vtlib.js"></script>
	<script type="text/javascript" id="_current_language_" src="include/js/{$LANGUAGE}.lang.js"></script>
	<script type="text/javascript" src="include/js/QuickCreate.js"></script>
	<script type="text/javascript" src="modules/cbCalendar/script.js"></script>
	<script type="text/javascript" src="include/js/notificationPopup.js"></script>
	{include file='Components/ComponentsJS.tpl'}
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
<div id="status" style="position:absolute;display:none;left:65%;top:95px;height:27px;white-space:nowrap;">
	<div role="status" class="slds-spinner slds-spinner_small slds-spinner_brand">
		<div class="slds-spinner__dot-a"></div>
		<div class="slds-spinner__dot-b"></div>
	</div>
</div>
{if empty($Module_Popup_Edit)}

<!-- LDS Global header -->

<header class="slds-global-header_container noprint" id="global-header" style="position:sticky;">
	<div class="slds-global-header slds-grid slds-grid_align-spread">
		<div class="slds-global-header__item">
			<div class="slds-global-header__logo" style="background-image: url('{$COMPANY_DETAILS.applogo}');"></div>
		</div>
		{if $Application_Global_Search_Active || (isset($GS_AUTOCOMP) && isset($GS_AUTOCOMP['searchin']))}
		{if (isset($GS_AUTOCOMP) && isset($GS_AUTOCOMP['searchin']))}{$GLOBAL_AC = true}{else}{$GLOBAL_AC = false}{/if}
		<div class="slds-global-header__item slds-global-header__item_search">
			<div class="slds-form-element">
				<div class="slds-form-element__control">
					<div class="slds-combobox-group">
						{if $Application_Global_Search_Active}
						<div class="slds-combobox_object-switcher slds-combobox-addon_start">
							<div class="slds-form-element">
								<label class="slds-form-element__label slds-assistive-text" for="globalsearch-moduleselect">{$APP.LBL_SELECT_MODULES_FOR_SEARCH}</label>
								<div class="slds-form-element__control">
									<div class="slds-combobox_container">
										<div id="globalsearch-moduleselect" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-controls="globalsearch-moduleselect" aria-expanded="false" aria-haspopup="listbox" role="combobox">
											<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
												<input type="text" class="slds-input slds-combobox__input slds-combobox__input-value" id="globalsearch-moduleselect-input" aria-controls="UnifiedSearch_moduleformwrapper" autoComplete="off" role="textbox" placeholder="{$APP.LBL_SELECT_MODULES_FOR_SEARCH}" value="" onfocus="UnifiedSearch_GetModules();" />
												<span class="slds-icon_container slds-icon-utility-down slds-input__icon slds-input__icon_right">
													<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
													</svg>
												</span>
											</div>
											<div id="UnifiedSearch_modulelistwrapper" class="slds-dropdown slds-dropdown_length-10 slds-dropdown_x-small" role="listbox">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{/if}
						{if $GLOBAL_AC || $Application_Global_Search_Active}
						<div class="slds-combobox_container slds-combobox-addon_end">
							{if $Application_Global_Search_Active}
							<form name="UnifiedSearch" method="post" action="index.php" style="margin:0px" onsubmit="if (document.getElementById('query_string').value=='') return false; VtigerJS_DialogBox.block();">
							{else}
							<form name="UnifiedSearch" style="margin:0px" onsubmit="return false;">
							{/if}
								<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" id="primary-search-combobox-id-1" role="combobox">
									<input type="hidden" name="action" value="UnifiedSearch">
									<input type="hidden" name="module" value="Utilities">
									<input type="hidden" name="search_onlyin" value="--USESELECTED--">
									<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_left slds-global-search__form-element" role="none">
										<span class="slds-icon_container slds-icon-utility-search slds-input__icon slds-input__icon_left">
												<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
													<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
												</svg>
											</span>
										<input name="query_string" id="query_string" class="slds-input slds-combobox__input{if $GLOBAL_AC} autocomplete-input{/if}" type="text" role="textbox" placeholder="{$APP.LBL_SEARCH_TITLE}{$coreBOS_app_name}" aria-autocomplete="list" autoComplete="off" data-autocomp='{$GS_AUTOCOMP|@json_encode}' />
										{if $GLOBAL_AC}
										<div role="listbox" class="">
											<ul class="slds-listbox slds-listbox_vertical slds-dropdown slds-dropdown_fluid relation-autocomplete__target" style="opacity: 0;display:block;visibility: visible;" role="presentation"></ul>
										</div>
										{/if}
									</div>
								</div>
							</form>
						</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
		{/if}
		<div class="slds-global-header__item">
			<ul class="slds-global-actions">
				<li class="slds-global-actions__item">
					<div class="slds-global-actions__favorites slds-dropdown-trigger">
						<div class="slds-button-group">
							<button class="slds-button slds-button_icon slds-global-actions__favorites-action slds-button_icon-border " aria-pressed="false" title="{$APP.LNK_HELP}" onclick="window.open('{$HELP_URL}')">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
									</svg>
									<span class="slds-assistive-text">{$APP.LNK_HELP}</span>
							</button>
							<button class="slds-button slds-button_icon slds-global-actions__favorites-action slds-button_icon-border" aria-pressed="false" title="{$APP.LBL_LAST_VIEWED}" onclick="panelViewToggle('cbds-last-visited');">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#attach"></use>
								</svg>
								<span class="slds-assistive-text">{$APP.LBL_LAST_VIEWED}</span>
							</button>
							<button
								class="slds-button slds-button_icon slds-global-actions__notifications slds-global-actions__item-action slds-global-actions__favorites-action slds-button_icon-border slds-button_last" title="{'LBL_NOTIFICATION'|@getTranslatedString:'Settings'}"
								aria-live="assertive"
								aria-atomic="true"
								id="header_notification_button"
								onclick="ActivityReminderCallback(true);"
							>
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#notification"></use>
								</svg>
								<span class="slds-assistive-text"></span>
							</button>
							<span aria-hidden="true" class="slds-notification-badge slds-incoming-notification" id="header_notification_items"></span>
						</div>
					</div>
				</li>
				{if $HEADERLINKS}
				<li class="slds-global-actions__item">
					<div class="slds-dropdown-trigger slds-dropdown-trigger_hover">
						<button class="slds-button slds-button_icon slds-global-actions__favorites-action slds-button_icon slds-button_icon-border" aria-haspopup="true" title="{$APP.LBL_MORE}">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
							</svg>
							<span class="slds-assistive-text">{$APP.LBL_MORE}</span>
						</button>
						<div class="slds-dropdown slds-dropdown_right">
							<ul class="slds-dropdown__list" role="menu" aria-label="{$APP.LBL_MORE}">
								{foreach key=actionlabel item=HEADERLINK from=$HEADERLINKS}
									{assign var="headerlink_href" value=$HEADERLINK->linkurl}
									{assign var="headerlink_label" value=$HEADERLINK->linklabel}
									{if $headerlink_label eq ''}
										{assign var="headerlink_label" value=$headerlink_href}
									{else}
										{assign var="headerlink_label" value=$headerlink_label|@getTranslatedString:$HEADERLINK->module()}
									{/if}
									<li class="slds-dropdown__item" role="presentation">
										<a href="{$headerlink_href}" role="menuitem" title="{$headerlink_label}">
											<span class="slds-truncate" >{$headerlink_label}</span>
										</a>
									</li>
								{/foreach}
							</ul>
						</div>
					</div>
				</li>
				{/if}
				{if !empty($ADMIN_LINK)}
				<li class="slds-global-actions__item">
					<div class="slds-dropdown-trigger slds-dropdown-trigger_hover">
						<button class="slds-button slds-button_icon slds-global-actions__favorites-action slds-button_icon slds-button_icon-border" aria-haspopup="true" title="{$APP.LBL_CRM_SETTINGS}" onclick="window.location.assign('index.php?module=Settings&action=index')">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#settings"></use>
							</svg>
							<span class="slds-assistive-text">{'LBL_CRM_SETTINGS'|@getTranslatedString:$MODULE_NAME}</span>
						</button>
						<div class="slds-dropdown slds-dropdown_right">
							<ul class="slds-dropdown__list" role="menu" aria-label="{$APP.LBL_CRM_SETTINGS}">
								{foreach key=actionlabel item=actionlink from=$HEADERS}
									<li class="slds-dropdown__item" role="presentation">
									<a href="{$actionlink}" role="menuitem" tabindex="0">
										<span class="slds-truncate" title="{$actionlabel}">{$actionlabel}</span>
									</a>
								</li>
								{/foreach}
								<li class="slds-has-divider_top-space" role="separator"></li>
								<li class="slds-dropdown__item" role="presentation">
									<a href="index.php?module=Settings&action=index" role="menuitem" tabindex="-1">
										<span class="slds-truncate" title="{$APP.LBL_CRM_SETTINGS}">{'LBL_CRM_SETTINGS'|@getTranslatedString:$MODULE_NAME}</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</li>
				{/if}
				<li class="slds-global-actions__item">
					<div class="slds-dropdown-trigger slds-dropdown-trigger_hover">
						<button class="slds-button slds-global-actions__avatar slds-global-actions__item-action" title="{$USER}" aria-haspopup="true" onclick="window.location.assign('index.php?module=Users&action=DetailView&record={$CURRENT_USER_ID}')">
							<span class="slds-avatar slds-avatar_circle slds-avatar_medium">
								{if $CURRENT_USER_IMAGE}
								<img alt="{$USER}" src="{$CURRENT_USER_IMAGE}" />
								{else}
								<img alt="{$USER}" src="include/LD/assets/images/avatar2.jpg" />
								{/if}
							</span>
						</button>
						<div class="slds-dropdown slds-dropdown_right">
							<ul class="slds-dropdown__list" role="menu" aria-label="{$APP.LBL_MORE}">
								<li class="slds-dropdown__item" role="presentation">
									<a href="index.php?module=Users&action=DetailView&record={$CURRENT_USER_ID}" role="menuitem" tabindex="0">
										<span class="slds-truncate" title="{$USER}"><strong>{$USER}</strong></span>
									</a>
								</li>
								<li class="slds-has-divider_top-space" role="separator"></li>
								<li class="slds-dropdown__item" role="presentation">
									<a href="index.php?module=Users&action=DetailView&record={$CURRENT_USER_ID}" role="menuitem" tabindex="0">
										<span class="slds-truncate" title="{$APP.LBL_MY_PREFERENCES}">{$APP.LBL_MY_PREFERENCES}</span>
									</a>
								</li>
								<li class="slds-dropdown__item" role="presentation">
									<a href="index.php?module=Users&action=Logout&{$CSRFNAME}={''|csrf_get_tokens}" role="menuitem" tabindex="-1">
										<span class="slds-truncate" title="{$APP.LBL_LOGOUT}">{$APP.LBL_LOGOUT}</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</li>
				{* <li class="slds-global-actions__item">
					<button class="slds-button slds-button_icon slds-global-actions__favorites-action slds-button_icon slds-button_icon-border" aria-haspopup="true" title="{$APP.LBL_LOGOUT}" onclick="window.location.assign('index.php?module=Users&action=Logout')">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#logout"></use>
						</svg>
						<span class="slds-assistive-text">{$APP.LBL_LOGOUT}</span>
					</button>
				</li> *}
			</ul>
		</div>
	</div>
	{if $COREBOS_HEADER_PREMENU}
	<div style="width:100%; background-color:#fff;"  id="premenu-wrapper">
	{$COREBOS_HEADER_PREMENU}
	</div>
	{/if}
	<div class="noprint">
		<div class="slds-context-bar">
			<div class="slds-context-bar__primary slds-context-bar__item_divider-right">
				<div class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger_click slds-no-hover">
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
						<span class="slds-truncate" title="{$coreBOS_app_name}">{$coreBOS_app_nameHTML}</span>
					</span>
				</div>
			</div>
		{call cbmenu menu=$MENU}
		</div>
	</div>
</header>
<!-- END LDS Global header -->
<a name="top"></a>

<div id='miniCal' style='position:absolute; display:none; left:100px; top:100px; z-index:100000'></div>

{if $MODULE_NAME eq 'Calendar4You'}
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
<!-- header - master tabs -->

{include file="Clock.tpl"}

<div id="qcform" style="position:absolute;width:700px;top:80px;left:450px;z-index:90000;"></div>

<!-- Last visited panel -->
<div id="cbds-last-visited" class="slds-panel slds-size_medium slds-panel_docked slds-panel_docked-right slds-is-open slds-is-fixed cbds-last-visited containernpanel" aria-hidden="false" style="height: 90%;">
<div class="slds-panel__header cbds-bg-blue--gray slds-text-color_default slds-text-color_inverse">
	<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="{$APP.LBL_LAST_VIEWED}">{$APP.LBL_LAST_VIEWED}
	</h2>
	<button class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse slds-panel__close" title="{'Close LAST_VIEWED'|@getTranslatedString}" onclick="panelViewHide(document.getElementById('cbds-last-visited'));">
		<svg class="slds-button__icon" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
		</svg>
		<span class="slds-assistive-text">{'Close LAST_VIEWED'|@getTranslatedString}</span>
	</button>
</div>
<div class="slds-panel__body containernpanel" style="height: 92%;">
	{foreach name=trackinfo item=trackelements from=$TRACINFO}
		<article class="slds-card">
			<div class="slds-card__header slds-grid">
				<header class="slds-media slds-media_center slds-has-flexi-truncate">
					<div class="slds-media__figure">
						<span class="{$trackelements.__ICONContainerClass}" title="{$trackelements.module_name}">
							<svg class="{$trackelements.__ICONClass}" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/{$trackelements.__ICONLibrary}-sprite/svg/symbols.svg#{$trackelements.__ICONName}"></use>
							</svg>
						<span class="slds-assistive-text">{$trackelements.module_name}</span>
					</span>
					</div>
					<div class="slds-media__body">
						<h2 class="slds-card__header-title slds-truncate">
							<a href="index.php?module={$trackelements.module_name}&action=DetailView&record={$trackelements.crmid}" class="slds-card__header-link" title="{$trackelements.module_name}">
								<span>{$trackelements.item_summary}</span>
							</a>
						</h2>
						<span></span>
					</div>
				</header>
			</div>
		</article>
	{/foreach}
</div>
</div>
<!-- Last // visited panel -->

<div id="mainsettings" class="drop_mnu_user" onmouseout="fnHideDrop('mainsettings');" onmouseover="fnvshNrm('mainsettings');" style="width:180px;">
	<ul>
		{foreach key=actionlabel item=actionlink from=$HEADERS}
			<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger_hover" aria-haspopup="true">
				<a href="{$actionlink}" class="slds-context-bar__label-action" title="{$actionlabel}">
					<span class="slds-truncate">{$actionlabel}</span>
				</a>
			</li>
		{/foreach}
		<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger_hover" aria-haspopup="true">
			<a href="index.php?module=Settings&action=index" class="slds-context-bar__label-action" title="{'LBL_CRM_SETTINGS'|@getTranslatedString:$MODULE_NAME}">
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
<audio id="newEvents" src="{$Calendar_Notification_Sound}" preload="auto"></audio>
<div id="cbds-notificationpanel" class="slds-panel slds-size_medium slds-panel_docked slds-panel_docked-right slds-is-open slds-is-fixed cbds-last-visited containernpanel" aria-hidden="false" style="height: 90%;">
<div class="slds-panel__header cbds-bg-blue--gray slds-text-color_default slds-text-color_inverse">
	<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="{'LBL_NOTIFICATION'|@getTranslatedString:'Settings'}">{'LBL_NOTIFICATION'|@getTranslatedString:'Settings'}
	</h2>
	<button class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse slds-panel__refresh" title="{'LBL_REFRESH'|@getTranslatedString}" onclick="ActivityReminderCallback();">
		<svg class="slds-button__icon" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#refresh"></use>
		</svg>
		<span class="slds-assistive-text">{'LBL_REFRESH'|@getTranslatedString}</span>
	</button>
	<button class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse slds-panel__close" title="{'LBL_CLOSE'|@getTranslatedString}" onclick="panelViewHide(document.getElementById('cbds-notificationpanel'));">
		<svg class="slds-button__icon" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
		</svg>
		<span class="slds-assistive-text">{'LBL_CLOSE'|@getTranslatedString}</span>
	</button>
</div>
<div class="slds-panel__body containernpanel" style="height: 92%;">
<ul id="todolist"></ul>
</div>
</div>
<!-- End -->

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
