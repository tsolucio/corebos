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
	<link rel="stylesheet" href="include/LD/assets/styles/mainmenu.css" type="text/css" />
	<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css" type="text/css" />
	<link rel="stylesheet" href="include/style.css" type="text/css" />
</head>
{include file='BrowserVariables.tpl'}
{include file='Components/Components.tpl'}
<body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 class=small style="min-width:1100px; width: 100%">
	<script type="text/javascript" src="include/sw-precache/service-worker-registration.js"></script>
	<script type="text/javascript" src="include/jquery/jquery.js"></script>
	<script type="text/javascript" src="include/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/meld.js"></script>
	<script type="text/javascript" src="include/js/corebosjshooks.js"></script>
	<script type="text/javascript" src="include/js/general.js"></script>
	<script type="text/javascript" src="include/js/vtlib.js"></script>
	<script type="text/javascript" id="_current_language_" src="include/js/{$LANGUAGE}.lang.js"></script>
	<script type="text/javascript" src="modules/cbCalendar/script.js"></script>
	<script type="text/javascript" src="include/js/notificationPopup.js"></script>
	<script type="text/javascript" src="modules/Calendar4You/fullcalendar/lib/moment.min.js"></script>
	{if $Application_JSCalendar_Load neq 0}
	<script type="text/javascript" src="jscalendar/calendar.js"></script>
	<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
	<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
	{/if}
	{* PREFECTHING IMAGE FOR BLOCKING SCREEN USING VtigerJS_DialogBox API *}
	<img src="{'layerPopupBg.gif'|@vtiger_imageurl:$THEME}" style="display: none;"/>
<script>
	function saveMapAction(parameters) {
		fetch(
			'index.php?module=cbMap&action=cbMapAjax&actionname=mapactions&method=saveMap',
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: parameters+'&'+csrfMagicName+'='+csrfMagicToken
			}
		).then(response => {
			window.opener.location.reload();
			window.close();
		});
	}
</script>
