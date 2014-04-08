<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Webforms/Webforms.php');
require_once('modules/Webforms/model/WebformsModel.php');

global $current_user,$log;
Webforms::checkAdminAccess($current_user);

$webform=Webforms_Model::retrieveWithId(vtlib_purify($_REQUEST['id']));
$webform->delete();

$listURL='index.php?module=Webforms&action=WebformsListView&parenttab=Settings';
header(sprintf("Location: %s",$listURL));
?>
