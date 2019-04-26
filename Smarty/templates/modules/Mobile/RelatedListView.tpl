<!DOCTYPE html>
<head>
<!-- the following header content gets only loaded with a direct http call-->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<link REL="SHORTCUT ICON" HREF="resources/images/favicon.ico">
<link rel="stylesheet" href="resources/css/jquery.mobile-1.4.5.min.css">
<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
<script type="text/javascript" src="resources/getScrollcontent.js"></script>
<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css" >
<link rel="stylesheet" href="resources/css/jquery.mobile.icons.min.css" >
<link rel="stylesheet" href="resources/css/theme.css" >
<script type="text/javascript" src="resources/jquery.blockUI.js" ></script>
<script type="text/javascript" src="resources/crmtogo.js"></script>
<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
{literal}
	<!-- define the collapsible button size-->
	<style>	
	.collapse_header .ui-btn-text{
		font-size: 10px
	}
	</style>
{/literal}
</head>
<body>
<div data-role="page" data-theme="b" >
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}"  data-position="fixed">
		<a href="index.php?_operation=fetchRecord&record={$RECORDID}" class="ui-btn ui-btn-left ui-corner-all ui-icon-back ui-btn-icon-notext" rel="external">{$MOD.LBL_CANCEL}</a>
		<h4>{$MOD.LBL_RELATED_LIST}</h4>
		<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
	</div>
	<div data-role="collapsible-set">
	{assign var=relListRecords value=$_RECORDS->getResult()}
	{if $relListRecords eq ''}
		<div data-role="main" class="ui-content">
			<label> {$MOD.LBL_NO_RELATEDLIST}</label>
		</div>
	{else}
	{foreach item=_RECORD key=_MODULE from=$relListRecords}
		{if $_MODULE eq 'Timecontrol'}
			<div data-role="collapsible" data-collapsed="false">
		{else}
			<div data-role="collapsible" data-collapsed="true">
		{/if}
			<h3>{$_MODULE|@getTranslatedString:'Mobile'}</h3>
			<div class="ui-collapsible-content ui-body-c ui-corner-bottom" aria-hidden="false">
				<ul class="ui-listview" data-role="listview">
						{foreach item=_FIELD from=$_RECORD}
							<li >
								<a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="?_operation=fetchRecord&record={$_FIELD.relatedlistcontent.id}&lang={$LANGUAGE}" target="_self">
									{$_FIELD.relatedlistcontent.0}
									{if isset($_FIELD.relatedlistcontent.1) && $_FIELD.relatedlistcontent.1 neq ''},
										 {$_FIELD.relatedlistcontent.1}
									{/if}
									{if $_MODULE eq 'Timecontrol'}
										{if $_FIELD.relatedlistcontent.2 neq ''},
											 {$_FIELD.relatedlistcontent.2}
										{/if}
										{if $_FIELD.relatedlistcontent.3 neq '0.00'},
											 {$_FIELD.relatedlistcontent.3}
										{/if}
									{/if}
								</a>
							</li>
						{/foreach}
				</ul>
			</div>
		</div>
	{/foreach}
	{/if}
	</div>
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<h1></h1>
		{if $_PARENT_MODULE eq "HelpDesk" && 'Timecontrol'|vtlib_isModuleActive}
		<a href="?_operation=create&module=Timecontrol&record=''&relatedto={$RECORDID}&returnto={$RECORDID}&returntomodule={$_PARENT_MODULE}" class="ui-btn ui-btn-right ui-corner-all ui-icon-clock ui-btn-icon-notext" rel="external" data-transition="turn" data-iconpos="right">{$MOD.LBL_NEW}</a>
		{/if}
		<a href="?_operation=create&module=Documents&record=''&relations={$RECORDID}&returnto={$RECORDID}&returntomodule={$_PARENT_MODULE}" class="ui-btn ui-btn-left ui-corner-all ui-icon-camera ui-btn-icon-notext" rel="external" data-transition="turn" data-iconpos="left">{$MOD.LBL_NEW}</a>
	</div>
	{include file="modules/Mobile/PanelMenu.tpl"}
</div>
</body>