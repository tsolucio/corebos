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

<div id="loginWrapper">
<div id="loginTop"><a href="index.php"><img src="{php} $logo_login=fetch_logo(1); echo $logo_login;{/php}"></a></div>	<div id="loginBody">			
			<div class="loginForm">
				<div class="poweredBy">Powered by coreBOS</div>
				<form action="index.php" method="post" name="DetailView" id="form">
					<input type="hidden" name="module" value="Users" />
					<input type="hidden" name="action" value="Authenticate" />
					<input type="hidden" name="return_module" value="Users" />
					<input type="hidden" name="return_action" value="Login" />
					<table border="0">
							<tr>
							<td valign="middle"><img src="themes/login/images/user.png"></td><td  valign="middle"><input type="text" name="user_name" /></td>
							<td rowspan="2" align="center" valign="middle"><input type="submit" id="submitButton" value=""></td>
							</tr>
							<tr><td  valign="middle"><img src="themes/login/images/password.png"></td><td  valign="middle"><input type="password" name="user_password"></td></tr>
					</table>
						{if $LOGIN_ERROR neq ''}
						<div class="errorMessage">
							{$LOGIN_ERROR}
						</div>
						{/if}
									</form>
			</div>
		</div>
	<div id="loginFooter">
		<a href="http://www.evolutivo.it/" target="_blank"><img src="themes/login/images/icon_builder.png"></a>
		<a href="http://www.evolutivo.it/index.php/comunita-vtiger" target="_blank"><img src="themes/login/images/icon_app.png"></a>
		<a href="http://www.evolutivo.it/index.php/2011-05-02-12-33-35" target="_blank"><img src="themes/login/images/icon_more.png"></a>
</div>
</div>



{include file="LoginFooter.tpl}
