<link rel="stylesheet" href="themes/login/lds/sfdc_210.css">
{literal}
<style type="text/css">
	body {
		margin: 0;
		padding: 0;
		background-color: #0e6cc4;
		overflow-x: hidden;
		overflow-y: hidden;
	}

	/*waves****************************/

	.box {
		position: fixed;
		top: 0;
		transform: rotate(80deg);
		left: 0;
	}

	.wave {
		position: fixed;
		top: 0;
		left: 0;
		opacity: .4;
		position: absolute;
		top: 3%;
		left: 10%;
		background: #0af;
		width: 1500px;
		height: 1300px;
		margin-left: -150px;
		margin-top: -250px;
		transform-origin: 50% 48%;
		border-radius: 43%;
		animation: drift 7000ms infinite linear;
	}

	.wave.-three {
		animation: drift 7500ms infinite linear;
		position: fixed;
		background-color: #77daff;
	}

	.wave.-two {
		animation: drift 3000ms infinite linear;
		opacity: .1;
		background: black;
		position: fixed;
	}

	.box:after {
		content: '';
		display: block;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		z-index: 11;
		transform: translate3d(0, 0, 0);
	}

	@keyframes drift {
		from {
			transform: rotate(0deg);
		}

		from {
			transform: rotate(360deg);
		}
	}

	/*LOADING SPACE*/

	.contain {
		animation-delay: 4s;
		z-index: 1000;
		position: fixed;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-flow: row nowrap;
		flex-flow: row nowrap;
		-webkit-box-pack: center;
		-ms-flex-pack: center;
		justify-content: center;
		-webkit-box-align: center;
		-ms-flex-align: center;
		align-items: center;

		background: #25a7d7;
		background: -webkit-linear-gradient(#25a7d7, #2962FF);
		background: linear-gradient(#25a7d7, #25a7d7);
	}

	.icon {
		width: 100px;
		height: 100px;
		margin: 0 5px;
	}

	/*Animation*/
	.icon:nth-child(2) img {-webkit-animation-delay: 0.2s;animation-delay: 0.2s}
	.icon:nth-child(3) img {-webkit-animation-delay: 0.3s;animation-delay: 0.3s}
	.icon:nth-child(4) img {-webkit-animation-delay: 0.4s;animation-delay: 0.4s}

	.icon img {
		-webkit-animation: anim 2s ease infinite;
		animation: anim 2s ease infinite;
		-webkit-transform: scale(0, 0) rotateZ(180deg);
		transform: scale(0, 0) rotateZ(180deg);
	}

	@-webkit-keyframes anim {
		0% {
			-webkit-transform: scale(0, 0) rotateZ(-90deg);
			transform: scale(0, 0) rotateZ(-90deg);
			opacity: 0
		}

		30% {
			-webkit-transform: scale(1, 1) rotateZ(0deg);
			transform: scale(1, 1) rotateZ(0deg);
			opacity: 1
		}

		50% {
			-webkit-transform: scale(1, 1) rotateZ(0deg);
			transform: scale(1, 1) rotateZ(0deg);
			opacity: 1
		}

		80% {
			-webkit-transform: scale(0, 0) rotateZ(90deg);
			transform: scale(0, 0) rotateZ(90deg);
			opacity: 0
		}
	}

	@keyframes anim {
		0% {
			-webkit-transform: scale(0, 0) rotateZ(-90deg);
			transform: scale(0, 0) rotateZ(-90deg);
			opacity: 0
		}

		30% {
			-webkit-transform: scale(1, 1) rotateZ(0deg);
			transform: scale(1, 1) rotateZ(0deg);
			opacity: 1
		}

		50% {
			-webkit-transform: scale(1, 1) rotateZ(0deg);
			transform: scale(1, 1) rotateZ(0deg);
			opacity: 1
		}

		80% {
			-webkit-transform: scale(0, 0) rotateZ(90deg);
			transform: scale(0, 0) rotateZ(90deg);
			opacity: 0
		}
	}

	a {
		color: #0070d2;
	}

	#content,
	.container {
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

	.errorMessage {
		font-size: 12px;
		color: #982121;
	}
</style>
{/literal}
</head>
<body onload="set_focus()" data-gr-c-s-loaded="true">
	<div class='box'>
		<div class='wave -one'></div>
		<div class='wave -two'></div>
		<div class='wave -three'></div>
	</div>
	<div id="left" class="pr">
		<div id="wrap">
			<div id="main">
				<div id="wrapper">
					<div id="logo_wrapper" class="standard_logo_wrapper mb24">
						<h1 style="height: 100%; display: table-cell; vertical-align: bottom;">
							<img id="logo" class="standard_logo"
								src="{$COMPANY_DETAILS.companylogo}"
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
							<form method="post" id="login_form" action="index.php" target="_top" autocomplete="off" novalidate="novalidate">
								<input type="hidden" name="module" value="Users" />
								<input type="hidden" name="action" value="Authenticate" />
								<input type="hidden" name="return_module" value="Users" />
								<input type="hidden" name="return_action" value="Login" />
								<input type="hidden" name="twofauserauth" value="{$authuserid}" />
								<div id="usernamegroup" class="inputgroup">
									<label for="username" class="label">{'LBL_USER_NAME'|getTranslatedString:'Users'}</label>
									<div id="username_container">
										<input class="input r4 wide mb16 mt8 username" type="email" value="{$uname}" name="user_name" id="username" style="display: block;" readonly>
									</div>
								</div>
								<label for="password" class="label">{'LBL_2FACODE'|getTranslatedString:'Users'}</label>
								<input class="input r4 wide mb16 mt8 password" type="text" id="user_2facode" name="user_2facode"
									onkeypress="checkCaps(event)" autocomplete="off">
								<div class="mb16"><a href="javascript:sendnew2facode({$authuserid});">{'LBL_2FAGETCODE'|getTranslatedString:'Users'}</a></div>
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

			<div id="footer" style="color:white;">
				© Powered by {$coreBOS_uiapp_name}.
			</div>
		</div>
	</div>