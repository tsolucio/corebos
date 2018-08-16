<link rel="stylesheet" href="themes/login/lds/sfdc_210.css">
{literal}
<style type="text/css">
a {
	color: #0070d2;
}

body {
	background-color: #F4F6F9;
}

#content, .container {
	background-color: #ffffff;
}

#header {
	color: #16325c;
}

body {
	display: table;
	width: 100%;
}

#content {
	margin-bottom: 24px;
}

#wrap {
	height: 100%;
}

#right {
	vertical-align: middle;
}

.errorMessage {
	font-size: 12px;
	color: #982121;
}
.success-msg {
    color: #4F8A10;
    color: green;
}
</style>
{/literal}
</head>
<body onload="set_focus()" data-gr-c-s-loaded="true">
	<div id="left" class="pr">
		<div id="wrap">
			<div id="main">
				<div id="wrapper">
					<div id="logo_wrapper" class="standard_logo_wrapper mb24">
						<h1 style="height: 100%; display: table-cell; vertical-align: bottom;">
							<img id="logo" class="standard_logo"
								src="test/logo/{$COMPANY_DETAILS.logo}"
								alt="{$coreBOS_uiapp_name}" border="0" name="logo">
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
							{if $SUCCESS_MESSAGE neq ''}
							<div class="success-msg">{$SUCCESS_MESSAGE}</div>
							{/if}
							<form method="post" id="login_form" action="index.php" target="_top" autocomplete="off" novalidate="novalidate">
								<input type="hidden" name="module" value="Users" />
								<input type="hidden" name="action" value="Authenticate" />
								<input type="hidden" name="return_module" value="Users" />
								<input type="hidden" name="return_action" value="Login" />
								<div id="usernamegroup" class="inputgroup">
									<label for="username" class="label">{'LBL_USER_NAME'|getTranslatedString:'Users'}</label>
									<div id="username_container">
										<input class="input r4 wide mb16 mt8 username" type="email" value="" name="user_name" id="username" style="display: block;">
									</div>
								</div>
								<label for="password" class="label">{'LBL_PASSWORD'|getTranslatedString:'Users'}</label>
								<input class="input r4 wide mb16 mt8 password" type="password" id="password" name="user_password"
									onkeypress="checkCaps(event)" autocomplete="off">
								<div id="pwcaps" class="mb16" style="display: none">
									<img id="pwcapsicon" alt="{'CapsLockActive'|getTranslatedString}" width="12" src="themes/login/lds/capslock_blue.png">
									{'CapsLockActive'|getTranslatedString}
								</div>
								<input class="button r4 wide primary" type="submit" id="Login" name="Login" value="{'StartSession'|getTranslatedString}">
								<div><br></div>
								<a href="#" class="forgotPasswordLink" style="color: #15c;"  onclick="forgotPassword()">{'LBL_FORGOT_PASSWORD'|getTranslatedString:'Users'}</a>
							</form>
						</div>
						<div id="forgotPasswordDiv" style="display: none;">
							<form method="POST" id="login_form" action="forgotPassword.php" target="_top" autocomplete="off" novalidate="novalidate">
								<div>
									<label class="label">{'LBL_SEND_MAIL_PASSWORD'|getTranslatedString:'Users'}</label>
									</div></br>
									<div id="usernamegroup" class="inputgroup">

										<label for="username" class="label">{'LBL_USER_NAME'|getTranslatedString:'Users'}</label>
										<div id="username_container">
											<input class="input r4 wide mb16 mt8 username" type="email" value="" name="user_name_p" id="username" style="display: block;">
										</div>
									</div>
									<div id="pwcaps" class="mb16" style="display: none">
										<img id="pwcapsicon" alt="{'CapsLockActive'|getTranslatedString}" width="12" src="themes/login/lds/capslock_blue.png">
										{'CapsLockActive'|getTranslatedString}
									</div>
									<input class="button r4 wide primary" type="submit" id="Forgot_Password" name="Forgot_Password" value="Reset Password">
									<div><br></div>
									<a href="#" class="forgotPasswordLink" style="color: #15c;" onclick="goBack()">{'LBL_GO_BACK'|getTranslatedString:'Users'}</a>
								</form>
							</div>
					</div>
				</div>
			</div>

			<div id="footer">
				© Powered by {$coreBOS_uiapp_name}.
			</div>
		</div>
	</div>
