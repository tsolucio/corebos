{strip}
<!DOCTYPE html>
<head>
	<title>{$MOD.LBL_ERROR}</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta charset="utf-8">
	<link REL="SHORTCUT ICON" HREF="resources/images/favicon.ico">	
	<link rel="stylesheet" href="resources/css/jquery.mobile-1.4.5.min.css">	
	<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
	<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css" >
	<link rel="stylesheet" href="resources/css/jquery.mobile.icons.min.css" >
	<link rel="stylesheet" href="resources/css/theme.css" >
	<script type="text/javascript" src="resources/jquery.blockUI.js" ></script>
	<script type="text/javascript" src="resources/crmtogo.js"></script>
	<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
</head>
<body> 
<div data-role="page" data-theme="b" data-mini="true" id="unsupported_page">
	<!-- header -->
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<h2>{$MOD.LBL_ERROR}</h2>
		<a href="#"  onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
	</div>
	<!-- /header -->
	<h4 class='error'>{$MESSAGE}</h4>
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<h1></h1>
	</div>	
</div>		
</body>
{/strip}