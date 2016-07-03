{include file="modules/Mobile/generic/Header.tpl"}
<body>
<div data-role="page" data-theme="b">
	<!-- header -->
	<div data-role="header"  data-theme="b" >
			<h4>CRM</h4>
	</div><!-- /header -->
	<!-- /header -->
	<h4 class='error'>{$errormsg}</h4>
	<div data-theme="b">
		<a class="ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-b" data-icon="arrow-l" data-theme="b" data-role="button" href="index.php">
			<span class="ui-btn-inner ui-btn-corner-all">
			<span class="ui-btn-text">Login</span>
			<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		</a>
	</div>
</div>		

</body>

{include file="modules/Mobile/generic/Footer.tpl"}