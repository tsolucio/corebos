{strip}
<!DOCTYPE html>
<head>
	<!-- the following header content gets only loaded with a direct http call-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link rel="stylesheet" href="resources/css/jquery.mobile-1.4.5.min.css">
	<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
	<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
<head>
<body>
<div class="ui-corner-bottom ui-content ui-body-c" data-theme="b" data-role="content" role="main">
	{$MOD.LBL_DELETE_COMMENT}
	<a class="ui-btn ui-shadow ui-btn-corner-all ui-btn-up-b" data-theme="b" rel="external" data-role="button" href="?_operation=listModuleRecords&delaction=deleteEntity&module={$_MODULE}&record={$id}" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span">
		<span class="ui-btn-inner ui-btn-corner-all">
			<span class="ui-btn-text">{$MOD.LBL_YES_BUTTON}</span>
		</span>
	</a>
	<a class="ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c" data-theme="c" data-rel="back" data-role="button" href="?_operation=listModuleRecords" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-transition="pop" data-direction="reverse">
		<span class="ui-btn-inner ui-btn-corner-all">
			<span class="ui-btn-text">{$MOD.LBL_CANCEL_BUTTON}</span>
		</span>
	</a>
</div>
</body>
</html>
{/strip}