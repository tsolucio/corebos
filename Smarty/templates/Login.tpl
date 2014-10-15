{*<!--
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *********************************************************************************/
-->*}
{include file="LoginHeader.tpl}

<table class="loginWrapper" width="100%" height="100%" cellpadding="10" cellspacing="0" border="0">
	<tr valign="top">
		<td valign="top" align="left" colspan="2">
			<img align="absmiddle" src="test/logo/{$COMPANY_DETAILS.logo}" alt="{$COMPANY_DETAILS.name}"/>
			<br />
			<a target="_blank" href="http://{$COMPANY_DETAILS.website}">{$COMPANY_DETAILS.name}</a>
			<br />
		</td>
	</tr>

	<tr>
		<td valign="top" align="center" width="50%">
		</td>

		<td valign="top" align="center" width="50%">
			<div class="loginForm">
				<div class="poweredBy">Powered by coreBOS</div>
				<form action="index.php" method="post" name="DetailView" id="form">
					<input type="hidden" name="module" value="Users" />
					<input type="hidden" name="action" value="Authenticate" />
					<input type="hidden" name="return_module" value="Users" />
					<input type="hidden" name="return_action" value="Login" />
					<div class="inputs">
						<div class="label">User Name</div>
						<div class="input"><input type="text" name="user_name"/></div>
						<br />
						<div class="label">Password</div>
						<div class="input"><input type="password" name="user_password"/></div>
						{if $LOGIN_ERROR neq ''}
						<div class="errorMessage">
							{$LOGIN_ERROR}
						</div>
						{/if}
						<br />
						<div class="button">
							<input type="submit" id="submitButton" value="Login" />
						</div>
					</div>
				</form>
			</div>
			<div class="importantLinks">
			<a href='copyright.html' target='_blank'>{$APP.LNK_READ_LICENSE}</a>
			|
			<a href='http://corebos.org/page/privacy-policy' target='_blank'>{$APP.LNK_PRIVACY_POLICY}</a>
			|
			&copy; 2004- {php} echo date('Y'); {/php}
			</div>
		</td>
	</tr>
</table>

{include file="LoginFooter.tpl}
