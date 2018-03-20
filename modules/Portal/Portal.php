<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/

/** Function to save the portal in database
 *  @param $portalname : Type String
 *  @param $portalurl : Type String
 *  This function saves the portal with the given $portalname,$portalurl
 *  This Returns $portalid
 */
function SavePortal($portalname, $portalurl) {
	global $adb;
	$adb->println('just entered the SavePortal method');
	$portalid=$adb->getUniqueID('vtiger_portal');
	$params=array($portalid, $portalname, $portalurl, 0, 0);
	$adb->pquery('insert into vtiger_portal values(?,?,?,?,?)', $params);
	return $portalid;
}

/** Function to update the portal in database
 *  @param $portalname : Type String
 *  @param $portalurl : Type String
 *  @param $portalid : Type Integer
 *  This function updates the portal with the given $portalname,$portalurl
 *  This Returns $portalid
 */
function UpdatePortal($portalname, $portalurl, $portalid) {
	global $adb;
	$adb->println('just entered the SavePortal method');
	$params=array($portalname, $portalurl, $portalid);
	$adb->pquery('update vtiger_portal set portalname=? ,portalurl=? where portalid=?', $params);
	return $portalid;
}
?>
