<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $mod_strings, $adb;
if (isPermitted('VtigerBackup', '')=='yes') {
	checkFileAccessForInclusion('include/database/PearDatabase.php');
	require_once 'include/database/PearDatabase.php';

	$server = isset($_REQUEST['server']) ? vtlib_purify($_REQUEST['server']) : '';
	$port=(empty($_REQUEST['port']) ? 0 : vtlib_purify($_REQUEST['port']));
	$server_username = isset($_REQUEST['server_username']) ? vtlib_purify($_REQUEST['server_username']) : '';
	$server_password = isset($_REQUEST['server_password']) ? vtlib_purify($_REQUEST['server_password']) : '';
	$server_type = isset($_REQUEST['server_type']) ? vtlib_purify($_REQUEST['server_type']) : '';
	$server_path = isset($_REQUEST['server_path']) ? vtlib_purify($_REQUEST['server_path']) : '';
	$from_email_field = '';
	$smtp_auth = '';
	$db_update = true;

	$error_str = '';
	if ($server_type == 'ftp_backup') {
		$action='BackupServerConfig&bkp_server_mode=edit&server='.urlencode($server).'&server_user='.urlencode($server_username).'&password='.urlencode($server_password);
		if (!function_exists('ftp_connect')) {
			$error_str = '&error='.urlencode(getTranslatedString('FTP support is not enabled', 'VtigerBackup').'.');
			$db_update = false;
		} else {
			list($host,$port) = explode(':', $server);

			if (empty($port)) {
				$conn_id = @ftp_connect($server);
			} else {
				$conn_id = @ftp_connect($host, $port);
			}
			if (!$conn_id) {
				$error_str = '&error='.urlencode(getTranslatedString('Unable to connect to', 'VtigerBackup').' "'.$server.'"');
				$db_update = false;
			} else {
				if (!@ftp_login($conn_id, $server_username, $server_password)) {
					$error_str = '&error='.urlencode(getTranslatedString('User name or password were not accepted', 'VtigerBackup').'.');
					$db_update = false;
				} else {
					$action = 'BackupServerConfig';
				}
				ftp_close($conn_id);
			}
		}
	}
	if ($server_type == 'local_backup') {
		$action = 'BackupServerConfig&local_server_mode=edit&server_path="'.urlencode($server_path).'"';
		if (!is_dir($server_path)) {
			$error_str = '&error1='.urlencode(getTranslatedString('Incorrect Folder', 'VtigerBackup'));
			$db_update = false;
		} else {
			if (!is_writable($server_path)) {
				$error_str = '&error1='.urlencode(getTranslatedString('Access Denied to write in specified folder', 'VtigerBackup').'.');
				$db_update = false;
			} else {
				$action = 'BackupServerConfig';
			}
		}
	}
	if ($server_type == 'ftp_backup' || $server_type == 'local_backup') {
		if ($db_update) {
			$sql='select * from vtiger_systems where server_type = ?';
			$idrs=$adb->pquery($sql, array($server_type));
			if ($idrs && $adb->num_rows($idrs)>0) {
				$id=$adb->query_result($idrs, 0, 'id');
				$sql='update vtiger_systems set
					server = ?, server_username = ?, server_password = ?, smtp_auth= ?, server_type = ?, server_port= ?, server_path = ?, from_email_field=?
					where id = ?';
				$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port, $server_path,$from_email_field, $id);
			} else {
				$id = $adb->getUniqueID('vtiger_systems');
				$sql= 'insert into vtiger_systems values(?,?,?,?,?,?,?,?,?)';
				$params = array($id, $server, $port, $server_username, $server_password, $server_type, $smtp_auth,$server_path,$from_email_field);
			}
			$adb->pquery($sql, $params);
		}
	}
	header("Location: index.php?module=VtigerBackup&action=$action".$error_str);
} else {
	echo '<br><br>';
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_PERMISSION'));
	$smarty->display('applicationmessage.tpl');
}
?>
