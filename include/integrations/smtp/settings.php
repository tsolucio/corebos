<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module    : SMTP Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/smtp/smtp.php';

$smarty = new vtigerCRM_Smarty();
$smtpconfig = new corebos_smtp();
$savemode = (empty($_REQUEST['savemode']) ? 'false' : vtlib_purify($_REQUEST['savemode']));
if ($savemode == 'true') {
	/**
	 * for Incoming Mail Server Configuration
	 */
	if (isset($_REQUEST['ic_mail_server_active']) && $_REQUEST['ic_mail_server_active'] == 'on') {
		$ic_mail_server_active = ((empty($_REQUEST['ic_mail_server_active']) || $_REQUEST['ic_mail_server_active']!='on') ? '0' : '1');
		$ic_mail_server_displayname = vtlib_purify($_REQUEST['ic_mail_server_type']);
		$ic_mail_server_protocol = vtlib_purify($_REQUEST['ic_mail_server_protocol']);
		$ic_mail_server_username = vtlib_purify($_REQUEST['ic_mail_server_username']);
		$ic_mail_server_password = vtlib_purify($_REQUEST['ic_mail_server_password']);
		$ic_mail_server_name = vtlib_purify($_REQUEST['ic_mail_server_name']);
		$ic_mail_server_box_refresh = vtlib_purify($_REQUEST['ic_mail_server_refresh_time']);
		$ic_mail_server_ssltype = vtlib_purify($_REQUEST['ic_mail_server_ssltype']);
		$ic_mail_server_sslmeth = vtlib_purify($_REQUEST['ic_mail_server_sslmeth']);
		$ic_mail_server_mails_per_page = '0';
		$og_mail_server_active = ((empty($_REQUEST['og_mail_server_active']) || $_REQUEST['og_mail_server_active']!='on') ? '0' : '1');

		$smtpconfig->saveIncomingMailServerConfiguration(
			$ic_mail_server_active,
			$ic_mail_server_displayname,
			$ic_mail_server_protocol,
			$ic_mail_server_username,
			$ic_mail_server_password,
			$ic_mail_server_name,
			$ic_mail_server_box_refresh,
			$ic_mail_server_mails_per_page,
			$ic_mail_server_ssltype,
			$ic_mail_server_sslmeth,
			$og_mail_server_active
		);
	}
	/**
	 * for Outgoing Mail Server Configuration
	 *  */
	if (isset($_REQUEST['og_mail_server_active']) && $_REQUEST['og_mail_server_active'] == 'on') {
		$og_mail_server_active = ((empty($_REQUEST['og_mail_server_active']) || $_REQUEST['og_mail_server_active']!='on') ? '0' : '1');
		$og_mail_server_username = (empty($_REQUEST['og_mail_server_username']) ? '' : vtlib_purify($_REQUEST['og_mail_server_username']));
		$og_mail_server_password = (empty($_REQUEST['og_mail_server_password']) ? '' : vtlib_purify($_REQUEST['og_mail_server_password']));
		$og_mail_server_smtp_auth = (empty($_REQUEST['og_mail_server_smtp_auth']) ? '' : vtlib_purify($_REQUEST['og_mail_server_smtp_auth']));
		$og_mail_server_name = vtlib_purify($_REQUEST['og_mail_server_name']);
		$og_mail_server_port= (empty($_REQUEST['port']) ? 0 : vtlib_purify($_REQUEST['port']));
		$og_mail_server_path = '';
		$ic_mail_server_active = ((empty($_REQUEST['ic_mail_server_active']) || $_REQUEST['ic_mail_server_active']!='on') ? '0' : '1');
		$smtpconfig->saveOutgoingMailServerConfiguration(
			$og_mail_server_active,
			$og_mail_server_username,
			$og_mail_server_password,
			$og_mail_server_smtp_auth,
			$og_mail_server_name,
			$og_mail_server_port,
			$og_mail_server_path,
			$ic_mail_server_active
		);
	}

	$response = array(
		'ic_validation_error_status' => $ic_mail_server_validation_error,
		'ic_validation_error_message' => $ic_mail_server_validation_error_message,
		'ic_mail_server_validation_success' => $ic_mail_server_validation_success,
		'og_validation_error_status' => $og_mail_server_validation_error,
		'og_validation_error_message' => $og_mail_server_validation_error_message,
		'og_mail_server_validation_success' => $og_mail_server_validation_success
	);
	echo json_encode($response);
} else {
	$smtp_settings_mode = isset($_REQUEST['smtp_settings']) ? vtlib_purify($_REQUEST['smtp_settings']) : '';
	/**
	 * delete Incoming Mail Server Configuration
	 */
	if ($smtp_settings_mode == 'inc_set') {
		$smtpconfig->clearIncSMTPSettings();
		$smtpconfig = new corebos_smtp();
	}
	/**
	 * delete Outgoing Mail Server Configuration
	 */
	if ($smtp_settings_mode == 'og_set') {
		$smtpconfig->clearOgSMTPSettings();
		$smtpconfig = new corebos_smtp();
	}

	$ic_mail_server_validation_error = false;
	$ic_mail_server_validation_success = false;
	$og_mail_server_validation_error = false;
	$og_mail_server_validation_success = false;
	$og_mail_server_validation_error_message = '';
	$ic_mail_server_validation_error_message = '';

	// Code for Displaying Info
	require_once 'include/database/PearDatabase.php';
	require_once 'modules/Users/Users.php';
	$focus = new Users();
	$smarty->assign('TITLE_MESSAGE', getTranslatedString('LBL_USER_SMTP_CONFIG', $currentModule));

	$smarty->assign('ic_mail_server_validation_error', $ic_mail_server_validation_error);
	$smarty->assign('ic_mail_server_validation_success', $ic_mail_server_validation_success);
	$smarty->assign('og_mail_server_validation_error', $og_mail_server_validation_error);
	// Controlled by the Database Validation Status field
	$smarty->assign('og_mail_server_validation_error_message', $og_mail_server_validation_error_message);
	$smarty->assign('ic_mail_server_validation_error_message', $ic_mail_server_validation_error_message);
	$smarty->assign('og_mail_server_validation_success', $og_mail_server_validation_success);
	$smarty->assign('success_config_validation_message', 'LBL_SUCCESS_CONFIG_VALIDATION');
	$smarty->assign('warning_config_validation_message', 'LBL_WARNING_CONFIG_VALIDATION');

	// incoming mail server config values
	$smarty->assign('ic_mail_server_active', $smtpconfig->getIncomingMailServerActiveStatus());
	$smarty->assign('ic_mail_server_displayname', $smtpconfig->getIncomingMailServerDisplayName());
	$smarty->assign('ic_mail_server_email', $smtpconfig->getIncomingMailServerEmail());
	$smarty->assign('ic_mail_server_account_name', $smtpconfig->getIncomingMailServerAccountName());
	$smarty->assign('ic_mail_server_protocol', $smtpconfig->getIncomingMailServerProtocol());
	$smarty->assign('ic_mail_server_username', $smtpconfig->getIncomingMailServerUsername());
	$smarty->assign('ic_mail_server_password', $focus->de_cryption($smtpconfig->getIncomingMailServerPassword()));
	$smarty->assign('ic_mail_server_name', $smtpconfig->getIncomingMailServerName());
	$smarty->assign('ic_mail_server_refresh_time', $smtpconfig->getIncomingMailServerRefreshTime());
	$smarty->assign('ic_mail_server_mails_per_page', $smtpconfig->getIncomingMailServerMailsPerPage());
	$smarty->assign('ic_mail_server_ssltype', $smtpconfig->getIncomingMailServerSSLTYPE());
	$smarty->assign('ic_mail_server_sslmeth', $smtpconfig->getIncomingMailServerSSLMETH());
	// outgoing mail server config values
	$smarty->assign('og_mail_server_active', $smtpconfig->getOutgoingMailServerActiveStatus());
	$smarty->assign('og_mail_server_username', $smtpconfig->getOutgoingMailServerUsername());
	$smarty->assign('og_mail_server_password', $focus->de_cryption($smtpconfig->getOutgoingMailServerPassword()));
	$smarty->assign('og_mail_server_smtp_auth', $smtpconfig->getOutgoingMailServerSMTPAuthetication());
	$smarty->assign('og_mail_server_name', $smtpconfig->getOutgoingMailServerName());
	$smarty->assign('port', $smtpconfig->getOutgoingMailServerPort());
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	$smarty->assign('THEME', $theme);
	include 'include/integrations/forcedButtons.php';
	$smarty->assign('CHECK', $tool_buttons);
	$smarty->display('modules/Utilities/smtp.tpl');
}
?>