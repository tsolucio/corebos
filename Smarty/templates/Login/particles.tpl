<!--created by Nedi for corebos-->
<link rel="stylesheet" href="themes/login/lds/sfdc_210.css">
<link rel="stylesheet" href="themes/login/particles/login.css">
</head>
<body onload="set_focus()" data-gr-c-s-loaded="true">
<canvas id="canvas"></canvas>
<div id="left" class="pr">
	<div id="wrap">
		<div id="main">
			<div id="wrapper">
				<div id="logo_wrapper" class="standard_logo_wrapper mb24">
					<h1 style="height: 100%; display: table-cell; vertical-align: bottom;">
						<img id="logo" class="standard_logo" src="{$COMPANY_DETAILS.companylogo}" alt="{$coreBOS_uiapp_name}" border="0" name="logo">
					</h1>
				</div>
				<h2 id="header" class="mb12" style="display: none;"></h2>
				<div id="content" style="display: block;">
					<div id="chooser" style="display: none">
						<div class="loginError" id="chooser_error" style="display: block;"></div>
					</div>
					<div id="theloginform" style="display: block;">
						{if $LOGIN_ERROR neq ''}
						<div class="errorMessage">{$LOGIN_ERROR}</div>
						{/if}
						<form method="post" id="login_form" action="index.php" target="_top" autocomplete="off" novalidate="novalidate">
							<input type="hidden" name="module" value="Users" />
							<input type="hidden" name="action" value="Authenticate" />
							<input type="hidden" name="return_module" value="Users" />
							<input type="hidden" name="return_action" value="Login" />
							<div id="usernamegroup" class="inputgroup">
								<div id="username_container">
									<input class="input r4 wide mb16 mt8 username" type="email" value="" name="user_name" id="username" style="display: block;" placeholder="{'LBL_USER_NAME'|getTranslatedString:'Users'}">
								</div>
							</div>
							<input class="input r4 wide mb16 mt8 password" type="password" id="password" name="user_password"
								onkeypress="checkCaps(event)" autocomplete="off" placeholder="{'LBL_PASSWORD'|getTranslatedString:'Users'}">
							<div id="pwcaps" class="mb16" style="display: none">
								<img id="pwcapsicon" alt="{'CapsLockActive'|getTranslatedString}" width="12" src="themes/login/lds/capslock_blue.png">
								{'CapsLockActive'|getTranslatedString}
							</div>
							<input class="button r4 wide primary" type="submit" id="Login" name="Login" value="{'StartSession'|getTranslatedString}">
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="footer" style="color:white">
			© Made by   {$coreBOS_uiapp_name}.
		</div>
	</div>
</div>
<script src="themes/login/particles/login.js"></script>
