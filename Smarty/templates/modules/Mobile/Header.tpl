{strip}
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	{if $IS_SAFARI}
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
	{else}
	<meta name="viewport" content="width=device-width, initial-scale=1">
	{/if}
	<link REL="SHORTCUT ICON" HREF="resources/images/favicon.ico">
	<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
	<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css">
	<link rel="stylesheet" href="resources/css/theme.css">
	<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
	<script type="text/javascript" src="resources/jquery.blockUI.js"></script>
	<script type="text/javascript" src="resources/crmtogo.js"></script>
	<script type="text/javascript" src="resources/settings.js"></script>
	<title>{if isset($TITLE)}{$TITLE}{else}CRM{/if}</title>
	<link rel="stylesheet" href="resources/css/jquery.mobile.icons.min.css">
</head>
{/strip}