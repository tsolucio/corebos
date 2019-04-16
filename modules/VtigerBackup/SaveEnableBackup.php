<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/cbSettings.php';

if (isPermitted('VtigerBackup', '')=='yes') {
	if (isset($_REQUEST['enable_ftp_backup']) && vtlib_purify($_REQUEST['enable_ftp_backup']) != '') {
		$enable_backup = coreBOS_Settings::getSetting('enable_ftp_backup', false);
		coreBOS_Settings::setSetting('enable_ftp_backup', !$enable_backup);
	} elseif (isset($_REQUEST['enable_local_backup']) && vtlib_purify($_REQUEST['enable_local_backup']) != '') {
		$enable_backup = coreBOS_Settings::getSetting('enable_local_backup', false);
		coreBOS_Settings::setSetting('enable_local_backup', !$enable_backup);
	} elseif (!empty($_REQUEST['GetBackupDetail']) && ($_REQUEST['servertype'] == 'local_backup' || $_REQUEST['servertype'] == 'ftp_backup')) {
		require_once 'include/database/PearDatabase.php';
		global $mod_strings,$adb;
		$servertype = vtlib_purify($_REQUEST['servertype']);
		$GetBackup = $adb->pquery('select * from vtiger_systems where server_type = ?', array($servertype));
		$BackRowsCheck = $adb->num_rows($GetBackup);
		if ($BackRowsCheck > 0) {
			echo 'SUCCESS';
		} else {
			echo 'FAILURE';
		}
	}
} // ispermitted
?>
