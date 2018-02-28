<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once('install/installAddons.php');
require_once('config.php');
require_once('include/logging.php');
require_once('modules/Users/Users.php');
require_once('modules/Users/LoginHistory.php');
require_once('data/Tracker.php');
require_once('include/utils/utils.php');
require_once('modules/Users/DefaultDataPopulator.php');
require_once('modules/Users/CreateUserPrivilegeFile.php');

global $php_max_execution_time;
set_time_limit($php_max_execution_time);

session_start();

$auth_key = $_REQUEST['auth_key'];
if($_SESSION['authentication_key'] != $auth_key) {
	die($installationStrings['ERR_NOT_AUTHORIZED_TO_PERFORM_THE_OPERATION']);
}
global $selected_optional_modules;
if(isset($_REQUEST['selected_modules'])) {
	$_SESSION['installation_info']['selected_optional_modules'] = $_REQUEST['selected_modules'] ;
}

if (isset($_SESSION['installation_info']['admin_email'])) $admin_email = $_SESSION['installation_info']['admin_email'];
if (isset($_SESSION['installation_info']['admin_password'])) $admin_password = $_SESSION['installation_info']['admin_password'];
if (isset($_SESSION['installation_info']['currency_name'])) $currency_name = $_SESSION['installation_info']['currency_name'];
if (isset($_SESSION['installation_info']['currency_code'])) $currency_code = $_SESSION['installation_info']['currency_code'];
if (isset($_SESSION['installation_info']['currency_symbol'])) $currency_symbol = $_SESSION['installation_info']['currency_symbol'];
if (isset($_SESSION['installation_info']['selected_optional_modules'])) $selected_optional_modules = $_SESSION['installation_info']['selected_optional_modules'];

include 'install/InitSchema.php';
global $adb;
$adb = PearDatabase::getInstance();
$initSchema = new Install_InitSchema($adb);
$initSchema->initialize();

installAddons();

// Install Vtlib Compliant Modules
Common_Install_Wizard_Utils::installMandatoryModules();
Installation_Utils::installOptionalModules($selected_optional_modules);

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (isset($_COOKIE[session_name()])) {
   setcookie(session_name(), '', time()-42000, '/');
}

// Finally, destroy the session.
session_destroy(); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['APP_NAME']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_FINISH']?></title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="include/jquery/jquery.js"></script>
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

	<br>
	<!-- Table for cfgwiz starts -->
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>" hspace="20" title="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>"></td>
			<td class="cwHeadBg1" align=right><img src="include/install/images/app_logo.png" alt="<?php echo $installationStrings['APP_NAME']; ?>" title="<?php echo $installationStrings['APP_NAME']; ?>"></td>
			<td class="cwHeadBg1" width=2%></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td background="include/install/images/topInnerShadow.gif" align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
	<tr>
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=0 width=97%>
				<tr>
					<td width=80% valign=top class="cwContentDisplay" align=left>
					<!-- Right side tabs -->
						<table border=0 cellspacing=0 cellpadding=10 width=100%>
							<tr>
								<td align=left class="small paddingTop">
									<span class="bigHeading"><?php echo $installationStrings['LBL_CONFIG_COMPLETED']; ?></span>
									<br />
									<hr noshade size=1>
								</td>
							</tr>
							<tr>
								<td align=center class="small" style="height:250px;">
									<table border=0 cellspacing=0 cellpadding=5 align="center" width="80%" class="contentDisplay">
										<tr>
											<td align=center class=small>
												<form action="install.php" method="post" name="finishform" id="finishform">
													<input type="hidden" name="file" value="InstallationComplete.php" />
												</form>
												<div class="fixedSmallHeight textCenter fontBold">
													<div style="padding-top:50px;width:100%;">
														<?php echo $installationStrings['APP_NAME'].' - '.$coreBOS_app_version. ' - ' . $installationStrings['LBL_SUCCESSFULLY_INSTALLED']; ?></b><br /><br />
														<script type="text/javascript">
															window.document.finishform.submit();
														</script>
														<img src="themes/images/plsWaitAnimated.gif" alt="<?php echo $installationStrings['LBL_PLEASE_WAIT']; ?>" title="<?php echo $installationStrings['LBL_PLEASE_WAIT']; ?>"/>
													</div>
												</div>
											</td>
										</tr>
									</table>
									<br>
								</td>
							</tr>
						</table>
						<!-- Master display stops -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td class=small align=center> <a href="<?php echo $coreBOS_app_url; ?>" target="_blank"><?php echo $coreBOS_app_name; ?></a></td>
	</tr>
</table>
</body>
</html>	