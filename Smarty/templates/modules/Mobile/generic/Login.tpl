<!DOCTYPE html>
<head>
<title>Login</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link REL="SHORTCUT ICON" HREF="../../themes/images/crm-now_icon.ico">	
	<script src="Js/jquery-1.11.2.min.js"></script>
	<script src="Js/jquery.mobile-1.4.5.min.js"></script>
	<link rel="stylesheet" href="Css/jquery.mobile.structure-1.4.5.min.css" >
	<link rel="stylesheet" href="Css/theme.css" />
	<!-- <link rel="stylesheet" href="Css/crmnow.min.css" /> -->
	<script type="text/javascript" src="Mobile.js"></script>
</head>
 
<body>
<div data-role="page" data-theme="b" data-mini="true" id="login_page">
	<div data-role="header"  data-theme="b" >
			<h4>CRM</h4>
	</div><!-- /header -->
	
	<div>
		{if $_ERR}<p class='error'>{$_ERR}</p>
		{/if}
	 
	</div>
	<div data-role="fieldcontain" class="ui-hide-label">
		<form method="post" data-transition="pop" data-ajax="false" action="index.php?_operation=loginAndFetchModules">
			<label for="username">Name:</label>
			<input type="text" name="username" id="username" value="" placeholder="Name"/>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" value="" placeholder="Password"/>
			<div data-theme="b" data-role="button" >
				<input  type="submit" value="Login" >
			</div>
		</form>
	</div>	
</div>


</body>

