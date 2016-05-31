<!DOCTYPE html>
<head>
	<!-- the following header content gets only loaded with a direct http call-->
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta charset="utf-8">
	<script src="Js/jquery-1.11.2.min.js"></script>
	<link rel="stylesheet" href="Css/jquery.mobile-1.4.5.min.css" />
	<script src="Js/jquery.mobile-1.4.5.min.js"></script>
	<link rel="stylesheet" href="Css/theme.css" />
	<link rel="stylesheet" href="Css/jquery.mobile.icons.min.css" />
	<script src="Js/lang/{$LANGUAGE}.lang.js"></script>
	{literal}
	<!-- define the collapsible button size-->
	<style>	
	.collapse_header .ui-btn-text{
		font-size: 10px
	}
	</style>
	{/literal}

<head>
<body>
<div data-role="page" data-theme="b" >
	<div data-role="header" class="ui-bar" data-theme="b"  data-position="fixed">
		<h4>{$MOD.LBL_RELATED_LIST}</h4>
	</div>
	<div data-role="collapsible-set">
	{foreach item=_RECORD key=_MODULE from=$_RECORDS->getResult()}
		<div data-role="collapsible" data-collapsed="true">
			<h3>{$_MODULE|@getTranslatedString:$_MODULE}</h3>
			<div class="ui-collapsible-content ui-body-c ui-corner-bottom" aria-hidden="false">
				<ul class="ui-listview" data-role="listview">
						{foreach item=_FIELD from=$_RECORD}
							<li >
								<a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="?_operation=fetchRecordWithGrouping&record={$_FIELD.relatedlistcontent.id}&lang={$LANGUAGE}" target="_self">{$_FIELD.relatedlistcontent.0}{if $_FIELD.relatedlistcontent.1 neq ''}, {$_FIELD.relatedlistcontent.1}
								{/if}
								</a>
							</li>
						{/foreach}
				</ul>
			</div>
		</div>
	{/foreach}
</div>
	<div  data-type="horizontal" data-mini="true">
		<a href="#"  onclick="window.history.back()" data-mini="true" data-role="button"> {$MOD.LBL_CANCEL}</a>
	</div>
</div>
</body>
</html>