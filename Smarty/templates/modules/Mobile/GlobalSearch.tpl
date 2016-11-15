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
<link rel="stylesheet" href="resources/css/theme.css" >
<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
</header>
<body>
<div data-role="page" data-theme="b">
	<!-- header -->
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed" class="ui-grid-b ui-responsive">
		<a href="index.php?_operation=logout" class="ui-btn ui-corner-all ui-icon-power ui-btn-icon-notext" >Logout</a>
		<h4>{$MOD.LBL_SEARCH_RESULTS}</h4>
	</div><!-- /header -->
	<!-- /header -->
	{foreach item=module key=modulename from=$LISTENTITY}
		<a href="index.php?_operation=listModuleRecords&module={$modulename}" data-role="button" data-corners="false" data-icon="bullets" data-iconpos="right" rel=external>{$MODLABEL.$modulename}</a> 
		<div data-role="collapsible-set"   data-mini="true">	
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