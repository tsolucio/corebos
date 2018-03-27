<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Type: image/gif');
header('Content-Length: 42');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'); //equivalent to readfile('pixel.gif')
chdir('../..');

require_once 'include/utils/utils.php';

include_once 'config.inc.php';
global $application_unique_key;
if (vtlib_purify($_REQUEST['app_key']) != $application_unique_key) {
	exit;
}

$Vtiger_Utils_Log = false;
global $adb, $current_user;
include_once 'vtlib/Vtiger/Module.php';
// index.php/modules/Emails/TrackAccess.php?record=133&mailid=197&app_key=17565f92d80ec6d79c0d50bd4ce5b05f
$crmid = vtlib_purify($_REQUEST['record']);
$mailid = vtlib_purify($_REQUEST['mailid']);
$current_user = Users::getActiveAdminUser();
$em = new VTEventsManager($adb);
// Initialize Event trigger cache
$em->initTriggerCache();
$entityData = VTEntityData::fromEntityId($adb, $mailid);
//Event triggering code
$em->triggerEvent('vtiger.entity.beforesave', $entityData);
$adb->pquery('INSERT INTO vtiger_email_access(crmid, mailid, accessdate, accesstime) VALUES(?,?,?,?)', array($crmid, $mailid, date('Y-m-d'), date('H:i:s')));

$result = $adb->pquery('select count(*) as count from vtiger_email_access where crmid=? and mailid=?', array($crmid, $mailid));
$count = $adb->query_result($result, 0, 'count');

$result = $adb->pquery('select 1 from vtiger_email_track where crmid=? and mailid=?', array($crmid, $mailid));
if ($result && $adb->num_rows($result)>0) {
	$adb->pquery('update vtiger_email_track set access_count=? where crmid=? and mailid=?', array($count+1, $crmid, $mailid));
} else {
	$adb->pquery('insert into vtiger_email_track(crmid,mailid,access_count) values(?,?,?)', array($crmid, $mailid, 1));
}

//Event triggering code
$entityData->set('access_count', $count + 1);
$em->triggerEvent('vtiger.entity.aftersave', $entityData);
die();
?>