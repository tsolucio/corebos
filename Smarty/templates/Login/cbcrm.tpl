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
<link rel="stylesheet" href="themes/login/cbcrm/style.css">
</head>
<body onload="set_focus()" data-gr-c-s-loaded="true">
	<div class="loginContainer">
		<table class="loginWrapper" width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td valign="top" align="left" colspan="2">
					<img align="absmiddle" src="{$COMPANY_DETAILS.companylogo}" alt="logo" width="145px" height="65" />
					<br /> <a target="_blank" href="http://{$COMPANY_DETAILS.website}"><span style="color: blacksmoke">{$COMPANY_DETAILS.name}</span></a> <br />
				</td>
			</tr>
			<tr>
				<td valign="top" align="center" width="50%"></td>
				<td valign="top" align="center" width="50%"></td>
			</tr>
		</table>
		</td>
		</tr>
		</table>
		<div class="vtmktTitle">
			&nbsp;coreBOSCRM
			<div class="poweredBy">Powered by {$coreBOS_uiapp_name} - {$VTIGER_VERSION}</div>
		</div>
		<div class="loginForm">
			<form action="index.php" method="post" id="form">
				<input type="hidden" name="module" value="Users" />
				<input type="hidden" name="action" value="Authenticate" />
				<input type="hidden" name="return_module" value="Users" />
				<input type="hidden" name="return_action" value="Login" />
				<div class="inputs">
					<div class="label">{'LBL_USER_NAME'|getTranslatedString:'Users'}</div>
					<div class="input">
						<input type="text" name="user_name" />
					</div>
					<br />
					<div class="label">{'LBL_PASSWORD'|getTranslatedString:'Users'}</div>
					<div class="input">
						<input type="password" name="user_password" />
					</div>
					{if $LOGIN_ERROR neq ''}
					<div class="errorMessage">{$LOGIN_ERROR}</div>
					{/if} <br />
					<div class="button">
						<input type="submit" id="submitButton" value="LOGIN" />
					</div>
				</div>
			</form>
		</div>
	</div>