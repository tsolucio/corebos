{strip}
{include file="modules/Mobile/Header.tpl"}
<body>
<div data-role="page" data-theme="b">
	<!-- header -->
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}">
		<h4>CRM</h4>
	</div><!-- /header -->
	<!-- /header -->
	<h4 class='error'>{$errormsg}</h4>
	<div data-theme="b">
		<a class="ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-b" data-icon="arrow-l" data-theme="b" data-role="button" href="index.php">
			<span class="ui-btn-inner ui-btn-corner-all">
			<span class="ui-btn-text">Login</span>
			<span></span>
			</span>
		</a>
	</div>
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<h1></h1>
	</div>
</div>
</body>
{/strip}