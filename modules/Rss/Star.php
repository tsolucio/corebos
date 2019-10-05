<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';

global $adb;

if (isset($_REQUEST['record'])) {
	$recordid = vtlib_purify($_REQUEST['record']);
	$starred = (!isset($_REQUEST['starred']) ? 0 : vtlib_purify($_REQUEST['starred']));
	$result = $adb->pquery('update vtiger_rss set starred=? where vtiger_rssid=?', array($starred, $recordid));
}
header('Location: index.php?module=Rss&action=index');
?>