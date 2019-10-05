{*
<!--
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *********************************************************************************/
-->
*}
<link rel="stylesheet" href="include/style.css">
</head>
<body onload="set_focus()" data-gr-c-s-loaded="true">
	<div class="loginContainer">
		<div id="loginWrapper">
			<div id="loginTop">
				<a href="index.php"><img src="{$COMPANY_DETAILS.companylogo}"></a>
			</div>
			<div id="loginBody">
				<div class="loginForm">
					<div class="poweredBy">Powered by {$coreBOS_uiapp_name}</div>
					<form action="index.php" method="post" id="form">
						<input type="hidden" name="module" value="Users" />
						<input type="hidden" name="action" value="Authenticate" />
						<input type="hidden" name="return_module" value="Users" />
						<input type="hidden" name="return_action" value="Login" />
						<table border="0">
							<tr>
								<td valign="middle">{'LBL_USER_NAME'|getTranslatedString:'Users'}</td>
								<td valign="middle"><input type="text" name="user_name" tabindex="1"></td>
								<td rowspan="2" align="center" valign="middle"><input type="submit" id="submitButton" value="" tabindex="3"></td>
							</tr>
							<tr>
								<td valign="middle">{'LBL_PASSWORD'|getTranslatedString:'Users'}</td>
								<td valign="middle"><input type="password" name="user_password" tabindex="2"></td>
							</tr>
						</table>
						{if $LOGIN_ERROR neq ''}
						<div class="errorMessage">{$LOGIN_ERROR}</div>
						{/if}
					</form>
				</div>
			</div>
		</div>
	</div>