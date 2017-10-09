{strip}
<!DOCTYPE html>
<header>
<title>{$MOD.LBL_SEARCH_RESULTS}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link REL="SHORTCUT ICON" HREF="resources/images/crm-now_icon.ico">
<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css" >
<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
<link rel="stylesheet" href="resources/css/jquery.mobile.icons.min.css" >
<script type="text/javascript" src="resources/jquery.blockUI.js" ></script>
<link rel="stylesheet" href="resources/css/theme.css" >
<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
</header>
<body>
<div data-role="page" data-theme="b">
	{include file="modules/Mobile/PanelMenu.tpl"}
	<!-- header -->
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed" class="ui-grid-b ui-responsive">
		<h4>{$MOD.LBL_SEARCH_RESULTS}</h4>
		<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
	</div><!-- /header -->
	<!-- /header -->
	{foreach item=module key=modulename from=$LISTENTITY}
		<a href="?_operation=listModuleRecords&module={$modulename}" data-role="button" data-corners="false" data-icon="bullets" data-iconpos="right">{$MODLABEL.$modulename}</a>
		<div data-role="collapsible-set" data-mini="true">
			<ul data-role="listview" data-theme="c" >
			{foreach item=reco from=$module}
				{if $reco.id neq ''}
				{assign var="output1" value=$reco.entry1}
				{assign var="output2" value=$reco.entry2}
				{assign var="RECORD" value=$reco.id}
				<li><a href="?_operation=fetchRecord&record={$RECORD}" target="_self">{$output1} {$output2}</a></li>
				{/if}
			{/foreach}
			</ul>
		</div>
	{/foreach}
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<h2></h2>
	</div>
</div>
</body>
{/strip}	