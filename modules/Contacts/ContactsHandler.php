<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function Contacts_sendCustomerPortalLoginDetails($entityData) {
	$adb = PearDatabase::getInstance();
	//$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];

	$email = $entityData->get('email');

	if ($entityData->get('portal') == 'on' || $entityData->get('portal') == '1') {
		$result = $adb->pquery('SELECT id, user_name, user_password, isactive FROM vtiger_portalinfo WHERE id=?', array($entityId));
		$insert = false;
		if ($adb->num_rows($result) == 0) {
			$insert = true;
		} else {
			$dbusername = $adb->query_result($result, 0, 'user_name');
			$isactive = $adb->query_result($result, 0, 'isactive');
			if ($email == $dbusername && $isactive == 1 && !$entityData->isNew()) {
				$update = false;
			} elseif ($entityData->get('portal') == 'on' ||  $entityData->get('portal') == '1') {
				$adb->pquery('UPDATE vtiger_portalinfo SET user_name=?, isactive=1 WHERE id=?', array($email, $entityId));
				$password = $adb->query_result($result, 0, 'user_password');
				$update = true;
			} else {
				$adb->pquery('UPDATE vtiger_portalinfo SET user_name=?, isactive=? WHERE id=?', array($email, 0, $entityId));
				$update = false;
			}
		}
		if ($insert == true) {
			$password = makeRandomPassword();
			$adb->pquery(
				'INSERT INTO vtiger_portalinfo(id,user_name,user_password,type,isactive) VALUES(?,?,?,?,?)',
				array($entityId, $email, $password, 'C', 1)
			);
		}

		if ($insert == true || $update == true) {
			require_once 'modules/Emails/mail.php';
			global $current_user;
			$emailData = Contacts::getPortalEmailContents($entityData, $password, 'LoginDetails');
			$subject = $emailData['subject'];
			$contents = $emailData['body'];
			send_mail('Contacts', $entityData->get('email'), $current_user->user_name, "", $subject, $contents);
		}
	} else {
		$adb->pquery('UPDATE vtiger_portalinfo SET user_name=?,isactive=0 WHERE id=?', array($email, $entityId));
	}
}
?>
