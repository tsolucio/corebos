<?php
/*************************************************************************************************
* Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
*  Module       : set Email Opt Out
*  Version      : 5.4.0
*  Author       : JPL TSolucio, S. L.
*  Format       : setEmailOptOut.php?email={email}&optout={0|1}&appkey={appkey}
*************************************************************************************************/
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);

require_once 'include/utils/utils.php';
include_once 'config.inc.php';

global $application_unique_key;
if (vtlib_purify($_REQUEST['appkey']) != $application_unique_key) {
	header('HTTP/1.0 400 Error appkey');
	header('HTTP/1.0 433 Incorrect appkey');
	exit;
}

global $adb;
$email = vtlib_purify($_REQUEST['email']);
$eoo = (vtlib_purify($_REQUEST['optout']) == '0' ? '0' : '1');
$cefound = false;
$em = new VTEventsManager($adb);
// Initialize Event trigger cache
$em->initTriggerCache();

$ars = $adb->pquery('select accountid from vtiger_account where email1=?', array($email));
while ($a = $adb->fetch_array($ars)) {
	$cefound = true;
	$entityData = VTEntityData::fromEntityId($adb, $a['accountid']);
	$em->triggerEvent('vtiger.entity.beforesave.modifiable', $entityData);
	$em->triggerEvent('vtiger.entity.beforesave', $entityData);
	$em->triggerEvent('vtiger.entity.beforesave.final', $entityData);
	$entityData->focus->column_fields['emailoptout']=$eoo;
	$adb->pquery('update vtiger_account set emailoptout=? where accountid=?', array($eoo, $a['accountid']));
	$em->triggerEvent('vtiger.entity.aftersave', $entityData);
	$em->triggerEvent('vtiger.entity.aftersave.final', $entityData);
}
$ars = $adb->pquery('select contactid from vtiger_contactdetails where email=?', array($email));
while ($c = $adb->fetch_array($ars)) {
	$cefound = true;
	$entityData = VTEntityData::fromEntityId($adb, $c['contactid']);
	$em->triggerEvent('vtiger.entity.beforesave.modifiable', $entityData);
	$em->triggerEvent('vtiger.entity.beforesave', $entityData);
	$em->triggerEvent('vtiger.entity.beforesave.final', $entityData);
	$entityData->focus->column_fields['emailoptout']=$eoo;
	$adb->pquery('update vtiger_contactdetails set emailoptout=? where contactid=?', array($eoo, $c['contactid']));
	$em->triggerEvent('vtiger.entity.aftersave', $entityData);
	$em->triggerEvent('vtiger.entity.aftersave.final', $entityData);
}
$ars = $adb->pquery('select leadid from vtiger_leaddetails where email=?', array($email));
while ($l = $adb->fetch_array($ars)) {
	$cefound = true;
	$entityData = VTEntityData::fromEntityId($adb, $l['leadid']);
	$em->triggerEvent('vtiger.entity.beforesave.modifiable', $entityData);
	$em->triggerEvent('vtiger.entity.beforesave', $entityData);
	$em->triggerEvent('vtiger.entity.beforesave.final', $entityData);
	$entityData->focus->column_fields['emailoptout']=$eoo;
	$adb->pquery('update vtiger_leaddetails set emailoptout=? where leadid=?', array($eoo, $l['leadid']));
	$em->triggerEvent('vtiger.entity.aftersave', $entityData);
	$em->triggerEvent('vtiger.entity.aftersave.final', $entityData);
}

if (!$cefound) {
	header('HTTP/1.0 400 Error no email');
	header('HTTP/1.0 433 No record found with given email');
} else {
	header('HTTP/1.0 200 Ok');
}
die();
?>