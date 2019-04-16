<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
if (isPermitted('VtigerBackup', '')=='yes') {
	global $mod_strings, $app_strings, $enable_backup, $adb, $theme;
	$theme_path='themes/'.$theme.'/';
	$image_path=$theme_path.'images/';

	if (isset($_REQUEST['opmode']) && $_REQUEST['opmode'] != '') {
		$sql_del = "delete from vtiger_systems where server_type=?";
		$adb->pquery($sql_del, array('ftp_backup'));
	}

	$smarty = new vtigerCRM_Smarty;
	if (!empty($_REQUEST['error'])) {
		$smarty->assign('ERROR_MSG', '<b><font color="red">'.vtlib_purify($_REQUEST["error"]).'</font></b>');
	}
	if (!empty($_REQUEST['error1'])) {
		$smarty->assign('ERROR_STR', '<b><font color="red">'.vtlib_purify($_REQUEST["error1"]).'</font></b>');
	}
	if (!is_writable('user_privileges/enable_backup.php')) {
		echo '<br>';
		$smarty->assign('ERROR_MESSAGE', 'user_privileges/enable_backup.php '.getTranslatedString('VTLIB_LBL_NOT_WRITEABLE', 'Settings'));
		$smarty->display('applicationmessage.tpl');
		$smarty->assign('ERROR_MESSAGE', '');
	}
	$sql = 'select * from vtiger_systems where server_type = ?';
	$result = $adb->pquery($sql, array('ftp_backup'));
	$server = $adb->query_result($result, 0, 'server');
	$server_username = $adb->query_result($result, 0, 'server_username');
	$server_password = $adb->query_result($result, 0, 'server_password');

	$result = $adb->pquery($sql, array('local_backup'));
	$local_server = $adb->query_result($result, 0, 'server');
	$server_path = $adb->query_result($result, 0, 'server_path');

	if (isset($_REQUEST['bkp_server_mode']) && $_REQUEST['bkp_server_mode'] != '') {
		$smarty->assign('BKP_SERVER_MODE', vtlib_purify($_REQUEST['bkp_server_mode']));
	} else {
		$smarty->assign('BKP_SERVER_MODE', 'view');
	}

	if (isset($_REQUEST['local_server_mode']) && $_REQUEST['local_server_mode'] != '') {
		$smarty->assign('LOCAL_SERVER_MODE', vtlib_purify($_REQUEST['local_server_mode']));
	} else {
		$smarty->assign('LOCAL_SERVER_MODE', 'view');
	}

	if (isset($_REQUEST['server'])) {
		$smarty->assign('FTPSERVER', vtlib_purify($_REQUEST['server']));
	} elseif (isset($server)) {
		$smarty->assign('FTPSERVER', $server);
	}
	if (isset($_REQUEST['server_user'])) {
		$smarty->assign('FTPUSER', vtlib_purify($_REQUEST['server_user']));
	} elseif (isset($server_username)) {
		$smarty->assign('FTPUSER', $server_username);
	}
	if (isset($_REQUEST['password'])) {
		$smarty->assign('FTPPASSWORD', vtlib_purify($_REQUEST['password']));
	} elseif (isset($server_password)) {
		$smarty->assign('FTPPASSWORD', $server_password);
	}
	if (isset($_REQUEST['path'])) {
		$smarty->assign('SERVER_BACKUP_PATH', vtlib_purify($_REQUEST['server_path']));
	} elseif (isset($server_path)) {
		$smarty->assign('SERVER_BACKUP_PATH', $server_path);
	}
	$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
	$smarty->assign('IMAGE_PATH', $image_path);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('THEME', $theme);
	$smarty->assign('CMOD', $mod_strings);

	require_once 'include/utils/cbSettings.php';

	if (coreBOS_Settings::getSetting('enable_local_backup', false)) {
		$local_backup_status = 'enabled';
	} else {
		$local_backup_status = 'disabled';
	}
	if (coreBOS_Settings::getSetting('enable_ftp_backup', false)) {
		$ftp_backup_status = 'enabled';
	} else {
		$ftp_backup_status = 'disabled';
	}
	$smarty->assign("FTP_BACKUP_STATUS", $ftp_backup_status);
	$smarty->assign("LOCAL_BACKUP_STATUS", $local_backup_status);

	require_once 'include/logging.php';
	require_once 'modules/Users/LoginHistory.php';
	require_once 'modules/Users/Users.php';
	require_once 'include/db_backup/backup.php';
	require_once 'include/db_backup/ftp.php';
	require_once 'include/database/PearDatabase.php';

	global $adb, $enable_backup;

	if (isset($_REQUEST['backupnow'])) {
		if (!defined('DBSERVER')) {
			define('DBSERVER', $dbconfig['db_hostname']);
		}
		if (!defined('DBUSER')) {
			define('DBUSER', $dbconfig['db_username']);
		}
		if (!defined('DBPASS')) {
			define('DBPASS', $dbconfig['db_password']);
		}
		if (!defined('DBNAME')) {
			define('DBNAME', $dbconfig['db_name']);
		}

		$path_query = $adb->pquery("SELECT * FROM vtiger_systems WHERE server_type = ?", array('local_backup'));
		$path = $adb->query_result($path_query, 0, 'server_path');
		$currenttime=date("Ymd_His");
		if (is_dir($path) && is_writable($path)) {
			require_once 'modules/VtigerBackup/VtigerBackup.php';
			require_once 'include/db_backup/DatabaseBackup.php';
			$backup = new VtigerBackup();
			$backup->backup();
			$fileName = $backup->getBackupFileName();
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-info');
			$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_BACKEDUPSUCCESSFULLY_TO_FILE').' : '.$fileName);
		} else {
			$smarty->assign('ERROR_MESSAGE', getTranslatedString('Failed to backup'));
		}
	}

	if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 'true' && $_REQUEST['server_type'] == 'ftp_backup') {
		$smarty->display('modules/VtigerBackup/BackupServerContents.tpl');
	} else {
		$smarty->display('modules/VtigerBackup/BackupServer.tpl');
	}
} else {
	echo '<br><br>';
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_PERMISSION'));
	$smarty->display('applicationmessage.tpl');
}
?>
