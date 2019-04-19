<link rel="stylesheet" href="themes/login/splitleft/bootstrap4.min.css">
<link rel="stylesheet" href="themes/login/splitleft/splitleft.public.css">
<link rel="stylesheet" href="themes/login/splitleft/splitleft.formula.css">
<style>
.content-login form .btn-primary:after {
	content: ' ';
	height: 0;
	position: absolute;
	width: 0;
	border: 18px solid transparent;
	border-left-color: #006baa;
	font-family: 'Times New Roman", Georgia, Serif;';
	font-weight: 300;
	font-size: 40px;
	right: 20px;
	top: 8px;
	margin-top: 8px;
}

.errorMessage {
	font-size: 12px;
	color: #982121;
}
</style>
</head>
<body onload="set_focus()" data-gr-c-s-loaded="true">
<div class="d-flex content content-login">
	<div class="w-100 w-xl-40 w-lg-50 d-flex justify-content-center align-items-center">
		<div class="col-10 my-5">
			<div class="row mb-5">
				<div class="col-4 text-right d-el-none">
				</div>
				<h1 class="col-8 ml-auto">|{$coreBOS_uiapp_name}</h1>
			</div>
			<form sim-component="inline-formula" data-inline="yes" class="i-formula i-inline-formula" action="index.php" method="post" id="form">
				<fieldset data-domain="default">
					<input type="hidden" name="module" value="Users" />
					<input type="hidden" name="action" value="Authenticate" />
					<input type="hidden" name="return_module" value="Users" />
					<input type="hidden" name="return_action" value="Login" />
					<div class="form-group row i-required">
						<label class="form-control-label col-form-label col-4 text-right"
							for="input_a52510ad-f89c-43de-a600-5c527e644782">{'LBL_USER_NAME'|getTranslatedString:'Users'}</label>
						<div class="col-8">
							<input class="form-control"
								id="input_a52510ad-f89c-43de-a600-5c527e644782" name="user_name"
								type="text" required="">
						</div>
					</div>
					<div class="form-group row i-required">
						<label class="form-control-label col-form-label col-4 text-right"
							for="input_8ba9a4bb-9a53-4f25-87c4-23e961bfcf2a">{'LBL_PASSWORD'|getTranslatedString:'Users'}</label>
						<div class="col-8">
							<input class="form-control"
								id="input_8ba9a4bb-9a53-4f25-87c4-23e961bfcf2a" name="user_password"
								type="password" required="">
						</div>
					</div>
					{if $LOGIN_ERROR neq ''}
					<div class="errorMessage">{$LOGIN_ERROR}</div>
					{/if}
				</fieldset>
				<div class="row">
					<div class="col-8 ml-auto">
						<button class="btn btn-primary btn-block" type="submit">{'LBL_SIGN_IN'|getTranslatedString:'Users'}</button>
					</div>
				</div>
			</form>
			<div class="row">
				<div class="col-8 ml-auto" style="margin-top:100px;">
					<img src="{$COMPANY_DETAILS.companylogo}">
				</div>
			</div>
		</div>
	</div>
	<div class="w-50 w-xl-60 w-lg-50 d-none d-lg-flex align-items-center justify-content-center gradient gradient-vertical">
		<div class="row">
			<div class="col-20 ml-auto">
				<img src="themes/login/splitleft/splitleft.jpg">
			</div>
		</div>
	</div>
</div>